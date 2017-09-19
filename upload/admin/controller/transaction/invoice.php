<?php
require_once(DIR_QC . 'vendor/autoload.php');
require_once(DIR_SYSTEM . 'engine/qccontroller.php');
require_once(DIR_SYSTEM . 'library/quickcommerce/doctrine.php');
require_once(DIR_SYSTEM . 'library/quickcommerce/entity_manager.php');
require_once(DIR_SYSTEM . 'library/quickcommerce/lines.php');
require_once(DIR_SYSTEM . 'library/quickcommerce/session.php');

use App\Resource\Product;
use App\Resource\Invoice;
use App\Resource\InvoiceLine;
//use App\Resource\Language;
use App\Resource\Option;
use App\Resource\ProductOption;
use App\Resource\ProductOptionValue;

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Reader\ArrayReader;
use Ddeboer\DataImport\Reader\OneToManyReader;
use Ddeboer\DataImport\Writer\ArrayWriter;
use Ddeboer\DataImport\Writer\CallbackWriter;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Ddeboer\DataImport\ItemConverter\MappingItemConverter;
use Ddeboer\DataImport\ItemConverter\NestedMappingItemConverter;
use Ddeboer\DataImport\ValueConverter\DateTimeValueConverter;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\NamingStrategy;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Tools\EntityGenerator;
use Doctrine\ORM\Tools\DisconnectedClassMetadataFactory;
use Doctrine\Common\Util\Inflector;
use Doctrine\Common\Util\Debug;

class ControllerTransactionInvoice extends Controller {
	private $error = array();

	/**
	 * @param $registry
	 * @throws Exception
	 * @throws \Doctrine\DBAL\DBALException
	 * @throws \Doctrine\ORM\ORMException
	 */
	function __construct($registry) {
		//if (empty($this->tableName)) // TODO: Interface yo
		//throw new Exception('Mapping table name ($tableName) was not specified in the extending controller class');
		//if (empty($this->joinTableName)) // TODO: Interface yo
		//throw new Exception('Join table name ($joinTableName) was not specified in the extending controller class');
		//if (empty($this->joinCol)) // TODO: Interface yo
		//throw new Exception('Join column name ($joinCol) was not specified in the extending controller class');

		$registry->set('tax', new Tax($registry));
		$registry->set('lines', new Lines($registry)); // Lines requires tax, load in order
		parent::__construct($registry);

		$di = new DoctrineInitializer($this, $registry);
	}

	// TODO: Make some kind of Doctrine helper, or move this to EntityMapper class
	// I'm going to turn this into a static method too
	// This is also in qccontroller class need to refactor
	public function mapDoctrineEntity(&$mappings, $config = array(), $children = false, $foreign = true) {
		try {
			// Being able to nest like this would be cool, but I don't know if there will be issues
			// with the mapping converter if there are similarly named keys...
			/*$this->context->mapDoctrineEntity($mappings, array(
				'OcTransaction' => array(
					'foreign' => 'Transaction',
					'meta' => $tMeta,
					'children' => array(
						'OcInvoice' => array(
							'foreign' => 'Invoice',
							'meta' => $iMeta,
							'children' => array(
								'OcInvoiceLine' => array(
									'foreign' => 'Line',
									'meta' => $ilMeta
								),
								'OcOrderOption' => array(
									'foreign' => 'Option',
									'meta' => $ooMeta
								),
								'OcCustomer' => array(
									'foreign' => 'Customer',
									'meta' => $cMeta
								)
							)
						)
					)
				)
			), 2);*/

			// How many levels of nesting?
			// 0: Root only
			// 1: One level
			// n: n levels
			$children = abs((int)$children);

			$entityName = key($config);
			$params = $config[$entityName];
			$meta = $params['meta'];

			$mapping = array_fill_keys(array_keys($meta->fieldMappings), null);

			// Mapping against the remote entity
			if ($foreign && isset($params['foreign'])) {
				// Get relevant XML node(s);
				$xpath = '//entity[@foreign="' . $params['foreign'] . '"]';
				// TODO: Check to see if key exists - if anything's missing throw an Exception
				foreach($this->mapXml->xpath($xpath) as $map) {
					$fields = $map->xpath('./field | ./id');

					// TODO: This is very similar to EntityMapper::mapFields
					// I might want to do make this a utility method
					foreach ($fields as $field) {
						$attributes = $field->attributes();
						$col = (string)$attributes['column'];
						$field = (string)$attributes['name'];
						$type = (string)$attributes['type'];

						// Is the local model property a complex type?
						//var_dump(isset($attributes['column']));
						if ($field && isset($attributes['column'])) {
							//var_dump($name . ' => ' . $foreign); // Keep this one in here
							//var_dump($attributes);
							if ($field && $type && array_key_exists($field, $mapping))
								$mapping[$field] = $col;
						}

					}

					if (($children != 0) && array_key_exists('children', $params)) {
						$assoc = $map->xpath('./many-to-one');

						foreach ($assoc as $field) {
							$attributes = $field->attributes();
							$type = (string)$attributes['type'];
							$joinColAttr = false;
							$field = (string)$attributes['field'];
							$childEntityName = (string)$attributes['target-entity'];

							// Children should have been provided in params
							if (array_key_exists($childEntityName, $params['children'])) {
								$childMapping = [];


								$this->mapDoctrineEntity($childMapping, array(
									$childEntityName => $params['children'][$childEntityName]
								), false); // Do not automatically map children

								$mapping[$field] = $childMapping;
							}
						}

						$assoc = $map->xpath('./one-to-many');

						foreach ($assoc as $field) {
							$attributes = $field->attributes();
							$type = (string)$attributes['type'];
							$joinColAttr = false;
							$field = (string)$attributes['field'];
							$childEntityName = (string)$attributes['target-entity'];

							// Children should have been provided in params
							if (array_key_exists($childEntityName, $params['children'])) {
								$childMapping = [];

								$this->mapDoctrineEntity($childMapping, array(
									$childEntityName => $params['children'][$childEntityName]
								), false); // Do not automatically map children

								$mapping[$field] = array($childMapping);
							}
						}
					}

					/*$tableize = false; // TODO: What is this for again?

					if (isset($metadata)) {
						if (property_exists($metadata, 'associationmapping')) {
							foreach ($metadata->associationmapping as $field => $mapping) {
								$key = ($tableize == true) ? Inflector::tableize($field) : $field;
							}
						}
					}*/
				}
			} else {
				// Just tableize the field name if we're mapping a local entity
				foreach ($mapping as $field => $col) {
					$mapping[$field] = Inflector::tableize($field);
				}

				// TODO: Fix me!
				if (($children != 0) && array_key_exists('children', $params)) {
					$assoc = $meta->getAssociationMappings();

					foreach ($assoc as $field) {
						$childEntityName = (string)$field['targetEntity'];

						//if ($childEntityName != 'OcStore') continue; // TODO: TEMP Fix

						// Children should have been provided in params
						if (array_key_exists($childEntityName, $params['children'])) {
							$childMapping = [];

							$this->mapDoctrineEntity($childMapping, array(
								$childEntityName => $params['children'][$childEntityName]
							), false, false); // Do not automatically map children and it's a local op too

							$mapping[$field['fieldName']] = $childMapping;
						}
					}

					// TODO: Other association types?
				}
			}

			$mappings = array_merge($mappings, $mapping);
		} catch (Exception $e) {
			// Fail silently for now
			throw $e;
		}
	}

	public function store() {
		$this->load->language('report/product_purchased');

		//$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = '2015-12-30'; // TODO: Set to one month before last sale
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = '2016-02-29'; // TODO: Set to date of last sale
		}

		if (isset($this->request->get['filter_order_status_id'])) {
			$filter_order_status_id = $this->request->get['filter_order_status_id'];
		} else {
			$filter_order_status_id = 0;
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		} else {
			$url .= 'filter_date_start=2015-12-30';
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		} else {
			$url .= '&filter_date_end=2016-02-29';
		}

		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		/*$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('report/product_purchased', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);*/

		$this->load->model('report/product');

		$data['products'] = array();

		$filter_data = array(
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
			'filter_order_status_id' => $filter_order_status_id,
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);

		$product_total = $this->model_report_product->getTotalPurchased($filter_data);

		// TODO: OCMod editited report model getPurchased to include id in query
		$results = $this->model_report_product->getPurchased($filter_data);

		$this->load->model('catalog/product');
		foreach ($results as $result) {

			$product = $this->model_catalog_product->getProduct((int)$result['product_id']);
			$result = array_merge($product, $result);

			$data['products'][] = array(
				'product_id' => (int)$result['product_id'],
				'name'       => $result['name'],
				'model'      => $result['model'],
				'quantity'   => $result['quantity'],
				'price'      => (isset($product['product_id'])) ? $result['price'] : 0.0000,// TODO: Adjust for customer group?
				'cost'       => (isset($result['cost'])) ? $result['cost'] : 5.00,
				'total'      => $result['total'],
				'price_formatted'      =>  (isset($product['product_id'])) ? $this->currency->format($result['price'], $this->config->get('config_currency')): null, // TODO: Adjust for customer group?
				'total_formatted'      => $this->currency->format($result['total'], $this->config->get('config_currency'))
			);
		}

		// Totals
		$this->load->model('extension/extension');

		// Totals aren't working for this, quick fix...

		// Cart
		$this->registry->set('tax', new Tax($this->registry));
		$this->registry->set('customer', new Customer($this->registry));
		$this->registry->set('cart', new Cart($this->registry));

		$this->cart->getProducts();

		$total_data = array();
		$total = 0;
		$taxes = $this->cart->getTaxes();

		// We would need to upgrade to OC 2.2 to leverage cart controller events (pre/post)
		// Cart is not a final class so we can extend it instead
		//var_dump($this->cart->getProducts());

		$sort_order = array();

		$results = $this->model_extension_extension->getExtensions('total');


		foreach ($results as $key => $value) {
			$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
		}

		array_multisort($sort_order, SORT_ASC, $results);

		foreach ($results as $result) {
			if ($this->config->get($result['code'] . '_status')) {
				$this->load->model('total/' . $result['code']);

				$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
			}
		}

		$sort_order = array();

		foreach ($total_data as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}

		array_multisort($sort_order, SORT_ASC, $total_data);

		$data['totals'] = array();

		foreach ($total_data as $total) {
			$data['totals'][] = array(
				'title' => $total['title'],
				'text'  => $this->currency->format($total['value'])
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_all_status'] = $this->language->get('text_all_status');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_total'] = $this->language->get('column_total');

		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_filter'] = $this->language->get('button_filter');

		$data['token'] = $this->session->data['token'];

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$url = '';

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}

		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/product_purchased', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));

		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;
		$data['filter_order_status_id'] = $filter_order_status_id;

		return $data;
	}

	public function index() {
		$this->load->language('transaction/invoice');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('resource/transaction');
		$tModel = &$this->model_resource_transaction;

		$tService = new TransactionInvoice($this, 'OcInvoice');
		$tService->setEntityManager($this->em);
		$tModel->setTransactionType($tService);

		$this->document->addScript('view/javascript/quickcommerce/qc_invoice.js');

		$this->getList();

		//$this->testMax($tService); // Test
	}

	/**
	 * Test methods - move these to unit testing later
	 */
	private function testMax($service) {
		//var_dump(get_class_methods($service));

		$where = new \Doctrine\ORM\Query\Expr();
		$where = $where->eq('i.invoicePrefix', (new \Doctrine\ORM\Query\Expr())->literal('CT'));
		$result = $service->getMax('i', 'invoiceNo', $where);

		Debug::dump($result[0]);
	}

	/**
	 * Credits mcuadros/currency-detector
	 * https://github.com/mcuadros/currency-detector
	 * TODO: Move this to a utility class or something
	 */
	public static function cleanCurrency($money) {
		$cleanString = preg_replace('/([^0-9\.,])/i', '', $money);
		$onlyNumbersString = preg_replace('/([^0-9])/i', '', $money);

		$separatorsCountToBeErased = strlen($cleanString) - strlen($onlyNumbersString) - 1;

		$stringWithCommaOrDot = preg_replace('/([,\.])/', '', $cleanString, $separatorsCountToBeErased);
		$removedThousendSeparator = preg_replace('/(\.|,)(?=[0-9]{3,}$)/', '',  $stringWithCommaOrDot);

		return (float) str_replace(',', '.', $removedThousendSeparator);
	}

	public function post($close = false) {
		// Test run
		$this->load->language('api/order');

		$json = array();

		//var_dump($this->session->data);

		//if (!isset($this->session->data['api_id'])) {
		//$json['error'] = $this->language->get('error_permission');
		//} else {
		// Customer
		/*if (!isset($this->session->data['customer'])) {
            $json['error'] = $this->language->get('error_customer');
        }*/

		// Payment Address
		/*if (!isset($this->session->data['payment_address'])) {
            $json['error'] = $this->language->get('error_payment_address');
        }*/

		// Payment Method
		/*if (!isset($this->session->data['payment_method'])) {
            $json['error'] = $this->language->get('error_payment_method');
        }*/

		// Shipping
		//if ($this->lines->hasShipping()) {
		// Shipping Address
		/*if (!isset($this->session->data['shipping_address'])) {
            $json['error'] = $this->language->get('error_shipping_address');
        }*/

		// Shipping Method
		/*if (!isset($this->request->post['shipping_method'])) {
            $json['error'] = $this->language->get('error_shipping_method');
        }*/
		//} else {
		//ModelOcSessionShipping::clearShipping($this);
		//}

		// Cart
		if ((/*!$this->lines->hasLines() &&*/ empty($this->session->data['vouchers'])) || (!$this->lines->hasStock() && !$this->config->get('config_stock_checkout'))) {
			//$json['error'] = $this->language->get('error_stock');
		}

		//Debug::dump($this->lines);
		// Validate minimum quantity requirements.

		/*foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $json['error'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);

                break;
            }
        }*/

		//$lines = $this->model_resource_transaction->getLineItems($invoice_id);
		$lines = $this->load->controller('api/lines/lines', array('export' => false));
		//var_dump($json);
		if (!$json) {
			$invoice_data = array();

			// Store Details
			$invoice_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
			$invoice_data['store_id'] = ($this->config->get('config_store_id') != null) ? $this->config->get('config_store_id') : 0;
			$invoice_data['store_name'] = $this->config->get('config_name');
			$invoice_data['store_url'] = $this->config->get('config_url');

			$invoice_data['oc_entity_id'] = null; // TODO: WTF

			$customer = ModelOcSessionCustomer::getCustomer($this);

			$invoice_data = array_merge($invoice_data, $customer);
			$invoice_data['bill_email'] = $customer['email'];
			$invoice_data['bill_telephone'] = $customer['telephone'];
			$invoice_data['bill_fax'] = $customer['fax'];

			$payment = ModelOcSessionPayment::getPayment($this);
			//echo 'Payment';
			//var_dump($payment);
			$invoice_data = array_merge($invoice_data, array(
				'bill_addr_line1' => $payment['payment_address_1'],
				'bill_addr_line2' => $payment['payment_address_2'],
				'bill_addr_firstname' => $payment['payment_firstname'],
				'bill_addr_lastname' => $payment['payment_lastname'],
				'bill_addr_company' => $payment['payment_company'],
				'bill_addr_city' => $payment['payment_city'],
				'bill_addr_country' => $payment['payment_country'],
				'bill_addr_country_id' => $payment['payment_country_id'],
				'bill_addr_zone' => $payment['payment_zone'],
				'bill_addr_zone_id' => $payment['payment_zone_id']
			));

			// Shipping Details
			//if ($this->lines->hasShipping()) {
			$shipping = ModelOcSessionShipping::getShipping($this);
			$invoice_data = array_merge($invoice_data, array(
				'ship_addr_line1' => $shipping['shipping_address_1'],
				'ship_addr_line2' => $shipping['shipping_address_2'],
				'ship_addr_firstname' => $shipping['shipping_firstname'],
				'ship_addr_lastname' => $shipping['shipping_lastname'],
				'ship_addr_company' => $shipping['shipping_company'],
				'ship_addr_city' => $shipping['shipping_city'],
				'ship_addr_country' => $shipping['shipping_country'],
				'ship_addr_country_id' => $shipping['shipping_country_id'],
				'ship_addr_zone' => $shipping['shipping_zone'],
				'ship_addr_zone_id' => $shipping['shipping_zone_id']
			));
			//} else {
			/*$invoice_data['shipping_firstname'] = '';
            $invoice_data['shipping_lastname'] = '';
            $invoice_data['shipping_company'] = '';
            $invoice_data['shipping_address_1'] = '';
            $invoice_data['shipping_address_2'] = '';
            $invoice_data['shipping_city'] = '';
            $invoice_data['shipping_postcode'] = '';
            $invoice_data['shipping_zone'] = '';
            $invoice_data['shipping_zone_id'] = '';
            $invoice_data['shipping_country'] = '';
            $invoice_data['shipping_country_id'] = '';
            $invoice_data['shipping_address_format'] = '';
            $invoice_data['shipping_custom_field'] = array();
            $invoice_data['shipping_method'] = '';
            $invoice_data['shipping_code'] = '';
        }*/

			// Products
			$invoice_data['products'] = array();

			/*foreach ($this->lines->getProducts() as $product) {
                $option_data = array();

                foreach ($product['option'] as $option) {
                    $option_data[] = array(
                        'product_option_id'       => $option['product_option_id'],
                        'product_option_value_id' => $option['product_option_value_id'],
                        'option_id'               => $option['option_id'],
                        'option_value_id'         => $option['option_value_id'],
                        'name'                    => $option['name'],
                        'value'                   => $option['value'],
                        'type'                    => $option['type']
                    );
                }

                $invoice_data['products'][] = array(
                    'product_id' => $product['product_id'],
                    'name'       => $product['name'],
                    'model'      => $product['model'],
                    'option'     => $option_data,
                    'download'   => $product['download'],
                    'quantity'   => $product['quantity'],
                    'subtract'   => $product['subtract'],
                    'price'      => $product['price'],
                    'total'      => $product['total'],
                    'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
                    'reward'     => $product['reward']
                );
            }*/

			// Need to set lines to session
			foreach ($lines['lines'] as $line) {
				// Get extra line props from raw key
				$line['line_id'] = (isset($line['rawkey']['line_id'])) ? $line['rawkey']['line_id'] : null;
				$line['detail_type'] = (isset($line['rawkey']['detail_type'])) ? $line['rawkey']['detail_type'] : null;
				$line['invoice_id'] = (isset($line['rawkey']['invoice_id'])) ? $line['rawkey']['invoice_id'] : null;
				$line['order_product_id'] = (isset($line['rawkey']['order_product_id'])) ? $line['rawkey']['order_product_id'] : null;

				$options = array(); // TODO: I need to enforce order product id - it is required later
				if (isset($line['invoice_id']) && isset($line['order_product_id'])) {
					$options = $this->model_sale_order->getOrderOptions($line['invoice_id'], $line['order_product_id']);
				}

				// TODO: Modify whatever's converting the entity to an array so we don't have to do this
				// Either that or make a utility method to get assoc keys
				$product_id = null;
				if (isset($line['product']) && isset($line['product']['productId'])) {
					$product_id = $line['product']['productId'];
				} elseif (isset($line['product_id'])) {
					$product_id = $line['product_id'];
				}

				$invoice_data['lines'][] = array(
					'line_id'			=> $line['line_id'],
					'detail_type'		=> $line['detail_type'],
					'invoice_id'			=> $line['invoice_id'],
					'order_product_id'	=> $line['invoice_id'],
					'product_id'		=> $product_id,
					'name'			=> (isset($line['name'])) ? $line['name'] : '',
					'model'			=> (isset($line['model'])) ? $line['model'] : '',
					//'option'		=> (isset($line['invoice_id'])) ? $this->model_sale_order->getOrderOptions($data['invoice_id'], $line['order_product_id']) : null,
					'quantity'		=> (isset($line['quantity'])) ? (int)$line['quantity'] : 1,
					'revenue'    	=> (isset($line['revenue'])) ? self::cleanCurrency($line['revenue']) : '',
					'vest'       	=> (isset($line['vest'])) ? self::cleanCurrency($line['vest']) : '',
					'price'      	=> (isset($line['price'])) ? self::cleanCurrency($line['price']) : 0.00,
					'royalty'    	=> (isset($line['royalty'])) ? self::cleanCurrency($line['royalty']) : '',
					'reward'     	=> (isset($line['reward'])) ? self::cleanCurrency($line['reward']) : null,
					'total'      	=> (isset($line['total'])) ? self::cleanCurrency($line['total']) : 0.00,
					'tax'      		=> (isset($line['tax'])) ? $line['tax'] : 0.00,
					'tax_class_id'  => (isset($line['tax_class_id'])) ? (int)$line['tax_class_id'] : 4
				);

				/*if ($product_id != null) {
                    $this->lines->add($product_id, $line['quantity'], $options);
                } elseif (isset($line['model'])) {
                    $this->lines->addDescription($line['model'], $line['quantity']);
                }*/
			}

			// Gift Voucher
			$invoice_data['vouchers'] = array();

			$vouchers = ModelOcSessionVoucher::getVouchers($this);
			if ($vouchers) {
				$invoice_data = array_merge($invoice_data, $vouchers);
			}

			// Order Totals
			$this->load->model('extension/extension');

			$invoice_data['totals'] = array();
			$total = 0;
			$taxes = $this->lines->getTaxes();

			$sort_order = array();

			$results = $this->model_extension_extension->getExtensions('total');

			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
			}

			array_multisort($sort_order, SORT_ASC, $results);

			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('total/' . $result['code']);

					$this->{'model_total_' . $result['code']}->getTotal($invoice_data['totals'], $total, $taxes, true);
				}
			}

			$sort_order = array();

			foreach ($invoice_data['totals'] as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $invoice_data['totals']);

			$invoice_data['total'] = $total;

			if (isset($this->request->post['affiliate_id']) && is_int($this->request->post['affiliate_id'])) {
				$subtotal = $this->lines->getSubTotal();

				// Affiliate
				$this->load->model('affiliate/affiliate');

				$affiliate_info = $this->model_affiliate_affiliate->getAffiliate($this->request->post['affiliate_id']);

				if ($affiliate_info) {
					$invoice_data['affiliate_id'] = $affiliate_info['affiliate_id'];
					$invoice_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
				} else {
					$invoice_data['affiliate_id'] = 0;
					$invoice_data['commission'] = 0;
				}

				// Marketing
				$invoice_data['marketing_id'] = 0;
				$invoice_data['tracking'] = '';
			} else {
				$invoice_data['affiliate_id'] = 0;
				$invoice_data['commission'] = 0;
				$invoice_data['marketing_id'] = 0;
				$invoice_data['tracking'] = '';
			}

			$invoice_data['language_id'] = $this->config->get('config_language_id');
			$invoice_data['currency_id'] = $this->currency->getId();
			$invoice_data['currency_code'] = $this->currency->getCode();
			$invoice_data['currency_value'] = $this->currency->getValue($this->currency->getCode());
			$invoice_data['ip'] = $this->request->server['REMOTE_ADDR'];

			//var_dump($invoice_data);

			if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
				$invoice_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
			} elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
				$invoice_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
			} else {
				$invoice_data['forwarded_ip'] = '';
			}

			if (isset($this->request->server['HTTP_USER_AGENT'])) {
				$invoice_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
			} else {
				$invoice_data['user_agent'] = '';
			}

			if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
				$invoice_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
			} else {
				$invoice_data['accept_language'] = '';
			}

			// TODO: Create order if necessary?
			//$this->load->model('checkout/order');
			//$json['invoice_id'] = $this->model_checkout_order->addOrder($invoice_data);

			//$this->load->model('transaction/invoice');

			if (isset($this->request->post['invoice_status_id'])) {
				$invoice_data['invoice_status_id'] = $this->request->post['invoice_status_id'];
			} else {
				$invoice_data['invoice_status_id'] = 1; //$this->config->get('config_invoice_status_id');
			}

			// Set the order history
			if (isset($this->request->post['order_status_id'])) {
				$invoice_data['order_status_id'] = $this->request->post['order_status_id'];
			} else {
				$invoice_data['order_status_id'] = $this->config->get('config_order_status_id');
			}

			if (isset($this->request->post['invoice_date'])) {
				$invoice_data['invoice_date'] = new DateTime($this->request->post['invoice_date']);
			} else {
				$invoice_data['invoice_date'] = new DateTime('now');
			}

			if (isset($this->request->post['due_date'])) {
				$invoice_data['due_date'] = new DateTime($this->request->post['due_date']);
			} else {
				if (isset($invoice_data['invoice_date'])) {
					$invoice_data['due_date'] = (new DateTime($this->request->post['invoice_date']))->modify('+1 month');
				}
			}

			if (isset($this->request->post['customer_memo'])) {
				$invoice_data['customer_memo'] = htmlspecialchars($this->request->post['customer_memo']);
			} else {
				$invoice_data['customer_memo'] = '';
			}

			if (isset($this->request->post['statement_memo'])) {
				$invoice_data['statement_memo'] = htmlspecialchars($this->request->post['statement_memo']);
			} else {
				$invoice_data['statement_memo'] = '';
			}

			//$this->model_checkout_order->addOrderHistory($json['invoice_id'], $order_status_id);

			$json['success'] = $this->language->get('text_success');
		}
		//}

		return $invoice_data;
	}

	public function add() {
		$this->load->language('transaction/invoice');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('resource/transaction');
		$tModel = &$this->model_resource_transaction;

		$tService = new TransactionInvoice($this, 'OcInvoice');
		$tService->setEntityManager($this->em);
		$tModel->setTransactionType($tService);

		$this->load->model('sale/order');

		unset($this->session->data['cookie']);

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$model = null;
			if ($this->validate()) {
				$model = $this->post();
				$now = date('Y-m-d H:i:s');

				$model = array(
					'transaction_id' => null, // No way these can be set
					'store_id' => (isset($model['store_id'])) ? $model['store_id'] : 0,
					'store_name' => (isset($model['store_name'])) ? $model['store_name'] : '',
					'store_url' => (isset($model['store_url'])) ? $model['store_url'] : '',
					'currency_id' => (isset($model['currency_id'])) ? $model['currency_id'] : '',
					'currency_code' => (isset($model['currency_code'])) ? $model['currency_code'] : '',
					'currency_value' => (isset($model['currency_value'])) ? $model['currency_value'] : '',
					'forwarded_ip' => (isset($model['forwarded_ip'])) ? $model['forwarded_ip'] : '',
					'user_agent' => (isset($model['accept_language'])) ? $model['accept_language'] : '',
					'date_added' => (isset($model['date_added'])) ? $model['date_added'] : $now, // Now
					'date_modified' => $now, // Now,
					'invoice' => $model
				);

				//$model['invoice']['lines'] = array();
			}

			if ($model != null) {
				$this->event->trigger('pre.admin.invoice.add');

				$invoiceId = $tService->addTransaction($model);

				// TODO: If success...
				$this->response->addHeader('Content-Type: application/json');

				if ($invoiceId) {
					$this->event->trigger('post.admin.invoice.add', $invoiceId);

					$json['invoice_id'] = $invoiceId;
					$json['redirect'] = $this->url->link('transaction/invoice/edit&invoice_id=' . $invoiceId . '&token=' . $this->session->data['token'], 'SSL');
				}

				$this->response->setOutput(json_encode($json));

				return;
			}
		} else {
			$this->getForm('add');
		}
	}

	public function edit() {
		$this->load->model('sale/order');

		$this->load->model('resource/transaction');
		$tModel = &$this->model_resource_transaction;

		$tService = new TransactionInvoice($this, 'OcInvoice');
		$tService->setEntityManager($this->em);
		$tModel->setTransactionType($tService);

		unset($this->session->data['cookie']);

		if (isset($this->request->get['invoice_id']) && ($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$invoice_info = $this->model_resource_transaction->getTransaction($this->request->get['invoice_id']);

			$model = null;
			if ($this->validate()) {
				$model = $this->post();

				$lines = $model['lines'];
				unset($model['lines']); // TODO: Lines are/may be attached to the wrong branch of the array? Quick fix

				$model = array(
					'transaction_id' => (isset($model['transaction_id'])) ? $model['transaction_id'] : $invoice_info['transaction_id'],
					'oc_entity_id' => (isset($model['oc_entity_id'])) ? $model['oc_entity_id'] : $invoice_info['oc_entity_id'],
					'store_id' => (isset($model['store_id'])) ? $model['store_id'] : $invoice_info['store_id'],
					'store_name' => (isset($model['store_name'])) ? $model['store_name'] : $invoice_info['store_name'],
					'store_url' => (isset($model['store_url'])) ? $model['store_url'] : $invoice_info['store_url'],
					'currency_id' => (isset($model['currency_id'])) ? $model['currency_id'] : $invoice_info['currency_id'],
					'currency_code' => (isset($model['currency_code'])) ? $model['currency_code'] : $invoice_info['currency_code'],
					'currency_value' => (isset($model['currency_value'])) ? $model['currency_value'] : $invoice_info['currency_value'],
					'forwarded_ip' => (isset($model['forwarded_ip'])) ? $model['forwarded_ip'] : $invoice_info['forwarded_ip'],
					'user_agent' => (isset($model['accept_language'])) ? $model['accept_language'] : $invoice_info['accept_language'],
					'date_added' => (isset($model['date_added'])) ? $model['date_added'] : $invoice_info['date_added'],
					'date_modified' => date('Y-m-d H:i:s'), // Now
					'invoice' => array_merge($invoice_info, $model)
				);

				$model['invoice']['lines'] =  $lines;
				//var_dump($model);
				//exit;
			}

			if ($model != null) {
				$this->event->trigger('pre.admin.invoice.edit', $this->request->get['invoice_id']);

				$tService->editTransaction($model);

				$this->event->trigger('post.admin.invoice.edit', $this->request->get['invoice_id']);
			}
		} else {
			$this->getForm('edit');
		}
	}

	public function delete() {
		$this->load->language('transaction/invoice');

		$this->document->setTitle($this->language->get('heading_title'));

		unset($this->session->data['cookie']);

		$this->load->model('sale/order');

		$this->load->model('resource/transaction');
		$tModel = &$this->model_resource_transaction;

		$tService = new TransactionInvoice($this, 'OcInvoice');
		$tService->setEntityManager($this->em);
		$tModel->setTransactionType($tService);

		if (isset($this->request->get['invoice_id']) && ($this->request->server['REQUEST_METHOD'] == 'GET')) {
			$invoice_info = $tService->getEntity($this->request->get['invoice_id']);

			$model = null;
			if ($this->validate()) {
				$model = $this->post();
				$model = array_merge(array(
					'transaction_id' => $invoice_info['transaction_id'],
					'oc_entity_id' => $invoice_info['oc_entity_id'],
					'store_id' => $invoice_info['store_id'],
					'store_name' => $invoice_info['store_name'],
					'store_url' => $invoice_info['store_url'],
					'date_added' => $invoice_info['date_added'],
					'date_modified' => $invoice_info['date_added'],
					'invoice' => $invoice_info
				), $model);

				//var_dump($model);
				//exit;
				$model['invoice']['lines'] = $model['lines'];
				unset($model['lines']); // TODO: Lines are/may be attached to the wrong branch of the array? Quick fix
			}

			if ($model != null) {
				$tService->deleteTransaction($model);
			}
		}

		if (isset($response['error'])) {
			$this->error['warning'] = $response['error'];
		}

		// Quick fix
		//if (isset($response['success'])) {
		$this->session->data['success'] = $response['success'];

		$url = '';

		if (isset($this->request->get['filter_invoice_id'])) {
			$url .= '&filter_invoice_id=' . $this->request->get['filter_invoice_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_invoice_status'])) {
			$url .= '&filter_invoice_status=' . $this->request->get['filter_invoice_status'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->response->redirect($this->url->link('transaction/invoice', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		//}

		$this->document->addScript('view/javascript/quickcommerce/qc_invoice.js');

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_invoice_id'])) {
			$filter_invoice_id = $this->request->get['filter_invoice_id'];
		} else {
			$filter_invoice_id = null;
		}

		if (isset($this->request->get['filter_customer'])) {
			$filter_customer = $this->request->get['filter_customer'];
		} else {
			$filter_customer = null;
		}

		if (isset($this->request->get['filter_invoice_status'])) {
			$filter_invoice_status = $this->request->get['filter_invoice_status'];
		} else {
			$filter_invoice_status = null;
		}

		if (isset($this->request->get['filter_total'])) {
			$filter_total = $this->request->get['filter_total'];
		} else {
			$filter_total = null;
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'invoiceId';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_invoice_id'])) {
			$url .= '&filter_invoice_id=' . $this->request->get['filter_invoice_id'];
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_invoice_status'])) {
			$url .= '&filter_invoice_status=' . $this->request->get['filter_invoice_status'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data = $this->load->language('transaction/invoice');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('transaction/invoice', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['invoice'] = $this->url->link('transaction/invoice/invoice', 'token=' . $this->session->data['token'], 'SSL');
		$data['shipping'] = $this->url->link('transaction/invoice/shipping', 'token=' . $this->session->data['token'], 'SSL');
		$data['add'] = $this->url->link('transaction/invoice/add', 'token=' . $this->session->data['token'], 'SSL');

		$data['invoices'] = array();

		$limit = $this->config->get('config_limit_admin');
		$filter_data = array(
			'filter_invoice_id'    => $filter_invoice_id,
			'filter_customer'	   => $filter_customer,
			'filter_invoice_status'  => $filter_invoice_status,
			'filter_total'         => $filter_total,
			'filter_date_added'    => $filter_date_added,
			'filter_date_modified' => $filter_date_modified,
			'sort'                 => $sort,
			'order'                => $order,
			'start'                => ($page - 1) * $limit,
			'limit'                => $limit
		);

		$total = $this->model_resource_transaction->getTotalTransactions();

		$results = $this->model_resource_transaction->getTransactions($filter_data);

		// Not sure if this is the right model to be dumping everything... should really just call the appropriate service
		$data['invoice_statuses'] = $this->model_resource_transaction->getInvoiceStatuses();

		if ($results != null) {
			$this->load->model('sale/customer');

			foreach ($results as $result) {
				$customer = false;
				if (isset($result['customer']) && isset($result['customer']['customerId'])) {
					$customer = array();
					$customer['customer_id'] = $result['customer']['customerId'];
					// Shouldn't be an invoice without a customer ID
					if (is_numeric($customer['customer_id'])) {
						$customer = $this->model_sale_customer->getCustomer($customer['customer_id']);
					}
				}

				// TODO: This is temporary implementation for customers, I think I'm going to get getTransactions return unserialized entities
				//$status = $this->model_resource_transaction->getInvoiceStatus($result['invoice_id']);

				// TODO: I am using an anon func to do this in getForm... change to use convertDateArray
				$invoiceDate = '';
				if (isset($result['invoice_date'])) {
					$invoiceDate = date($this->language->get('date_format_short'), self::convertDateArray($result['invoice_date'])->getTimestamp());
				}

				$dueDate = '';
				if (isset($result['due_date'])) {
					$dueDate = date($this->language->get('date_format_short'), self::convertDateArray($result['due_date'])->getTimestamp());
				}

				$data['invoices'][] = array(
					'transaction_id'    => $result['transaction_id'],
					'invoice_id'    => $result['invoice_id'],
					'invoice_no'    => $result['invoice_no'],
					'order_id'      => (isset($result['oc_entity_id'])) ? $result['oc_entity_id'] : '',
					'order_url'     => (isset($result['oc_entity_id'])) ? $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['oc_entity_id'] . $url, 'SSL') : '',
					'feed_id'		=> $result['feed_id'],
					'customer_name' => htmlspecialchars_decode(implode(' ', [$result['firstname'], $result['lastname']])),
					'customer'      => $customer,
					'email'         => $result['bill_email'],
					//'status'        => (isset($status) && isset($status['name'])) ? $status['name'] : 'N/A',
					// TODO: This is a temporary status implementation
					'status'        => (isset($result['feed_id']) && (int)$result['feed_id'] > 0) ? 'Posted' : 'Not Posted',
					'total'         => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
					'date_added'    => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
					'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
					'invoice_date'  => $invoiceDate, // TODO: I am using an anon func to do this in getForm... change to use convertDateArray
					'due_date'      => $dueDate, // TODO: I am using an anon func to do this in getForm... change to use convertDateArray
					'shipping_code' => $result['shipping_code'],
					'view'          => $this->url->link('transaction/invoice/invoice', 'token=' . $this->session->data['token'] . '&invoice_id=' . $result['invoice_id'] . $url, 'SSL'),
					'edit'          => $this->url->link('transaction/invoice/edit', 'token=' . $this->session->data['token'] . '&invoice_id=' . $result['invoice_id'] . $url, 'SSL'),
					'delete'        => $this->url->link('transaction/invoice/delete', 'token=' . $this->session->data['token'] . '&invoice_id=' . $result['invoice_id'] . $url, 'SSL')
				);
			}
		} else {
			// Error
		}


		$data['token'] = $this->session->data['token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_invoice_id'])) {
			$url .= '&filter_invoice_id=' . $this->request->get['filter_invoice_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_order'] = $this->url->link('transaction/invoice', 'token=' . $this->session->data['token'] . '&sort=o.invoice_id' . $url, 'SSL');
		$data['sort_customer'] = $this->url->link('transaction/invoice', 'token=' . $this->session->data['token'] . '&sort=customer' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('transaction/invoice', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');
		$data['sort_total'] = $this->url->link('transaction/invoice', 'token=' . $this->session->data['token'] . '&sort=o.total' . $url, 'SSL');
		$data['sort_date_added'] = $this->url->link('transaction/invoice', 'token=' . $this->session->data['token'] . '&sort=o.date_added' . $url, 'SSL');
		$data['sort_date_modified'] = $this->url->link('transaction/invoice', 'token=' . $this->session->data['token'] . '&sort=o.date_modified' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['filter_invoice_id'])) {
			$url .= '&filter_invoice_id=' . $this->request->get['filter_invoice_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $total;
		$pagination->page = $page;
		$pagination->limit = 50; //$this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('transaction/invoice', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($total - $limit)) ? $total : ((($page - 1) * $limit) + $limit), $total, ceil($total / $limit));

		$data['filter_invoice_id'] = $filter_invoice_id;
		$data['filter_customer'] = $filter_customer;
		$data['filter_invoice_status'] = $filter_invoice_status;
		$data['filter_total'] = $filter_total;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_date_modified'] = $filter_date_modified;

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		// Get invoice statuses
		// Not sure if this is the right model to be dumping everything... should really just call the appropriate service
		$data['invoice_statuses'] = $this->model_resource_transaction->getInvoiceStatuses();

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('transaction/invoice_list.tpl', $data));
	}

	public function convertOrder() {
		$this->response->redirect($this->url->link('qc/invoice/convertOrder', 'token=' . $this->session->data['token'] . '&order_id=' . $this->request->get['order_id'], 'SSL'));
	}

	public function getForm($action = 'add') {
		$this->load->model('sale/customer');
		$this->load->model('catalog/product');

		$invoice_id = (isset($this->request->get['invoice_id'])) ? $this->request->get['invoice_id'] : null;

		$data = array_merge(
			$this->language->load('module/payment_processor'),
			$this->load->language('transaction/invoice')
		);

		$data['action'] = $action;

		if ($action == 'add') {
			$this->document->setTitle($this->language->get('heading_title_add'));
			$data['text_form'] = $this->language->get('text_add');
		} elseif ($action == 'edit') {
			$this->document->setTitle($this->language->get('heading_title_edit'));
			$data['text_form'] = $this->language->get('text_edit');
		}

		$data['heading_title'] = $this->language->get('heading_title');
		$data['heading_title_add'] = $this->language->get('heading_title_add');
		$data['heading_title_edit'] = $this->language->get('heading_title_edit');

		$data['token'] = $this->session->data['token'];

		$this->error = (isset($this->session->data['ipp_error'])) ? array_merge($this->session->data['ipp_error']) : $this->error;

		if (isset($this->error['warning'])) {
			if (is_array($this->error['warning'])) {
				$warning = $this->error['warning'];

				$msg = '<b>' . $warning['error'] . '</b> ' . $warning['message'] . '.<br>';
				$msg .= '<ul style="list-style-type: none; padding-left: 12px"><li><b>' . $warning['code'] . '</b>: ' . $warning['detail'] . '</li></ul>';

				$data['error_warning'] = $msg;
			} else {
				$data['error_warning'] = $this->error['warning'];
			}
		} else {
			$data['error_warning'] = '';

			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];

				unset($this->session->data['success']);
			} else {
				$data['success'] = '';
			}
		}

		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = '2015-12-30'; // TODO: Set to one month before last sale
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = '2016-02-29'; // TODO: Set to date of last sale
		}

		$url = '';

		if (isset($this->request->get['filter_invoice_id'])) {
			$url .= '&filter_invoice_id=' . $this->request->get['filter_invoice_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		} else {
			$url .= 'filter_date_start=2015-12-30';
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		} else {
			$url .= '&filter_date_end=2016-02-29';
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('transaction/invoice', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		if (isset($this->request->get['invoice_id'])) {
			$data['shipping'] = $this->url->link('transaction/invoice/shipping', 'token=' . $this->session->data['token'] . '&invoice_id=' . (int)$this->request->get['invoice_id'], 'SSL'); // TODO: This should be using order id instead?
			$data['invoice'] = $this->url->link('transaction/invoice/invoice', 'token=' . $this->session->data['token'] . '&invoice_id=' . (int)$this->request->get['invoice_id'], 'SSL');
		} else {
			$data['shipping'] = false;
			$data['invoice'] = false;
		}

		$data['cancel'] = $this->url->link('transaction/invoice', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;

		if (isset($this->request->get['invoice_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$invoice_info = $this->model_resource_transaction->getTransaction($invoice_id);
		}

		// TODO: Static method in utility class
		$checkIfIsDateArray = function($arr) {
			$isDate = (array_key_exists('date', $arr) && array_key_exists('timezone_type', $arr) && array_key_exists('timezone', $arr));

			return $isDate;
		};

		// TODO: Static method in utility class
		$convertDateArray = function($arr) {
			$date = new DateTime($arr['date']);
			$date->setTimezone(new DateTimeZone($arr['timezone']));

			return $date->format('Y-m-d');
		};

		// Clear session and get any existing line items
		$this->lines->clear();

		if (!empty($invoice_info)) {
			foreach ($invoice_info as $field => $val) {
				if (is_array($val) && $checkIfIsDateArray($val)) {
					$date = $convertDateArray($val);
					$data[$field] = $date;
				} else {
					$data[$field] = $val;
				}
			}

			unset($data['custom_field']);
			$data['account_custom_field'] = (isset($invoice_info['custom_field'])) ? $invoice_info['custom_field'] : null;

			// TODO: Description, and other line types
			$data['lines'] = array();

			$lines = $this->model_resource_transaction->getLineItems($invoice_id);
			//var_dump($lines);
			// We need to set lines to session
			foreach ($lines as $line) {
				$options = array();
				if (isset($line['order_id'])) {
					$options = $this->model_sale_order->getOrderOptions($data['order_id'], $line['order_product_id']);
				}

				// TODO: Modify whatever's converting the entity to an array so we don't have to do this
				// Either that or make a utility method to get assoc keys
				$product_id = null;
				//if (isset($line['product']) && isset($line['product']['productId'])) { // Old REST API
				if (isset($line['product']) && isset($line['product']['product_id'])) {
					$product_id = $line['product']['product_id'];
				}

				$tax_class_id = null;
				//if (isset($line['tax_class']) && isset($line['tax_class']['taxClassId'])) { // Old REST API
				if (isset($line['tax_class']) && isset($line['tax_class']['tax_class_id'])) {
					$tax_class_id = $line['tax_class']['tax_class_id'];
				}

				$item = array(
					'product_id' 		=> $product_id,
					'line_id'	 		=> $line['line_id'],
					'detail_type'	 	=> $line['detail_type'],
					'invoice_id'	 	=> $invoice_id,
					'order_product_id'	=> $line['order_product_id'],
					'description'	 	=> $line['description'],
					'name'       		=> $line['name'],
					'model'      		=> $line['model'],
					'option'     		=> (isset($line['invoice_id'])) ? $this->model_sale_order->getOrderOptions($data['invoice_id'], $line['order_product_id']) : null,
					'quantity'  		=> (int)$line['quantity'],
					'price'      		=> (float)$line['price'],
					'revenue'     		=> (float)$line['revenue'],
					'royalty'     		=> (float)$line['royalty'],
					'total'     		=> (float)$line['total'],
					'tax'     			=> (float)$line['tax'],
					'tax_class_id'     	=> $tax_class_id,
					'reward'     		=> $line['reward']
				);

				$data['lines'][] = $item;

				$this->addLine($item, $item['quantity'], $item['price']);
			}

			$this->load->model('sale/customer'); // Load customer and customer addresses
			$customer = $this->model_resource_transaction->getCustomer($data['invoice_id']);

			if (!isset($customer_group_id)) {
				$customer['customer_group_id'] = 0; // TODO: Set a default value
			}

			$data['addresses'] = array();
			$customer_data['customer_id'] = 0;
			$customer_data['customer'] = '';
			$customer_data['account_custom_field'] = '';

			if (isset($customer['customer_id'])) {
				$data['addresses'] = $this->model_sale_customer->getAddresses($customer['customer_id']);

				// Clean fields - I noticed special characters in some of the invoice address fields
				$idx = 0;
				foreach ($data['addresses'] as $address) {
					foreach ($address as $field => $val) {
						$data['addresses'][$idx][$field] = htmlspecialchars_decode($val);
					}

					$idx++;
				}

				unset ($idx);

				$customer_data['customer_id'] = (isset($invoice_info['customer_id'])) ? $invoice_info['customer_id'] : $customer['customer_id'];
				$customer_data['customer'] = (isset($invoice_info['fullname'])) ? htmlspecialchars_decode($invoice_info['fullname']) : htmlspecialchars_decode($customer['fullname']);
				$customer_data['account_custom_field'] = $customer['custom_field'];
			}

			// Set customer display fields
			$customer_data['customer_group_id'] = (isset($invoice_info['customer_group_id'])) ? $invoice_info['customer_group_id'] : $customer['customer_group_id'];
			$customer_data['firstname'] = (isset($invoice_info['firstname'])) ? $invoice_info['firstname'] : $customer['firstname']; // Could be possible that the customer's name changed
			$customer_data['lastname'] = (isset($invoice_info['lastname'])) ? $invoice_info['lastname'] : $customer['lastname'];
			$customer_data['bill_email'] = (isset($invoice_info['bill_email'])) ? $invoice_info['bill_email'] : $customer['bill_email'];
			$customer_data['bill_telephone'] = (isset($invoice_info['bill_telephone'])) ? $invoice_info['bill_telephone'] : $customer['telephone'];
			$customer_data['bill_fax'] = (isset($invoice_info['bill_fax'])) ? $invoice_info['bill_fax'] : $customer['fax'];

			ModelOcSessionCustomer::setCustomer($this, $customer_data);
			// TODO: Set payment and shipping

			$data = array_merge($data, $customer_data);

			$mapInvoiceAddress = function ($invoice, &$data, $type, $prefix = '') {
				if (!in_array($type, array('bill', 'ship'))) {
					return false;
				}

				// Keys must match session keys set via api/payment/address and api/shipping/address
				$keys = array(
					'firstname' => htmlspecialchars_decode($invoice[$type . '_addr_firstname']),
					'lastname' => htmlspecialchars_decode($invoice[$type . '_addr_lastname']),
					'company' => htmlspecialchars_decode($invoice[$type . '_addr_company']),
					'address_1' => htmlspecialchars_decode($invoice[$type . '_addr_line1']),
					'address_2' => htmlspecialchars_decode($invoice[$type . '_addr_line2']),
					'postcode' => $invoice[$type . '_addr_postcode'],
					'city' => htmlspecialchars_decode($invoice[$type . '_addr_city']),
					'zone_id' => $invoice[$type . '_addr_zone_id'],
					'country_id' => $invoice[$type . '_addr_country_id']
				);

				foreach ($keys as $key => $value) {
					$data[$prefix . $key] = $value;
				}
			};

			// Field names in view do not match exactly to the invoice table fields, and I'd rather reuse the logic anyway - typing sucks!
			$mapInvoiceAddress($invoice_info, $data, 'bill', 'payment_');
			$mapInvoiceAddress($invoice_info, $data, 'ship', 'shipping_');

			// Set map addresses and set them to session
			$mapInvoiceAddress($invoice_info, $this->request->post, 'bill');
			$payment = $this->load->controller('api/payment/address', array('export' => false));

			$mapInvoiceAddress($invoice_info, $this->request->post, 'ship');
			$shipping = $this->load->controller('api/shipping/address', array('export' => false));

			$data['payment_custom_field'] = (isset($invoice_info['payment_custom_field'])) ? $invoice_info['payment_custom_field'] : null;
			$data['payment_method'] = $invoice_info['payment_method'];
			$data['payment_code'] = $invoice_info['payment_code'];

			$data['shipping_custom_field'] = (isset($invoice_info['shipping_custom_field'])) ? $invoice_info['shipping_custom_field'] : null;
			$data['shipping_method'] = $invoice_info['shipping_method'];
			$data['shipping_code'] = $invoice_info['shipping_code'];

			//var_dump($invoice_info);
			if (isset($this->session->data['invoice_info'])) {
				$invoice_info = array_merge($invoice_info, $this->session->data['invoice_info']);
			}

			if (isset($invoice_info['payment_address_format'])) {
				$format = $invoice_info['payment_address_format'];
				$data['payment_address_format'] = $format;
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{city} {zone}' . "\n" . '{postcode} {country}';
				$data['payment_address_format'] = '{firstname} {lastname}\n{company}\n{address_1}\n{city} {zone}\n{postcode} {country}';
			}

			$data['payment_address'] = self::formatPaymentAddress($format, $invoice_info, false, false);

			if (isset($invoice_info['shipping_address_format'])) {
				$format = $invoice_info['shipping_address_format'];
				$data['shipping_address_format'] = $format;
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{city} {zone}' . "\n" . '{postcode} {country}';
				$data['shipping_address_format'] = '{firstname} {lastname}\n{company}\n{address_1}\n{city} {zone}\n{postcode} {country}';
			}

			$data['shipping_address'] = self::formatShippingAddress($format, $invoice_info, false, false);

			// Add vouchers to the API
			$data['order_vouchers'] = $this->model_sale_order->getOrderVouchers($data['invoice_id']);

			$data['coupon'] = '';
			$data['voucher'] = '';
			$data['reward'] = '';

			$data['order_totals'] = array();

			$order_totals = $this->model_sale_order->getOrderTotals($data['invoice_id']);

			foreach ($order_totals as $order_total) {
				// If coupon, voucher or reward points
				$start = strpos($order_total['title'], '(') + 1;
				$end = strrpos($order_total['title'], ')');

				if ($start && $end) {
					if ($order_total['code'] == 'coupon') {
						$data['coupon'] = substr($order_total['title'], $start, $end - $start);
					}

					if ($order_total['code'] == 'voucher') {
						$data['voucher'] = substr($order_total['title'], $start, $end - $start);
					}

					if ($order_total['code'] == 'reward') {
						$data['reward'] = substr($order_total['title'], $start, $end - $start);
					}
				}
			}

			$data['order_status_id'] = (isset($invoice_info['order_status_id'])) ? : null;
			$data['invoice_status_id'] = (isset($invoice_info['invoice_status_id'])) ? : null;
			$data['customer_memo'] = (isset($invoice_info['customer_memo'])) ? $invoice_info['customer_memo'] : null;
			$data['statement_memo'] = (isset($invoice_info['statement_memo'])) ? $invoice_info['statement_memo'] : null;
			$data['affiliate_id'] = (isset($invoice_info['affiliate_id'])) ? $invoice_info['affiliate_id'] : null;
			$data['affiliate'] = (isset($invoice_info['affiliate_firstname']) && isset($invoice_info['affiliate_firstname'])) ? $invoice_info['affiliate_firstname'] . ' ' . $invoice_info['affiliate_lastname'] : '';
			$data['currency_code'] = (isset($invoice_info['currency_code'])) ? $invoice_info['currency_code'] : '';
		} else {
			$data['invoice_id'] = '';
			$data['invoice_no'] = '';
			$data['store_id'] = '';
			$data['order_id'] = '';

			$date = new DateTime();
			$data['invoice_date'] = $date->format('Y-m-d');
			$data['due_date'] = $date->modify('+1 month')->format('Y-m-d');

			$data['customer_group_id'] = $this->config->get('config_customer_group_id');
			$data['firstname'] = '';
			$data['lastname'] = '';
			$data['email'] = '';
			$data['telephone'] = '';
			$data['fax'] = '';

			$data['addresses'] = array();
			$data['customer'] = null;
			$data['customer_id'] = '';
			$data['customer_group_id'] = '';
			$data['firstname'] = ''; // Could be possible that the customer's name changed
			$data['lastname'] = '';
			$data['email'] = '';
			$data['telephone'] = '';
			$data['fax'] = '';

			$data['bill_email'] = '';
			$data['bill_telephone'] = '';
			$data['bill_fax'] = '';

			$data['account_custom_field'] = null;

			$data['payment_firstname'] = '';
			$data['payment_lastname'] = '';
			$data['payment_company'] = '';
			$data['payment_address_1'] = '';
			$data['payment_address_2'] = '';
			$data['payment_city'] = '';
			$data['payment_postcode'] = '';
			$data['payment_country_id'] = '';
			$data['payment_zone_id'] = '';
			$data['payment_custom_field'] = array();
			$data['payment_method'] = '';
			$data['payment_code'] = '';

			$data['shipping_firstname'] = '';
			$data['shipping_lastname'] = '';
			$data['shipping_company'] = '';
			$data['shipping_address_1'] = '';
			$data['shipping_address_2'] = '';
			$data['shipping_city'] = '';
			$data['shipping_postcode'] = '';
			$data['shipping_country_id'] = '';
			$data['shipping_zone_id'] = '';
			$data['shipping_custom_field'] = array();
			$data['shipping_method'] = '';
			$data['shipping_code'] = '';

			$data['payment_address'] = '';
			$data['shipping_address'] = '';

			if (isset($invoice_info['payment_address_format'])) {
				$format = $invoice_info['payment_address_format'];
				$data['payment_address_format'] = $format;
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{city} {zone}' . "\n" . '{postcode} {country}';
				$data['payment_address_format'] = '{firstname} {lastname}\n{company}\n{address_1}\n{city} {zone}\n{postcode} {country}';
			}

			if (isset($invoice_info['shipping_address_format'])) {
				$format = $invoice_info['shipping_address_format'];
				$data['shipping_address_format'] = $format;
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{city} {zone}' . "\n" . '{postcode} {country}';
				$data['shipping_address_format'] = '{firstname} {lastname}\n{company}\n{address_1}\n{city} {zone}\n{postcode} {country}';
			}

			$data['lines'] = array();
			$data['order_vouchers'] = array();
			$data['order_totals'] = array();

			$data['order_status_id'] = $this->config->get('config_order_status_id');
			$data['customer_memo'] = '';
			$data['statement_memo'] = '';
			$data['affiliate_id'] = '';
			$data['affiliate'] = '';
			$data['currency_code'] = $this->config->get('config_currency');

			$data['coupon'] = '';
			$data['voucher'] = '';
			$data['reward'] = '';
		}

		// Stores
		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

		// Customer Groups
		$this->load->model('sale/customer_group');

		$data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();

		// Custom Fields
		$this->load->model('sale/custom_field');

		$data['custom_fields'] = array();

		$filter_data = array(
			'sort'  => 'cf.sort_order',
			'order' => 'ASC'
		);

		$custom_fields = $this->model_sale_custom_field->getCustomFields($filter_data);

		foreach ($custom_fields as $custom_field) {
			$data['custom_fields'][] = array(
				'custom_field_id'    => $custom_field['custom_field_id'],
				'custom_field_value' => $this->model_sale_custom_field->getCustomFieldValues($custom_field['custom_field_id']),
				'name'               => $custom_field['name'],
				'value'              => $custom_field['value'],
				'type'               => $custom_field['type'],
				'location'           => $custom_field['location'],
				'sort_order'         => $custom_field['sort_order']
			);
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		// Get invoice statuses
		// Not sure if this is the right model to be dumping everything... should really just call the appropriate service
		$data['invoice_statuses'] = $this->model_resource_transaction->getInvoiceStatuses();

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		$this->load->model('localisation/currency');

		$data['currencies'] = $this->model_localisation_currency->getCurrencies();

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		$data['voucher_min'] = $this->config->get('config_voucher_min');

		$this->load->model('sale/voucher_theme');

		$data['voucher_themes'] = $this->model_sale_voucher_theme->getVoucherThemes();

		$output = $this->store();
		$data['tab_store_invoice'] = $this->load->view('transaction/invoice_store_product_purchased.tpl', array_merge($data, $output));

		//$this->load->controller('report/product_purchased');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('transaction/invoice_form.tpl', $data));
	}

	protected function addLine($data = null, $quantity = null, $price = null) {
		// Determine appropriate line type
		$productId = null;
		$product = null;

		if ($data != null) {
			$this->load->model('catalog/product');

			$type = 'SalesItemLineDetail';
			$types = array(
				'SalesItemLineDetail',
				'DescriptionItemLineDetail',
				'DescriptionOnlyLineDetail',
				'CommissionLineDetail');

			if (isset($data['detail_type']) && !empty($data['detail_type'])) {
				if (in_array($data['detail_type'], $types)) {
					$type = $data['detail_type'];
				}
			}

			if ($type == 'SalesItemLineDetail') {
				if (is_numeric($data)) {
					$productId = (int)$productId;
				} elseif (is_array($data)) {
					$productId = (isset($data['product_id'])) ? $data['product_id'] : $productId;
				}

				$product = $this->model_catalog_product->getProduct($productId);

				if ($product != null) {
					$price = (empty($price)) ? $product['price'] : $price;
					$quantity = (empty($quantity)) ? 1 : $quantity;

					$this->lines->add($product, $quantity, array(), $price);
				}
			}

			if ($type == 'DescriptionOnlyLineDetail' || $type == 'DescriptionItemLineDetail') {
				if (!empty($data['description']) xor !empty($data['name'])) {
					$description = (!empty($data['name'])) ? $data['name'] : $data['description'];

					if ($quantity == null && $price == null) {
						// If no price or quantity necessary, create a DescriptionOnly line item
						$this->lines->addDescription($description);
					} else {
						$taxClassId = (isset($data['tax_class_id'])) ? $data['tax_class_id'] : null;
						$tax = (isset($data['tax'])) ? $data['tax'] : 0.0000;
						$this->lines->addDescriptionItem($description, $quantity, $price, $taxClassId, $tax);
					}
				}
			}

			if ($type == 'CommissionLineDetail') {
				if (!empty($data['description']) xor !empty($data['name'])) {
					$description = (!empty($data['name'])) ? $data['name'] : $data['description'];

					$this->lines->addCommission($description, 1, $data);
				}
			}
		}
	}

	public function info() {
		$this->load->model('sale/order');

		if (isset($this->request->get['invoice_id'])) {
			$invoice_id = $this->request->get['invoice_id'];
		} else {
			$invoice_id = 0;
		}

		$invoice_info = $this->model_sale_order->getOrder($invoice_id);

		if ($invoice_info) {
			$data = $this->load->language('transaction/invoice');

			$this->document->setTitle($this->language->get('heading_title'));

			$data['heading_title'] = $this->language->get('heading_title');

			$data['token'] = $this->session->data['token'];

			$url = '';

			if (isset($this->request->get['filter_invoice_id'])) {
				$url .= '&filter_invoice_id=' . $this->request->get['filter_invoice_id'];
			}

			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_order_status'])) {
				$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
			}

			if (isset($this->request->get['filter_total'])) {
				$url .= '&filter_total=' . $this->request->get['filter_total'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('transaction/invoice', 'token=' . $this->session->data['token'] . $url, 'SSL')
			);

			$data['shipping'] = $this->url->link('transaction/invoice/shipping', 'token=' . $this->session->data['token'] . '&invoice_id=' . (int)$this->request->get['invoice_id'], 'SSL');
			$data['invoice'] = $this->url->link('transaction/invoice/invoice', 'token=' . $this->session->data['token'] . '&invoice_id=' . (int)$this->request->get['invoice_id'], 'SSL');
			$data['edit'] = $this->url->link('transaction/invoice/edit', 'token=' . $this->session->data['token'] . '&invoice_id=' . (int)$this->request->get['invoice_id'], 'SSL');
			$data['cancel'] = $this->url->link('transaction/invoice', 'token=' . $this->session->data['token'] . $url, 'SSL');

			$data['invoice_id'] = $this->request->get['invoice_id'];
			$data['invoice_id'] = $invoice_info['invoice_id'];

			if ($invoice_info['invoice_no']) {
				$data['invoice_no'] = $invoice_info['invoice_prefix'] . $invoice_info['invoice_no'];
			} else {
				$data['invoice_no'] = '';
			}

			$data['store_name'] = $invoice_info['store_name'];
			$data['store_url'] = $invoice_info['store_url'];
			$data['firstname'] = $invoice_info['firstname'];
			$data['lastname'] = $invoice_info['lastname'];

			if ($invoice_info['invoice_info_id']) {
				$data['invoice_info'] = $this->url->link('sale/invoice_info/edit', 'token=' . $this->session->data['token'] . '&invoice_info_id=' . $invoice_info['invoice_info_id'], 'SSL');
			} else {
				$data['invoice_info'] = '';
			}

			$this->load->model('sale/customer_group');

			$customer_group_info = $this->model_sale_customer_group->getCustomerGroup($invoice_info['customer_group_id']);

			if ($customer_group_info) {
				$data['customer_group'] = $customer_group_info['name'];
			} else {
				$data['customer_group'] = '';
			}

			$data['email'] = $invoice_info['email'];
			$data['telephone'] = $invoice_info['telephone'];
			$data['fax'] = $invoice_info['fax'];

			$data['account_custom_field'] = $invoice_info['custom_field'];

			// Uploaded files
			$this->load->model('tool/upload');

			// Custom Fields
			$this->load->model('sale/custom_field');

			$data['account_custom_fields'] = array();

			$custom_fields = $this->model_sale_custom_field->getCustomFields();

			foreach ($custom_fields as $custom_field) {
				if ($custom_field['location'] == 'account' && isset($invoice_info['custom_field'][$custom_field['custom_field_id']])) {
					if ($custom_field['type'] == 'select' || $custom_field['type'] == 'radio') {
						$custom_field_value_info = $this->model_sale_custom_field->getCustomFieldValue($invoice_info['custom_field'][$custom_field['custom_field_id']]);

						if ($custom_field_value_info) {
							$data['account_custom_fields'][] = array(
								'name'  => $custom_field['name'],
								'value' => $custom_field_value_info['name']
							);
						}
					}

					if ($custom_field['type'] == 'checkbox' && is_array($invoice_info['custom_field'][$custom_field['custom_field_id']])) {
						foreach ($invoice_info['custom_field'][$custom_field['custom_field_id']] as $custom_field_value_id) {
							$custom_field_value_info = $this->model_sale_custom_field->getCustomFieldValue($custom_field_value_id);

							if ($custom_field_value_info) {
								$data['account_custom_fields'][] = array(
									'name'  => $custom_field['name'],
									'value' => $custom_field_value_info['name']
								);
							}
						}
					}

					if ($custom_field['type'] == 'text' || $custom_field['type'] == 'textarea' || $custom_field['type'] == 'file' || $custom_field['type'] == 'date' || $custom_field['type'] == 'datetime' || $custom_field['type'] == 'time') {
						$data['account_custom_fields'][] = array(
							'name'  => $custom_field['name'],
							'value' => $invoice_info['custom_field'][$custom_field['custom_field_id']]
						);
					}

					if ($custom_field['type'] == 'file') {
						$upload_info = $this->model_tool_upload->getUploadByCode($invoice_info['custom_field'][$custom_field['custom_field_id']]);

						if ($upload_info) {
							$data['account_custom_fields'][] = array(
								'name'  => $custom_field['name'],
								'value' => $upload_info['name']
							);
						}
					}
				}
			}

			$data['comment'] = nl2br($invoice_info['comment']);
			$data['shipping_method'] = $invoice_info['shipping_method'];
			$data['payment_method'] = $invoice_info['payment_method'];
			$data['total'] = $this->currency->format($invoice_info['total'], $invoice_info['currency_code'], $invoice_info['currency_value']);

			$this->load->model('sale/invoice_info');

			$data['reward'] = $invoice_info['reward'];

			// TODO: Replace with order id from data
			$data['reward_total'] = $this->model_sale_invoice_info->getTotalCustomerRewardsByOrderId($this->request->get['invoice_id']);

			$data['affiliate_firstname'] = $invoice_info['affiliate_firstname'];
			$data['affiliate_lastname'] = $invoice_info['affiliate_lastname'];

			if ($invoice_info['affiliate_id']) {
				$data['affiliate'] = $this->url->link('marketing/affiliate/edit', 'token=' . $this->session->data['token'] . '&affiliate_id=' . $invoice_info['affiliate_id'], 'SSL');
			} else {
				$data['affiliate'] = '';
			}

			$data['commission'] = $this->currency->format($invoice_info['commission'], $invoice_info['currency_code'], $invoice_info['currency_value']);

			$this->load->model('marketing/affiliate');

			// TODO: Replace with order id from data
			$data['commission_total'] = $this->model_marketing_affiliate->getTotalTransactionsByOrderId($this->request->get['invoice_id']);

			$this->load->model('localisation/order_status');

			// TODO: Fix me
			/*$order_status_info = $this->model_localisation_order_status->getInvoiceStatus($invoice_info['order_status_id']);

			if ($order_status_info) {
				$data['order_status'] = $order_status_info['name'];
			} else {
				$data['order_status'] = '';
			}*/

			$data['ip'] = $invoice_info['ip'];
			$data['forwarded_ip'] = $invoice_info['forwarded_ip'];
			$data['user_agent'] = $invoice_info['user_agent'];
			$data['accept_language'] = $invoice_info['accept_language'];
			$data['date_added'] = date($this->language->get('date_format_short'), strtotime($invoice_info['date_added']));
			$data['date_modified'] = date($this->language->get('date_format_short'), strtotime($invoice_info['date_modified']));

			// Payment
			$data['payment_firstname'] = $invoice_info['payment_firstname'];
			$data['payment_lastname'] = $invoice_info['payment_lastname'];
			$data['payment_company'] = $invoice_info['payment_company'];
			$data['payment_address_1'] = $invoice_info['payment_address_1'];
			$data['payment_address_2'] = $invoice_info['payment_address_2'];
			$data['payment_city'] = $invoice_info['payment_city'];
			$data['payment_postcode'] = $invoice_info['payment_postcode'];
			$data['payment_zone'] = $invoice_info['payment_zone'];
			$data['payment_zone_code'] = $invoice_info['payment_zone_code'];
			$data['payment_country'] = $invoice_info['payment_country'];

			// Custom fields
			$data['payment_custom_fields'] = array();

			foreach ($custom_fields as $custom_field) {
				if ($custom_field['location'] == 'address' && isset($invoice_info['payment_custom_field'][$custom_field['custom_field_id']])) {
					if ($custom_field['type'] == 'select' || $custom_field['type'] == 'radio') {
						$custom_field_value_info = $this->model_sale_custom_field->getCustomFieldValue($invoice_info['payment_custom_field'][$custom_field['custom_field_id']]);

						if ($custom_field_value_info) {
							$data['payment_custom_fields'][] = array(
								'name'  => $custom_field['name'],
								'value' => $custom_field_value_info['name'],
								'sort_order' => $custom_field['sort_order']
							);
						}
					}

					if ($custom_field['type'] == 'checkbox' && is_array($invoice_info['payment_custom_field'][$custom_field['custom_field_id']])) {
						foreach ($invoice_info['payment_custom_field'][$custom_field['custom_field_id']] as $custom_field_value_id) {
							$custom_field_value_info = $this->model_sale_custom_field->getCustomFieldValue($custom_field_value_id);

							if ($custom_field_value_info) {
								$data['payment_custom_fields'][] = array(
									'name'  => $custom_field['name'],
									'value' => $custom_field_value_info['name'],
									'sort_order' => $custom_field['sort_order']
								);
							}
						}
					}

					if ($custom_field['type'] == 'text' || $custom_field['type'] == 'textarea' || $custom_field['type'] == 'file' || $custom_field['type'] == 'date' || $custom_field['type'] == 'datetime' || $custom_field['type'] == 'time') {
						$data['payment_custom_fields'][] = array(
							'name'  => $custom_field['name'],
							'value' => $invoice_info['payment_custom_field'][$custom_field['custom_field_id']],
							'sort_order' => $custom_field['sort_order']
						);
					}

					if ($custom_field['type'] == 'file') {
						$upload_info = $this->model_tool_upload->getUploadByCode($invoice_info['payment_custom_field'][$custom_field['custom_field_id']]);

						if ($upload_info) {
							$data['payment_custom_fields'][] = array(
								'name'  => $custom_field['name'],
								'value' => $upload_info['name'],
								'sort_order' => $custom_field['sort_order']
							);
						}
					}
				}
			}

			// Shipping
			$data['shipping_firstname'] = $invoice_info['shipping_firstname'];
			$data['shipping_lastname'] = $invoice_info['shipping_lastname'];
			$data['shipping_company'] = $invoice_info['shipping_company'];
			$data['shipping_address_1'] = $invoice_info['shipping_address_1'];
			$data['shipping_address_2'] = $invoice_info['shipping_address_2'];
			$data['shipping_city'] = $invoice_info['shipping_city'];
			$data['shipping_postcode'] = $invoice_info['shipping_postcode'];
			$data['shipping_zone'] = $invoice_info['shipping_zone'];
			$data['shipping_zone_code'] = $invoice_info['shipping_zone_code'];
			$data['shipping_country'] = $invoice_info['shipping_country'];

			$data['shipping_custom_fields'] = array();

			foreach ($custom_fields as $custom_field) {
				if ($custom_field['location'] == 'address' && isset($invoice_info['shipping_custom_field'][$custom_field['custom_field_id']])) {
					if ($custom_field['type'] == 'select' || $custom_field['type'] == 'radio') {
						$custom_field_value_info = $this->model_sale_custom_field->getCustomFieldValue($invoice_info['shipping_custom_field'][$custom_field['custom_field_id']]);

						if ($custom_field_value_info) {
							$data['shipping_custom_fields'][] = array(
								'name'  => $custom_field['name'],
								'value' => $custom_field_value_info['name'],
								'sort_order' => $custom_field['sort_order']
							);
						}
					}

					if ($custom_field['type'] == 'checkbox' && is_array($invoice_info['shipping_custom_field'][$custom_field['custom_field_id']])) {
						foreach ($invoice_info['shipping_custom_field'][$custom_field['custom_field_id']] as $custom_field_value_id) {
							$custom_field_value_info = $this->model_sale_custom_field->getCustomFieldValue($custom_field_value_id);

							if ($custom_field_value_info) {
								$data['shipping_custom_fields'][] = array(
									'name'  => $custom_field['name'],
									'value' => $custom_field_value_info['name'],
									'sort_order' => $custom_field['sort_order']
								);
							}
						}
					}

					if ($custom_field['type'] == 'text' || $custom_field['type'] == 'textarea' || $custom_field['type'] == 'file' || $custom_field['type'] == 'date' || $custom_field['type'] == 'datetime' || $custom_field['type'] == 'time') {
						$data['shipping_custom_fields'][] = array(
							'name'  => $custom_field['name'],
							'value' => $invoice_info['shipping_custom_field'][$custom_field['custom_field_id']],
							'sort_order' => $custom_field['sort_order']
						);
					}

					if ($custom_field['type'] == 'file') {
						$upload_info = $this->model_tool_upload->getUploadByCode($invoice_info['shipping_custom_field'][$custom_field['custom_field_id']]);

						if ($upload_info) {
							$data['shipping_custom_fields'][] = array(
								'name'  => $custom_field['name'],
								'value' => $upload_info['name'],
								'sort_order' => $custom_field['sort_order']
							);
						}
					}
				}
			}

			$data['products'] = array();

			$products = $this->model_sale_order->getOrderProducts($data['invoice_id']);

			foreach ($products as $product) {
				$option_data = array();

				$options = $this->model_sale_order->getOrderOptions($data['invoice_id'], $product['order_product_id']);

				foreach ($options as $option) {
					if ($option['type'] != 'file') {
						$option_data[] = array(
							'name'  => $option['name'],
							'value' => $option['value'],
							'type'  => $option['type']
						);
					} else {
						$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

						if ($upload_info) {
							$option_data[] = array(
								'name'  => $option['name'],
								'value' => $upload_info['name'],
								'type'  => $option['type'],
								'href'  => $this->url->link('tool/upload/download', 'token=' . $this->session->data['token'] . '&code=' . $upload_info['code'], 'SSL')
							);
						}
					}
				}

				$data['products'][] = array(
					'order_product_id' => $product['order_product_id'],
					'product_id'       => $product['product_id'],
					'name'    	 	   => $product['name'],
					'model'    		   => $product['model'],
					'option'   		   => $option_data,
					'quantity'		   => $product['quantity'],
					'price'    		   => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $invoice_info['currency_code'], $invoice_info['currency_value']),
					'total'    		   => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $invoice_info['currency_code'], $invoice_info['currency_value']),
					'href'     		   => $this->url->link('catalog/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $product['product_id'], 'SSL')
				);
			}

			$data['vouchers'] = array();

			$vouchers = $this->model_sale_order->getOrderVouchers($data['invoice_id']);

			foreach ($vouchers as $voucher) {
				$data['vouchers'][] = array(
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'], $invoice_info['currency_code'], $invoice_info['currency_value']),
					'href'        => $this->url->link('sale/voucher/edit', 'token=' . $this->session->data['token'] . '&voucher_id=' . $voucher['voucher_id'], 'SSL')
				);
			}

			$data['totals'] = array();

			$totals = $this->model_sale_order->getOrderTotals($data['invoice_id']);

			foreach ($totals as $total) {
				$data['totals'][] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'], $invoice_info['currency_code'], $invoice_info['currency_value']),
				);
			}

			$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

			$data['order_status_id'] = $invoice_info['order_status_id'];

			// Unset any past sessions this page date_added for the api to work.
			unset($this->session->data['cookie']);

			// Set up the API session
			if ($this->user->hasPermission('modify', 'transaction/invoice')) {
				$this->load->model('user/api');

				$api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

				if ($api_info) {
					$curl = curl_init();

					// Set SSL if required
					if (substr(HTTPS_CATALOG, 0, 5) == 'https') {
						curl_setopt($curl, CURLOPT_PORT, 443);
					}

					curl_setopt($curl, CURLOPT_HEADER, false);
					curl_setopt($curl, CURLINFO_HEADER_OUT, true);
					curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
					curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($curl, CURLOPT_URL, HTTPS_CATALOG . 'index.php?route=api/login');
					curl_setopt($curl, CURLOPT_POST, true);
					curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($api_info));

					$json = curl_exec($curl);

					if (!$json) {
						$data['error_warning'] = sprintf($this->language->get('error_curl'), curl_error($curl), curl_errno($curl));
					} else {
						$response = json_decode($json, true);
					}

					if (isset($response['cookie'])) {
						$this->session->data['cookie'] = $response['cookie'];
					}
				}
			}

			if (isset($response['cookie'])) {
				$this->session->data['cookie'] = $response['cookie'];
			} else {
				$data['error_warning'] = $this->language->get('error_permission');
			}

			$data['payment_action'] = $this->load->controller('payment/' . $invoice_info['payment_code'] . '/action');

			$data['frauds'] = array();

			$this->load->model('extension/extension');

			$extensions = $this->model_extension_extension->getInstalled('fraud');

			foreach ($extensions as $extension) {
				if ($this->config->get($extension . '_status')) {
					$this->load->language('fraud/' . $extension);

					$content = $this->load->controller('fraud/' . $extension . '/order');

					if ($content) {
						$data['frauds'][] = array(
							'code'    => $extension,
							'title'   => $this->language->get('heading_title'),
							'content' => $content
						);
					}
				}
			}

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('transaction/invoice_info.tpl', $data));
		} else {
			$this->load->language('error/not_found');

			$this->document->setTitle($this->language->get('heading_title'));

			$data['heading_title'] = $this->language->get('heading_title');

			$data['text_not_found'] = $this->language->get('text_not_found');

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('error/not_found', 'token=' . $this->session->data['token'], 'SSL')
			);

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('error/not_found.tpl', $data));
		}
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'transaction/invoice')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function createInvoiceNo() {
		$this->load->language('transaction/invoice');

		$json = array();

		if (!$this->user->hasPermission('modify', 'transaction/invoice')) {
			$json['error'] = $this->language->get('error_permission');
		} elseif (isset($this->request->get['invoice_id'])) {
			if (isset($this->request->get['invoice_id'])) {
				$invoice_id = $this->request->get['invoice_id'];
			} else {
				$invoice_id = 0;
			}

			$this->load->model('sale/order');

			$invoice_no = $this->model_sale_order->createInvoiceNo($invoice_id);

			if ($invoice_no) {
				$json['invoice_no'] = $invoice_no;
			} else {
				$json['error'] = $this->language->get('error_action');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function addReward() {
		$this->load->language('transaction/invoice');

		$json = array();

		if (!$this->user->hasPermission('modify', 'transaction/invoice')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			if (isset($this->request->get['invoice_id'])) {
				$invoice_id = $this->request->get['invoice_id'];
			} else {
				$invoice_id = 0;
			}

			$this->load->model('sale/order');

			$order_info = $this->model_sale_order->getOrder($invoice_id);

			if ($order_info && $order_info['invoice_info_id'] && ($order_info['reward'] > 0)) {
				$this->load->model('sale/invoice_info');

				$reward_total = $this->model_sale_invoice_info->getTotalCustomerRewardsByInvoiceId($invoice_id);

				if (!$reward_total) {
					$this->model_sale_invoice_info->addReward($order_info['invoice_info_id'], $this->language->get('text_invoice_id') . ' #' . $invoice_id, $order_info['reward'], $invoice_id);
				}
			}

			$json['success'] = $this->language->get('text_reward_added');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function removeReward() {
		$this->load->language('transaction/invoice');

		$json = array();

		if (!$this->user->hasPermission('modify', 'transaction/invoice')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			if (isset($this->request->get['invoice_id'])) {
				$invoice_id = $this->request->get['invoice_id'];
			} else {
				$invoice_id = 0;
			}

			$this->load->model('sale/order');

			$order_info = $this->model_sale_order->getOrder($invoice_id);

			if ($order_info) {
				$this->load->model('sale/invoice_info');

				$this->model_sale_invoice_info->deleteReward($invoice_id);
			}

			$json['success'] = $this->language->get('text_reward_removed');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function addCommission() {
		$this->load->language('transaction/invoice');

		$json = array();

		if (!$this->user->hasPermission('modify', 'transaction/invoice')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			if (isset($this->request->get['invoice_id'])) {
				$invoice_id = $this->request->get['invoice_id'];
			} else {
				$invoice_id = 0;
			}

			$this->load->model('sale/order');

			$order_info = $this->model_sale_order->getOrder($invoice_id);

			if ($order_info) {
				$this->load->model('marketing/affiliate');

				$affiliate_total = $this->model_marketing_affiliate->getTotalTransactionsByInvoiceId($invoice_id);

				if (!$affiliate_total) {
					$this->model_marketing_affiliate->addTransaction($order_info['affiliate_id'], $this->language->get('text_invoice_id') . ' #' . $invoice_id, $order_info['commission'], $invoice_id);
				}
			}

			$json['success'] = $this->language->get('text_commission_added');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function removeCommission() {
		$this->load->language('transaction/invoice');

		$json = array();

		if (!$this->user->hasPermission('modify', 'transaction/invoice')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			if (isset($this->request->get['invoice_id'])) {
				$invoice_id = $this->request->get['invoice_id'];
			} else {
				$invoice_id = 0;
			}

			$this->load->model('sale/order');

			$order_info = $this->model_sale_order->getOrder($invoice_id);

			if ($order_info) {
				$this->load->model('marketing/affiliate');

				$this->model_marketing_affiliate->deleteTransaction($invoice_id);
			}

			$json['success'] = $this->language->get('text_commission_removed');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function history() {
		$this->load->language('transaction/invoice');

		$data['text_no_results'] = $this->language->get('text_no_results');

		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_notify'] = $this->language->get('column_notify');
		$data['column_comment'] = $this->language->get('column_comment');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['histories'] = array();

		$this->load->model('sale/order');

		$results = $this->model_sale_order->getOrderHistories($this->request->get['invoice_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['histories'][] = array(
				'notify'     => $result['notify'] ? $this->language->get('text_yes') : $this->language->get('text_no'),
				'status'     => $result['status'],
				'comment'    => nl2br($result['comment']),
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$history_total = $this->model_sale_order->getTotalInvoiceHistories($this->request->get['invoice_id']);

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('transaction/invoice/history', 'token=' . $this->session->data['token'] . '&invoice_id=' . $this->request->get['invoice_id'] . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

		$this->response->setOutput($this->load->view('transaction/invoice_history.tpl', $data));
	}

	public function invoice() {
		$data = $this->load->language('transaction/invoice');

		$data['title'] = $this->language->get('text_invoice');

		if ($this->request->server['HTTPS']) {
			$data['base'] = HTTPS_SERVER;
		} else {
			$data['base'] = HTTP_SERVER;
		}

		$data['direction'] = $this->language->get('direction');
		$data['lang'] = $this->language->get('code');

		$this->load->model('sale/order');

		$this->load->model('setting/setting');

		$data['invoices'] = array();

		$invoices = array();

		if (isset($this->request->post['selected'])) {
			$invoices = $this->request->post['selected'];
		} elseif (isset($this->request->get['invoice_id'])) {
			$invoices[] = $this->request->get['invoice_id'];
		}

		$this->load->model('resource/transaction');
		$tModel = &$this->model_resource_transaction;

		$tService = new TransactionInvoice($this, 'OcInvoice');
		$tService->setEntityManager($this->em);
		$tModel->setTransactionType($tService);

		foreach ($invoices as $invoiceId) {
			$i = $this->model_resource_transaction->getTransaction($invoiceId);

			if ($i) {
				$store = $this->model_setting_setting->getSetting('config', $i['store_id']);

				if ($store) {
					$storeAddress = $store['config_address'];
					$storeEmail = $store['config_email'];
					$storeTelephone = $store['config_telephone'];
					$storeFax = $store['config_fax'];
				} else {
					$storeAddress = $this->config->get('config_address');
					$storeEmail = $this->config->get('config_email');
					$storeTelephone = $this->config->get('config_telephone');
					$storeFax = $this->config->get('config_fax');
				}

				if ($i['invoice_no']) {
					$invoiceNo = $i['invoice_prefix'] . $i['invoice_no'];
				} else {
					$invoiceNo = '';
				}

				$this->load->model('sale/customer'); // Load customer and customer addresses
				$c = $this->model_resource_transaction->getCustomer($i['invoice_id']);
				/*if (!isset($customer_group_id)) {
					$c['customer_group_id'] = 0; // TODO: Set a default value
				}*/

				$i['shipping_method'] = $i['shipping_method'];
				$i['shipping_code'] = $i['shipping_code'];

				if (isset($this->session->data['invoice_info'])) {
					$i = array_merge($i, $this->session->data['invoice_info']);
				}

				if (isset($i['payment_address_format'])) {
					$format = $i['payment_address_format'];
					$i['payment_address_format'] = $format;
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{city} {zone}' . "\n" . '{postcode} {country}';
					$i['payment_address_format'] = '{firstname} {lastname}\n{company}\n{address_1}\n{city} {zone}\n{postcode} {country}';
				}

				$i['payment_address'] = self::formatPaymentAddress($format, $i, false, true); // Final "true" sets output to HTML

				if (isset($i['shipping_address_format'])) {
					$format = $i['shipping_address_format'];
					$i['shipping_address_format'] = $format;
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{city} {zone}' . "\n" . '{postcode} {country}';
					$i['shipping_address_format'] = '{firstname} {lastname}\n{company}\n{address_1}\n{city} {zone}\n{postcode} {country}';
				}

				$i['shipping_address'] = self::formatShippingAddress($format, $i, false, true); // Final "true" sets output to HTML

				$i['order_id'] = (isset($i['oc_entity_id'])) ? $i['oc_entity_id'] : null;

				$this->load->model('tool/upload');

				// This alternate method loads lines from session
				// $this->getLinesFromSession();

				$productData = array();

				$lines = $this->model_resource_transaction->getLineItems($i['invoice_id']);
				// We need to set lines to session
				foreach ($lines as $line) {
					$options = array();
					if (isset($line['invoice_id'])) {
						$options = $this->model_sale_order->getOrderOptions($i['invoice_id'], $line['order_product_id']);
					}

					// TODO: Modify whatever's converting the entity to an array so we don't have to do this
					// Either that or make a utility method to get assoc keys
					$productId = null;
					if (isset($line['product']) && isset($line['product']['productId'])) {
						$productId = $line['product']['productId'];
					}

					$item = array(
						'product_id' 		=> $productId,
						'line_id'	 		=> $line['line_id'],
						'detail_type'	 	=> $line['detail_type'],
						'invoice_id'	 	=> $invoiceId,
						'order_product_id'	=> $line['order_product_id'],
						'description'	 	=> $line['description'],
						'name'       		=> $line['name'],
						'model'      		=> $line['model'],
						'option'     		=> $options,
						'quantity'  		=> $line['quantity'],
						'revenue'    		=> (isset($line['revenue'])) ? $line['revenue'] : null, // ?
						//'track'       		=> (isset($line['track'])) ? $line['track'] : null, // ?
						'track'       		=> (isset($line['revenue'])) ? $line['revenue'] - $line['royalty'] - $line['total'] : null, // ?
						'price'      		=> (isset($line['price']) && (float)$line['price'] > 0) ? $line['price'] : null,
						'royalty'    		=> (isset($line['royalty'])) ? $line['royalty'] : null,
						'total'     		=> $line['total'],
						'reward'     		=> $line['reward']
					);

					$i['lines'][] = $item;
				}

				//$lines = $this->model_resource_transaction->getLineItems($invoice_id);


				$voucherData = array();

				$vouchers = $this->model_sale_order->getOrderVouchers($invoiceId);

				foreach ($vouchers as $voucher) {
					$voucherData[] = array(
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'], $i['currency_code'], $i['currency_value'])
					);
				}

				$totalData = array();

				// Totals
				$this->load->model('extension/extension');

				$totalData = array();
				$total = 0;
				$taxes = $this->lines->getTaxes();

				$sort_order = array();

				$results = $this->model_extension_extension->getExtensions('total');

				foreach ($results as $key => $value) {
					$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
				}

				array_multisort($sort_order, SORT_ASC, $results);

				foreach ($results as $result) {
					if ($this->config->get($result['code'] . '_status')) {
						$this->load->model('total/' . $result['code']);

						$this->{'model_total_' . $result['code']}->getTotal($totalData, $total, $taxes, true);
					}
				}

				$sort_order = array();

				foreach ($totalData as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $totalData);

				$totals = array();

				foreach ($totalData as $total) {
					$totals[] = array(
						'title' => $total['title'],
						'text'  => $this->currency->format($total['value'])
					);
				}

				$data['invoices'][] = array(
					'invoice_id'	     => $invoiceId,
					'order_id'	     	 => $i['order_id'],
					'invoice_no'         => $invoiceNo,
					'date_added'         => date($this->language->get('date_format_short'), strtotime($i['date_added'])),
					'store_name'         => $i['store_name'],
					'store_url'          => rtrim($i['store_url'], '/'),
					'store_address'      => nl2br($storeAddress),
					'store_email'        => $storeEmail,
					'store_telephone'    => $storeTelephone,
					'store_fax'          => $storeFax,
					'email'              => $i['bill_email'],
					'telephone'          => $i['bill_telephone'],
					'shipping_address'   => $i['shipping_address'],
					'shipping_method'    => $i['shipping_method'],
					'payment_address'    => $i['payment_address'],
					'payment_method'     => $i['payment_method'],
					'lines'            	 => (isset($i['lines'])) ? $i['lines'] : array(),
					'voucher'            => $voucherData,
					'total'              => $totals, //$total_data,
					'customer_memo'      => nl2br($i['customer_memo']),
					'statement_memo'     => nl2br($i['statement_memo'])
				);
			}
		}

		$this->response->setOutput($this->load->view('transaction/invoice_invoice.tpl', $data));
	}

	/**
	 * I ripped this out of the invoice method, but it could still be useful for like a preview or other applications.
	 * It will need to be updated accordingly... this is a straight-up copy and paste
	 */
	protected function getLinesFromSession() {
		$lines = $this->load->controller('api/lines/lines', array('export' => false));

		// Need to set lines to session
		foreach ($lines as $line) {
			$options = array();
			if (isset($line['invoice_id'])) {
				$options = $this->model_sale_order->getOrderOptions($data['invoice_id'], $line['order_product_id']);
			}

			// TODO: Modify whatever's converting the entity to an array so we don't have to do this
			// Either that or make a utility method to get assoc keys
			$product_id = null;
			if (isset($line['product']) && isset($line['product']['productId'])) {
				$product_id = $line['product']['productId'];
			}

			$lines[] = array(
				'product_id' => $product_id,
				'name'       => (isset($line['name'])) ? $line['name'] : '',
				'model'      => (isset($line['model'])) ? $line['model'] : '',
				//'option'     => (isset($line['invoice_id'])) ? $this->model_sale_order->getOrderOptions($data['invoice_id'], $line['order_product_id']) : null,
				'quantity'   => (isset($line['quantity'])) ? $line['quantity'] : 1,
				'revenue'    => (isset($line['revenue'])) ? $line['revenue'] : '',
				'vest'       => (isset($line['vest'])) ? $line['vest'] : '',
				'price'      => (isset($line['price'])) ? $line['price'] : 0.00,
				'royalty'    => (isset($line['royalty'])) ? $line['royalty'] : '',
				'total'      => (isset($line['total'])) ? $line['total'] : 0.00,
				'reward'     => (isset($line['reward'])) ? $line['reward'] : null
			);

			if ($product_id != null) {
				$this->lines->add($product_id, $line['quantity'], $options);
			} elseif (isset($line['model'])) {
				$this->lines->addDescription($line['model'], $line['quantity']);
			}
		}
	}

	protected static function formatPaymentAddress($format, $data, $replace = false, $html = true) {
		return self::formatAddress('bill', $format, $data, $replace, $html);
	}

	protected static function formatShippingAddress($format, $data, $replace = false, $html = true) {
		return self::formatAddress('ship', $format, $data, $replace, $html);
	}

	protected static function formatAddress($type, $format, $data, $replace = false, $html = true) {
		$prefix = '';
		if ($type == 'ship' || $type == 'bill') {
			$prefix = $type . '_addr_';
		}

		$find = array(
			'{firstname}',
			'{lastname}',
			'{company}',
			'{address_1}',
			'{address_2}',
			'{city}',
			'{postcode}',
			'{zone}',
			//'{zone_code}',
			'{country}'
		);

		if (!$replace) {
			$replace = array(
				'firstname' => (isset($data[$prefix . 'firstname'])) ? $data[$prefix . 'firstname'] : '',
				'lastname'  => (isset($data[$prefix . 'lastname'])) ? $data[$prefix . 'lastname'] : '',
				'company'   => (isset($data[$prefix . 'company'])) ? $data[$prefix . 'company'] : '',
				'address_1' => (isset($data[$prefix . 'line1'])) ? $data[$prefix . 'line1'] : '',
				'address_2' => (isset($data[$prefix . 'line2'])) ? $data[$prefix . 'line2'] : '',
				'city'      => (isset($data[$prefix . 'city'])) ? $data[$prefix . 'city'] : '',
				'postcode'  => (isset($data[$prefix . 'postcode'])) ? $data[$prefix . 'postcode'] : '',
				'zone'      => (isset($data[$prefix . 'zone'])) ? $data[$prefix . 'zone'] : '',
				//'zone_code' => $data[$prefix . 'zone_code'],
				'country'   => (isset($data[$prefix . 'country'])) ? $data[$prefix . 'country'] : ''
			);

		}

		if ($html) {
			$address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
		} else {
			$address = trim(str_replace($find, $replace, $format));
		}

		return $address;
	}

	// TODO: Static method in utility class
	protected static function convertDateArray($arr) {
		$date = new DateTime($arr['date']);
		$date->setTimezone(new DateTimeZone($arr['timezone']));

		return $date;
	}

	// Save this might need it later
	/*
	protected static function formatAddress($type, $format, $data, $replace = false) {
		$prefix = '';
		if ($type == 'shipping' || $type == 'billing') {
			$prefix = $type . '_';
		}
		
		$find = array(
			'{firstname}',
			'{lastname}',
			'{company}',
			'{address_1}',
			'{address_2}',
			'{city}',
			'{postcode}',
			'{zone}',
			'{zone_code}',
			'{country}'
		);
		
		if (!$replace) {
			$replace = array(
				'firstname' => $data[$prefix . 'firstname'],
				'lastname'  => $data[$prefix . 'lastname'],
				'company'   => $data[$prefix . 'company'],
				'address_1' => $data[$prefix . 'address_1'],
				'address_2' => $data[$prefix . 'address_2'],
				'city'      => $data[$prefix . 'city'],
				'postcode'  => $data[$prefix . 'postcode'],
				'zone'      => $data[$prefix . 'zone'],
				'zone_code' => $data[$prefix . 'zone_code'],
				'country'   => $data[$prefix . 'country']
			);
			
		}

		$address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
		
		return $address;
	}
	*/

	public function shipping() {
		$data = $this->load->language('transaction/invoice');

		$data['title'] = $this->language->get('text_shipping');

		if ($this->request->server['HTTPS']) {
			$data['base'] = HTTPS_SERVER;
		} else {
			$data['base'] = HTTP_SERVER;
		}

		$data['direction'] = $this->language->get('direction');
		$data['lang'] = $this->language->get('code');

		$this->load->model('sale/order');

		$this->load->model('catalog/product');

		$this->load->model('setting/setting');

		$data['invoices'] = array();

		$invoices = array();

		if (isset($this->request->post['selected'])) {
			$invoices = $this->request->post['selected'];
		} elseif (isset($this->request->get['invoice_id'])) {
			$invoices[] = $this->request->get['invoice_id'];
		}

		$this->load->model('resource/transaction');
		$tModel = &$this->model_resource_transaction;

		$tService = new TransactionInvoice($this, 'OcInvoice');
		$tService->setEntityManager($this->em);
		$tModel->setTransactionType($tService);

		foreach ($invoices as $invoiceId) {
			$i = $tModel->getTransaction($invoiceId);

			if ($i) {
				$store = $this->model_setting_setting->getSetting('config', $i['store_id']);

				if ($store) {
					$storeAddress = $store['config_address'];
					$storeEmail = $store['config_email'];
					$storeTelephone = $store['config_telephone'];
					$storeFax = $store['config_fax'];
				} else {
					$storeAddress = $this->config->get('config_address');
					$storeEmail = $this->config->get('config_email');
					$storeTelephone = $this->config->get('config_telephone');
					$storeFax = $this->config->get('config_fax');
				}

				if ($i['invoice_no']) {
					$invoiceNo = $i['invoice_prefix'] . $i['invoice_no'];
				} else {
					$invoiceNo = '';
				}

				$this->load->model('sale/customer'); // Load customer and customer addresses
				$c = $tModel->getCustomer($i['invoice_id']);
				/*if (!isset($customer_group_id)) {
					$c['customer_group_id'] = 0; // TODO: Set a default value
				}*/

				if (isset($i['payment_address_format'])) {
					$format = $i['payment_address_format'];
					$i['payment_address_format'] = $format;
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{city} {zone}' . "\n" . '{postcode} {country}';
					$i['payment_address_format'] = '{firstname} {lastname}\n{company}\n{address_1}\n{city} {zone}\n{postcode} {country}';
				}

				$i['payment_address'] = self::formatPaymentAddress($format, $i, false, true); // Final "true" sets output to HTML

				if (isset($i['shipping_address_format'])) {
					$format = $i['shipping_address_format'];
					$i['shipping_address_format'] = $format;
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{city} {zone}' . "\n" . '{postcode} {country}';
					$i['shipping_address_format'] = '{firstname} {lastname}\n{company}\n{address_1}\n{city} {zone}\n{postcode} {country}';
				}

				$i['shipping_address'] = self::formatShippingAddress($format, $i, false, true); // Final "true" sets output to HTML

				$i['order_id'] = (isset($i['oc_entity_id'])) ? $i['oc_entity_id'] : null;

				$i['shipping_method'] = $i['shipping_method'];
				$i['shipping_code'] = $i['shipping_code'];

				$this->load->model('tool/upload');

				// This alternate method loads lines from session
				// $this->getLinesFromSession();

				$productData = array();

				$lines = $tModel->getLineItems($invoiceId);
				// We need to set lines to session
				foreach ($lines as $line) {
					$options = array();
					if (isset($line['order_id'])) {
						$options = $this->model_sale_order->getOrderOptions($i['order_id'], $line['order_product_id']);
					}

					// TODO: Modify whatever's converting the entity to an array so we don't have to do this
					// Either that or make a utility method to get assoc keys
					$productId = null;
					$product = array();

					if (isset($line['product']) && isset($line['product']['productId'])) {
						$productId = $line['product']['productId'];

						$this->load->model('catalog/product');
						$product = $this->model_catalog_product->getProduct($productId);
					}

					$item = array(
						'product_id' 		=> $productId,
						'line_id'	 		=> $line['line_id'],
						'detail_type'	 	=> $line['detail_type'],
						'order_id'	 		=> $line['order_id'],
						'order_product_id'	=> $line['order_product_id'],
						'product'			=> $product,
						'description'	 	=> $line['description'],
						'name'       		=> $line['name'],
						'model'      		=> $line['model'],
						'option'     		=> (isset($line['order_id'])) ? $this->model_sale_order->getOrderOptions($i['order_id'], $line['order_product_id']) : array(),
						'quantity'  		=> $line['quantity'],
						'revenue'    		=> (isset($line['revenue'])) ? $line['revenue'] : null, // ?
						//'track'       		=> (isset($line['track'])) ? $line['track'] : null, // ?
						'track'       		=> (isset($line['revenue'])) ? $line['revenue'] - $line['royalty'] - $line['total'] : null, // ?
						'price'      		=> (isset($line['price']) && (float)$line['price'] > 0) ? $line['price'] : null,
						'royalty'    		=> (isset($line['royalty'])) ? $line['royalty'] : null,
						'total'     		=> $line['total'],
						'reward'     		=> $line['reward']
					);

					$i['lines'][] = $item;
				}

				$voucherData = array();

				$vouchers = $this->model_sale_order->getOrderVouchers($i['order_id']);

				foreach ($vouchers as $voucher) {
					$voucherData[] = array(
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'], $i['currency_code'], $i['currency_value'])
					);
				}

				$totalData = array();

				/*$totals = $this->model_sale_order->getOrderTotals($invoiceId);

				foreach ($totals as $total) {
					$total_data[] = array(
						'title' => $total['title'],
						'text'  => $this->currency->format($total['value'], $invoice_info['currency_code'], $invoice_info['currency_value']),
					);
				}*/

				// Totals
				$this->load->model('extension/extension');

				$totalData = array();
				$total = 0;
				$taxes = $this->lines->getTaxes();

				$sort = array();

				$results = $this->model_extension_extension->getExtensions('total');

				foreach ($results as $key => $value) {
					$sort[$key] = $this->config->get($value['code'] . '_sort_order');
				}

				array_multisort($sort, SORT_ASC, $results);

				foreach ($results as $result) {
					if ($this->config->get($result['code'] . '_status')) {
						$this->load->model('total/' . $result['code']);

						$this->{'model_total_' . $result['code']}->getTotal($totalData, $total, $taxes, true);
					}
				}

				$sort = array();

				foreach ($totalData as $key => $value) {
					$sort[$key] = $value['sort_order'];
				}

				array_multisort($sort, SORT_ASC, $totalData);

				$totals = array();

				foreach ($totalData as $total) {
					$totals[] = array(
						'title' => $total['title'],
						'text'  => $this->currency->format($total['value'])
					);
				}

				$data['invoices'][] = array(
					'invoice_id'	     => $invoiceId,
					'order_id'	     	 => $i['order_id'],
					'invoice_no'         => $invoiceNo,
					'date_added'         => date($this->language->get('date_format_short'), strtotime($i['date_added'])),
					'store_name'         => $i['store_name'],
					'store_url'          => rtrim($i['store_url'], '/'),
					'store_address'      => nl2br($storeAddress),
					'store_email'        => $storeEmail,
					'store_telephone'    => $storeTelephone,
					'store_fax'          => $storeFax,
					'email'              => $i['bill_email'],
					'telephone'          => $i['bill_telephone'],
					'shipping_address'   => $i['shipping_address'],
					'shipping_method'    => $i['shipping_method'],
					'payment_address'    => $i['payment_address'],
					'payment_method'     => $i['payment_method'],
					'lines'            	 => (isset($i['lines'])) ? $i['lines'] : array(),
					'voucher'            => $voucherData,
					'total'              => $totals, //$total_data,
					'customer_memo'      => nl2br($i['customer_memo']),
					'statement_memo'     => nl2br($i['statement_memo'])
				);
			}
		}

		$this->response->setOutput($this->load->view('transaction/invoice_shipping.tpl', $data));
	}

	public function api() {
		$this->load->language('transaction/invoice');

		if ($this->validate()) {
			// Store
			if (isset($this->request->get['store_id'])) {
				$store_id = $this->request->get['store_id'];
			} else {
				$store_id = 0;
			}

			$this->load->model('setting/store');

			$store_info = $this->model_setting_store->getStore($store_id);

			if ($store_info) {
				$url = $store_info['ssl'];
			} else {
				$url = HTTPS_CATALOG;
			}

			if (isset($this->session->data['cookie']) && isset($this->request->get['api'])) {
				// Include any URL perameters
				$url_data = array();

				foreach($this->request->get as $key => $value) {
					if ($key != 'route' && $key != 'token' && $key != 'store_id') {
						$url_data[$key] = $value;
					}
				}

				$curl = curl_init();

				// Set SSL if required
				if (substr($url, 0, 5) == 'https') {
					curl_setopt($curl, CURLOPT_PORT, 443);
				}

				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLINFO_HEADER_OUT, true);
				curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_URL, $url . 'index.php?route=' . $this->request->get['api'] . ($url_data ? '&' . http_build_query($url_data) : ''));

				if ($this->request->post) {
					curl_setopt($curl, CURLOPT_POST, true);
					curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($this->request->post));
				}

				curl_setopt($curl, CURLOPT_COOKIE, session_name() . '=' . $this->session->data['cookie'] . ';');

				$json = curl_exec($curl);

				curl_close($curl);
			}
		} else {
			$response = array();

			$response['error'] = $this->error;

			$json = json_encode($response);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput($json);
	}
}
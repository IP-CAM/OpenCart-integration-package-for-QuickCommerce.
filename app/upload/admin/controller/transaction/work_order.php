<?php
require_once(DIR_QC . 'vendor/autoload.php');
require_once(DIR_SYSTEM . 'engine/qccontroller.php');
require_once(DIR_SYSTEM . 'library/quickcommerce/doctrine.php');
require_once(DIR_SYSTEM . 'library/quickcommerce/entity_manager.php');
require_once(DIR_SYSTEM . 'library/quickcommerce/lines.php');
require_once(DIR_SYSTEM . 'library/quickcommerce/resource.php');
require_once(DIR_SYSTEM . 'library/quickcommerce/transaction/work_order.php');
require_once(DIR_SYSTEM . 'library/quickcommerce/session.php');

use App\Resource\Product;
use App\Resource\WorkOrder;
use App\Resource\WorkOrderLine;
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

class ControllerTransactionWorkOrder extends Controller {
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
		
		$registry->set('lines', new Lines($registry)); // For session ops
		parent::__construct($registry);
		
		$di = new DoctrineInitializer($this, $registry);
	}
	
	public function mapDoctrineEntity(&$mappings, $config = array(), $children = false, $foreign = true) {
		DoctrineEntityMapper::mapDoctrineEntity($this, $mappings, $config, $children, $foreign);
	}

	public function store() {
		
	}
	
	public function index() {
		$this->load->language('transaction/work_order');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('resource/transaction');
		$tModel = &$this->model_resource_transaction;
		
		$tService = new TransactionWorkOrder($this, 'OcWorkOrder');
		$tService->setEntityManager($this->em);
		$tModel->setTransactionType($tService);

		$this->getList();
	}

	/**
	 * Credits mcuadros/currency-detector
	 * https://github.com/mcuadros/currency-detector
	 * TODO: Move this to a utility class or something
	 */
	public static function cleanCurrency($money)
	{
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
			if ($this->lines->hasShipping()) {
				// Shipping Address
				/*if (!isset($this->session->data['shipping_address'])) {
					$json['error'] = $this->language->get('error_shipping_address');
				}*/

				// Shipping Method
				/*if (!isset($this->request->post['shipping_method'])) {
					$json['error'] = $this->language->get('error_shipping_method');
				}*/
			} else {
				ModelOcSessionShipping::clearShipping($this);
			}

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
			
			//$lines = $this->model_resource_transaction->getLineItems($work_order_id);
			$lines = $this->load->controller('api/lines/lines', array('export' => false));
			//var_dump($json);
			if (!$json) {
				$work_order_data = array();

				// Store Details
				$work_order_data['work_order_prefix'] = $this->config->get('config_work_order_prefix');
				$work_order_data['store_id'] = ($this->config->get('config_store_id') != null) ? $this->config->get('config_store_id') : 0;
				$work_order_data['store_name'] = $this->config->get('config_name');
				$work_order_data['store_url'] = $this->config->get('config_url');
				
				$work_order_data['oc_entity_id'] = (isset($this->request->post['order_id'])) ? $this->request->post['order_id'] : null;
				
				$customer = ModelOcSessionCustomer::getCustomer($this);

				$work_order_data = array_merge($work_order_data, $customer);
				$work_order_data['bill_email'] = $customer['email'];
				$work_order_data['bill_telephone'] = $customer['telephone'];
				$work_order_data['bill_fax'] = $customer['fax'];

				$payment = ModelOcSessionPayment::getPayment($this);
				//echo 'Payment';
				//var_dump($payment);
				$work_order_data = array_merge($work_order_data, array(
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
				if ($this->lines->hasShipping()) {
					$shipping = ModelOcSessionShipping::getShipping($this);
					$work_order_data = array_merge($work_order_data, array(
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
				} else {
					$work_order_data['shipping_firstname'] = '';
					$work_order_data['shipping_lastname'] = '';
					$work_order_data['shipping_company'] = '';
					$work_order_data['shipping_address_1'] = '';
					$work_order_data['shipping_address_2'] = '';
					$work_order_data['shipping_city'] = '';
					$work_order_data['shipping_postcode'] = '';
					$work_order_data['shipping_zone'] = '';
					$work_order_data['shipping_zone_id'] = '';
					$work_order_data['shipping_country'] = '';
					$work_order_data['shipping_country_id'] = '';
					$work_order_data['shipping_address_format'] = '';
					$work_order_data['shipping_custom_field'] = array();
					$work_order_data['shipping_method'] = '';
					$work_order_data['shipping_code'] = '';
				}

				// Products
				$work_order_data['products'] = array();

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

					$work_order_data['products'][] = array(
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
					$line['order_id'] = (isset($line['rawkey']['order_id'])) ? $line['rawkey']['order_id'] : null;
					$line['order_product_id'] = (isset($line['rawkey']['order_product_id'])) ? $line['rawkey']['order_product_id'] : null;

					$options = array(); // TODO: I need to enforce order product id - it is required later
					if (isset($line['order_id']) && isset($line['order_product_id'])) {
						$options = $this->model_sale_order->getOrderOptions($line['order_id'], $line['order_product_id']);
					}
					
					// TODO: Modify whatever's converting the entity to an array so we don't have to do this
					// Either that or make a utility method to get assoc keys
					$product_id = null;
					if (isset($line['product']) && isset($line['product']['productId'])) {
						$product_id = $line['product']['productId'];
					} elseif (isset($line['product_id'])) {
						$product_id = $line['product_id'];
					}
					
					$work_order_data['lines'][] = array(
						'line_id'			=> $line['line_id'],
						'detail_type'		=> $line['detail_type'],
						'order_id'			=> $line['order_id'],
						'order_product_id'	=> $line['order_id'],
						'product_id'		=> $product_id,
						'name'			=> (isset($line['name'])) ? $line['name'] : '',
						'model'			=> (isset($line['model'])) ? $line['model'] : '',
						//'option'		=> (isset($line['order_id'])) ? $this->model_sale_order->getOrderOptions($data['order_id'], $line['order_product_id']) : null,
						'quantity'		=> (isset($line['quantity'])) ? $line['quantity'] : 1,
						'revenue'    	=> (isset($line['revenue'])) ? self::cleanCurrency($line['revenue']) : '',
						'vest'       	=> (isset($line['vest'])) ? self::cleanCurrency($line['vest']) : '',
						'price'      	=> (isset($line['price'])) ? self::cleanCurrency($line['price']) : 0.00,
						'royalty'    	=> (isset($line['royalty'])) ? self::cleanCurrency($line['royalty']) : '',
						'total'      	=> (isset($line['total'])) ? self::cleanCurrency($line['total']) : 0.00,
						'reward'     	=> (isset($line['reward'])) ? self::cleanCurrency($line['reward']) : null
					);
					
					/*if ($product_id != null) {
						$this->lines->add($product_id, $line['quantity'], $options);	
					} elseif (isset($line['model'])) {
						$this->lines->addDescription($line['model'], $line['quantity']);
					}*/
				}

				// Gift Voucher
				$work_order_data['vouchers'] = array();
				
				$vouchers = ModelOcSessionVoucher::getVouchers($this);
				if ($vouchers) {
					$work_order_data = array_merge($work_order_data, $vouchers);
				}

				// Order Totals
				$this->load->model('extension/extension');

				$work_order_data['totals'] = array();
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

						//$this->{'model_total_' . $result['code']}->getTotal($work_order_data['totals'], $total, $taxes);
					}
				}

				$sort_order = array();

				foreach ($work_order_data['totals'] as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $work_order_data['totals']);

				if (isset($this->request->post['comment'])) {
					$work_order_data['comment'] = $this->request->post['comment'];
				} else {
					$work_order_data['comment'] = '';
				}

				$work_order_data['total'] = $total;

				if (isset($this->request->post['affiliate_id'])) {
					$subtotal = $this->lines->getSubTotal();

					// Affiliate
					$this->load->model('affiliate/affiliate');

					$affiliate_info = $this->model_affiliate_affiliate->getAffiliate($this->request->post['affiliate_id']);

					if ($affiliate_info) {
						$work_order_data['affiliate_id'] = $affiliate_info['affiliate_id'];
						$work_order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
					} else {
						$work_order_data['affiliate_id'] = 0;
						$work_order_data['commission'] = 0;
					}

					// Marketing
					$work_order_data['marketing_id'] = 0;
					$work_order_data['tracking'] = '';
				} else {
					$work_order_data['affiliate_id'] = 0;
					$work_order_data['commission'] = 0;
					$work_order_data['marketing_id'] = 0;
					$work_order_data['tracking'] = '';
				}

				$work_order_data['language_id'] = $this->config->get('config_language_id');
				$work_order_data['currency_id'] = $this->currency->getId();
				$work_order_data['currency_code'] = $this->currency->getCode();
				$work_order_data['currency_value'] = $this->currency->getValue($this->currency->getCode());
				$work_order_data['ip'] = $this->request->server['REMOTE_ADDR'];
				
				//var_dump($work_order_data);

				if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
					$work_order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
				} elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
					$work_order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
				} else {
					$work_order_data['forwarded_ip'] = '';
				}

				if (isset($this->request->server['HTTP_USER_AGENT'])) {
					$work_order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
				} else {
					$work_order_data['user_agent'] = '';
				}

				if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
					$work_order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
				} else {
					$work_order_data['accept_language'] = '';
				}
				
				// TODO: Create order if necessary?
				//$this->load->model('checkout/order');
				//$json['order_id'] = $this->model_checkout_order->addOrder($work_order_data);

				//$this->load->model('transaction/invoice');
				// Set the order history
				if (isset($this->request->post['order_status_id'])) {
					$order_status_id = $this->request->post['order_status_id'];
				} else {
					$order_status_id = $this->config->get('config_order_status_id');
				}
				
				//$this->model_checkout_order->addOrderHistory($json['order_id'], $order_status_id);

				$json['success'] = $this->language->get('text_success');
			}
		//}
		
		return $work_order_data;
	}
	
	public function add() {
		$this->load->language('transaction/work_order');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('resource/transaction');
		$tModel = &$this->model_resource_transaction;
		
		$tService = new TransactionWorkOrder($this, 'OcWorkOrder');
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
					'oc_entity_id' => 0, // TODO: This could be set, but I need to finish tying in orders
					'store_id' => (isset($model['store_id'])) ? $model['store_id'] : 0,
					'store_name' => (isset($model['store_name'])) ? $model['store_name'] : '',
					'store_url' => (isset($model['store_url'])) ? $model['store_url'] : '',
					'currency_id' => (isset($model['currency_id'])) ? $model['currency_id'] : '',
					'currency_code' => (isset($model['currency_code'])) ? $model['currency_code'] : '',
					'currency_value' => (isset($model['currency_value'])) ? $model['currency_value'] : '',
					'forwarded_ip' => (isset($model['forwarded_ip'])) ? $model['forwarded_ip'] : '',
					'user_agent' => (isset($model['accept_language'])) ? $model['accept_language'] : '',
					'date_added' => $now, // Now
					'date_modified' => $now, // Now,
					'invoice' => (isset($work_order_info)) ? array_merge($work_order_info, $model) : $model
				);

				$model['invoice']['lines'] = array();
			}

			if ($model != null) {
				$tService->addTransaction($model);

				// TODO: If success...
				$this->response->redirect($this->url->link('transaction/invoice', 'token=' . $this->session->data['token'], 'SSL'));
				//$this->response->redirect($this->url->link('transaction/invoice', 'token=' . $this->session->data['token'] . $url, 'SSL'));
			}
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->model('sale/order');
		
		$this->load->model('resource/transaction');
		$tModel = &$this->model_resource_transaction;
		
		$tService = new TransactionWorkOrder($this, 'OcWorkOrder');
		$tService->setEntityManager($this->em);
		$tModel->setTransactionType($tService);

		unset($this->session->data['cookie']);
		
		if (isset($this->request->get['work_order_id']) && ($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$work_order_info = $this->model_resource_transaction->getTransaction($this->request->get['work_order_id']);
			
			$model = null;
			if ($this->validate()) {
				$model = $this->post();

				$lines = $model['lines'];
				unset($model['lines']); // TODO: Lines are/may be attached to the wrong branch of the array? Quick fix

				$model = array(
					'transaction_id' => (isset($model['transaction_id'])) ? $model['transaction_id'] : $work_order_info['transaction_id'],
					'oc_entity_id' => (isset($model['oc_entity_id'])) ? $model['oc_entity_id'] : $work_order_info['oc_entity_id'],
					'store_id' => (isset($model['store_id'])) ? $model['store_id'] : $work_order_info['store_id'],
					'store_name' => (isset($model['store_name'])) ? $model['store_name'] : $work_order_info['store_name'],
					'store_url' => (isset($model['store_url'])) ? $model['store_url'] : $work_order_info['store_url'],
					'currency_id' => (isset($model['currency_id'])) ? $model['currency_id'] : $work_order_info['currency_id'],
					'currency_code' => (isset($model['currency_code'])) ? $model['currency_code'] : $work_order_info['currency_code'],
					'currency_value' => (isset($model['currency_value'])) ? $model['currency_value'] : $work_order_info['currency_value'],
					'forwarded_ip' => (isset($model['forwarded_ip'])) ? $model['forwarded_ip'] : $work_order_info['forwarded_ip'],
					'user_agent' => (isset($model['accept_language'])) ? $model['accept_language'] : $work_order_info['accept_language'],
					'date_added' => (isset($model['date_added'])) ? $model['date_added'] : $work_order_info['date_added'],
					'date_modified' => date('Y-m-d H:i:s'), // Now
					'invoice' => array_merge($work_order_info, $model)
				);

				$model['invoice']['lines'] =  $lines;
				//var_dump($model);
				//exit;
			}
			
			if ($model != null) {
				$tService->editTransaction($model);
			}
		} else {
			$this->getForm();
		}
	}

	public function delete() {
		$this->load->language('transaction/work_order');

		$this->document->setTitle($this->language->get('heading_title'));

		unset($this->session->data['cookie']);

		$this->load->model('sale/order');
		
		$this->load->model('resource/transaction');
		$tModel = &$this->model_resource_transaction;
		
		$tService = new TransactionWorkOrder($this, 'OcWorkOrder');
		$tService->setEntityManager($this->em);
		$tModel->setTransactionType($tService);
		
		if (isset($this->request->get['work_order_id']) && ($this->request->server['REQUEST_METHOD'] == 'GET')) {
			$work_order_info = $tService->getEntity($this->request->get['work_order_id']);

			$model = null;
			if ($this->validate()) {
				$model = $this->post();
				$model = array_merge(array(
					'transaction_id' => $work_order_info['transaction_id'],
					'oc_entity_id' => $work_order_info['oc_entity_id'],
					'store_id' => $work_order_info['store_id'],
					'store_name' => $work_order_info['store_name'],
					'store_url' => $work_order_info['store_url'],
					'date_added' => $work_order_info['date_added'],
					'date_modified' => $work_order_info['date_added'],
					'invoice' => $work_order_info
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

			if (isset($this->request->get['filter_work_order_id'])) {
				$url .= '&filter_work_order_id=' . $this->request->get['filter_work_order_id'];
			}

			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_work_order_status'])) {
				$url .= '&filter_work_order_status=' . $this->request->get['filter_work_order_status'];
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

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_work_order_id'])) {
			$filter_work_order_id = $this->request->get['filter_work_order_id'];
		} else {
			$filter_work_order_id = null;
		}

		if (isset($this->request->get['filter_customer'])) {
			$filter_customer = $this->request->get['filter_customer'];
		} else {
			$filter_customer = null;
		}

		if (isset($this->request->get['filter_work_order_status'])) {
			$filter_work_order_status = $this->request->get['filter_work_order_status'];
		} else {
			$filter_work_order_status = null;
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
			$sort = 'o.work_order_id';
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

		if (isset($this->request->get['filter_work_order_id'])) {
			$url .= '&filter_work_order_id=' . $this->request->get['filter_work_order_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_work_order_status'])) {
			$url .= '&filter_work_order_status=' . $this->request->get['filter_work_order_status'];
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
		
		$data = $this->load->language('transaction/work_order');

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

		$filter_data = array(
			'filter_work_order_id'    => $filter_work_order_id,
			'filter_customer'	   => $filter_customer,
			'filter_work_order_status'  => $filter_work_order_status,
			'filter_total'         => $filter_total,
			'filter_date_added'    => $filter_date_added,
			'filter_date_modified' => $filter_date_modified,
			'sort'                 => $sort,
			'order'                => $order,
			'start'                => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                => $this->config->get('config_limit_admin')
		);
		
		$order_total = $this->model_resource_transaction->getTotalTransactions($filter_data);

		$results = $this->model_resource_transaction->getTransactions($filter_data);
		
		if ($results != null) {
			foreach ($results as $result) {
				$data['invoices'][] = array(
					'transaction_id'    => $result['transaction_id'],
					'work_order_id'    => $result['invoice_id'], // TODO: Fix me! Wrong ID
					'work_order_no'    => $result['invoice_no'], // FIX ME
					'order_id'      => $result['oc_entity_id'],
					'feed_id'		=> $result['feed_id'],
					'customer'      => $result['bill_email'],
					'status'        => (isset($result['status'])) ? $result['status'] : '',
					'total'         => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
					'date_added'    => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
					'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
					'shipping_code' => $result['shipping_code'],
					'view'          => $this->url->link('transaction/invoice/invoice', 'token=' . $this->session->data['token'] . '&work_order_id=' . $result['invoice_id'] . $url, 'SSL'),
					'edit'          => $this->url->link('transaction/work_order/edit', 'token=' . $this->session->data['token'] . '&work_order_id=' . $result['invoice_id'] . $url, 'SSL'),
					'delete'        => $this->url->link('transaction/invoice/delete', 'token=' . $this->session->data['token'] . '&work_order_id=' . $result['invoice_id'] . $url, 'SSL')
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

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
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

		$data['sort_order'] = $this->url->link('transaction/invoice', 'token=' . $this->session->data['token'] . '&sort=o.order_id' . $url, 'SSL');
		$data['sort_customer'] = $this->url->link('transaction/invoice', 'token=' . $this->session->data['token'] . '&sort=customer' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('transaction/invoice', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');
		$data['sort_total'] = $this->url->link('transaction/invoice', 'token=' . $this->session->data['token'] . '&sort=o.total' . $url, 'SSL');
		$data['sort_date_added'] = $this->url->link('transaction/invoice', 'token=' . $this->session->data['token'] . '&sort=o.date_added' . $url, 'SSL');
		$data['sort_date_modified'] = $this->url->link('transaction/invoice', 'token=' . $this->session->data['token'] . '&sort=o.date_modified' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
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
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('transaction/invoice', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['filter_work_order_id'] = $filter_work_order_id;
		$data['filter_customer'] = $filter_customer;
		$data['filter_work_order_status'] = $filter_work_order_status;
		$data['filter_total'] = $filter_total;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_date_modified'] = $filter_date_modified;

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('transaction/work_order_list.tpl', $data));
	}
	
	public function convertOrder() {
		$this->response->redirect($this->url->link('qc/invoice/convertOrder', 'token=' . $this->session->data['token'] . '&order_id=' . $this->request->get['order_id'], 'SSL'));
	}

	public function getForm() {
		$this->load->model('sale/customer');

		$data = array_merge(
			$this->language->load('module/payment_processor'),
			$this->load->language('transaction/work_order')
		);
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_form'] = !isset($this->request->get['order_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$data['token'] = $this->session->data['token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
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

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
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
		
		if (isset($this->request->get['work_order_id'])) {
			$data['shipping'] = $this->url->link('transaction/invoice/shipping', 'token=' . $this->session->data['token'] . '&work_order_id=' . (int)$this->request->get['work_order_id'], 'SSL'); // TODO: This should be using order id instead?
			$data['invoice'] = $this->url->link('transaction/invoice/invoice', 'token=' . $this->session->data['token'] . '&work_order_id=' . (int)$this->request->get['work_order_id'], 'SSL');
		} else {
			$data['shipping'] = false;
			$data['invoice'] = false;
		}
		
		$data['cancel'] = $this->url->link('transaction/invoice', 'token=' . $this->session->data['token'] . $url, 'SSL');
		
		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;

		if (isset($this->request->get['work_order_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$work_order_info = $this->model_resource_transaction->getTransaction($this->request->get['work_order_id']);
		}

		if (!empty($work_order_info)) {
			foreach ($work_order_info as $field => $val) {
				$data[$field] = $val;
			}

			$data['work_order_id'] = $this->request->get['work_order_id'];

			unset($data['custom_field']);
			$data['account_custom_field'] = (isset($work_order_info['custom_field'])) ? $work_order_info['custom_field'] : null;

			// TODO: Description, and other line types
			$data['lines'] = array();
			$data['order_id'] = $work_order_info['oc_entity_id'];

			// Clear session and get any existing line items
			$this->lines->clear();

			$lines = $this->model_resource_transaction->getLineItems($data['work_order_id']);
			// We need to set lines to session
			foreach ($lines as $line) {
				$options = array();
				if (isset($line['order_id'])) {
					$options = $this->model_sale_order->getOrderOptions($data['order_id'], $line['order_product_id']);
				}

				// TODO: Modify whatever's converting the entity to an array so we don't have to do this
				// Either that or make a utility method to get assoc keys
				$product_id = null;
				if (isset($line['product']) && isset($line['product']['productId'])) {
					$product_id = $line['product']['productId'];
				}

				$item = array(
					'product_id' 		=> $product_id,
					'line_id'	 		=> $line['line_id'],
					'detail_type'	 	=> $line['detail_type'],
					'order_id'	 		=> $line['order_id'],
					'order_product_id'	=> $line['order_product_id'],
					'description'	 	=> $line['description'],
					'name'       		=> $line['name'],
					'model'      		=> $line['model'],
					'option'     		=> (isset($line['order_id'])) ? $this->model_sale_order->getOrderOptions($data['order_id'], $line['order_product_id']) : null,
					'quantity'  		=> $line['quantity'],
					'price'      		=> $line['price'],
					'total'     		=> $line['total'],
					'reward'     		=> $line['reward']
				);

				$data['lines'][] = $item;

				if ($product_id != null) {
					//var_dump($product_id);
					$this->lines->add($item, $line['quantity'], $options);
				} elseif (isset($line['model'])) {
					//var_dump($line['model']);
					$this->lines->addDescription($line['model'], $line['quantity']);
				}
			}
			
			$this->load->model('sale/customer'); // Load customer and customer addresses
			$customer = $this->model_resource_transaction->getCustomer($data['work_order_id']);
			if (!isset($customer_group_id)) {
				$customer['customer_group_id'] = 0; // TODO: Set a default value
			}

			$data['addresses'] = $this->model_sale_customer->getAddresses($customer['customer_id']);

			// Set customer display fields
			$customer_data['customer'] = (isset($work_order_info['fullname'])) ? $work_order_info['fullname'] : $customer['fullname'];
			$customer_data['customer_id'] = (isset($work_order_info['customer_id'])) ? $work_order_info['customer_id'] : $customer['customer_id'];
			$customer_data['customer_group_id'] = (isset($work_order_info['customer_group_id'])) ? $work_order_info['customer_group_id'] : $customer['customer_group_id'];
			$customer_data['firstname'] = (isset($work_order_info['firstname'])) ? $work_order_info['firstname'] : $customer['firstname']; // Could be possible that the customer's name changed
			$customer_data['lastname'] = (isset($work_order_info['lastname'])) ? $work_order_info['lastname'] : $customer['lastname'];
			$customer_data['bill_email'] = (isset($work_order_info['bill_email'])) ? $work_order_info['bill_email'] : $customer['bill_email'];
			$customer_data['bill_telephone'] = (isset($work_order_info['bill_telephone'])) ? $work_order_info['bill_telephone'] : $customer['telephone'];
			$customer_data['bill_fax'] = (isset($work_order_info['bill_fax'])) ? $work_order_info['bill_fax'] : $customer['fax'];
			$customer_data['account_custom_field'] = $customer['custom_field'];

			ModelOcSessionCustomer::setCustomer($this, $customer_data);
			// TODO: Set payment and shipping

			$data = array_merge($data, $customer_data);

			// Field names in view do not match exactly to the invoice table fields
			$data['payment_firstname'] = $work_order_info['bill_addr_firstname']; //$work_order_info['payment_firstname'];
			$data['payment_lastname'] = $work_order_info['bill_addr_lastname']; //$work_order_info['payment_lastname'];
			$data['payment_company'] = (isset($work_order_info['bill_addr_company'])) ? $work_order_info['bill_addr_company'] : null;
			$data['payment_address_1'] = $work_order_info['bill_addr_line1'];
			$data['payment_address_2'] = $work_order_info['bill_addr_line2'];
			$data['payment_city'] = $work_order_info['bill_addr_city'];
			$data['payment_postcode'] = $work_order_info['bill_addr_postcode'];
			$data['payment_country_id'] = $work_order_info['bill_addr_country_id'];
			//$data['payment_zone'] = $work_order_info['bill_addr_zone'];
			$data['payment_zone_id'] = $work_order_info['bill_addr_zone_id'];
			$data['payment_custom_field'] = (isset($work_order_info['payment_custom_field'])) ? $work_order_info['payment_custom_field'] : null;
			$data['payment_method'] = $work_order_info['payment_method'];
			$data['payment_code'] = $work_order_info['payment_code'];

			$data['shipping_firstname'] = $work_order_info['ship_addr_firstname']; //$work_order_info['shipping_firstname'];
			$data['shipping_lastname'] = $work_order_info['ship_addr_lastname']; //$work_order_info['shipping_lastname'];
			$data['shipping_company'] = (isset($work_order_info['ship_addr_company'])) ? $work_order_info['ship_addr_company'] : null;
			$data['shipping_address_1'] = $work_order_info['ship_addr_line1'];
			$data['shipping_address_2'] = $work_order_info['ship_addr_line2'];
			$data['shipping_city'] = $work_order_info['ship_addr_city'];
			$data['shipping_postcode'] = $work_order_info['ship_addr_postcode'];
			$data['shipping_country_id'] = $work_order_info['ship_addr_country_id'];
			$data['shipping_zone_id'] = $work_order_info['ship_addr_zone_id'];
			$data['shipping_custom_field'] = (isset($work_order_info['shipping_custom_field'])) ? $work_order_info['shipping_custom_field'] : null;
			$data['shipping_method'] = $work_order_info['shipping_method'];
			$data['shipping_code'] = $work_order_info['shipping_code'];

			//var_dump($work_order_info);
			if (isset($this->session->data['work_order_info'])) {
				$work_order_info = array_merge($work_order_info, $this->session->data['work_order_info']);
			}

			// Add missing fields in invoice info
			/*if ($work_order_info) {
				$work_order_info['bill_addr_firstname'] = $work_order_info['firstname'];
				$work_order_info['bill_addr_lastname'] = $work_order_info['lastname'];
				//$work_order_info['bill_addr_fullname'] = $work_order_info['fullname'];

				$work_order_info['ship_addr_firstname'] = $work_order_info['firstname'];
				$work_order_info['ship_addr_lastname'] = $work_order_info['lastname'];
				//$work_order_info['ship_addr_fullname'] = $work_order_info['fullname'];

				$work_order_info['telephone'] =  (isset($work_order_info['telephone'])) ? $work_order_info['telephone'] : ''; // TODO: This is the current contact number, not the one at the time the invoice was created
			}*/

			if (isset($work_order_info['payment_address_format'])) {
				$format = $work_order_info['payment_address_format'];
				$data['payment_address_format'] = $format;
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{city} {zone}' . "\n" . '{postcode} {country}';
				$data['payment_address_format'] = '{firstname} {lastname}\n{company}\n{address_1}\n{city} {zone}\n{postcode} {country}';
			}
			
			$data['payment_address'] = self::formatPaymentAddress($format, $work_order_info, false, false);

			if (isset($work_order_info['shipping_address_format'])) {
				$format = $work_order_info['shipping_address_format'];
				$data['shipping_address_format'] = $format;
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{city} {zone}' . "\n" . '{postcode} {country}';
				$data['shipping_address_format'] = '{firstname} {lastname}\n{company}\n{address_1}\n{city} {zone}\n{postcode} {country}';
			}

		
			$data['shipping_address'] = self::formatShippingAddress($format, $work_order_info, false, false);

			// Add vouchers to the API
			$data['order_vouchers'] = $this->model_sale_order->getOrderVouchers($data['order_id']);

			$data['coupon'] = '';
			$data['voucher'] = '';
			$data['reward'] = '';

			$data['order_totals'] = array();

			$order_totals = $this->model_sale_order->getOrderTotals($data['order_id']);

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

			$data['order_status_id'] = (isset($work_order_info['order_status_id'])) ? : null;
			$data['comment'] = (isset($work_order_info['comment'])) ? $work_order_info['comment'] : null;
			$data['affiliate_id'] = (isset($work_order_info['affiliate_id'])) ? $work_order_info['affiliate_id'] : null;
			$data['affiliate'] = (isset($work_order_info['affiliate_firstname']) && isset($work_order_info['affiliate_firstname'])) ? $work_order_info['affiliate_firstname'] . ' ' . $work_order_info['affiliate_lastname'] : '';
			$data['currency_code'] = (isset($work_order_info['currency_code'])) ? $work_order_info['currency_code'] : '';
		} else {
			$data['work_order_id'] = null;
			$data['order_id'] = 0;
			$data['store_id'] = '';
			$data['work_order_info'] = '';
			$data['work_order_info_id'] = '';
			$data['customer_group_id'] = $this->config->get('config_customer_group_id');
			$data['firstname'] = '';
			$data['lastname'] = '';
			$data['email'] = '';
			$data['telephone'] = '';
			$data['fax'] = '';
			$data['work_order_info_custom_field'] = array();
			
			$data['addresses'] = array();
			$data['customer'] = null;
			$data['customer_id'] = null;
			$data['customer_group_id'] = null;
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
			
			if (isset($work_order_info['payment_address_format'])) {
				$format = $work_order_info['payment_address_format'];
				$data['payment_address_format'] = $format;
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{city} {zone}' . "\n" . '{postcode} {country}';
				$data['payment_address_format'] = '{firstname} {lastname}\n{company}\n{address_1}\n{city} {zone}\n{postcode} {country}';
			}
			
			if (isset($work_order_info['shipping_address_format'])) {
				$format = $work_order_info['shipping_address_format'];
				$data['shipping_address_format'] = $format;
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{city} {zone}' . "\n" . '{postcode} {country}';
				$data['shipping_address_format'] = '{firstname} {lastname}\n{company}\n{address_1}\n{city} {zone}\n{postcode} {country}';
			}

			$data['lines'] = array();
			$data['order_vouchers'] = array();
			$data['order_totals'] = array();

			$data['order_status_id'] = $this->config->get('config_order_status_id');
			$data['comment'] = '';
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

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		$this->load->model('localisation/currency');

		$data['currencies'] = $this->model_localisation_currency->getCurrencies();

		$data['voucher_min'] = $this->config->get('config_voucher_min');

		$this->load->model('sale/voucher_theme');

		$data['voucher_themes'] = $this->model_sale_voucher_theme->getVoucherThemes();
		
		//$this->load->controller('report/product_purchased');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('transaction/work_order_form.tpl', $data));
	}

	public function info() {
		$this->load->model('sale/order');

		if (isset($this->request->get['work_order_id'])) {
			$work_order_id = $this->request->get['work_order_id'];
		} else {
			$work_order_id = 0;
		}

		$work_order_info = $this->model_sale_order->getOrder($work_order_id);

		if ($work_order_info) {
			$data = $this->load->language('transaction/work_order');

			$this->document->setTitle($this->language->get('heading_title'));

			$data['heading_title'] = $this->language->get('heading_title');

			$data['token'] = $this->session->data['token'];

			$url = '';

			if (isset($this->request->get['filter_order_id'])) {
				$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
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

			$data['shipping'] = $this->url->link('transaction/invoice/shipping', 'token=' . $this->session->data['token'] . '&work_order_id=' . (int)$this->request->get['work_order_id'], 'SSL');
			$data['invoice'] = $this->url->link('transaction/invoice/invoice', 'token=' . $this->session->data['token'] . '&work_order_id=' . (int)$this->request->get['work_order_id'], 'SSL');
			$data['edit'] = $this->url->link('transaction/work_order/edit', 'token=' . $this->session->data['token'] . '&work_order_id=' . (int)$this->request->get['work_order_id'], 'SSL');
			$data['cancel'] = $this->url->link('transaction/invoice', 'token=' . $this->session->data['token'] . $url, 'SSL');

			$data['work_order_id'] = $this->request->get['work_order_id'];
			$data['order_id'] = $work_order_info['order_id'];

			if ($work_order_info['work_order_no']) {
				$data['work_order_no'] = $work_order_info['work_order_prefix'] . $work_order_info['work_order_no'];
			} else {
				$data['work_order_no'] = '';
			}

			$data['store_name'] = $work_order_info['store_name'];
			$data['store_url'] = $work_order_info['store_url'];
			$data['firstname'] = $work_order_info['firstname'];
			$data['lastname'] = $work_order_info['lastname'];

			if ($work_order_info['work_order_info_id']) {
				$data['work_order_info'] = $this->url->link('sale/work_order_info/edit', 'token=' . $this->session->data['token'] . '&work_order_info_id=' . $work_order_info['work_order_info_id'], 'SSL');
			} else {
				$data['work_order_info'] = '';
			}

			$this->load->model('sale/customer_group');

			$customer_group_info = $this->model_sale_customer_group->getCustomerGroup($work_order_info['customer_group_id']);

			if ($customer_group_info) {
				$data['customer_group'] = $customer_group_info['name'];
			} else {
				$data['customer_group'] = '';
			}

			$data['email'] = $work_order_info['email'];
			$data['telephone'] = $work_order_info['telephone'];
			$data['fax'] = $work_order_info['fax'];

			$data['account_custom_field'] = $work_order_info['custom_field'];

			// Uploaded files
			$this->load->model('tool/upload');

			// Custom Fields
			$this->load->model('sale/custom_field');

			$data['account_custom_fields'] = array();

			$custom_fields = $this->model_sale_custom_field->getCustomFields();

			foreach ($custom_fields as $custom_field) {
				if ($custom_field['location'] == 'account' && isset($work_order_info['custom_field'][$custom_field['custom_field_id']])) {
					if ($custom_field['type'] == 'select' || $custom_field['type'] == 'radio') {
						$custom_field_value_info = $this->model_sale_custom_field->getCustomFieldValue($work_order_info['custom_field'][$custom_field['custom_field_id']]);

						if ($custom_field_value_info) {
							$data['account_custom_fields'][] = array(
								'name'  => $custom_field['name'],
								'value' => $custom_field_value_info['name']
							);
						}
					}

					if ($custom_field['type'] == 'checkbox' && is_array($work_order_info['custom_field'][$custom_field['custom_field_id']])) {
						foreach ($work_order_info['custom_field'][$custom_field['custom_field_id']] as $custom_field_value_id) {
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
							'value' => $work_order_info['custom_field'][$custom_field['custom_field_id']]
						);
					}

					if ($custom_field['type'] == 'file') {
						$upload_info = $this->model_tool_upload->getUploadByCode($work_order_info['custom_field'][$custom_field['custom_field_id']]);

						if ($upload_info) {
							$data['account_custom_fields'][] = array(
								'name'  => $custom_field['name'],
								'value' => $upload_info['name']
							);
						}
					}
				}
			}

			$data['comment'] = nl2br($work_order_info['comment']);
			$data['shipping_method'] = $work_order_info['shipping_method'];
			$data['payment_method'] = $work_order_info['payment_method'];
			$data['total'] = $this->currency->format($work_order_info['total'], $work_order_info['currency_code'], $work_order_info['currency_value']);

			$this->load->model('sale/work_order_info');

			$data['reward'] = $work_order_info['reward'];

			// TODO: Replace with order id from data
			$data['reward_total'] = $this->model_sale_work_order_info->getTotalCustomerRewardsByOrderId($this->request->get['work_order_id']);

			$data['affiliate_firstname'] = $work_order_info['affiliate_firstname'];
			$data['affiliate_lastname'] = $work_order_info['affiliate_lastname'];

			if ($work_order_info['affiliate_id']) {
				$data['affiliate'] = $this->url->link('marketing/affiliate/edit', 'token=' . $this->session->data['token'] . '&affiliate_id=' . $work_order_info['affiliate_id'], 'SSL');
			} else {
				$data['affiliate'] = '';
			}

			$data['commission'] = $this->currency->format($work_order_info['commission'], $work_order_info['currency_code'], $work_order_info['currency_value']);

			$this->load->model('marketing/affiliate');

			// TODO: Replace with order id from data
			$data['commission_total'] = $this->model_marketing_affiliate->getTotalTransactionsByOrderId($this->request->get['work_order_id']);

			$this->load->model('localisation/order_status');

			// TODO: Fix me
			/*$order_status_info = $this->model_localisation_order_status->getWorkOrderStatus($work_order_info['order_status_id']);

			if ($order_status_info) {
				$data['order_status'] = $order_status_info['name'];
			} else {
				$data['order_status'] = '';
			}*/

			$data['ip'] = $work_order_info['ip'];
			$data['forwarded_ip'] = $work_order_info['forwarded_ip'];
			$data['user_agent'] = $work_order_info['user_agent'];
			$data['accept_language'] = $work_order_info['accept_language'];
			$data['date_added'] = date($this->language->get('date_format_short'), strtotime($work_order_info['date_added']));
			$data['date_modified'] = date($this->language->get('date_format_short'), strtotime($work_order_info['date_modified']));

			// Payment
			$data['payment_firstname'] = $work_order_info['payment_firstname'];
			$data['payment_lastname'] = $work_order_info['payment_lastname'];
			$data['payment_company'] = $work_order_info['payment_company'];
			$data['payment_address_1'] = $work_order_info['payment_address_1'];
			$data['payment_address_2'] = $work_order_info['payment_address_2'];
			$data['payment_city'] = $work_order_info['payment_city'];
			$data['payment_postcode'] = $work_order_info['payment_postcode'];
			$data['payment_zone'] = $work_order_info['payment_zone'];
			$data['payment_zone_code'] = $work_order_info['payment_zone_code'];
			$data['payment_country'] = $work_order_info['payment_country'];

			// Custom fields
			$data['payment_custom_fields'] = array();

			foreach ($custom_fields as $custom_field) {
				if ($custom_field['location'] == 'address' && isset($work_order_info['payment_custom_field'][$custom_field['custom_field_id']])) {
					if ($custom_field['type'] == 'select' || $custom_field['type'] == 'radio') {
						$custom_field_value_info = $this->model_sale_custom_field->getCustomFieldValue($work_order_info['payment_custom_field'][$custom_field['custom_field_id']]);

						if ($custom_field_value_info) {
							$data['payment_custom_fields'][] = array(
								'name'  => $custom_field['name'],
								'value' => $custom_field_value_info['name'],
								'sort_order' => $custom_field['sort_order']
							);
						}
					}

					if ($custom_field['type'] == 'checkbox' && is_array($work_order_info['payment_custom_field'][$custom_field['custom_field_id']])) {
						foreach ($work_order_info['payment_custom_field'][$custom_field['custom_field_id']] as $custom_field_value_id) {
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
							'value' => $work_order_info['payment_custom_field'][$custom_field['custom_field_id']],
							'sort_order' => $custom_field['sort_order']
						);
					}

					if ($custom_field['type'] == 'file') {
						$upload_info = $this->model_tool_upload->getUploadByCode($work_order_info['payment_custom_field'][$custom_field['custom_field_id']]);

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
			$data['shipping_firstname'] = $work_order_info['shipping_firstname'];
			$data['shipping_lastname'] = $work_order_info['shipping_lastname'];
			$data['shipping_company'] = $work_order_info['shipping_company'];
			$data['shipping_address_1'] = $work_order_info['shipping_address_1'];
			$data['shipping_address_2'] = $work_order_info['shipping_address_2'];
			$data['shipping_city'] = $work_order_info['shipping_city'];
			$data['shipping_postcode'] = $work_order_info['shipping_postcode'];
			$data['shipping_zone'] = $work_order_info['shipping_zone'];
			$data['shipping_zone_code'] = $work_order_info['shipping_zone_code'];
			$data['shipping_country'] = $work_order_info['shipping_country'];

			$data['shipping_custom_fields'] = array();

			foreach ($custom_fields as $custom_field) {
				if ($custom_field['location'] == 'address' && isset($work_order_info['shipping_custom_field'][$custom_field['custom_field_id']])) {
					if ($custom_field['type'] == 'select' || $custom_field['type'] == 'radio') {
						$custom_field_value_info = $this->model_sale_custom_field->getCustomFieldValue($work_order_info['shipping_custom_field'][$custom_field['custom_field_id']]);

						if ($custom_field_value_info) {
							$data['shipping_custom_fields'][] = array(
								'name'  => $custom_field['name'],
								'value' => $custom_field_value_info['name'],
								'sort_order' => $custom_field['sort_order']
							);
						}
					}

					if ($custom_field['type'] == 'checkbox' && is_array($work_order_info['shipping_custom_field'][$custom_field['custom_field_id']])) {
						foreach ($work_order_info['shipping_custom_field'][$custom_field['custom_field_id']] as $custom_field_value_id) {
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
							'value' => $work_order_info['shipping_custom_field'][$custom_field['custom_field_id']],
							'sort_order' => $custom_field['sort_order']
						);
					}

					if ($custom_field['type'] == 'file') {
						$upload_info = $this->model_tool_upload->getUploadByCode($work_order_info['shipping_custom_field'][$custom_field['custom_field_id']]);

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

			$products = $this->model_sale_order->getOrderProducts($data['order_id']);

			foreach ($products as $product) {
				$option_data = array();

				$options = $this->model_sale_order->getOrderOptions($data['order_id'], $product['order_product_id']);

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
					'price'    		   => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $work_order_info['currency_code'], $work_order_info['currency_value']),
					'total'    		   => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $work_order_info['currency_code'], $work_order_info['currency_value']),
					'href'     		   => $this->url->link('catalog/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $product['product_id'], 'SSL')
				);
			}

			$data['vouchers'] = array();

			$vouchers = $this->model_sale_order->getOrderVouchers($data['order_id']);

			foreach ($vouchers as $voucher) {
				$data['vouchers'][] = array(
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'], $work_order_info['currency_code'], $work_order_info['currency_value']),
					'href'        => $this->url->link('sale/voucher/edit', 'token=' . $this->session->data['token'] . '&voucher_id=' . $voucher['voucher_id'], 'SSL')
				);
			}

			$data['totals'] = array();

			$totals = $this->model_sale_order->getOrderTotals($data['order_id']);

			foreach ($totals as $total) {
				$data['totals'][] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'], $work_order_info['currency_code'], $work_order_info['currency_value']),
				);
			}

			$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

			$data['order_status_id'] = $work_order_info['order_status_id'];

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

			$data['payment_action'] = $this->load->controller('payment/' . $work_order_info['payment_code'] . '/action');

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

			$this->response->setOutput($this->load->view('transaction/work_order_info.tpl', $data));
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

	public function createWorkOrderNo() {
		$this->load->language('transaction/work_order');

		$json = array();

		if (!$this->user->hasPermission('modify', 'transaction/invoice')) {
			$json['error'] = $this->language->get('error_permission');
		} elseif (isset($this->request->get['order_id'])) {
			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			$this->load->model('sale/order');

			$work_order_no = $this->model_sale_order->createWorkOrderNo($order_id);

			if ($work_order_no) {
				$json['work_order_no'] = $work_order_no;
			} else {
				$json['error'] = $this->language->get('error_action');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function addReward() {
		$this->load->language('transaction/work_order');

		$json = array();

		if (!$this->user->hasPermission('modify', 'transaction/invoice')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			$this->load->model('sale/order');

			$order_info = $this->model_sale_order->getOrder($order_id);

			if ($order_info && $order_info['work_order_info_id'] && ($order_info['reward'] > 0)) {
				$this->load->model('sale/work_order_info');

				$reward_total = $this->model_sale_work_order_info->getTotalCustomerRewardsByWorkOrderId($order_id);

				if (!$reward_total) {
					$this->model_sale_work_order_info->addReward($order_info['work_order_info_id'], $this->language->get('text_order_id') . ' #' . $order_id, $order_info['reward'], $order_id);
				}
			}

			$json['success'] = $this->language->get('text_reward_added');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function removeReward() {
		$this->load->language('transaction/work_order');

		$json = array();

		if (!$this->user->hasPermission('modify', 'transaction/invoice')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			$this->load->model('sale/order');

			$order_info = $this->model_sale_order->getOrder($order_id);

			if ($order_info) {
				$this->load->model('sale/work_order_info');

				$this->model_sale_work_order_info->deleteReward($order_id);
			}

			$json['success'] = $this->language->get('text_reward_removed');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function addCommission() {
		$this->load->language('transaction/work_order');

		$json = array();

		if (!$this->user->hasPermission('modify', 'transaction/invoice')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			$this->load->model('sale/order');

			$order_info = $this->model_sale_order->getOrder($order_id);

			if ($order_info) {
				$this->load->model('marketing/affiliate');

				$affiliate_total = $this->model_marketing_affiliate->getTotalTransactionsByWorkOrderId($order_id);

				if (!$affiliate_total) {
					$this->model_marketing_affiliate->addTransaction($order_info['affiliate_id'], $this->language->get('text_order_id') . ' #' . $order_id, $order_info['commission'], $order_id);
				}
			}

			$json['success'] = $this->language->get('text_commission_added');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function removeCommission() {
		$this->load->language('transaction/work_order');

		$json = array();

		if (!$this->user->hasPermission('modify', 'transaction/invoice')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			$this->load->model('sale/order');

			$order_info = $this->model_sale_order->getOrder($order_id);

			if ($order_info) {
				$this->load->model('marketing/affiliate');

				$this->model_marketing_affiliate->deleteTransaction($order_id);
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
		$this->load->language('transaction/work_order');

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

		$results = $this->model_sale_order->getOrderHistories($this->request->get['order_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['histories'][] = array(
				'notify'     => $result['notify'] ? $this->language->get('text_yes') : $this->language->get('text_no'),
				'status'     => $result['status'],
				'comment'    => nl2br($result['comment']),
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$history_total = $this->model_sale_order->getTotalWorkOrderHistories($this->request->get['order_id']);

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('transaction/invoice/history', 'token=' . $this->session->data['token'] . '&order_id=' . $this->request->get['order_id'] . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

		$this->response->setOutput($this->load->view('transaction/work_order_history.tpl', $data));
	}

	public function invoice() {
		$data = $this->load->language('transaction/work_order');

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
		} elseif (isset($this->request->get['work_order_id'])) {
			$invoices[] = $this->request->get['work_order_id'];
		}
		
		$this->load->model('resource/transaction');
		$tModel = &$this->model_resource_transaction;
		
		$tService = new TransactionWorkOrder($this, 'OcWorkOrder');
		$tService->setEntityManager($this->em);
		$tModel->setTransactionType($tService);
		
		foreach ($invoices as $work_order_id) {
			$work_order_info = $this->model_resource_transaction->getTransaction($work_order_id);

			if ($work_order_info) {
				$store_info = $this->model_setting_setting->getSetting('config', $work_order_info['store_id']);

				if ($store_info) {
					$store_address = $store_info['config_address'];
					$store_email = $store_info['config_email'];
					$store_telephone = $store_info['config_telephone'];
					$store_fax = $store_info['config_fax'];
				} else {
					$store_address = $this->config->get('config_address');
					$store_email = $this->config->get('config_email');
					$store_telephone = $this->config->get('config_telephone');
					$store_fax = $this->config->get('config_fax');
				}

				if ($work_order_info['work_order_no']) {
					$work_order_no = $work_order_info['work_order_prefix'] . $work_order_info['work_order_no'];
				} else {
					$work_order_no = '';
				}
				
				//var_dump($work_order_info);
				/*if (isset($this->session->data['work_order_info'])) {
					$this->load->model('work_order_info/work_order_info');

					$work_order_info = $this->session->data['work_order_info']; // Recycle
					$work_order_info = array_merge($this->model_work_order_info_work_order_info->getCustomer($work_order_info['work_order_info_id']), $work_order_info);
				} else {
					$work_order_info = $this->model_resource_transaction->getCustomer($work_order_id);
				}
				
				// Add missing fields in invoice info
				if ($work_order_info) {
					$work_order_info['bill_addr_firstname'] = $work_order_info['firstname'];
					$work_order_info['bill_addr_lastname'] = $work_order_info['lastname'];
					//$work_order_info['bill_addr_fullname'] = $work_order_info['fullname'];
					
					$work_order_info['ship_addr_firstname'] = $work_order_info['firstname'];
					$work_order_info['ship_addr_lastname'] = $work_order_info['lastname'];
					//$work_order_info['ship_addr_fullname'] = $work_order_info['fullname'];
					
					$work_order_info['telephone'] =  $work_order_info['telephone']; // TODO: This is the current contact number, not the one at the time the invoice was created
				}*/
				
				$this->load->model('sale/customer'); // Load customer and customer addresses
				$customer = $this->model_resource_transaction->getCustomer($work_order_info['work_order_id']);
				if (!isset($customer_group_id)) {
					$customer['customer_group_id'] = 0; // TODO: Set a default value
				}
				
				$work_order_info['shipping_method'] = $work_order_info['shipping_method'];
				$work_order_info['shipping_code'] = $work_order_info['shipping_code'];

				//var_dump($work_order_info);
				if (isset($this->session->data['work_order_info'])) {
					$work_order_info = array_merge($work_order_info, $this->session->data['work_order_info']);
				}

				// Add missing fields in invoice info
				/*if ($work_order_info) {
					$work_order_info['bill_addr_firstname'] = $work_order_info['firstname'];
					$work_order_info['bill_addr_lastname'] = $work_order_info['lastname'];
					//$work_order_info['bill_addr_fullname'] = $work_order_info['fullname'];

					$work_order_info['ship_addr_firstname'] = $work_order_info['firstname'];
					$work_order_info['ship_addr_lastname'] = $work_order_info['lastname'];
					//$work_order_info['ship_addr_fullname'] = $work_order_info['fullname'];

					$work_order_info['telephone'] =  (isset($work_order_info['telephone'])) ? $work_order_info['telephone'] : ''; // TODO: This is the current contact number, not the one at the time the invoice was created
				}*/

				if (isset($work_order_info['payment_address_format'])) {
					$format = $work_order_info['payment_address_format'];
					$work_order_info['payment_address_format'] = $format;
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{city} {zone}' . "\n" . '{postcode} {country}';
					$work_order_info['payment_address_format'] = '{firstname} {lastname}\n{company}\n{address_1}\n{city} {zone}\n{postcode} {country}';
				}
				

				$work_order_info['payment_address'] = self::formatPaymentAddress($format, $work_order_info, false, true); // Final "true" sets output to HTML
				if (isset($work_order_info['shipping_address_format'])) {
					$format = $work_order_info['shipping_address_format'];
					$work_order_info['shipping_address_format'] = $format;
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{city} {zone}' . "\n" . '{postcode} {country}';
					$work_order_info['shipping_address_format'] = '{firstname} {lastname}\n{company}\n{address_1}\n{city} {zone}\n{postcode} {country}';
				}

			
				$work_order_info['shipping_address'] = self::formatShippingAddress($format, $work_order_info, false, true); // Final "true" sets output to HTML
				
				$work_order_info['order_id'] = $work_order_info['oc_entity_id'];

				$this->load->model('tool/upload');

				$product_data = array();

				//$lines = $this->model_resource_transaction->getLineItems($work_order_id);
				$lines = $this->load->controller('api/lines/lines', array('export' => false));
				$lines = array();
				
				// Need to set lines to session
				foreach ($lines as $line) {
					$options = array();
					if (isset($line['order_id'])) {
						$options = $this->model_sale_order->getOrderOptions($data['order_id'], $line['order_product_id']);
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
						//'option'     => (isset($line['order_id'])) ? $this->model_sale_order->getOrderOptions($data['order_id'], $line['order_product_id']) : null,
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

				$voucher_data = array();

				$vouchers = $this->model_sale_order->getOrderVouchers($work_order_id);

				foreach ($vouchers as $voucher) {
					$voucher_data[] = array(
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'], $work_order_info['currency_code'], $work_order_info['currency_value'])
					);
				}

				$total_data = array();

				/*$totals = $this->model_sale_order->getOrderTotals($work_order_id);

				foreach ($totals as $total) {
					$total_data[] = array(
						'title' => $total['title'],
						'text'  => $this->currency->format($total['value'], $work_order_info['currency_code'], $work_order_info['currency_value']),
					);
				}*/
				
				// Totals
				$this->load->model('extension/extension');

				$total_data = array();
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
						
						$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes, true);
					}
				}

				$sort_order = array();

				foreach ($total_data as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $total_data);

				$totals = array();

				foreach ($total_data as $total) {
					$totals[] = array(
						'title' => $total['title'],
						'text'  => $this->currency->format($total['value'])
					);
				}

				$data['invoices'][] = array(
					'order_id'	         => $work_order_id,
					'work_order_no'         => $work_order_no,
					'date_added'         => date($this->language->get('date_format_short'), strtotime($work_order_info['date_added'])),
					'store_name'         => $work_order_info['store_name'],
					'store_url'          => rtrim($work_order_info['store_url'], '/'),
					'store_address'      => nl2br($store_address),
					'store_email'        => $store_email,
					'store_telephone'    => $store_telephone,
					'store_fax'          => $store_fax,
					'email'              => $work_order_info['bill_email'],
					'telephone'          => $work_order_info['bill_telephone'],
					'shipping_address'   => $work_order_info['shipping_address'],
					'shipping_method'    => $work_order_info['shipping_method'],
					'payment_address'    => $work_order_info['payment_address'],
					'payment_method'     => $work_order_info['payment_method'],
					'lines'            	 => $lines,
					'voucher'            => $voucher_data,
					'total'              => $totals, //$total_data,
					'comment'            => '' // nl2br($work_order_info['comment'])
				);
			}
		}

		$this->response->setOutput($this->load->view('transaction/work_order_invoice.tpl', $data));
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
		$data = $this->load->language('transaction/work_order');

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
		} elseif (isset($this->request->get['work_order_id'])) {
			$invoices[] = $this->request->get['work_order_id'];
		}
		
		$this->load->model('resource/transaction');
		$tModel = &$this->model_resource_transaction;
		
		$tService = new TransactionWorkOrder($this, 'OcWorkOrder');
		$tService->setEntityManager($this->em);
		$tModel->setTransactionType($tService);
		
		foreach ($invoices as $work_order_id) {
			$work_order_info = $this->model_resource_transaction->getTransaction($work_order_id);

			if ($work_order_info) {
				$store_info = $this->model_setting_setting->getSetting('config', $work_order_info['store_id']);

				if ($store_info) {
					$store_address = $store_info['config_address'];
					$store_email = $store_info['config_email'];
					$store_telephone = $store_info['config_telephone'];
					$store_fax = $store_info['config_fax'];
				} else {
					$store_address = $this->config->get('config_address');
					$store_email = $this->config->get('config_email');
					$store_telephone = $this->config->get('config_telephone');
					$store_fax = $this->config->get('config_fax');
				}

				if ($work_order_info['work_order_no']) {
					$work_order_no = $work_order_info['work_order_prefix'] . $work_order_info['work_order_no'];
				} else {
					$work_order_no = '';
				}

				if (isset($work_order_info['payment_address_format'])) {
					$format = $work_order_info['payment_address_format'];
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}
				
				//var_dump($work_order_info);
				if (isset($this->session->data['work_order_info'])) {
					$this->load->model('work_order_info/work_order_info');

					$work_order_info = $this->session->data['work_order_info']; // Recycle
					$work_order_info = array_merge($this->model_work_order_info_work_order_info->getCustomer($work_order_info['work_order_info_id']), $work_order_info);
				} else {
					$work_order_info = $this->model_resource_transaction->getCustomer($work_order_id);
				}
				
				// Add missing fields in invoice info
				if ($work_order_info) {
					$work_order_info['bill_addr_firstname'] = $work_order_info['firstname'];
					$work_order_info['bill_addr_lastname'] = $work_order_info['lastname'];
					//$work_order_info['bill_addr_fullname'] = $work_order_info['fullname'];
					
					$work_order_info['ship_addr_firstname'] = $work_order_info['firstname'];
					$work_order_info['ship_addr_lastname'] = $work_order_info['lastname'];
					//$work_order_info['ship_addr_fullname'] = $work_order_info['fullname'];
					
					$work_order_info['telephone'] =  $work_order_info['telephone']; // TODO: This is the current contact number, not the one at the time the invoice was created
				}

				$payment_address = self::formatPaymentAddress($format, $work_order_info);

				if (isset($work_order_info['shipping_address_format'])) {
					$format = $work_order_info['shipping_address_format'];
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}

				$shipping_address = self::formatShippingAddress($format, $work_order_info);
				
				$data['order_id'] = $work_order_info['oc_entity_id'];

				$this->load->model('tool/upload');

				$product_data = array();

				//$lines = $this->model_resource_transaction->getLineItems($work_order_id);
				$lines = $this->load->controller('api/lines/lines', array(
					'export' => false, 
					'children' => array(
						'product'
					)
				));

				$voucher_data = array();

				$vouchers = $this->model_sale_order->getOrderVouchers($work_order_id);

				foreach ($vouchers as $voucher) {
					$voucher_data[] = array(
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'], $work_order_info['currency_code'], $work_order_info['currency_value'])
					);
				}

				$total_data = array();

				/*$totals = $this->model_sale_order->getOrderTotals($work_order_id);

				foreach ($totals as $total) {
					$total_data[] = array(
						'title' => $total['title'],
						'text'  => $this->currency->format($total['value'], $work_order_info['currency_code'], $work_order_info['currency_value']),
					);
				}*/
				
				// Totals
				$this->load->model('extension/extension');

				$total_data = array();
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
						
						$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes, true);
					}
				}

				$sort_order = array();

				foreach ($total_data as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $total_data);

				$totals = array();

				foreach ($total_data as $total) {
					$totals[] = array(
						'title' => $total['title'],
						'text'  => $this->currency->format($total['value'])
					);
				}
				
				$data['invoices'][] = array(
					'order_id'	         => $work_order_id,
					'work_order_no'         => $work_order_no,
					'date_added'         => date($this->language->get('date_format_short'), strtotime($work_order_info['date_added'])),
					'store_name'         => $work_order_info['store_name'],
					'store_url'          => rtrim($work_order_info['store_url'], '/'),
					'store_address'      => nl2br($store_address),
					'store_email'        => $store_email,
					'store_telephone'    => $store_telephone,
					'store_fax'          => $store_fax,
					'email'              => $work_order_info['bill_email'],
					'telephone'          => $work_order_info['telephone'],
					'shipping_address'   => $shipping_address,
					'shipping_method'    => $work_order_info['shipping_method'],
					'payment_address'    => $payment_address,
					'payment_method'     => $work_order_info['payment_method'],
					'lines'            	 => $lines['lines'], // We don't need vouchers or totals
					'voucher'            => $voucher_data,
					'total'              => $totals, //$total_data,
					'comment'            => '' // nl2br($work_order_info['comment'])
				);
			}
		}

		$this->response->setOutput($this->load->view('transaction/work_order_shipping.tpl', $data));
	}

	public function api() {
		$this->load->language('transaction/work_order');

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
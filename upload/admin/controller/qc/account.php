<?php
require_once(DIR_QC . 'vendor/autoload.php');
require_once(DIR_SYSTEM . 'engine/qccontroller.php');
require_once(DIR_SYSTEM . 'library/quickcommerce/entity_manager.php');
require_once(DIR_SYSTEM . 'library/quickcommerce/doctrine.php');
require_once(DIR_SYSTEM . 'library/quickcommerce/resource.php');

use Doctrine\Common\Util\Inflector;
use Doctrine\Common\Util\Debug;

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Reader\ArrayReader;
use Ddeboer\DataImport\Reader\OneToManyReader;
use Ddeboer\DataImport\Writer\ArrayWriter;
use Ddeboer\DataImport\Writer\CallbackWriter;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Ddeboer\DataImport\ItemConverter\MappingItemConverter;
use Ddeboer\DataImport\ItemConverter\NestedMappingItemConverter;
use Ddeboer\DataImport\ValueConverter\DateTimeValueConverter;

class ControllerQCAccount extends QCController {
	// TODO: Some of these properties should be moved to the model and accessed in controller
	// I've just copied and pasted them into the model(s) for now..
	protected $tableName = 'qcli_account';
	protected $joinTableName = 'order';
	protected $joinCol = 'account_id';
	protected $foreign = 'Account';
	protected $foreignType = 'account';

	protected $error = array();

	function __construct($registry) {
		parent::__construct($registry);
		parent::before();
	}

	// TODO: I can make a generic one of these, just copying for now...
	protected function getService() {}

	public function index() {
		$this->load->language('quickcommerce/account');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/extension');

		$this->load->model('extension/event');
		
		$this->load->model('setting/store');

		$this->load->model('resource/namelist');
		$rModel = &$this->model_resource_namelist;

		$rService = new NameListAccount($this, 'OcAccount');
		$rService->setEntityManager($this->em);
		$rModel->setResourceType($rService);

		$this->document->addScript('view/javascript/quickcommerce/qc_account.js');

		$this->getList();
	}
	
	public function getList() {
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/event', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_layout'] = sprintf($this->language->get('text_layout'), $this->url->link('design/layout', 'token=' . $this->session->data['token'], 'SSL'));
		$data['text_list'] = $this->language->get('text_list');
		$data['text_form'] = $this->language->get('text_form');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		
		$data['entry_store'] = $this->language->get('entry_store');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_enable'] = $this->language->get('button_enable');
		$data['button_disable'] = $this->language->get('button_disable');
		$data['button_add'] = $this->language->get('button_add');
		$data['button_remove'] = $this->language->get('button_remove');
		$data['button_install'] = $this->language->get('button_install');
		$data['button_uninstall'] = $this->language->get('button_uninstall');

		$data['token'] = $this->session->data['token'];
		
		$data['action'] = $this->url->link('extension/event/edit', 'token=' . $this->session->data['token'], 'SSL');

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

		$data['delete'] = $this->url->link('extension/event/delete', 'token=' . $this->session->data['token'], 'SSL');		
		
		$data['stores'] = $this->model_setting_store->getStores();

		$total = $this->model_resource_namelist->getTotalResources();
		$filter_data = array();
		$results = $this->model_resource_namelist->getResources($filter_data);

		// Not sure if this is the right model to be dumping everything... should really just call the appropriate service
		//$data['invoice_statuses'] = $this->model_resource_transaction->getInvoiceStatuses();
		$url = '';

		if ($results != null) {
			foreach ($results as $result) {
				$actions =  array(
					//'view'          => $this->url->link('transaction/invoice/invoice', 'token=' . $this->session->data['token'] . '&invoice_id=' . $result['invoice_id'] . $url, 'SSL'),
					//'edit'          => $this->url->link('transaction/invoice/edit', 'token=' . $this->session->data['token'] . '&invoice_id=' . $result['invoice_id'] . $url, 'SSL'),
					//'delete'        => $this->url->link('transaction/invoice/delete', 'token=' . $this->session->data['token'] . '&invoice_id=' . $result['invoice_id'] . $url, 'SSL')
				);

				if (!isset($result['account_num'])) {
					$result['account_num'] = '';
				}

				$data['accounts'][] = array_merge($result, $actions);
			}
		}

		$page = 1;

		$pagination = new Pagination();
		$pagination->total = $total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('qc/account', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($total - $this->config->get('config_limit_admin'))) ? $total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $total, ceil($total / $this->config->get('config_limit_admin')));

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('quickcommerce/account.tpl', $data)); // Only list view
	}

	// Left in for reference
	/*public function getStockStatus($status_id = null) {
		$this->load->model('localisation/stock_status');

		$statuses = $this->model_localisation_stock_status->getStockStatuses();

		foreach ($statuses as $status) {
			if ($status['stock_status_id'] == $status_id)
				return $status;
		}
	}

	public function getStockStatusByName($name = '') {
		$this->load->model('localisation/stock_status');

		$statuses = $this->model_localisation_stock_status->getStockStatuses();

		foreach ($statuses as $status) {
			if ($status['name'] == $name)
				return $status;
		}
	}

	public function getStockStatuses($stockStatusId) {
		$this->load->model('localisation/stock_status');
		return $this->model_localisation_stock_status->getStockStatuses();
	}*/

	// Working on it...
	// This is alright for base OpenCart install
	// OC doesn't like nulls... need to set default vals somehow

	private function loadMetadata() {
		$this->aMeta = $this->em->getClassMetadata('OcAccount');
	}

	/**
	 * This is a good simple fetch for a single level entity
	 * I want this to be relatively copy and paste, so I can make a generic version later
	 */
	public function fetch() {
		$this->loadMetadata();
		// getMappings is used to map remote entities unlike mapDoctrineEntity and its wrappers which simply convert between OpenCart/OpenCart DB fields and their Doctrine entity equivalents
		$mappings = $this->getMappings($this->foreign); // Get the remote item mappings

		$a = null;
		$data = array();

		$service = new \App\Resource\Account($this->em, 'OcAccount');
		//$items = $this->getCollection();

		$importItem = function (&$item, &$data) {
			$mappings = $this->getMappings($this->foreign); // They're already loaded, just getting the reference
			$a = array();
			self::importEntity($item, $mappings, $this->aMeta, $a);
			$data[] = $a;
		};

		$this->iterateCollection($importItem, $data);
		// TODO: Would be better if I didn't have to deal with huge arrays of data... maybe I can process on the fly?

		// Parent refs on the OC side need to be populated after all the products are imported
		// We can't do this in the same loop because we don't know what order the items are coming in from QuickBooks
		// Store the ids in an array for processing later

		$importedIds = array();

		//$store = $sService->getEntity(1, false);
		//$stock_status = $this->getStockStatusByName('In Stock');

		$reader = new ArrayReader($data);
		$writer = new CallbackWriter(
			function ($item) use (&$importedIds, &$a, &$service) {
				try {
					// Product should be tested for a unique email address - we don't want duplicates in OpenCart
					// That might not be the case in QuickBooks?
					$exists = false; //$this->exists('product', 'qbname', $item['qbname']); // TODO: Method defaults maybe?

					if (!$exists) {
						$date = new DateTime();

						if (isset($item['status'])) {
							$item['status'] = (strtolower($item['status']) === 'true');
						}

						$a = $service->writeItem($item, true);
						//$a->setDateAdded($date);
						//$a->setDateModified($date);

						$service->updateEntity($a);

						//$this->db->query("UPDATE " .  DB_PREFIX . "customer SET store_id = '0' WHERE store_id IS NULL"); // MySQL-only fix for "0" store id - no idea how to work around this in Doctrine
						//$this->db->query("UPDATE " .  DB_PREFIX . "customer SET address_id = '0' WHERE address_id IS NULL"); // MySQL-only fix for "0" store id - no idea how to work around this in Doctrine

						$item['_entity']->setOcId($a->getAccountId());
						$this->_writeListItem($item['_entity']);

						$id = $a->getAccountId();
						//$qbId = self::qbId($item['_entity']->getId());
						$parentRefId = self::qbId($item['_entity']->getParentRef());

						if ($parentRefId && $parentRefId > 0) {
							$importedIds[$id] = $parentRefId;
						}

					} else {
						// If the customer exists, maybe we can do an update instead, if the QBO record is more current than the OC record
						// Just ignore for now
					}

				} catch (Exception $e) {
					throw $e;
				}
			});

		$workflow = new Workflow($reader);
		//self::addDateConverters($workflow); // TODO: Where is this method?
		$workflow->addWriter($writer);
		$workflow->process();

		// OK
		foreach ($importedIds as $id => $parentRefId) {
			// Update the parent reference
			$entity = $service->getEntity($id, false);

			$sql = "SELECT oc_entity_id FROM " . DB_PREFIX . $this->tableName . " WHERE feed_id = '" . $parentRefId . "'";

			// Fast nasty and easy, should do this with Doctrine later though, or find a way to decorate the entity with the link table stuff
			$query = $this->db->query($sql);
			$parentId = $query->row['oc_entity_id'];

			if ($parentId) {
				$entity->setParentId($parentId);
				$service->updateEntity($entity);
			}
		}
	}

	/**
	 * return QuickBooks_IPP_Object_Account
	 */
	public function get($id = 4, $data = array()) {
		$itemService = new QuickBooks_IPP_Service_Account();

		// Get the existing item 
		$items = $itemService->query($this->Context, $this->realm, "SELECT * FROM Account WHERE Id = '" . $id . "'");
		$item = ($items && count($items) > 0) ? $items[0] : null;

		return $item;
	}
	
	// TODO: IMPORTANT!!!
	// It's time to embed the Slim PHP, we can use it as a service gateway
	public function getAccounts() {
		$filter_data = null;

		$this->load->model('resource/namelist');
		$rModel = &$this->model_resource_namelist;

		$rService = new NameListAccount($this, 'OcAccount');
		$rService->setEntityManager($this->em);
		$rModel->setResourceType($rService);

		$results = $this->model_resource_namelist->getResources($filter_data); // Get resources currently does not accept any parameters
		
		$this->response->addHeader('Content-type: application/json');
		return $this->response->setOutput(json_encode($results)); // Only list view
	}

	/**
	 * return array[QuickBooks_IPP_Object_Account]
	 */
	public function dump() {
		$itemService = new QuickBooks_IPP_Service_Account();

		$items = $itemService->query($this->Context, $this->realm, "SELECT * FROM Account ORDER BY Metadata.LastUpdatedTime");

		/*foreach ($items as $item) {
			print('Account Id=' . $item->getId() . ' is named: ' . $item->getName() . '<br>');
		}*/

		var_dump($items);

		return $items;
	}

	// Do not delete -- will be moved to unit tests
	// TODO: Or called from the QC module admin in test section
	public function convertOrder() {
		// Create a blank account transaction
		$this->load->model('resource/transaction');
		$tModel = &$this->model_resource_transaction;
		// TransactionAccount extends TransactionBase(Controller $context, $id = null)
		$tModel->setTransactionType(new TransactionAccount($this));

		// Get the OpenCart model and con
		$tModel->convert($this->request->get['order_id']);

		$this->response->redirect($this->url->link('transaction/account', 'token=' . $this->session->data['token'] . '&order_id=' . $this->request->get['order_id'], 'SSL'));
	}

	/**
	 * @param $accountId
	 */
	public function add($accountId = 0) {
		$mappings = [];
		$export = false; // Saves a step later

		EntityMapper::mapEntities($this->em, 'Account', $this->mapXml, $mappings, $export);
		EntityMapper::mapEntities($this->em, 'Customer', $this->mapXml, $mappings, $export);
		EntityMapper::mapEntities($this->em, 'Address', $this->mapXml, $mappings, $export);
		EntityMapper::mapEntities($this->em, 'Line', $this->mapXml, $mappings, $export);

		//var_dump($mappings);

		$this->load->model('resource/transaction');
		$tModel = &$this->model_resource_transaction;

		$tModel->setTransactionType(new TransactionAccount($this));

		$tModel->getTransaction($accountId);

		$this->load->model('sale/order');
		$this->load->model('customer/customer');
		$this->load->model('customer/customer_group');

		// These should already be converted
		// Order to account...
		$data = $this->model_sale_order->getOrder($accountId);
		$products = $this->model_sale_order->getOrderProducts($data['order_id']);
		$customer = $this->model_customer_customer->getCustomer($data['customer_id']);
		$group = $this->model_customer_customer_group->getCustomerGroup($data['customer_group_id']);

		/* Order model methods
		getOrder($order_id)
		getOrders($data = array())
		getOrderProducts($order_id)
		getOrderOption($order_id, $order_option_id)
		getOrderOptions($order_id, $order_product_id)
		getOrderVouchers($order_id)
		getOrderVoucherByVoucherId($voucher_id)
		getOrderTotals($order_id)
		getTotalOrders($data = array())
		getTotalOrdersByStoreId($store_id)
		getTotalOrdersByProcessingStatus()
		getTotalOrdersByCompleteStatus()
		getTotalOrdersByLanguageId($language_id)
		getTotalOrdersByCurrencyId($currency_id)
		createAccountNo($order_id)
		getOrderHistories($order_id, $start = 0, $limit = 10)
		getTotalOrderHistories($order_id)
		getTotalOrderHistoriesByOrderStatusId($order_status_id)
		getEmailsByProductsOrdered($products, $start, $end)
		getTotalEmailsByProductsOrdered($products)*/


		$entityService = new QuickBooks_IPP_Service_Account();
		$entity = new QuickBooks_IPP_Object_Account();
		$entity->setOcId($accountId);
		$meta = $this->em->getClassMetadata('OcAccount');
		//$odMeta = $this->em->getClassMetadata('OcAccountDescription');

		// TODO: Ideally I should be able to specify 'products' => $products... but the doctrine mapping isn't set up-to-date
		// I need to mod the entity generator to auto-generate class methods so until then we're gonna work around by loading individual entities
		$o = ObjectFactory::createEntity($this->em, 'OcAccount', $data, array('customer' => $customer, 'customerGroup' => $group));

		// TODO: Regarding above, I may be able to reuse this code block for something? LoadCollection helper or something? 
		// I could change how loadAssoc works, but I should probably wait until I get code generation smoothed out
		$opMeta = $this->em->getClassMetadata('OcAccountLine');
		$ooMeta = $this->em->getClassMetadata('OcOrderOption'); // TODO: Check if there's a quantity?
		// Strategy!

		//var_dump($mappings);

		$ln = 1;
		foreach ($products as $product) {
			$op = ObjectFactory::createEntity($this->em, 'OcAccountLine', $product);
			$o['products'][] = &$op; // For now, this is super easy, and this is basically all we need to do anyway

			$lEntity = new QuickBooks_IPP_Object_Line();
			$this->fillEntity($lEntity, $mappings['Line']['fields'], $opMeta, $op); // Populate entity data
			$this->fillEntityObjects('Line', $lEntity, $mappings, $opMeta, $op);

			//var_dump($mappings['Line']);
			//var_dump($lEntity);
			//exit;

			$lEntity->setLineNum($ln);
			$lEntity->setDetailType('SalesItemLineDetail');
			$entity->addLine($lEntity);

			$ln++;

			var_dump($o);

			$options = $this->model_sale_order->getOrderOptions($o['order_id'], $op['order_product_id']);
			if (count($options) > 0) {
				foreach ($options as $option) {
					//var_dump($option);
					$oo = ObjectFactory::createEntity($this->em, 'OcOrderOption', $option);
					$o['options'][] = &$oo; // For now, this is super easy, and this is basically all we need to do anyway

					$lEntity = new QuickBooks_IPP_Object_Line();
					$this->fillEntity($lEntity, $mappings['Line']['fields'], $ooMeta, $oo); // Populate entity data
					$this->fillEntityObjects('Line', $lEntity, $mappings, $ooMeta, $oo);

					$lEntity->setLineNum($ln);
					$lEntity->setDetailType('DescriptionOnly');
					$entity->addLine($lEntity);

					$ln++;
				}

			}

			//var_dump($o);
			//exit;
		}

		//var_dump($o['products']);
		//var_dump($mappings);
		//exit;

		//$od = ObjectFactory::createEntity($this->em, 'OcAccountDescription', $data);
		//$od['description'] = (array_key_exists('description', $od)) ? strip_tags(html_entity_decode(trim($od['description']))) : '';

		// TODO: How am I going to manage conditional mappings?
		//if ($o['taxClass']['title'] == 'Taxable Goods') $entity->setTaxable(true);
		//if ($o['stockStatus']['name'] == 'In Stock');
		//$entity->setType('Inventory');
		//$entity->setTrackQtyOnHand(true); // OpenCart accounts all have quantities

		$this->fillEntity($entity, $mappings['Account']['fields'], $meta, $o); // Populate entity data
		//$this->fillEntity($entity, $mappings['Account']['fields'], $odMeta, $od); // Populate entity data
		$this->fillEntityObjects('Account', $entity, $mappings, $meta, $o);
		$this->fillEntityRefs($entity, $mappings['Account']['refs'], $o);

		var_dump($o);
		$entity->setTxnDate('2013-10-11');
		//$entity->setCustomerRef('67');

		//$entity->setIncomeAccountRef('157'); // These are sandbox values!
		//$entity->setAssetAccountRef('159'); // These are sandbox values!
		//$entity->setExpenseAccountRef('158'); // These are sandbox values!

		//$entity->setCustomerRef($o['customer']['customer_id']);

		//$entity->setInvStartDate(date('Y-m-d', strtotime($p['date_added']))); // Quick fix to strip time stuff out which is preventing QBO from saving as Inventory entity

		// TODO: Extend services with export func.
		// I've isolated the code using static helpers right now
		// so it should be pretty easy to move around later
		$this->export($entityService, $entity, true);
	}

	/**
	 * @param int $accountId
	 * @param array $data
	 */
	public function edit($accountId = 0, $data = array()) {
		$mappings = [];
		$entities = false;
		$export = false; // Saves a step later

		EntityMapper::mapEntities($this->em, 'Account', $this->mapXml, $mappings, $export);
		EntityMapper::mapEntities($this->em, 'Customer', $this->mapXml, $mappings, $export);
		EntityMapper::mapEntities($this->em, 'Address', $this->mapXml, $mappings, $export);
		EntityMapper::mapEntities($this->em, 'Line', $this->mapXml, $mappings, $export);

		//$tMeta = $this->em->getClassMetadata('OcTransaction');
		//$iMeta = $this->em->getClassMetadata('OcAccount');

		$this->load->model('resource/transaction');
		$tModel = &$this->model_resource_transaction;

		$tModel->setTransactionType(new TransactionAccount($this));


		// Load OpenCart models and fetch any required data
		// TODO: When Doctrine integration is complete this won't be necessary
		$this->load->model('sale/order');
		$this->load->model('localisation/tax_class');
		$this->load->model('localisation/stock_status');
		$data = $this->model_sale_order->getAccount($accountId); // TODO: Did I mod the model for this?
		$tax = $this->model_localisation_tax_class->getTaxClass($data['tax_class_id']);
		$stock = $this->model_localisation_stock_status->getStockStatus($data['stock_status_id']);
		// Data loaded

		// Just leaving this in as an example of some extra stuff I could do with this
		// Returns properties of current node not including... ./*[not(name()=\'Network\')]
		//$filtered = EntityMapper::filterEntities($xml, '*[name() = "Account"]');

		//$data = XML2Array::createArray($filtered); // Just filters crap out
		//$data = (!empty($data['entities'])) ? $data['entities']['Account'] : array();

		// Create the service client
		$entityService = new QuickBooks_IPP_Service_Item();

		// Get the feed (remote) ID
		if (!is_int($feedId)) {
			// If this is a push operation, $feedId should be provided and there's no need for an extra query
			// If this is an atomic update operation, we're going to need to grab the corresponding feed id
			$feedId = $this->getFeedId($productId);
		}

		// Get the entity from the remote service
		if (is_int($feedId)) {
			// Get the entity from the service 
			$entities = $entityService->query($this->Context, $this->realm, "SELECT * FROM Account WHERE Id = '" . $feedId . "'");
		}

		if ($entities != false && count($entities) > 0) {
			$entity = $entities[0]; // Get the first item
			$entity->setOcId($accountId); // Set the corresponding OpenCart "entity" ID

			// Create a blank OpenCart entity using Doctrine
			$pMeta = $this->em->getClassMetadata('OcAccount'); // Get the Doctrine class metadata
			$pdMeta = $this->em->getClassMetadata('OcAccountDescription');

			// Returns an array representation of the OpenCart entity using Doctrine metadata, populated with any provided data
			$o = ObjectFactory::createEntity($this->em, 'OcOrder', $data, array('customer' => $customer, 'customerGroup' => $group));

			// TODO: Regarding above, I may be able to reuse this code block for something? LoadCollection helper or something? 
			// I could change how loadAssoc works, but I should probably wait until I get code generation smoothed out
			$opMeta = $this->em->getClassMetadata('OcOrderProduct');
			$ooMeta = $this->em->getClassMetadata('OcOrderOption'); // TODO: Check if there's a quantity?
			// Strategy!

			//var_dump($mappings);

			$ln = 1;
			foreach ($products as $product) {
				// Returns an array representation of the OpenCart entity using Doctrine metadata, populated with any provided data
				$op = ObjectFactory::createEntity($this->em, 'OcOrderProduct', $product);
				$o['products'][] = &$op; // For now, this is super easy, and this is basically all we need to do anyway

				// Populate the returned entity with OpenCart data using the appropriate mapping
				$lEntity = new QuickBooks_IPP_Object_Line();
				$this->fillEntity($lEntity, $mappings['Line']['fields'], $opMeta, $op); // Populate entity data
				$this->fillEntityObjects('Line', $lEntity, $mappings, $opMeta, $op);

				//var_dump($mappings['Line']);
				//var_dump($lEntity);
				//exit;

				$lEntity->setLineNum($ln);
				$lEntity->setDetailType('SalesItemLineDetail');
				$entity->addLine($lEntity);

				$ln++;

				$options = $this->model_sale_order->getOrderOptions($o['order_id'], $op['order_product_id']);
				if (count($options) > 0) {
					foreach ($options as $option) {
						//var_dump($option);
						$oo = ObjectFactory::createEntity($this->em, 'OcOrderOption', $option);
						$o['options'][] = &$oo; // For now, this is super easy, and this is basically all we need to do anyway

						$lEntity = new QuickBooks_IPP_Object_Line();
						$this->fillEntity($lEntity, $mappings['Line']['fields'], $ooMeta, $oo); // Populate entity data
						$this->fillEntityObjects('Line', $lEntity, $mappings, $ooMeta, $oo);

						$lEntity->setLineNum($ln);
						$lEntity->setDetailType('DescriptionOnly');
						$entity->addLine($lEntity);

						$ln++;
					}

				}

				//var_dump($o);
				//exit;
			}

			//var_dump($o['products']);
			//var_dump($mappings);
			//exit;

			//$od = ObjectFactory::createEntity($this->em, 'OcAccountDescription', $data);
			//$od['description'] = (array_key_exists('description', $od)) ? strip_tags(html_entity_decode(trim($od['description']))) : '';

			// TODO: How am I going to manage conditional mappings?
			//if ($o['taxClass']['title'] == 'Taxable Goods') $entity->setTaxable(true);
			//if ($o['stockStatus']['name'] == 'In Stock');
			//$entity->setType('Inventory');
			//$entity->setTrackQtyOnHand(true); // OpenCart accounts all have quantities

			// Populate the returned entity with OpenCart data using the appropriate mapping
			$this->fillEntity($entity, $mappings['Account']['fields'], $meta, $o); // Populate entity data
			//$this->fillEntity($entity, $mappings['Account']['fields'], $odMeta, $od); // Populate entity data
			$this->fillEntityObjects('Account', $entity, $mappings, $meta, $o);

			$entity->setDocNumber('WEB' . mt_rand(0, 10000));
			$entity->setTxnDate('2013-10-11');
			$entity->setCustomerRef('67');

			$this->export($entityService, $entity, true);
		} else {
			// If the account was deleted in QBO re-add it
			$this->add($accountId);
		}
	}

	private static function explodeSubAccount($item, $fullyQualified = false) {
		if (!$fullyQualified) {
			$item = explode(':', trim($item));
			return array_pop($item);
		}

		return $item;
	}

	/**
	 * Proxy method allows for stronger type hinting
	 */
	protected function export (QuickBooks_IPP_Service_Account &$service, QuickBooks_IPP_Object_Account &$item, $asXml = false) {
		$this->_export($service, $item, $asXml);
	}

	/**
	 * Event hook triggered before adding a account
	 */
	public function eventBeforeAddAccount($accountId) {

	}

	/**
	 * Event hook triggered after adding a account
	 */
	public function eventAfterAddAccount($accountId) {
		// Post account to QBO
		$this->add($accountId);
	}

	/**
	 * Event hook triggered before editing a account
	 */
	public function eventBeforeEditAccount() {

	}

	/**
	 * Event hook triggered after editing a account
	 */
	public function eventAfterEditAccount($accountId) {
		// Post changes to QBO
		$this->edit($accountId);
	}

	/*public function eventOnDeleteAccount() {
		
	}*/
}
<?php
require_once(DIR_SYSTEM . 'engine/qccontroller.php');
require_once(DIR_SYSTEM . 'library/quickcommerce/entity_manager.php');

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Reader\ArrayReader;
use Ddeboer\DataImport\Reader\OneToManyReader;
use Ddeboer\DataImport\Writer\ArrayWriter;
use Ddeboer\DataImport\Writer\CallbackWriter;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Ddeboer\DataImport\ItemConverter\MappingItemConverter;
use Ddeboer\DataImport\ItemConverter\NestedMappingItemConverter;
use Ddeboer\DataImport\ValueConverter\DateTimeValueConverter;

class ControllerQCTaxClass extends QCController {
	protected $tableName = 'qcli_tax_code';
	protected $joinTableName = 'tax_class';
	protected $joinCol = 'tax_class_id';
	protected $foreign = 'TaxCode';

	function __construct($registry) {
		parent::__construct($registry);
		parent::before();
	}

	public function getTaxClass($taxClassId = null) {
		$this->load->model('localisation/tax_class');

		$classes = $this->model_localisation_tax_class->getTaxClasses();

		foreach ($classes as $class) {
			if ($class['tax_class_id'] == $taxClassId)
				return $class;
		}
	}

	public function getTaxClassByName($name = '') {
		$this->load->model('localisation/tax_class');

		$classes = $this->model_localisation_tax_class->getTaxClasses();

		foreach ($classes as $class) {
			if ($class['title'] == $name)
				return $class;
		}
	}

	public function getTaxStatuses($taxClassId)
	{
		$this->load->model('localisation/tax_class');
		return $this->model_localisation_tax_class->getTaxClasses();
	}

	public function fetch($taxClassId = 0) {
		$this->loadMetadata();
		// getMappings is used to map remote entities unlike mapDoctrineEntity and its wrappers which simply convert between OpenCart/OpenCart DB fields and their Doctrine entity equivalents
		$mappings = $this->getMappings($this->foreign); // Get the remote item mappings

		$tc = null;
		$data = array();

		$tcService = new \App\Resource\TaxClass($this->em, 'OcTaxClass');
		//$trService = new \App\Resource\TaxRate($this->em, 'OcTaxRate');

		//$items = $this->getCollection();

		$importItem = function (&$item, &$data) {
			$mappings = $this->getMappings($this->foreign); // They're already loaded, just getting the reference
			$tc = array();
			self::importEntity($item, $mappings, $this->tcMeta, $tc);
			$data[] = $tc;
		};

		$this->iterateCollection($importItem, $data);
		// TODO: Would be better if I didn't have to deal with huge arrays of data... maybe I can process on the fly?

		// Parent refs on the OC side need to be populated after all the products are imported
		// We can't do this in the same loop because we don't know what order the items are coming in from QuickBooks
		// Store the ids in an array for processing later

		$importedIds = array();

		$reader = new ArrayReader($data);
		$writer = new CallbackWriter(
			function ($item) use (&$importedIds, &$tc, &$tcService, &$trService) {
				try {
					// Product should be tested for a unique email address - we don't want duplicates in OpenCart
					// That might not be the case in QuickBooks?
					$exists = $this->exists('tax_class', 'title', $item['title']); // TODO: Method defaults maybe?
					
					if (!$exists) {
						$date = new DateTime();
						$tc = $tcService->writeItem($item);
						//$tc->setDateAvailable($date); // TODO: Wtf to do with this? What is OC default? Can I null?
						$tc->setDateAdded($date);
						$tc->setDateModified($date);
						//$tc->setTaxRate(); // Cannot be null

						$tcService->updateEntity($tc);

						$item['_entity']->setOcId($tc->getTaxClassId());
						$this->_writeListItem($item['_entity']);

						$id = $tc->getTaxClassId();
						var_dump($id);
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
		/*foreach ($importedIds as $id => $parentRefId) {
			// Update the parent reference
			$entity = $pService->getEntity($id, false);

			$sql = "SELECT oc_entity_id FROM " . DB_PREFIX . $this->tableName . " WHERE feed_id = '" . $parentRefId . "'";

			// Fast nasty and easy, should do this with Doctrine later though, or find a way to decorate the entity with the link table stuff
			$query = $this->db->query($sql);
			$parentId = $query->row['oc_entity_id'];

			if ($parentId) {
				$entity->setParentId($parentId);
				$pService->updateEntity($entity);
			}
		}*/
	}

	// Working on it...
	// This is alright for base OpenCart install
	// OC doesn't like nulls... need to set default vals somehow

	private function loadMetadata() {
		$this->tcMeta = $this->em->getClassMetadata('OcTaxClass');
		$this->trMeta = $this->em->getClassMetadata('OcTaxRate');
	}

	public function sync() {
		if ($this->doSync()) {
			echo 'yes, we do the sync';
		}
	}

	public function test() {
		$obj = ObjectFactory::createObject($this->em, 'MetaData', array());
		//var_dump($obj->getLastUpdatedTime());
	}

	/**
	 * Event hook triggered before adding a product
	 */
	public function eventBeforeAddProduct($taxClassId) {

	}

	/**
	 * Event hook triggered after adding a product
	 */
	public function eventAfterAddProduct($taxClassId) {
		// Post product to QBO
		$this->add($taxClassId);
	}

	/**
	 * @param $taxClassId
	 */
	public function add($taxClassId) {
		$this->getMappings($this->foreign);
		$mappings = $this->mappings;

		$this->load->model('catalog/product');
		$this->load->model('localisation/tax_class');
		$this->load->model('localisation/stock_status');
		$data = $this->model_catalog_product->getProduct($taxClassId);
		$tax = $this->model_localisation_tax_class->getTaxClass($data['tax_class_id']);
		$stock = $this->model_localisation_stock_status->getStockStatus($data['stock_status_id']);

		$entityService = new QuickBooks_IPP_Service_Item();
		$entity = new QuickBooks_IPP_Object_Item();
		$entity->setOcId($taxClassId);
		$pMeta = $this->em->getClassMetadata('OcProduct');
		$pdMeta = $this->em->getClassMetadata('OcProductDescription');

		$p = ObjectFactory::createEntity($this->em, 'OcProduct', $data, array('taxClass' => $tax, 'stockStatus' => $stock));
		$pd = ObjectFactory::createEntity($this->em, 'OcProductDescription', $data);
		$pd['description'] = (array_key_exists('description', $pd)) ? strip_tags(html_entity_decode(trim($pd['description']))) : '';

		// TODO: How am I going to manage conditional mappings?
		if ($p['taxClass']['title'] == 'Taxable Goods') $entity->setTaxable(true);
		//if ($p['stockStatus']['name'] == 'In Stock');
		$entity->setType('Inventory');
		$entity->setTrackQtyOnHand(true); // OpenCart products all have quantities

		$this->fillEntity($entity, $mappings['Item']['fields'], $pMeta, $p); // Populate entity data
		$this->fillEntity($entity, $mappings['Item']['fields'], $pdMeta, $pd); // Populate entity data
		$this->fillEntityRefs($entity, $mappings['Item']['refs'], $p); // Populate entity data

		// TODO: This is an improvement over the previous hardcoding, but there still isn't anything in the QC module admin that allows you to configure which attributes correlate to accounts
		$incomeAcct = $this->model_catalog_product->getProductAttributeByName($taxClassId, 'Accounting', 'Income Account'); // TODO: Must be "Sales of Product Income" account
		$expenseAcct = $this->model_catalog_product->getProductAttributeByName($taxClassId, 'Accounting', 'COGS Account'); // TODO: Must be "Cost of Goods Sold" account
		$assetAcct = $this->model_catalog_product->getProductAttributeByName($taxClassId, 'Accounting', 'Asset Account'); // TODO: Yeah.... drawing a blank here

		// TODO: Validate!
		$entity->setIncomeAccountRef($incomeAcct['text']);
		$entity->setExpenseAccountRef($expenseAcct['text']);
		$entity->setAssetAccountRef($assetAcct['text']);

		$entity->setInvStartDate(date('Y-m-d', strtotime($p['date_added']))); // Quick fix to strip time stuff out which is preventing QBO from saving as Inventory entity

		// TODO: Extend services with export func.
		// I've isolated the code using static helpers right now
		// so it should be pretty easy to move around later
		$this->export($entityService, $entity);
	}

	/**
	 * Proxy method allows for stronger type hinting
	 */
	protected function export (QuickBooks_IPP_Service_Item &$service, QuickBooks_IPP_Object_Item &$item, $asXml = false) {
		$this->_export($service, $item, $asXml);
	}

	/**
	 * Event hook triggered before editing a product
	 */
	public function eventBeforeEditProduct() {

	}

	/**
	 * Event hook triggered after editing a product
	 */
	public function eventAfterEditProduct($taxClassId) {
		// Post changes to QBO
		$this->edit($taxClassId);
	}

	/**
	 * @param int $taxClassId
	 * @param array $data
	 */
	public function edit($taxClassId = 0, $feedId = false) {
		$this->getMappings($this->foreign);
		$mappings = $this->mappings;
		$entities = false;

		$this->load->model('catalog/product');
		$this->load->model('localisation/tax_class');
		$this->load->model('localisation/stock_status');
		$data = $this->model_catalog_product->getProduct($taxClassId); // TODO: Did I mod the model for this?
		$tax = $this->model_localisation_tax_class->getTaxClass($data['tax_class_id']);
		$stock = $this->model_localisation_stock_status->getStockStatus($data['stock_status_id']);

		// Just leaving this in as an example of some extra stuff I could do with this
		// Returns properties of current node not including... ./*[not(name()=\'Network\')]
		//$filtered = EntityMapper::filterEntities($xml, '*[name() = "Item"]');

		//$data = XML2Array::createArray($filtered); // Just filters crap out
		//$data = (!empty($data['entities'])) ? $data['entities']['Item'] : array();

		$entityService = new QuickBooks_IPP_Service_Item();

		if (!is_int($feedId)) {
			// If this is a push operation, $feedId should be provided and there's no need for an extra query
			// If this is an atomic update operation, we're going to need to grab the corresponding feed id
			$feedId = (int)$this->getFeedId($taxClassId);
		}

		if (is_int($feedId)) {
			// Get the existing item
			$entities = $entityService->query($this->Context, $this->realm, "SELECT * FROM Item WHERE Id = '" . $feedId . "'");
		}

		if ($entities != false && count($entities) > 0) {
			$entity = $entities[0];
			$entity->setOcId($taxClassId);
			// TODO: Move this metadata stuff to _before so it's always available
			$pMeta = $this->em->getClassMetadata('OcProduct');
			$pdMeta = $this->em->getClassMetadata('OcProductDescription');

			$p = ObjectFactory::createEntity($this->em, 'OcProduct', $data, array('taxClass' => $tax, 'stockStatus' => $stock));
			$pd = ObjectFactory::createEntity($this->em, 'OcProductDescription', $data);

			$pd['description'] = (array_key_exists('description', $pd)) ? strip_tags(html_entity_decode($pd['description'])) : '';

			// TODO: How am I going to manage conditional mappings?
			//if ($p['taxClass']['title'] == 'Taxable Goods') $entity->setTaxable(true);
			//if ($p['stockStatus']['name'] == 'In Stock');
			$entity->setType('Inventory');
			$entity->setTrackQtyOnHand(true); // OpenCart products all have quantities

			$this->fillEntity($entity, $mappings['Item']['fields'], $pMeta, $p); // Populate entity data
			$this->fillEntity($entity, $mappings['Item']['fields'], $pdMeta, $pd); // Populate entity data
			$this->fillEntityRefs($entity, $mappings['Item']['refs'], $p);

			// TODO: This is an improvement over the previous hardcoding, but there still isn't anything in the QC module admin that allows you to configure which attributes correlate to accounts
			$incomeAcct = $this->model_catalog_product->getProductAttributeByName($taxClassId, 'Accounting', 'Income Account'); // TODO: Must be "Sales of Product Income" account
			$expenseAcct = $this->model_catalog_product->getProductAttributeByName($taxClassId, 'Accounting', 'COGS Account'); // TODO: Must be "Cost of Goods Sold" account
			$assetAcct = $this->model_catalog_product->getProductAttributeByName($taxClassId, 'Accounting', 'Asset Account'); // TODO: Yeah.... drawing a blank here

			// TODO: Validate!
			// Temp - error!
			if (true) {
				// If it's an inventory type of item
				if (!isset($incomeAcct['text']) || !isset($expenseAcct['text']) || !isset($assetAcct['text'])) {
					throw new Exception('Inventory items must have income, expense and asset accounts assigned!');
				}

			}

			$entity->setIncomeAccountRef($incomeAcct['text']);
			$entity->setExpenseAccountRef($expenseAcct['text']);
			$entity->setAssetAccountRef($assetAcct['text']);

			$entity->setInvStartDate(date('Y-m-d', strtotime($p['date_added']))); // Quick fix to strip time stuff out which is preventing QBO from saving as Inventory entity

			$isSub = false;
			if (empty($p['parent_ref_id']) || !($p['parent_ref_id'] > 0) || isset($p['parent_id'])) {
				if (isset($p['parent_id'])) {
					// Fetch the ref
					$parent = $this->getByOpenCartId($p['parent_id']);

					if (isset($parent['feed_id'])) {
						$entity->setSubItem('true');
						$entity->setParentRef($parent['feed_id']);

						$isSub = true;
					}
				}
			}

			// If not a subitem, we're explicitly have to remove ParentRef and set SubItem to false
			if (!$isSub) {
				$entity->setSubItem('false');
				$entity->remove('ParentRef');
			}

			$entity->remove('FullyQualifiedName');

			// Quick fix to make name save on edit
			/*if (strpos($p['sku'], ':')) {
				$entity->setName(self::explodeSubItem($entity->getName()));
			}*/

			$this->export($entityService, $entity);
		} else {
			// If the product was deleted in QBO re-add it
			$this->add($taxClassId);
		}
	}

	protected function getService() {
		$service = new \App\Resource\TaxClass($this->em, 'OcTaxClass');
		return $service;
	}

	/*public function eventOnDeleteProduct() {
		
	}*/
}
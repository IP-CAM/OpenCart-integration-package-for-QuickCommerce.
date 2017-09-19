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

class ControllerQCTaxRate extends QCController {
	protected $tableName = 'qcli_tax_rate';
	protected $joinTableName = 'tax_rate';
	protected $joinCol = 'tax_rate_id';
	protected $foreign = 'TaxRate';

	function __construct($registry) {
		parent::__construct($registry);
		parent::before();
	}

	public function getTaxRate($taxRateId = null) {
		$this->load->model('localisation/tax_rate');

		$classes = $this->model_localisation_tax_rate->getTaxRatees();

		foreach ($classes as $class) {
			if ($class['tax_rate_id'] == $taxRateId)
				return $class;
		}
	}

	public function getTaxRateByName($name = '') {
		$this->load->model('localisation/tax_rate');

		$classes = $this->model_localisation_tax_rate->getTaxRatees();

		foreach ($classes as $class) {
			if ($class['title'] == $name)
				return $class;
		}
	}

	public function getTaxStatuses($taxRateId)
	{
		$this->load->model('localisation/tax_rate');
		return $this->model_localisation_tax_rate->getTaxRatees();
	}

	public function fetch() {
		$this->loadMetadata();
		// getMappings is used to map remote entities unlike mapDoctrineEntity and its wrappers which simply convert between OpenCart/OpenCart DB fields and their Doctrine entity equivalents
		$mappings = $this->getMappings($this->foreign); // Get the remote item mappings

		$tr = null;
		$data = array();
		
		$trService = new \App\Resource\TaxRate($this->em, 'OcTaxRate');
		$geoService = new \App\Resource\GeoZone($this->em, 'OcGeoZone');
		
		$importItem = function (&$item, &$data) {
			$mappings = $this->getMappings($this->foreign); // They're already loaded, just getting the reference
			$tr = array();
			self::importEntity($item, $mappings, $this->trMeta, $tr);
			$data[] = $tr;
		};

		$this->iterateCollection($importItem, $data);
		// TODO: Would be better if I didn't have to deal with huge arrays of data... maybe I can process on the fly?

		// Parent refs on the OC side need to be populated after all the products are imported
		// We can't do this in the same loop because we don't know what order the items are coming in from QuickBooks
		// Store the ids in an array for processing later

		$importedIds = array();
		
		$geozone = $geoService->getEntity(5, false); // TODO: Default geozone in config

		$reader = new ArrayReader($data);
		$writer = new CallbackWriter(
			function ($item) use (&$importedIds, &$tr, &$trService, &$geozone) {
				try {
					// Product should be tested for a unique email address - we don't want duplicates in OpenCart
					// That might not be the case in QuickBooks?
					$exists = $this->exists('tax_rate', 'name', $item['name']); // TODO: Method defaults maybe?
					
					if (!$exists) {
						$date = new DateTime();
						$tr = $trService->writeItem($item);
						//$tr->setDateAvailable($date); // TODO: Wtf to do with this? What is OC default? Can I null?
						$tr->setDateAdded($date);
						$tr->setDateModified($date);
						//$tr->setTaxRate(); // Cannot be null
						
						$tr->setType('P'); // P = Percentage, F = Fixed Amount
						$tr->setGeoZone($geozone);
						$trService->updateEntity($tr);

						$item['_entity']->setOcId($tr->getTaxRateId());
						$this->_writeListItem($item['_entity']);

						$id = $tr->getTaxRateId();
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
	}

	// Working on it...
	// This is alright for base OpenCart install
	// OC doesn't like nulls... need to set default vals somehow

	private function loadMetadata() {
		$this->trMeta = $this->em->getClassMetadata('OcTaxRate');
		$this->tcMeta = $this->em->getClassMetadata('OcTaxClass');
	}

	public function sync() {
		if ($this->doSync()) {
			echo 'yes, we do the sync';
		}
	}

	public function test() {
		$obj = ObjectFactory::createObject($this->em, 'MetaData', array());
		//var_dump($obj->getLastUpdatedTime());
		
		// TODO: I know it's a waste to initialize these over and over, but just trying to get this working for now
		$trService = new \App\Resource\TaxRate($this->em, 'OcTaxRate');
		$tcService = new \App\Resource\TaxClass($this->em, 'OcTaxClass');
		$adService = new \App\Resource\AttributeDescription($this->em, 'OcAttributeDescription');
		//$agService = new \App\Resource\AttributeGroup($this->em, 'OcAttributeGroup');
		$paService = new \App\Resource\ProductAttribute($this->em, 'OcProductAttribute');
		$intlService = new \App\Resource\Language($this->em, 'OcLanguage');
		
		$language = $intlService->getEntity(1, false);
		
		$incomeAccount = $item['_entity']->getIncomeAccountRef();
		$expenseAccount = $item['_entity']->getExpenseAccountRef();
		$assetAccount = $item['_entity']->getAssetAccountRef();
		
		$expenseAccount = (!empty($expenseAccount)) ? self::qbId($expenseAccount) : null;
		$assetAccount = (!empty($assetAccount)) ? self::qbId($assetAccount) : null;
		$incomeAccount = (!empty($incomeAccount)) ? self::qbId($incomeAccount) : null;
		
		
	}

	/**
	 * Event hook triggered before adding a product
	 */
	public function eventBeforeAddProduct($taxRateId) {

	}

	/**
	 * Event hook triggered after adding a product
	 */
	public function eventAfterAddProduct($taxRateId) {
		// Post product to QBO
		$this->add($taxRateId);
	}

	/**
	 * @param $taxRateId
	 */
	public function add($taxRateId) {
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
	public function eventAfterEditProduct($taxRateId) {
		// Post changes to QBO
		$this->edit($taxRateId);
	}

	/**
	 * @param int $taxRateId
	 * @param array $data
	 */
	public function edit($taxRateId = 0, $feedId = false) {
	}

	protected function getService() {
		$service = new \App\Resource\TaxRate($this->em, 'OcTaxRate');
		return $service;
	}

	/*public function eventOnDeleteProduct() {
		
	}*/
}
<?php
require_once(DIR_SYSTEM . 'engine/qccontroller.php');
require_once(DIR_SYSTEM . 'library/quickcommerce/entity_manager.php');

use Doctrine\Common\Util\Inflector;
use Doctrine\Common\Util\Debug;
use Doctrine\Common\Collections\Criteria;

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Reader\ArrayReader;
use Ddeboer\DataImport\Reader\OneToManyReader;
use Ddeboer\DataImport\Writer\ArrayWriter;
use Ddeboer\DataImport\Writer\CallbackWriter;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Ddeboer\DataImport\ItemConverter\MappingItemConverter;
use Ddeboer\DataImport\ItemConverter\NestedMappingItemConverter;
use Ddeboer\DataImport\ValueConverter\DateTimeValueConverter;

class ControllerQCCurrency extends QCController {
	protected $tableName = 'qcli_currency';
	protected $joinTableName = 'currency';
	protected $joinCol = 'currency_id';
	protected $foreign = 'CompanyCurrency';

	function __construct($registry) {
		parent::__construct($registry);
		parent::before();
	}

	public function fetch() {
	}

	// Working on it...
	// This is alright for base OpenCart install
	// OC doesn't like nulls... need to set default vals somehow

	private function loadMetadata() {
		$this->cMeta = $this->em->getClassMetadata('OcCurrency');
	}

	public function sync() {
		if ($this->doSync()) {
			echo 'yes, we do the sync';
		}
	}

	public function test() {
		// TODO: I know it's a waste to initialize these over and over, but just trying to get this working for now
		$cService = new \App\Resource\Currency($this->em, 'OcCurrency');
		
		$collection = $cService->getCollection(false); // Don't serialize
		
		echo '<pre>';
		foreach ($collection as $item) {
			if (empty($item)) continue;
			echo '<br><b>' . $item->getCode() .  '</b><br>';
			echo '<br><b>' . $item->getTitle() .  '</b><br>';
			Debug::dump($item);
			echo '<br>----------------------------------------------------------';
		}
		echo '</pre>';
	}

	/**
	 * Event hook triggered before adding a product
	 */
	public function eventBeforeAddProduct($currencyId) {

	}

	/**
	 * Event hook triggered after adding a product
	 */
	public function eventAfterAddProduct($currencyId) {
		// Post product to QBO
		$this->add($currencyId);
	}

	/**
	 * @param $currencyId
	 */
	public function add($currencyId) {
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
	public function eventAfterEditProduct($currencyId) {
		// Post changes to QBO
		$this->edit($currencyId);
	}

	/**
	 * @param int $currencyId
	 * @param array $data
	 */
	public function edit($currencyId = 0, $feedId = false) {
	}

	protected function getService() {
		$service = new \App\Resource\Currency($this->em, 'OcCurrency');
		return $service;
	}

	/*public function eventOnDeleteProduct() {
		
	}*/
}
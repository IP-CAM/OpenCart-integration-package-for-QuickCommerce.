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

use App\Resource\TaxClass;

class ControllerQCTaxRule extends QCController {
	protected $tableName = 'qcli_tax_rule';
	protected $joinTableName = 'tax_rule';
	protected $joinCol = 'tax_rule_id';
	protected $foreign = 'TaxCode';

	function __construct($registry) {
		parent::__construct($registry);
		parent::before();
	}

	public function getTaxRule($taxRuleId = null) {
		$this->load->model('localisation/tax_rule');

		$classes = $this->model_localisation_tax_rule->getTaxRulees();

		foreach ($classes as $class) {
			if ($class['tax_rule_id'] == $taxRuleId)
				return $class;
		}
	}

	public function getTaxRuleByName($name = '') {
		$this->load->model('localisation/tax_rule');

		$classes = $this->model_localisation_tax_rule->getTaxRulees();

		foreach ($classes as $class) {
			if ($class['title'] == $name)
				return $class;
		}
	}

	public function getTaxStatuses($taxRuleId)
	{
		$this->load->model('localisation/tax_rule');
		return $this->model_localisation_tax_rule->getTaxRules();
	}

	public function fetch() {
		$feedId = null;

		if (isset($this->request->get['tax_class_id'])) {
			$taxClassId = $this->request->get['tax_class_id'];
			$feedId = (int)$this->getFeedId($taxClassId, 'qcli_tax_code');
		} else {
			// Throw an error or something, we can't return tax rules without a code...
		}

		$entityService = new QuickBooks_IPP_Service_TaxCode();
		$entity = new QuickBooks_IPP_Object_TaxCode();

		if (is_int($feedId)) {
			// Get the existing item
			$entities = $entityService->query($this->Context, $this->realm, "SELECT * FROM TaxCode WHERE Id = '" . $feedId . "'");
		}

		if ($entities != false && count($entities) > 0) {
			$entity = $entities[0];
			$entity->setOcId($taxClassId);
		} else {
			// Throw some sort of error here
		}

		// Get the rules
		$salesTaxRates = ($entity->getSalesTaxRateList() instanceof QuickBooks_IPP_Object_SalesTaxRateList) ? $entity->getSalesTaxRateList()->getTaxRateDetail() : null;
		$purchaseTaxRates = ($entity->getPurchaseTaxRateList() instanceof QuickBooks_IPP_Object_PurchaseTaxRateList) ? $entity->getPurchaseTaxRateList()->getTaxRateDetail() : null;

		$this->loadMetadata();
		// getMappings is used to map remote entities unlike mapDoctrineEntity and its wrappers which simply convert between OpenCart/OpenCart DB fields and their Doctrine entity equivalents
		$mappings = $this->getMappings($this->foreign); // Get the remote item mappings

		$tc = null;
		$data = array();

		$tcService = new \App\Resource\TaxClass($this->em, 'OcTaxClass');
		$txService = new \App\Resource\TaxRate($this->em, 'OcTaxRate');
		$trService = new \App\Resource\TaxRule($this->em, 'OcTaxRule');

		//$items = $this->getCollection();

		$importItem = function (&$item, &$data) {
			$mappings = $this->getMappings($this->foreign); // They're already loaded, just getting the reference
			$tc = array();
			self::importEntity($item, $mappings, $this->tcMeta, $tc);
			$data[] = $tc;
		};

		if ($salesTaxRates instanceof QuickBooks_IPP_Object_TaxRateDetail) {
			$qbId = self::qbId($salesTaxRates->getTaxRateRef());

			$row = $this->getByFeedId($qbId, 'qcli_tax_rate');
			if (!empty($row)) {
				try {
					//$exists = $this->exists('tax_class', 'title', $item['title']); // TODO: Method defaults maybe?

					// Get tax class / code
					$tc = $tcService->getEntity($taxClassId, false);
					$tx = $txService->getEntity($row['oc_entity_id'], false);

					$tr = new OcTaxRule();
					$tr->setTaxClass($tc);
					$tr->setTaxRate($tx);
					// If importing default to the store address - there are no distinctions between shipping, payment and store addresses as far as taxes are concerned in QuickBooks Online
					$tr->setBased('store'); // [store | payment | shipping]
					// TODO: Confirm above assertion
					$trService->updateEntity($tr);
				} catch (Exception $e) {

				}
			}
		} elseif (is_array($salesTaxRates) && count($salesTaxRates) > 0) {
			try {
				// TODO: This block is repetitive, we should refactor later
				foreach ($salesTaxRates as $taxRate) {
					$row = null;
					if ($taxRate instanceof QuickBooks_IPP_Object_TaxRateDetail) {
						$qbId = self::qbId($taxRate->getTaxRateRef());

						$row = $this->getByFeedId($qbId, 'qcli_tax_rate');
						if (!empty($row)) {
							//$exists = $this->exists('tax_class', 'title', $item['title']); // TODO: Method defaults maybe?

							// Get tax class / code
							$tc = $tcService->getEntity($taxClassId, false);
							$tx = $txService->getEntity($row['oc_entity_id'], false);

							$tr = new OcTaxRule();
							$tr->setTaxClass($tc);
							$tr->setTaxRate($tx);
							// If importing default to the store address - there are no distinctions between shipping, payment and store addresses as far as taxes are concerned in QuickBooks Online
							$tr->setBased('store'); // [store | payment | shipping]
							// TODO: Confirm above assertion
							$trService->updateEntity($tr);
						}
					}
				}

			} catch (Exception $e) {

			}
		}
	}

	// Working on it...
	// This is alright for base OpenCart install
	// OC doesn't like nulls... need to set default vals somehow

	private function loadMetadata() {
		$this->tMeta = $this->em->getClassMetadata('OcTaxRule');
		$this->tcMeta = $this->em->getClassMetadata('OcTaxClass');
		$this->trMeta = $this->em->getClassMetadata('OcTaxRate');
	}

	public function sync() {
		if ($this->doSync()) {
			echo 'yes, we do the sync';
		}
	}

	public function test() {
		// TODO: I know it's a waste to initialize these over and over, but just trying to get this working for now
		$tService = new \App\Resource\TaxRule($this->em, 'OcTaxRule');
		$tcService = new \App\Resource\TaxClass($this->em, 'OcTaxClass');
		$trService = new \App\Resource\TaxRule($this->em, 'OcTaxRate');
		
		$collection = $tService->getCollection(false); // Don't serialize
		
		echo '<pre>';
		foreach ($collection as $item) {
			if (empty($item)) continue;
			echo '<br><b>' . $item->getTaxRuleId() .  '</b><br>';
			echo '<br>Tax Class: <b>' . $item->getTaxClass()->getTitle() .  '</b><br>';
			Debug::dump($item->getTaxClass());
			echo '<br>Tax Rate: <b>' . $item->getTaxRate()->getName() .  '</b><br>';
			Debug::dump($item->getTaxRate());
			echo '<br>----------------------------------------------------------';
		}
		echo '</pre>';
	}

	/**
	 * Event hook triggered before adding a product
	 */
	public function eventBeforeAddProduct($taxRuleId) {

	}

	/**
	 * Event hook triggered after adding a product
	 */
	public function eventAfterAddProduct($taxRuleId) {
		// Post product to QBO
		$this->add($taxRuleId);
	}

	/**
	 * @param $taxRuleId
	 */
	public function add($taxRuleId) {
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
	public function eventAfterEditProduct($taxRuleId) {
		// Post changes to QBO
		$this->edit($taxRuleId);
	}

	/**
	 * @param int $taxRuleId
	 * @param array $data
	 */
	public function edit($taxRuleId = 0, $feedId = false) {
	}

	protected function getService() {
		$service = new \App\Resource\TaxRule($this->em, 'OcTaxRule');
		return $service;
	}

	/*public function eventOnDeleteProduct() {
		
	}*/
}
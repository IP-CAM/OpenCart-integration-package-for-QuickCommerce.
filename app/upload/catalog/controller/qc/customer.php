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

class ControllerQCCustomer extends QCController {
	protected $tableName = 'qcli_customer';
	protected $joinTableName = 'customer';
	protected $joinCol = 'customer_id';
	protected $foreign = 'Customer';
	
	function __construct($registry) {
		parent::__construct($registry);		
		parent::before();
	}
	
	public function addWorkflow($entityName, $filterRegex = '', ArrayReader $reader) {
		$filterRegex = ($filterRegex != '') ? $filterRegex : '//' + $entityName;
		
		// DO customers
		EntityMapper::mapEntities($entityName, $mappings);
		// Start dealing with data
		// Returns properties of current node not including... ./*[not(name()=\'Network\')]
		$data = XML2Array::createArray($filterEntities($xmlResponse, $filterRegex)); // Just filters crap out
		$data = $data['entities'][$entityName];
		
		$reader = new ArrayReader($data); // OK for single level
		//$descriptionReader = new ArrayReader($data);
		//$customerDescriptionReader = new OneToManyReader($reader, $descriptionReader, 'description', 'ID', 'ID');
		
		//$workflow = new Workflow($customerDescriptionReader);
		$workflow = new Workflow($reader);
	}
	
	/** 
	 * Heavy batch operations should be changed to use XMLWriter or
	 * something that doesn't have to load everything into memory?
	 * This should be fine for small or medium-sized stores anyway
	 */
	public function fetch() {
		$output = [];
		
		$entities = $this->getCollection();
		
		$xml = '<entities>';
		foreach ($entities as $item) {
			$xml .= $entity->asXML(); // TODO: asIDSXML() is the one to use
			//print('Item Id=' . $entity->getId() . ' is named: ' . $entity->getName() . '<br>');
		}
		$xml .= '</entities>';
		
		//echo $xml;
		//exit;
		
		// TODO: Test vs schema to make sure def matches what Doctrine needs
		$converter = null;
		// Build mappings
		$converters = [];
		$mappings = [];
		
		// DO customers
		EntityMapper::mapEntities($this->em, 'Customer', $this->mapXml, $mappings);
		
		// Returns properties of current node not including... ./*[not(name()=\'Network\')]
		$filtered = EntityMapper::filterEntities($xml, '*[name() = "Customer"]');
		
		$data = XML2Array::createArray($filtered); // Just filters crap out
		$data = (!empty($data['entities'])) ? $data['entities']['Customer'] : array();
		
		$reader = new ArrayReader($data); // OK for single level
		//$descriptionReader = new ArrayReader($data);
		//$customerDescriptionReader = new OneToManyReader($reader, $descriptionReader, 'description', 'ID', 'ID');
		
		//$workflow = new Workflow($customerDescriptionReader);
		$workflow = new Workflow($reader);
		$output = [];
		
		// Adapter specific
		$customerWriter = new DoctrineWriter($this->em, 'OcCustomer');
		//$descriptionWriter = new DoctrineWriter($em, 'App\Entity\OpenCart\CustomerDescription');
		//$workflow->addWriter(new ArrayWriter($output));
		//$workflow->addWriter(new DoctrineWriter($em, 'App\Entity\OpenCart\Customer'));
		
		$this->load->model('rest/restadmin');
		
		$workflow->addWriter(new CallbackWriter(function ($row) use ($mappings, &$customerWriter) {
			$fields = $mappings['Customer']['fields'];
			$data = array();
			
			foreach (array_intersect_key($row, $fields) as $prop => $value) {
				if (array_key_exists($prop, $fields)) {
					$data[$fields[$prop]] = $value;
				}
			}
			
			$c = ObjectFactory::createEntity($this->em, 'OcCustomer', $data);
			$cd = ObjectFactory::createEntity($this->em, 'OcCustomerDescription', $data);
			
			// Shared value - reassign
			// TODO: Need to make a way to assign the same input to multiple fields... 
			// this is getting wiped out when I do the array flip
			
			$stock_status = $this->getStockStatusByName('In Stock');
			$c['stock_status_id'] = (int)$stock_status['stock_status_id']; // TODO: Set based on if quantity exists
			
			$taxClass = $this->getTaxClassByName('Taxable Goods');
			$c['tax_class_id'] = (int)$taxClass['tax_class_id']; // TODO: Set based on if quantity exists
			
			$c['customer_description'] = array();
			array_push($c['customer_description'], $cd);
			
			//var_dump($c);
			
			//return;
			
			$customerId = $this->model_rest_restadmin->add($c);
		}));
		
		$workflow->process();
	}
	
	/**
	 * return QuickBooks_IPP_Object_Customer
	 */
	public function get($id = 4, $data = array()) {
		$entityService = new QuickBooks_IPP_Service_Customer();

		// Get the existing item 
		$entities = $entityService->query($this->Context, $this->realm, "SELECT * FROM Customer WHERE Id = '" . $id . "'");
		$entity = ($entities && count($entities) > 0) ? $entities[0] : null;

		return $entity;
	}
	
	/**
	 * return array[QuickBooks_IPP_Object_Customer]
	 */
	public function getCollection() {
		$entityService = new QuickBooks_IPP_Service_Customer();

		$entities = $entityService->query($this->Context, $this->realm, "SELECT * FROM Customer ORDER BY Metadata.LastUpdatedTime");

		/*foreach ($entities as $entity) {
			print('Entity Id=' . $entity->getId() . ' is named: ' . $entity->getName() . '<br>');
		}*/
		
		var_dump($entities);
		
		return $entities;
	}
	
	/**
	 * @param $customerId
     */
	public function add($customerId) {
		$this->getMappings('Customer');
		$this->getMappings('Address');
		$mappings = $this->mappings;
		
		$this->load->model('customer/customer');
		$this->load->model('customer/customer_group');
		$data = $this->model_customer_customer->getCustomer($customerId);
		$address = $this->model_customer_customer->getAddress($data['address_id']); // TODO: Some way to automate this? From here on in it is done already...
		$group = $this->model_customer_customer_group->getCustomerGroup($data['customer_group_id']);
		
		$entityService = new QuickBooks_IPP_Service_Customer();
		$entity = new QuickBooks_IPP_Object_Customer();
		$entity->setOcId($customerId);
		$cMeta = $this->em->getClassMetadata('OcCustomer');
		
		$c = ObjectFactory::createEntity($this->em, 'OcCustomer', $data, array('address' => $address, 'customerGroup' => $group));
		
		// Populate entity data
		$this->fillEntity($entity, $mappings['Customer']['fields'], $cMeta, $c);
		$this->fillEntityObjects('Customer', $entity, $mappings, $cMeta, $c);
		$this->fillEntityRefs($entity, $mappings['Customer']['refs'], $c);
		
		// TODO: Extend services with export func.
		// I've isolated the code using static helpers right now
		// so it should be pretty easy to move around later
		$this->export($entityService, $entity, true);
	}
	
	/**
	 * @param int $customerId
	 * @param array $data
     */
	public function edit($customerId = 0, $feedId = false) {	
		$this->getMappings('Customer');
		$this->getMappings('Address');
		$mappings = $this->mappings;
		$entities = false;
		
		// TODO: Use admin model instead, front end model output (price) is affected by CFP module
		$this->load->model('customer/customer');
		$this->load->model('customer/customer_group');
		$data = $this->model_customer_customer->getCustomer($customerId);
		$address = $this->model_customer_customer->getAddress($data['address_id']); // TODO: Some way to automate this? From here on in it is done already...
		$group = $this->model_customer_customer_group->getCustomerGroup($data['customer_group_id']);
		
		$entityService = new QuickBooks_IPP_Service_Customer();
		
		if (!is_int($feedId)) {
			// If this is a push operation, $feedId should be provided and there's no need for an extra query
			// If this is an atomic update operation, we're going to need to grab the corresponding feed id
			$feedId = (int)$this->getFeedId($customerId);
		}
		
		if (is_int($feedId)) {
			// Get the existing item 
			$entities = $entityService->query($this->Context, $this->realm, "SELECT * FROM Customer WHERE Id = '" . $feedId . "'");
		}
		
		if ($entities != false && count($entities) > 0) {
			$entity = $entities[0];
			$entity->setOcId($customerId);
			$cMeta = $this->em->getClassMetadata('OcCustomer');

			$c = ObjectFactory::createEntity($this->em, 'OcCustomer', $data, array('address' => $address, 'customerGroup' => $group));
		
			// Populate entity data
			$this->fillEntity($entity, $mappings['Customer']['fields'], $cMeta, $c);
			$this->fillEntityObjects('Customer', $entity, $mappings, $cMeta, $c);
			$this->fillEntityRefs($entity, $mappings['Customer']['refs'], $c);
			
			var_dump($entity);

			$this->export($entityService, $entity, true);
		} else {
			// If the customer was deleted in QBO re-add it
			$this->add($customerId);
		}
	}
	
	private static function explodeSubCustomer($item, $fullyQualified = false) {
		if (!$fullyQualified) {
			$item = explode(':', trim($item));
			return array_pop($item);
		}
		
		return $item;
	}
	
	public function test() {
		$obj = ObjectFactory::createObject($this->em, 'MetaData', array());
		//var_dump($obj->getLastUpdatedTime());
	}
	
	/**
	 * Proxy method allows for stronger type hinting
	 */
	protected function export (QuickBooks_IPP_Service_Customer &$service, QuickBooks_IPP_Object_Customer &$item, $asXml = false) {
		$this->_export($service, $item, $asXml);
	}
	
	/**
	 * Event hook triggered before adding a customer
	 */
	public function eventBeforeAddCustomer($customerId) {
		
	}
	
	/**
	 * Event hook triggered after adding a customer
	 */
	public function eventAfterAddCustomer($customerId) {
		// Post customer to QBO
		$this->add($customerId);
	}
	
	/**
	 * Event hook triggered before editing a customer
	 */
	public function eventBeforeEditCustomer() {
		
	}
	
	/**
	 * Event hook triggered after editing a customer
	 */
	public function eventAfterEditCustomer($customerId) {
		// Post changes to QBO
		$this->edit($customerId);
	}
	
	/*public function eventOnDeleteCustomer() {
		
	}*/
}
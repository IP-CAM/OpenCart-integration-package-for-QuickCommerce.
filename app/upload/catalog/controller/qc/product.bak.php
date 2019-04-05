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

class ControllerQCProduct extends QCController {
	function __construct($registry) {
		parent::__construct($registry);
		parent::before();
	}
	
	public function getStockStatus($status_id = null) {
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
	
	public function getTaxStatuses($taxClassId) {
		$this->load->model('localisation/tax_class');
		return $this->model_localisation_tax_class->getTaxClasses();
	}
	
	public function addWorkflow($entityName, $filterRegex = '', ArrayReader $reader) {
		$filterRegex = ($filterRegex != '') ? $filterRegex : '//' + $entityName;
		
		// DO products
		EntityMapper::mapEntities($entityName, $mapXml, $mappings);
		// Start dealing with data
		// Returns properties of current node not including... ./*[not(name()=\'Network\')]
		$data = XML2Array::createArray($filterEntities($xmlResponse, $filterRegex)); // Just filters crap out
		$data = $data['entities'][$entityName];
		
		$productReader = new ArrayReader($data); // OK for single level
		//$descriptionReader = new ArrayReader($data);
		//$productDescriptionReader = new OneToManyReader($productReader, $descriptionReader, 'description', 'ID', 'ID');
		
		//$workflow = new Workflow($productDescriptionReader);
		$workflow = new Workflow($productReader);
	}
	
	public function fetch() {
		// Load definition
		$feedMap = DIR_QC . 'app/feeds/mappings/QBO.fcm.xml';

		if (!is_file($feedMap)) {
			//throw new \Slim\Exception\Exception("Oh crap something's not right with the feed map");
			echo '<pre>Error reading feed</pre>';
			exit;
		}
		
		$mapXml = simplexml_load_file($feedMap);

		$output = [];
		
		$items = $this->getProducts();
		
		$xml = '<entities>';
		foreach ($items as $item) {
			$xml .= $item->asXML();
			//print('Item Id=' . $item->getId() . ' is named: ' . $item->getName() . '<br>');
		}
		$xml .= '</entities>';
		
		//echo $xml;
		//exit;
		
		// TODO: Test vs schema to make sure def matches what Doctrine needs
		$converter = null;
		// Build mappings
		$converters = [];
		$mappings = [];
		
		// DO products
		EntityMapper::mapEntities($this->em, 'Item', $mapXml, $mappings);
		
		// Returns properties of current node not including... ./*[not(name()=\'Network\')]
		$filtered = EntityMapper::filterEntities($xml, '*[name() = "Item"]');
		
		$data = XML2Array::createArray($filtered); // Just filters crap out
		$data = (!empty($data['entities'])) ? $data['entities']['Item'] : array();
		
		$productReader = new ArrayReader($data); // OK for single level
		//$descriptionReader = new ArrayReader($data);
		//$productDescriptionReader = new OneToManyReader($productReader, $descriptionReader, 'description', 'ID', 'ID');
		
		//$workflow = new Workflow($productDescriptionReader);
		$workflow = new Workflow($productReader);
		$output = [];
		
		// Adapter specific
		$productWriter = new DoctrineWriter($this->em, 'OcProduct');
		//$descriptionWriter = new DoctrineWriter($em, 'App\Entity\OpenCart\ProductDescription');
		//$workflow->addWriter(new ArrayWriter($output));
		//$workflow->addWriter(new DoctrineWriter($em, 'App\Entity\OpenCart\Product'));
		
		$this->load->model('rest/restadmin');
		
		/*try {
			$dql = $this->em->createQueryBuilder()
				->select(array('pd'))
				->from('OcProductDescription', 'pd')
				->where('pd.language = :language')
				->andWhere('pd.product = :product')
				->setParameter('language', 1)
				->setParameter('product', 77);
				
			$result = $dql->getQuery()->getResult();
		}*/
		
		
		$workflow->addWriter(new CallbackWriter(function ($row) use ($mappings, &$productWriter) {
			$fields = $mappings['Item']['fields'];
			$data = array();
			
			foreach (array_intersect_key($row, $fields) as $prop => $value) {
				if (array_key_exists($prop, $fields)) {
					$data[$fields[$prop]] = $value;
				}
			}
			
			$p = EntityMapper::factory($this->em, 'OcProduct', $data);
			$pd = EntityMapper::factory($this->em, 'OcProductDescription', $data);
			
			// Shared value - reassign
			// TODO: Need to make a way to assign the same input to multiple fields... 
			// this is getting wiped out when I do the array flip
			$pd['name'] = $pd['description'];
			$pd['meta_title'] = $pd['description'];
			$pd['meta_description'] = $pd['description'];
			$pd['meta_keyword'] = '';
			$pd['tag'] = '';
			
			$stock_status = $this->getStockStatusByName('In Stock');
			$p['stock_status_id'] = (int)$stock_status['stock_status_id']; // TODO: Set based on if quantity exists
			
			$taxClass = $this->getTaxClassByName('Taxable Goods');
			$p['tax_class_id'] = (int)$taxClass['tax_class_id']; // TODO: Set based on if quantity exists
			
			$p['product_description'] = array();
			array_push($p['product_description'], $pd);
			
			//var_dump($p);
			
			//return;
			
			$productId = $this->model_rest_restadmin->addProduct($p);
			
			// Using Doctrine
			// Fill in any default values
			/*$product['stockStatusId'] = 1;
			$product['shipping'] = 0;
			$product['status'] = 1;*/
			//$productEntity = $productWriter->writeItem($product);
			//$productWriter->flushAndClear();
			
			/*$descriptionEntity = new \App\Entity\OpenCart\ProductDescription;
			$descriptionEntity->setProductId($productEntity->getProductId());
			$descriptionEntity->setLanguageId(1);
			$descriptionEntity->setName($row['Name']);
			$descriptionEntity->setDescription($row['Message']);
			$productEntity->addDescription($descriptionEntity);*/
			
			//$productWriter->EntityMapper->persist($descriptionEntity);
			//$descriptionEntity->setProduct($productEntity);
			//$productWriter->flushAndClear();
		}));
		
		/*$converter = new MappingItemConverter();
		$converter->setMappings($mappings['Item']['fields']);
		
		$descriptionConverter = new NestedMappingItemConverter('description');
		$descriptionConverter->setMappings($mappings['Item']['fields']);
			
		$workflow->addItemConverter($converter);
		$workflow->addItemConverter($descriptionConverter);*/
		$workflow->process();
	}
	
	/**
	 * return QuickBooks_IPP_Object_Item
	 */
	public function getProduct($productId = 4, $data = array()) {
		$itemService = new QuickBooks_IPP_Service_Item();

		// Get the existing item 
		$items = $itemService->query($this->Context, $this->realm, "SELECT * FROM Item WHERE Id = '" . $productId . "'");
		$item = $items[0];

		return $item;
	}
	
	/**
	 * return array[QuickBooks_IPP_Object_Item]
	 */
	public function getProducts() {
		$itemService = new QuickBooks_IPP_Service_Term();

		$items = $itemService->query($this->Context, $this->realm, "SELECT * FROM Item ORDER BY Metadata.LastUpdatedTime");

		/*foreach ($items as $item) {
			print('Item Id=' . $item->getId() . ' is named: ' . $item->getName() . '<br>');
		}*/
		
		return $items;
	}
	
	public function addProduct($productId) {
		// Load definition
		$feedMap = DIR_QC . 'app/feeds/mappings/QBO.fcm.xml';

		if (!is_file($feedMap)) {
			//throw new \Slim\Exception\Exception("Oh crap something's not right with the feed map");
			echo '<pre>Error reading feed</pre>';
			exit;
		}
		
		$mapXml = simplexml_load_file($feedMap);
		
		$mappings = [];
		
		// DO products
		$export = false; // Saves a step later
		EntityMapper::mapEntities($this->em, 'Item', $mapXml, $mappings, $export);
		
		// Returns properties of current node not including... ./*[not(name()=\'Network\')]
		//$filtered = EntityMapper::filterEntities($xml, '*[name() = "Item"]');
		
		//$data = XML2Array::createArray($filtered); // Just filters crap out
		//$data = (!empty($data['entities'])) ? $data['entities']['Item'] : array();

		$productId = 81;
		
		// TODO: Use admin model instead, front end model output (price) is affected by CFP module
		$this->load->model('catalog/product');
		$data = $this->model_catalog_product->getProduct($productId);
		
		//var_dump($productId);
		$fields = $mappings['Item']['fields'];
		
		$itemService = new QuickBooks_IPP_Service_Item();
		//var_dump($fields);
		
		$exportProduct = function($data) use (&$itemService, $fields, &$mappings) {
			$item = new QuickBooks_IPP_Object_Item();
			$children = array('OcTaxClass' => null,'OcWeightClass' => null, 'OcLengthClass' => null, 'OcManufacturer' => null);
			$p = EntityMapper::factory($this->em, 'OcProduct', $data);
			
			// No time to fully automate this binding process, and the doctrine entities are currently kind of f*****
			// OpenCart doesn't even have entities (what what?!), which is why this looks like a hack job
			$this->load->model('localisation/tax_class');
			$taxData = $this->model_localisation_tax_class->getTaxClass($data['tax_class_id']);
			$t = EntityMapper::factory($this->em, 'OcTaxClass', $taxData);
			
			$this->load->model('localisation/tax_class');
			$taxData = $this->model_localisation_tax_class->getTaxClass($data['tax_class_id']);
			$t = EntityMapper::factory($this->em, 'OcTaxClass', $taxData);
			
			/*$this->load->model('localisation/weight_class');
			$weightData = $this->model_localisation_tax_class->getTaxClass($data['weight_class_id']);
			$w = EntityMapper::factory($this->em, 'OcWeightClass', $weightData);*/
			//exit;
			
			$pd = EntityMapper::factory($this->em, 'OcProductDescription', $data);
			$pd['description'] = (array_key_exists('description', $pd)) ? strip_tags(html_entity_decode($pd['description'])) : '';
			
			$pColumnNames = $this->em->getClassMetadata('OcProduct')->columnNames;
			$pdColumnNames = $this->em->getClassMetadata('OcProductDescription')->columnNames;
			
			foreach ($mappings['Item']['fields'] as $foreign => $local) {
				if (array_key_exists($local, $pColumnNames) && array_key_exists($pColumnNames[$local], $p)) {
					$item->{'set' . $foreign}($p[$pColumnNames[$local]]);
				}
				
				if (array_key_exists($local, $pdColumnNames) && array_key_exists($pdColumnNames[$local], $pd)) {
					$item->{'set' . $foreign}($pd[$pdColumnNames[$local]]);
				}
			}
			
			$stockStatus = $this->getStockStatusByName('In Stock');
			$p['stock_status_id'] = (int)$stockStatus['stock_status_id']; // TODO: Set based on if quantity exists
			
			$p['tax_class_id'] = (int)$t['tax_class_id']; // TODO: Set based on if quantity exists
			
			// Whatever for now
			//$item->setName($data['name']);
			$item->setType('Inventory');
			$item->setIncomeAccountRef('53');
			
			$taxCodeService = new QuickBooks_IPP_Service_TaxCode();
			$salesTaxService = new QuickBooks_IPP_Service_SalesTax();
			$accountService = new QuickBooks_IPP_Service_Account();
			
			$codes = $taxCodeService->query($this->Context, $this->realm, "SELECT * FROM TaxCode");
			$accounts = $accountService->query($this->Context, $this->realm, "SELECT * FROM Account");
			
			var_dump($accounts);
			//exit;
			
			header('Content-Type: text/xml; charset=utf-8');
			echo $item->asXML();
			exit;
			
			exit;
			
			if ($resp = $itemService->add($this->Context, $this->realm, $item))
			{
				print('Our new Item ID is: [' . $resp . ']');
			}
			else
			{
				print($itemService->lastError($this->Context));
			}
		};
		
		$exportProduct($data);
	}
	
	public function editProduct($productId = 0, $data = array()) {
		// Load definition
		$feedMap = DIR_QC . 'app/feeds/mappings/QBO.fcm.xml';

		if (!is_file($feedMap)) {
			//throw new \Slim\Exception\Exception("Oh crap something's not right with the feed map");
			echo '<pre>Error reading feed</pre>';
			exit;
		}
		
		$mapXml = simplexml_load_file($feedMap);
		
		$mappings = [];
		
		// DO products
		$export = false; // Saves a step later
		EntityMapper::mapEntities($this->em, 'Item', $mapXml, $mappings, $export);
		
		// Returns properties of current node not including... ./*[not(name()=\'Network\')]
		//$filtered = EntityMapper::filterEntities($xml, '*[name() = "Item"]');
		
		//$data = XML2Array::createArray($filtered); // Just filters crap out
		//$data = (!empty($data['entities'])) ? $data['entities']['Item'] : array();
		
		
		$productId = 80;
		$itemService = new QuickBooks_IPP_Service_Item();

		// Get the existing item 
		$items = $itemService->query($this->Context, $this->realm, "SELECT * FROM Item WHERE Id = '2'");
		$item = $items[0];
		
		
		// TODO: Use admin model instead, front end model output (price) is affected by CFP module
		$this->load->model('catalog/product');
		$data = $this->model_catalog_product->getProduct($productId);
		
		//var_dump($productId);
		$fields = $mappings['Item']['fields'];
		
		$itemService = new QuickBooks_IPP_Service_Item();
		//var_dump($fields);
		
		$exportProduct = function($data) use (&$itemService, $fields, &$mappings) {			
			$p = EntityMapper::factory($this->em, 'OcProduct', $data);
			$pd = EntityMapper::factory($this->em, 'OcProductDescription', $data);
			
			$pd['description'] = (array_key_exists('description', $pd)) ? strip_tags(html_entity_decode($pd['description'])) : '';
			
			$pColumnNames = $this->em->getClassMetadata('OcProduct')->columnNames;
			$pdColumnNames = $this->em->getClassMetadata('OcProductDescription')->columnNames;
			
			foreach ($mappings['Item']['fields'] as $foreign => $local) {
				if (array_key_exists($local, $pColumnNames) && array_key_exists($pColumnNames[$local], $p)) {
					$item->{'set' . $foreign}($p[$pColumnNames[$local]]);
				}
				
				if (array_key_exists($local, $pdColumnNames) && array_key_exists($pdColumnNames[$local], $pd)) {
					$item->{'set' . $foreign}($pd[$pdColumnNames[$local]]);
				}
			}
			
			// Whatever for now
			//$item->setName($data['name']);
			$item->setType('Inventory');
			$item->setIncomeAccountRef('53');
			
			/*header('Content-Type: text/xml; charset=utf-8');
			echo $item->asXML();
			exit;*/
			
			if ($resp = $itemService->update($this->Context, $this->realm, $item))
			{
				print('Updated the item name to ' . $item->getName());
			}
			else
			{
				print($itemService->lastError($this->Context));
			}
		};
		
		$exportProduct($data);
	}
	
	public function eventBeforeAddProduct($productId) {
		$this->addProduct($productId);
	}
	
	public function eventAfterAddProduct($productId) {
		
	}
	
	public function eventBeforeEditProduct() {
		
	}
	
	public function eventAfterEditProduct($productId) {
		$this->editProduct($productId);
	}
	
	/*public function eventOnDeleteProduct() {
		
	}*/
}
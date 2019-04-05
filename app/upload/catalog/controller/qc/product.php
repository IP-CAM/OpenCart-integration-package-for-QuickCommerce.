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

	/**
	 * @param null $status_id
	 * @return mixed
     */
	public function getStockStatus($status_id = null) {
		$this->load->model('localisation/stock_status');

		$statuses = $this->model_localisation_stock_status->getStockStatuses();
		
		foreach ($statuses as $status) {
			if ($status['stock_status_id'] == $status_id)
				return $status;
		}
	}

	/**
	 * @param string $name
	 * @return mixed
     */
	public function getStockStatusByName($name = '') {
		$this->load->model('localisation/stock_status');

		$statuses = $this->model_localisation_stock_status->getStockStatuses();
		
		foreach ($statuses as $status) {
			if ($status['name'] == $name) 
				return $status;
		}
	}

	/**
	 * @param $stockStatusId
	 * @return mixed
     */
	public function getStockStatuses($stockStatusId) {
		$this->load->model('localisation/stock_status');
		return $this->model_localisation_stock_status->getStockStatuses();
	}

	/**
	 * @param null $taxClassId
	 * @return mixed
     */
	public function getTaxClass($taxClassId = null) {
		$this->load->model('localisation/tax_class');

		$classes = $this->model_localisation_tax_class->getTaxClasses();
		
		foreach ($classes as $class) {
			if ($class['tax_class_id'] == $taxClassId) 
				return $class;
		}
	}

	/**
	 * @param string $name
	 * @return mixed
     */
	public function getTaxClassByName($name = '') {
		$this->load->model('localisation/tax_class');

		$classes = $this->model_localisation_tax_class->getTaxClasses();
		
		foreach ($classes as $class) {
			if ($class['title'] == $name) 
				return $class;
		}
	}

	/**
	 * @param $taxClassId
	 * @return mixed
     */
	public function getTaxStatuses($taxClassId) {
		$this->load->model('localisation/tax_class');
		return $this->model_localisation_tax_class->getTaxClasses();
	}

	/**
	 * @param $entityName
	 * @param string $filterRegex
	 * @param ArrayReader $reader
     */
	/*public function addWorkflow($entityName, $filterRegex = '', ArrayReader $reader) {
		$filterRegex = ($filterRegex != '') ? $filterRegex : '//' + $entityName;
		
		// DO products
		EntityMapper::mapEntities($entityName, $mappings);
		// Start dealing with data
		// Returns properties of current node not including... ./*[not(name()=\'Network\')]
		$data = XML2Array::createArray($filterEntities($xmlResponse, $filterRegex)); // Just filters crap out
		$data = $data['entities'][$entityName];
		
		$productReader = new ArrayReader($data); // OK for single level
		//$descriptionReader = new ArrayReader($data);
		//$productDescriptionReader = new OneToManyReader($productReader, $descriptionReader, 'description', 'ID', 'ID');
		
		//$workflow = new Workflow($productDescriptionReader);
		$workflow = new Workflow($productReader);
	}*/
	
	/** 
	 * Heavy batch operations should be changed to use XMLWriter or
	 * something that doesn't have to load everything into memory?
	 * This should be fine for small or medium-sized stores anyway
	 */
	public function fetch() {
		$output = [];
		
		$items = $this->getProducts();
		
		$xml = '<entities>';
		foreach ($items as $item) {
			$xml .= $item->asXML(); // TODO: asIDSXML() is the one to use
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
		EntityMapper::mapEntities($this->em, 'Item', $this->mapXml, $mappings);
		
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
		}));
		
		$workflow->process();
	}
	
	/** 
	 * Heavy batch operations should be changed to use XMLWriter or
	 * something that doesn't have to load everything into memory?
	 * This should be fine for small or medium-sized stores anyway
	 */
	public function push() {
		$output = [];
		
		// TODO: Test vs schema to make sure def matches what Doctrine needs
		$converter = null;
		// Build mappings
		$converters = [];
		$mappings = [];
		
		// TODO: Remember to remove status WHERE clause when moving to admin
		$query = $this->db->query("SELECT p.product_id, fp.feed_id, fp.sync, p.date_added, p.date_modified FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "qc_feed_product fp ON (p.product_id = fp.product_id) WHERE p.status = 1");
		
		$productReader = new ArrayReader($query->rows); // OK for single level
		//$descriptionReader = new ArrayReader($data);
		//$productDescriptionReader = new OneToManyReader($productReader, $descriptionReader, 'description', 'ID', 'ID');
		
		//$workflow = new Workflow($productDescriptionReader);
		$workflow = new Workflow($productReader);
		$output = [];
		
		// Adapter specific
		$this->load->model('rest/restadmin');
		
		$i = 0;
		$workflow->addWriter(new CallbackWriter(function ($row) use ($mappings, &$i) {
			if ($i < 10) { // Temporary limit, just for testing
				// If the field has been mapped
				if (isset($row['feed_id'])) {
					// Get the product from feed (QBO)
					$item = $this->getProduct($row['feed_id']);
					// Check to see if it's up-to-date
				} else {
					if ($row['feed_id'] == null) {
						var_dump($row);
						$this->addProduct($row['product_id']);
					}
				}
			}
			
			$i++;
			/*$fields = $mappings['Item']['fields'];
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
			array_push($p['product_description'], $pd);*/
		}));
		
		$workflow->process();
	}
	
	/**
	 * return QuickBooks_IPP_Object_Item
	 */
	public function getProduct($productId = 4, $data = array()) {
		$itemService = new QuickBooks_IPP_Service_Item();

		// Get the existing item 
		$items = $itemService->query($this->Context, $this->realm, "SELECT Id, Metadata, SyncToken FROM Item WHERE Id = '" . $productId . "'");
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

	/**
	 * @param $productId
     */
	public function addProduct($productId) {
		$mappings = [];
		$export = false; // Saves a step later
		
		EntityMapper::mapEntities($this->em, 'Item', $this->mapXml, $mappings, $export);
		
		// TODO: Use admin model instead, front end model output (price) is affected by CFP module
		$this->load->model('catalog/product');
		$data = $this->model_catalog_product->getProduct($productId);
		//var_dump('data');
		//var_dump($data);
		
		$fields = $mappings['Item']['fields'];
		
		$itemService = new QuickBooks_IPP_Service_Item();
		$item = new QuickBooks_IPP_Object_Item();
		$children = array('OcTaxClass' => null,'OcWeightClass' => null, 'OcLengthClass' => null, 'OcManufacturer' => null);
		
		$p = EntityMapper::factory($this->em, 'OcProduct', $data);
		$pd = EntityMapper::factory($this->em, 'OcProductDescription', $data);
		$pd['description'] = (array_key_exists('description', $pd)) ? strip_tags(html_entity_decode(trim($pd['description']))) : '';
		
		$pColumnNames = $this->em->getClassMetadata('OcProduct')->columnNames;
		$pdColumnNames = $this->em->getClassMetadata('OcProductDescription')->columnNames;
		
		// OpenCart doesn't even have entities (what what?!), which is why this looks like a hack job!
		$this->load->model('localisation/tax_class');
		$taxData = $this->model_localisation_tax_class->getTaxClass($data['tax_class_id']);
		$tc = EntityMapper::factory($this->em, 'OcTaxClass', $taxData);
		
		$this->load->model('localisation/stock_status');
		$stockData = $this->model_localisation_stock_status->getStockStatus($data['stock_status_id']);
		$ss = EntityMapper::factory($this->em, 'OcStockStatus', $stockData);
		
		//var_dump('taxes');
		//var_dump($tc);
		
		//var_dump('stock');
		//var_dump($ss);
		
		/*$this->load->model('localisation/weight_class');
		$weightData = $this->model_localisation_tax_class->getTaxClass($data['weight_class_id']);
		$w = EntityMapper::factory($this->em, 'OcWeightClass', $weightData);*/
		//exit;
		
		foreach ($mappings['Item']['fields'] as $foreign => $local) {
			if (array_key_exists($local, $pColumnNames) && array_key_exists($pColumnNames[$local], $p)) {
				$item->{'set' . $foreign}($p[$pColumnNames[$local]]);
			}
			
			if (array_key_exists($local, $pdColumnNames) && array_key_exists($pdColumnNames[$local], $pd)) {
				$item->{'set' . $foreign}($pd[$pdColumnNames[$local]]);
			}
		}
		
		//var_dump($p);
		//var_dump($pd);
		
		// Whatever for now
		//$item->setName($data['name']);
		$item->setType('Inventory');
		$item->setIncomeAccountRef('53');
		
		/*$taxCodeService = new QuickBooks_IPP_Service_TaxCode();
		$salesTaxService = new QuickBooks_IPP_Service_SalesTax();
		$accountService = new QuickBooks_IPP_Service_Account();
		
		$codes = $taxCodeService->query($this->Context, $this->realm, "SELECT * FROM TaxCode");
		$accounts = $accountService->query($this->Context, $this->realm, "SELECT * FROM Account");*/
		
		// TODO: Extend services with export func.
		// I've isolated the code using static helpers right now
		// so it should be pretty easy to move around later
		$this->export($itemService, $item, true);
	}

	/**
	 * @param int $productId
	 * @param array $data
     */
	public function editProduct($productId = 0, $data = array()) {
		$mappings = [];
		$export = false; // Saves a step later
		
		EntityMapper::mapEntities($this->em, 'Item', $this->mapXml, $mappings, $export);
		$fields = $mappings['Item']['fields'];
		
		// Just leaving this in as an example of some extra stuff I could do with this
		// Returns properties of current node not including... ./*[not(name()=\'Network\')]
		//$filtered = EntityMapper::filterEntities($xml, '*[name() = "Item"]');
		
		//$data = XML2Array::createArray($filtered); // Just filters crap out
		//$data = (!empty($data['entities'])) ? $data['entities']['Item'] : array();
		
		$itemService = new QuickBooks_IPP_Service_Item();
		$this->load->model('catalog/product');
		$data = $this->model_catalog_product->getProduct($productId);
		
		$query = $this->db->query("SELECT feed_id FROM " . DB_PREFIX . "qc_feed_product WHERE product_id = '" . $productId . "'");
		$feedId = ($query->num_rows) ? $query->row['feed_id'] : null;

		// Get the existing item 
		//$items = $itemService->query($this->Context, $this->realm, "SELECT * FROM Item WHERE Id = '" . $productId . "'");
		$items = $itemService->query($this->Context, $this->realm, "SELECT * FROM Item WHERE Id = '" . $feedId . "'");
		if (count($items) > 0) {
			$item = $items[0];
		}
		/*else {
			// If it doesn't exist add a new one
			$item = new QuickBooks_IPP_Object_Item();
			$children = array('OcTaxClass' => null,'OcWeightClass' => null, 'OcLengthClass' => null, 'OcManufacturer' => null);
		}*/
		
		// How to do mappings for these?
		$obj = ObjectMapper::factory($this->em, 'MetaData', array());
		//var_dump($obj);
		
		//echo '-------------------------------';
		//ObjectMapper::mapObjects($this->mapXml, $mappings);
		/*header('Content-Type: text/xml; charset=utf-8');
		echo $this->mapXml->saveXML();
		exit;*/
		//var_dump($mappings);
		//exit;
		
		$p = EntityMapper::factory($this->em, 'OcProduct', $data);
		$pd = EntityMapper::factory($this->em, 'OcProductDescription', $data);
		
		$pd['description'] = (array_key_exists('description', $pd)) ? strip_tags(html_entity_decode($pd['description'])) : '';
		
		$pColumnNames = $this->em->getClassMetadata('OcProduct')->columnNames;
		$pdColumnNames = $this->em->getClassMetadata('OcProductDescription')->columnNames;
		
		foreach ($mappings['Item']['objects'] as $object) {
			// TODO: Adjust
			/*if (array_key_exists($local, $pColumnNames) && array_key_exists($pColumnNames[$local], $p)) {
				$item->{'set' . $foreign}($p[$pColumnNames[$local]]);
			}*/
			
			// TODO: Don't have to do this now, because qb metadata is read only
			// TODO: This should be in one of the mapper classes
			// Anyway, here's the logic...
			// Normalize
			//var_dump($object);
		}
		
		foreach ($mappings['Item']['fields'] as $foreign => $local) {
			if (array_key_exists($local, $pColumnNames) && array_key_exists($pColumnNames[$local], $p)) {
				$item->{'set' . $foreign}($p[$pColumnNames[$local]]);
			}
			
			if (array_key_exists($local, $pdColumnNames) && array_key_exists($pdColumnNames[$local], $pd)) {
				$item->{'set' . $foreign}($pd[$pdColumnNames[$local]]);
			}
		}
		
		// TODO: Account stuff later once accounts are implemented
		//$item->setType('Inventory');
		//$item->setIncomeAccountRef('53');
		
		$this->export($itemService, $item, true);
	}
	
	public function test() {
		$obj = ObjectMapper::factory($this->em, 'MetaData', array());
		//var_dump($obj->getLastUpdatedTime());
	}
	
	/**
	 * Proxy method allows for stronger type hinting
	 */
	protected function export (QuickBooks_IPP_Service_Item &$service, QuickBooks_IPP_Object_Item &$item, $asXml = false) {
		$this->_export($service, $item, $asXml);
	}
	
	/**
	 * Event hook triggered before adding a product
	 */
	public function eventBeforeAddProduct($productId) {
		
	}
	
	/**
	 * Event hook triggered after adding a product
	 */
	public function eventAfterAddProduct($productId) {
		// Post product to QBO
		$this->addProduct($productId);
	}
	
	/**
	 * Event hook triggered before editing a product
	 */
	public function eventBeforeEditProduct() {
		
	}
	
	/**
	 * Event hook triggered after editing a product
	 */
	public function eventAfterEditProduct($productId) {
		// Post changes to QBO
		$this->editProduct($productId);
	}
	
	/*public function eventOnDeleteProduct() {
		
	}*/
}
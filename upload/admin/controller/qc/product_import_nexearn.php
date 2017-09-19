<?php
require_once(dirname(__FILE__) . '/product.php');
require_once(DIR_SYSTEM . 'engine/qccontroller.php');
require_once(DIR_SYSTEM . 'library/quickcommerce/entity_manager.php');

use Doctrine\Common\Util\Inflector;
use Doctrine\Common\Util\Debug;
use Doctrine\Common\Collections\Criteria;

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Reader\ArrayReader;
use Ddeboer\DataImport\Reader\DoctrineReader;
use Ddeboer\DataImport\Reader\OneToManyReader;
use Ddeboer\DataImport\Writer\ArrayWriter;
use Ddeboer\DataImport\Writer\CallbackWriter;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Ddeboer\DataImport\ItemConverter\MappingItemConverter;
use Ddeboer\DataImport\ItemConverter\NestedMappingItemConverter;
use Ddeboer\DataImport\ValueConverter\DateTimeValueConverter;
use Ddeboer\DataImport\Filter\OffsetFilter;

class ControllerQCProductImportNexearn extends ControllerQCProduct {
	protected $tableName = 'qcli_product';
	protected $joinTableName = 'product';
	protected $joinCol = 'product_id';
	protected $foreign = 'data';
	
	/**
	 * @param $registry
	 * @throws Exception
	 * @throws \Doctrine\DBAL\DBALException
	 * @throws \Doctrine\ORM\ORMException
     */
	function __construct($registry) {
		/*if (empty($this->tableName)) // TODO: Interface yo
			throw new Exception('Mapping table name ($tableName) was not specified in the extending controller class');
		if (empty($this->joinTableName)) // TODO: Interface yo
			throw new Exception('Join table name ($joinTableName) was not specified in the extending controller class');
		if (empty($this->joinCol)) // TODO: Interface yo
			throw new Exception('Join column name ($joinCol) was not specified in the extending controller class');*/
		
		parent::__construct($registry);
		
		$di = new DoctrineInitializer($this, $registry);
		
		$this->feedMap = DIR_QC . 'vendor/quickcommerce/feeds/mappings/WYNIT.fcm.xml';

		if (!is_file($this->feedMap)) {
			//throw new \Slim\Exception\Exception("Oh crap something's not right with the feed map");
			echo '<pre>Error reading feed</pre>';
			exit;
		}

		$this->mapXml = simplexml_load_file($this->feedMap);
	}
	
	protected function mergeProduct($id) {
	}
	
	public function merge($fn) {
		$this->loadMetadata();
		// getMappings is used to map remote entities unlike mapDoctrineEntity and its wrappers which simply convert between OpenCart/OpenCart DB fields and their Doctrine entity equivalents
		$mappings = $this->getMappings($this->foreign); // Get the remote item mappings

		$p = null;
		$data = array();

		$sService = new \App\Resource\Store($this->em, 'OcStore');
		$pService = new \App\Resource\Product($this->em, 'OcProduct');
		$cService = new \App\Resource\Category($this->em, 'OcCategory');
		$mService = new \App\Resource\Manufacturer($this->em, 'OcManufacturer');
		$aService = new \App\Resource\Attribute($this->em, 'OcAttribute');
		$oService = new \App\Resource\Option($this->em, 'OcOption');
		$pdService = new \App\Resource\ProductDescription($this->em, 'OcProductDescription');
		$piService = new \App\Resource\ProductImage($this->em, 'OcProductImage');
		$paService = new \App\Resource\ProductAttribute($this->em, 'OcProductAttribute');
		$poService = new \App\Resource\ProductOption($this->em, 'OcProductOption');
		$povService = new \App\Resource\ProductOptionValue($this->em, 'OcProductOptionValue');
		$intlService = new \App\Resource\Language($this->em, 'OcLanguage');
		$ssService = new \App\Resource\StockStatus($this->em, 'OcStockStatus');
		$tcService = new \App\Resource\TaxClass($this->em, 'OcTaxClass');
		$wService = new \App\Resource\WeightClass($this->em, 'OcWeightClass');
		$lService = new \App\Resource\LengthClass($this->em, 'OcLengthClass');

		$importedIds = array();

		//$store = $sService->getEntity(1, false);
		//$stock_status = $this->getStockStatusByName('In Stock');
		$language = $intlService->getEntity(1, false);
		$stock = $ssService->getEntity(7, false); // In Stock
		$taxClass = $tcService->getEntity(13, false); // Taxable Goods
		$weightClass = $wService->getEntity(1, false); // Kilos
		$lengthClass = $lService->getEntity(3, false); // Inches
		
		// TODO: Real transactions and rollback!!! These actions are atomic right now
		$export = false; // Saves a step later
		
		$this->load->model('catalog/product');
		$this->load->model('catalog/manufacturer');
		$this->load->model('catalog/attribute');
		$this->load->model('catalog/option');
		
		$reader = new DoctrineReader($this->em, 'OcProduct');
		$writer = new CallbackWriter(
			function ($item) use (&$importedIds, &$p, &$pService, &$pdService, &$cService, &$mService, &$piService, &$poService, &$povService, &$aService, &$oService, &$language, &$stock, &$taxClass, &$weightClass, &$lengthClass) {
					try {
						$productDb2 = $this->model_catalog_product->getDb2Products(array('filter_mpn' => $item['model']));
						
						if (is_array($productDb2) && count($productDb2) == 1) {
							$productDb2Id = $productDb2[0]['product_id'];
							$productDb2 = $this->model_catalog_product->getDb2Product($productDb2Id);
							// Unset QuickCommerce fields
							unset($productDb2['product_id']);
							unset($productDb2['model']);
							unset($productDb2['sku']);
							unset($productDb2['upc']);
							unset($productDb2['ean']);
							unset($productDb2['jan']);
							unset($productDb2['isbn']);
							unset($productDb2['mpn']);
							unset ($productDb2['seo_url']);

							$productDb2['date_added'] = new DateTime($productDb2['date_added']);
							$productDb2['date_modified'] = new DateTime($productDb2['date_modified']);
							$productDb2['date_available'] = new DateTime($productDb2['date_available']);

							// Assume a product and description exist
							$p = $pService->findOrCreateItem($item);
							$pService->fillEntity($productDb2, $p, false);
							
							$manufacturerDb2 = $this->model_catalog_manufacturer->getDb2Manufacturer($productDb2['manufacturer_id']);
							//var_dump($manufacturerDb2);
							
							$m = $mService->findEntityByName($manufacturerDb2['name'], false)[0];
							$p->setManufacturer($m);
							//Debug::dump($m);

							// Categories
							$this->load->model('catalog/category');

							$p->getCategory()->clear(); // Clear associations
							// TODO: Unset categories before adding, or check to see if they exist - commenting this out just to get data transferred
							$categoriesDb2 = $this->model_catalog_product->getDb2ProductCategories($productDb2Id);
							foreach ($categoriesDb2 as $categoryDb2Id) {
								$categoryDb2 = $this->model_catalog_category->getDb2Category($categoryDb2Id);

								unset($categoryDb2['category_id']);
								$c = $cService->findEntityByDescriptionName($categoryDb2['name'], false)[0];
								$p->addCategory($c);
							}
							
							$imagesDb2 = $this->model_catalog_product->getDb2ProductImages($productDb2Id);
							foreach ($imagesDb2 as $imageDb2) {
								unset($imageDb2['product_image_id']); // Unset PK
								$pi = $piService->findOrCreateItem($imageDb2);
								$pi->setProduct($p);
								$piService->fillEntity($imageDb2, $pi, true);
								$piService->updateEntity($pi);
							}
							
							/*$discountsDb2 = $this->model_catalog_product->getDb2ProductDiscounts($productDb2Id);
							var_dump($discountsDb2);
							$specialsDb2 = $this->model_catalog_product->getDb2ProductSpecials($productDb2Id);
							var_dump($specialsDb2);
							$rewardsDb2 = $this->model_catalog_product->getDb2ProductRewards($productDb2Id);
							var_dump($rewardsDb2);
							$downloadsDb2 = $this->model_catalog_product->getDb2ProductDownloads($productDb2Id);
							var_dump($downloadsDb2);*/
							
							/*$p->getAttribute()->clear(); // Clear associations
							$productAttributesDb2 = $this->model_catalog_product->getDb2ProductAttributes($productDb2Id);
							//var_dump($productAttributesDb2);
							foreach ($productAttributesDb2 as $productAttributeDb2) {
								//unset($attributeDb2['product_attribute_id']); // Unset PK
								$attributeDb2Id = $productAttributeDb2['attribute_id'];
								$attributeDb2 = $this->model_catalog_attribute->getDb2Attribute($attributeDb2Id);
								
								$a = $aService->findEntityByDescriptionName($attributeDb2['name'], false)[0];
								$desc = $productAttributeDb2['product_attribute_description'];
								$lang = (isset($desc[$language->getLanguageId()])) ? $desc[$language->getLanguageId()] : false;
								$text = ($lang != false && isset($lang['text'])) ? $lang['text'] : null;
								
								if (isset($text)) {
									$po = new OcProductAttribute();
									$po->setAttribute($a);
									$po->setProduct($p);
									$po->setLanguage($language);
									$po->setText($text);
									
									$p->addAttribute($po);
									$poService->updateEntity($po);
								}
							}*/
							
							/*$p->getOption()->clear(); // Clear associations
							$productOptionsDb2 = $this->model_catalog_product->getDb2ProductOptions($productDb2Id);
							//var_dump($productOptionsDb2);
							foreach ($productOptionsDb2 as $productOptionDb2) {
								//unset($optionDb2['product_option_id']); // Unset PK
								$optionDb2Id = $productOptionDb2['option_id'];
								$optionDb2 = $this->model_catalog_option->getDb2Option($optionDb2Id);
								$o = $oService->findEntityByDescriptionName($optionDb2['name'], false)[0];
								
								$ovs = $o->getOptionValues();
								
								$po = new OcProductOption();

								unset($productOptionDb2['product_option_id']);
								$poService->fillEntity($productOptionDb2, $po, false);

								$po->setOption($o);
								$po->setProduct($p);

								$poService->updateEntity($po);
								
								$povs = $po->getProductOptionValues();
								
								$optionValueDescriptions = $this->model_catalog_option->getDb2OptionValueDescriptions($productOptionDb2['option_id']);
								foreach ($productOptionDb2['product_option_value'] as $productOptionValue) {
									$pov = new OcProductOptionValue();
									$povService->fillEntity($productOptionValue, $pov, false);
									
									$optionValueDb2Id = $productOptionValue['option_value_id'];
									$optionValueDb2 = null;
									
									foreach ($optionValueDescriptions as $optionValueDescription) {
										if ((int)$optionValueDescription['option_value_id'] == (int)$optionValueDb2Id) {
											$optionValueDescriptionDb2 = $optionValueDescription['option_value_description'][$language->getLanguageId()];
										}
									}
									
									$pov->setProductOption($po);
									$pov->setProduct($p);
									$pov->setOption($o);
									
									foreach ($ovs as $ov) {
										$desc = $ov->getDescription()->first()->getName();
										if ($desc == $optionValueDescriptionDb2['name']) {
											$pov->setOptionValue($ov);
										}
									}
									
									$po->addProductOptionValue($pov);
									
									$pService->updateEntity($pov);
								}
								
								$poService->updateEntity($po);
								
								$p->addOption($po);
							}*/
							
							$pService->updateEntity($p);

							$pd = $p->getDescription()->first(); // English only for now
							$pdService->fillEntity($productDb2, $pd, true);
							$pd->setLanguage($language); // Just in case
							$pd->setProduct($p); // Just in case
							$pdService->updateEntity($pd);
						}
					} catch (Exception $e) {
						throw $e;
					}
			});
			

		$workflow = new Workflow($reader);
		//self::addDateConverters($workflow); // TODO: Where is this method?
		$workflow->addWriter($writer);
		
		$filter = new OffsetFilter(1500, 1000);
		$workflow->addFilter($filter);
		
		$workflow->process();
	}
	
	public function fetch() {
		$this->loadMetadata();
		// getMappings is used to map remote entities unlike mapDoctrineEntity and its wrappers which simply convert between OpenCart/OpenCart DB fields and their Doctrine entity equivalents
		$mappings = $this->getMappings($this->foreign); // Get the remote item mappings

		$p = null;
		$data = array();

		$sService = new \App\Resource\Store($this->em, 'OcStore');
		$pService = new \App\Resource\Product($this->em, 'OcProduct');
		$pdService = new \App\Resource\ProductDescription($this->em, 'OcProductDescription');
		$intlService = new \App\Resource\Language($this->em, 'OcLanguage');
		$ssService = new \App\Resource\StockStatus($this->em, 'OcStockStatus');
		$tcService = new \App\Resource\TaxClass($this->em, 'OcTaxClass');
		$wService = new \App\Resource\WeightClass($this->em, 'OcWeightClass');
		$lService = new \App\Resource\LengthClass($this->em, 'OcLengthClass');

		$importedIds = array();

		//$store = $sService->getEntity(1, false);
		//$stock_status = $this->getStockStatusByName('In Stock');
		$language = $intlService->getEntity(1, false);
		$stock = $ssService->getEntity(7, false); // In Stock
		$taxClass = $tcService->getEntity(4, false); // Taxable Goods
		$weightClass = $wService->getEntity(1, false); // Kilos
		$lengthClass = $lService->getEntity(3, false); // Inches
		
		$doc = new DOMDocument();
		$doc->load(DIR_QC . 'data/435429-832.xml');
		$data = XML2Array::createArray($doc);
		
		$data = (isset($data['inventory']) && isset($data['inventory']['data'])) ? $data['inventory']['data'] : false;
		if (!$data || !is_array($data)) return false;
		
		//echo '<pre>';
		
		$reader = new ArrayReader($data);
		$writer = new CallbackWriter(
			function ($item) use (&$mappings, &$importedIds, &$p, &$pService, &$pdService, &$language, &$stock, &$taxClass, &$weightClass, &$lengthClass) {
					try {
						$fields = $mappings['fields'];
						$data = array();

						foreach (array_intersect_key($item, $fields) as $prop => $value) {
							if (array_key_exists($prop, $fields)) {
								$data[$fields[$prop]] = trim($value);
							}
						}

						$exists = $this->exists('product', 'model', $data['model']); // TODO: Method defaults maybe?
						
						if (!$exists) {
							$date = new DateTime();
							$p = $pService->writeItem($data);
							$p->setDateAvailable($date); // TODO: Wtf to do with this? What is OC default? Can I null?
							$p->setDateAdded($date);
							$p->setDateModified($date);

							if (empty($p->getSku())) $p->setSku('');
							if (empty($p->getUpc())) $p->setUpc('');

							$p->setPrice(floatval(str_replace(',', '', $p->getPrice()))); // Fix decimal formatting
							$p->setCost(floatval(str_replace(',', '', $p->getCost()))); // Fix decimal formatting

							$p->setEan(''); // Cannot be null
							$p->setJan(''); // Cannot be null
							$p->setIsbn(''); // Cannot be null
							$p->setLocation(''); // Cannot be null

							// Images
							$url = $p->getImage();

							if (!empty($url)) {
								$path = parse_url($url, PHP_URL_PATH);
								$imageName = array_pop(explode('/', $path));
								$fileName = 'catalog/wynit/' . $imageName;

								$ch = curl_init();

								curl_setopt($ch, CURLOPT_URL, $url);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($ch, CURLOPT_FAILONERROR, true);

								$source = curl_exec($ch);
								curl_close($ch);

								if ($source != false) {
									file_put_contents(DIR_IMAGE . '/' . $fileName, $source);

									$p->setImage($fileName);
								} else {
									if (curl_error($ch)) {
										echo 'error:' . curl_error($ch);
									}

									$p->setImage(null);
								}
							}

							$p->setStockStatus($stock); // Cannot be null
							$p->setWeightClass($weightClass); // Cannot be null
							$p->setLengthClass($lengthClass); // Cannot be null
							$p->setTaxClass($taxClass); // Cannot be null
							
							$p->setStatus(1);

							$pService->updateEntity($p);

							$pd = $pdService->writeItem($data);

							if (empty($pd->getDescription())) {
								$pd->setDescription($pd->getName()); // Cannot be null;
							}

							//$pd->setName($p->getModel()); // Name in OC is not mapped to QB; we don't want store product titles tied to our QB item name
							$pd->setMetaTitle($pd->getName());
							$pd->setMetaDescription($pd->getName());
							$pd->setMetaKeyword($pd->getName());
							$pd->setTag('');

							$pd->setProduct($p); // No multi-language support in QBO
							$pd->setLanguage($language); // No multi-language support in QBO

							$p->addDescription($pd); // No multi-language support in QBO*/

							$pdService->updateEntity($pd);

							$id = $p->getProductId();
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
		
		$filter = new OffsetFilter(0, 700);
		//$filter = new OffsetFilter(0, 10);
		$workflow->addFilter($filter);
		
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
	
	private static function addDateConverters(&$workflow) {
		$dateConverter = new DateTimeValueConverter();
		$workflow->addValueConverter('dateAdded', $dateConverter);
		$workflow->addValueConverter('dateModified', $dateConverter);
	}
	
	private function mapProduct(&$mappings) {
		$this->mapDoctrineEntity($mappings, array(
			'OcProduct' => array(
				'foreign' => 'Product',
				'meta' => $this->pMeta,
				'children' => array(
					'OcProductDescription' => array(
						'foreign' => 'ProductDescription',
						'meta' => $this->pdMeta
					)
				)
			)
		), true, false);
	}
	
	// Working on it...
	// This is alright for base OpenCart install
	// OC doesn't like nulls... need to set default vals somehow

	private function loadMetadata() {
		$this->pMeta = $this->em->getClassMetadata('OcProduct');
		$this->pdMeta = $this->em->getClassMetadata('OcProductDescription');
	}
	
	public function test() {
		$obj = ObjectFactory::createObject($this->em, 'MetaData', array());
		//var_dump($obj->getLastUpdatedTime());
	}

	protected function getService() {
		$service = new \App\Resource\Product($this->em, 'OcProduct');
		return $service;
	}
	
	/*public function eventOnDeleteProduct() {
		
	}*/
}
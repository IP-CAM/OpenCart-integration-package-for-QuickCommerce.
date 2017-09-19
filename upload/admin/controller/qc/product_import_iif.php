<?php
require_once(dirname(__FILE__) . '/product.php');
require_once(DIR_SYSTEM . 'engine/qccontroller.php');
require_once(DIR_SYSTEM . 'library/quickcommerce/entity_manager.php');

use Doctrine\Common\Util\Inflector;
use Doctrine\Common\Util\Debug;
use Doctrine\Common\Collections\Criteria;

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Reader\ArrayReader;
use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Reader\DoctrineReader;
use Ddeboer\DataImport\Reader\OneToManyReader;
use Ddeboer\DataImport\Writer\ArrayWriter;
use Ddeboer\DataImport\Writer\CallbackWriter;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Ddeboer\DataImport\ItemConverter\MappingItemConverter;
use Ddeboer\DataImport\ItemConverter\NestedMappingItemConverter;
use Ddeboer\DataImport\ValueConverter\DateTimeValueConverter;
use Ddeboer\DataImport\Filter\OffsetFilter;

final class SourceManufacturerCodes {
	const ASTORIA = 'AS';
	const BEZZERA = 'BE';
	const BFC = 'BFC';
	const BUNN = 'BN';
	const CARIMALI = 'CA';
	const CIMBALI = 'C';
	const COMPAK = 'K';
	const E61 = 'E61';
	const ELEKTRA = 'EL';
	#const FPR = 'PR';
	#const FLT = 'PA';
	const IGF = 'IGF';
	const INNOVA = 'IN';
	const IZZO = 'IZ';
	const JURA = 'J';
	const LA_MARZOCCO = 'LZ';
	const LA_MINERVA = 'LM';
	#const PAV = 'PA';
	const LA_SAN_MARCO = 'SM';
	const LA_SPAZIALE = 'SP';
	const MACAP = 'MC';
	const KAHLKONIG = 'M';
	const MAZZER = 'MZ';
	const NUOVA_SIMONELLI = 'S';
	const NS_GRINDER_PARTS = 'MDS';
	const QUICK_MILL = 'QM';
	const RANCILIO = 'R';
	const ROCKET = 'RK';
	const SAECO = 'SCO';
	const SHAERER = 'SHA';
	const SILANOS = 'SIL';
	const H20 = 'FM';
	const WEGA = 'W';
	const VARIOUS = 'V';
	// make this private so noone can make one
	private function __construct(){
		// throw an exception if someone can get in here (I'm paranoid)
		throw new Exception("Can't get an instance of MapManufacturerCodes");
	}

	static function getConstants() {
		$oClass = new ReflectionClass(__CLASS__);
		return $oClass->getConstants();
	}
}

final class SysManufacturerCodes {
	const ASTORIA = 'AST';
	const BEZZERA = 'BZZ';
	const BFC = 'BFC';
	const BUNN = 'BNN';
	const CARIMALI = 'CRM';
	const CIMBALI = 'CIM';
	const COMPAK = 'CPK';
	const E61 = 'E61';
	const ELEKTRA = 'ELK';
	#const FPR = 'PR';
	#const FLT = 'PA';
	const IGF = 'IGF';
	const INNOVA = 'INO';
	const IZZO = 'IZO';
	const JURA = 'JUR';
	const LA_MARZOCCO = 'LMZ';
	const LA_MINERVA = 'LMV';
	#const PA = 'PAV';
	const LA_SAN_MARCO = 'LSM';
	const LA_SPAZIALE = 'SPZ';
	const MACAP = 'MAC';
	const MAHLKONIG = 'MKG';
	const MAZZER = 'MZZ';
	const NUOVA_SIMONELLI = 'NSI';
	const NS_GRINDER_PARTS = 'NSG';
	const QUICK_MILL = 'QML';
	const RANCILIO = 'RAN';
	const ROCKET = 'RCK';
	const SAECO = 'SCO';
	const SCHAERER= 'SHA';
	const SILANOS = 'SIL';
	const H20 = 'H20';
	const WEGA = 'WGA';
	const VARIOUS = 'VVV';
	// make this private so noone can make one
	private function __construct(){
		// throw an exception if someone can get in here (I'm paranoid)
		throw new Exception("Can't get an instance of SysManufacturerCodes");
	}

	static function getConstants() {
		$oClass = new ReflectionClass(__CLASS__);
		return $oClass->getConstants();
	}
}

final class CategoryMap {
	private function __construct(){
		// throw an exception if someone can get in here (I'm paranoid)
		throw new Exception("Can't get an instance of CategoryMap");
	}
}

class ControllerQCProductImportIIF extends ControllerQCProduct {
	protected $tableName = 'qcli_product';
	protected $joinTableName = 'product';
	protected $joinCol = 'product_id';
	protected $foreign = 'Item';
	
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
		
		$this->feedMap = DIR_QC . 'vendor/quickcommerce/feeds/mappings/QBIIF.fcm.xml';

		/*if (!is_file($this->feedMap)) {
			//throw new \Slim\Exception\Exception("Oh crap something's not right with the feed map");
			echo '<pre>Error reading feed</pre>';
			exit;
		}*/

		$this->mapXml = simplexml_load_file($this->feedMap);
	}

	protected function init() {
		$this->sService = new \App\Resource\Store($this->em, 'OcStore');
		$this->pService = new \App\Resource\Product($this->em, 'OcProduct');
		$this->cService = new \App\Resource\Category($this->em, 'OcCategory');
		$this->mService = new \App\Resource\Manufacturer($this->em, 'OcManufacturer');
		$this->aService = new \App\Resource\Attribute($this->em, 'OcAttribute');
		$this->oService = new \App\Resource\Option($this->em, 'OcOption');
		$this->pdService = new \App\Resource\ProductDescription($this->em, 'OcProductDescription');
		$this->piService = new \App\Resource\ProductImage($this->em, 'OcProductImage');
		$this->paService = new \App\Resource\ProductAttribute($this->em, 'OcProductAttribute');
		$this->poService = new \App\Resource\ProductOption($this->em, 'OcProductOption');
		$this->povService = new \App\Resource\ProductOptionValue($this->em, 'OcProductOptionValue');
		$this->intlService = new \App\Resource\Language($this->em, 'OcLanguage');
		$this->ssService = new \App\Resource\StockStatus($this->em, 'OcStockStatus');
		$this->tcService = new \App\Resource\TaxClass($this->em, 'OcTaxClass');
		$this->wService = new \App\Resource\WeightClass($this->em, 'OcWeightClass');
		$this->lService = new \App\Resource\LengthClass($this->em, 'OcLengthClass');
	}

	private function clean($string) {
		$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens
		return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars
	}

	protected function mergeProduct(OcProduct &$p, $data, $params = array()) {
		$productId = $data['product_id'];

		$allowed = array(
			'copyInventoryFields' => true,
			'images' => true,
			'categories' => true,
			'attributes' => false,
			'options' => false,
			'downloads' => false
		);

		$params = array_merge($allowed, $params);

		// Unset QuickCommerce fields
		unset($data['product_id']);

		if ($params['copyInventoryFields'] == false) {
			unset($data['model']);
			unset($data['sku']);
			unset($data['upc']);
			unset($data['ean']);
			unset($data['jan']);
			unset($data['isbn']);
			unset($data['mpn']);
		}

		unset ($data['seo_url']);

		$data['date_added'] = new DateTime($data['date_added']);
		$data['date_modified'] = new DateTime($data['date_modified']);
		$data['date_available'] = new DateTime($data['date_available']);

		$this->pService->fillEntity($data, $p, false);

		// We can't do this because there's no manufacturer in the import data
		/*$manufacturer = $this->model_catalog_manufacturer->getManufacturer($data['manufacturer_id']);

		if (isset($manufacturer['name']) && !empty($manufacturer['name'])) {
			$m = $this->mService->findEntityByName($manufacturer['name'], false)[0];
			$p->setManufacturer($m);
		}*/

		//Debug::dump($m);

		// TODO: Remove hard-coded entity references! Set in admin instead...
		$productId = $p->getProductId();

		$language = $this->intlService->getEntity(1, false);
		$stock = $this->ssService->getEntity(7, false); // In Stock
		$taxClass = $this->tcService->getEntity(1, false); // Taxable Goods
		$weightClass = $this->wService->getEntity(1, false); // Kilos
		$lengthClass = $this->lService->getEntity(3, false); // Inches

		if ($productId == null || !is_int($productId)) {
			//$store = $sService->getEntity(1, false);
			//$stock_status = $this->getStockStatusByName('In Stock');

			if (empty($p->getSku())) {
				$p->setSku(''); // Cannot be null
			}

			$p->setUpc(''); // Cannot be null
			$p->setEan(''); // Cannot be null
			$p->setJan(''); // Cannot be null
			$p->setIsbn(''); // Cannot be null
			$p->setLocation(''); // Cannot be null

			if ($p->getCost() == null || !(is_numeric(($p->getCost())))) {
				$p->setCost(0.0000);
			}; // Cannot be null

			$p->setStockStatus($stock); // Cannot be null
			$p->setWeightClass($weightClass); // Cannot be null
			$p->setLengthClass($lengthClass); // Cannot be null
			$p->setTaxClass($taxClass); // Cannot be null

			$this->pService->updateEntity($p);

			$pd = new OcProductDescription();

			$this->pdService->fillEntity($data, $pd, false);

			$pd->setName($p->getSku()); // Name in OC is not mapped to QB; we don't want store product titles tied to our QB item name
			$pd->setMetaTitle($p->getSku());
			$pd->setMetaKeyword($p->getSku());
			$pd->setTag('');
			$pd->setMetaDescription($this->clean($data['metaDescription']));
			$pd->setDescription($this->clean($data['metaDescription']));

			$pd->setProduct($p); // No multi-language support in QBO
			$pd->setLanguage($language); // No multi-language support in QBO

			$p->addDescription($pd); // No multi-language support in QBO

			$this->pdService->updateEntity($pd);
		} else {
			if ($p instanceof OcProduct) {
				if ($p->getPrice() == null || !(is_numeric(($p->getPrice())))) {
					$p->setPrice(0.0000);
				}; // Cannot be null

				if ($p->getCost() == null || !(is_numeric(($p->getCost())))) {
					$p->setCost(0.0000);
				}; // Cannot be null

				$this->pService->updateEntity($p);

				$pd = $p->getDescription()->first(); // English only for now

				if (!($pd instanceof OcProductDescription)) {
					$pd = new OcProductDescription();

					$error = 'Cannot get description - product description does not exist';
					var_dump($error);
				}

				$this->pdService->fillEntity($data, $pd, false);

				if ($pd->getDescription() == '') {
					$pd->setDescription($this->clean($data['metaDescription']));
				}

				$pd->setName($p->getSku()); // Name in OC is not mapped to QB; we don't want store product titles tied to our QB item name
				$pd->setMetaTitle($p->getSku());
				$pd->setMetaKeyword($p->getSku());
				$pd->setTag('');
				$pd->setMetaDescription($this->clean($data['metaDescription']));

				$pd->setProduct($p); // Just in case
				$pd->setLanguage($language); // No multi-language support in QBO

				$this->pdService->updateEntity($pd);
			} else {
				$error = 'Cannot get description - product does not exist';
				var_dump($error);
			}
		}

		if ($params['category']) {
			// Categories
			$this->load->model('catalog/category');

			$p->getCategory()->clear(); // Clear associations
			// TODO: Unset categories before adding, or check to see if they exist - commenting this out just to get data transferred
			$categories = $this->model_catalog_product->getProductCategories($productId);
			foreach ($categories as $categoryId) {
				$category = $this->model_catalog_category->getCategory($categoryId);

				unset($category['category_id']);
				$c = $this->cService->findEntityByDescriptionName($category['name'], false)[0];
				$p->addCategory($c);
			}
		}

		/*if ($params['images'] && isset($productId)) {
			$images = $this->model_catalog_product->getProductImages($productId);
			foreach ($images as $image) {
				unset($image['product_image_id']); // Unset PK
				$pi = $this->piService->findOrCreateItem($image);
				$pi->setProduct($p);
				$this->piService->fillEntity($image, $pi, true);
				$this->piService->updateEntity($pi);
			}
		}*/

		/*$discounts = $this->model_catalog_product->getProductDiscounts($productId);
		var_dump($discounts);
		$specials = $this->model_catalog_product->getProductSpecials($productId);
		var_dump($specials);
		$rewards = $this->model_catalog_product->getProductRewards($productId);
		var_dump($rewards);
		$downloads = $this->model_catalog_product->getProductDownloads($productId);
		var_dump($downloads);*/

		if ($params['attributes']) {
			//$p->getAttribute()->clear(); // Clear associations
			$reserved = ['Income Account', 'COGS Account', 'Asset Account'];
			foreach ($p->getAttribute() as $pa) {
				// Don't overwrite accounts
				$name = $pa->getAttribute()->getDescription()->first()->getName();
				if (!in_array($name, $reserved)) {
					$p->removeAttribute($pa); // Delete from collection
					$this->em->remove($pa) ; // Delete record from DB
					// TODO: I can't call $this->paService->deleteEntity($pa), because it isn't designed to handle entities with composite primary keys
					// We can't do a simple find in Doctrine for composite key entities
				}
			}

			$this->em->flush() ; // Persist delete operation

			$productAttributes = $this->model_catalog_product->getProductAttributes($productId);
			//var_dump($productAttributes);
			foreach ($productAttributes as $productAttribute) {
				//unset($attribute['product_attribute_id']); // Unset PK
				$attributeId = $productAttribute['attribute_id'];
				$attribute = $this->model_catalog_attribute->getAttribute($attributeId);

				$a = $this->aService->findEntityByDescriptionName($attribute['name'], false)[0];
				$desc = $productAttribute['product_attribute_description'];
				$lang = (isset($desc[$this->language->getLanguageId()])) ? $desc[$this->language->getLanguageId()] : false;
				$text = ($lang != false && isset($lang['text'])) ? $lang['text'] : null;

				if (isset($a) && isset($text)) {
					$pa = new OcProductAttribute();
					$pa->setAttribute($a);
					$pa->setProduct($p);
					$pa->setLanguage($this->language);
					$pa->setText($text);

					$p->addAttribute($pa);
					$this->paService->updateEntity($pa);
				}
			}
		}

		if ($params['options']) {
			$p->getOption()->clear(); // Clear associations
			$productOptions = $this->model_catalog_product->getProductOptions($productId);
			//var_dump($productOptions);
			foreach ($productOptions as $productOption) {
				//unset($option['product_option_id']); // Unset PK
				$optionId = $productOption['option_id'];
				$option = $this->model_catalog_option->getOption($optionId);
				$o = $this->oService->findEntityByDescriptionName($option['name'], false)[0];

				$ovs = $o->getOptionValues();

				$po = new OcProductOption();

				unset($productOption['product_option_id']);
				$this->poService->fillEntity($productOption, $po, false);

				$po->setOption($o);
				$po->setProduct($p);

				$this->poService->updateEntity($po);

				$povs = $po->getProductOptionValues();

				$optionValueDescriptions = $this->model_catalog_option->getOptionValueDescriptions($productOption['option_id']);
				foreach ($productOption['product_option_value'] as $productOptionValue) {
					$pov = new OcProductOptionValue();
					$this->povService->fillEntity($productOptionValue, $pov, false);

					$optionValueId = $productOptionValue['option_value_id'];
					$optionValue = null;

					foreach ($optionValueDescriptions as $optionValueDescription) {
						if ((int)$optionValueDescription['option_value_id'] == (int)$optionValueId) {
							$optionValueDescription = $optionValueDescription['option_value_description'][$this->language->getLanguageId()];
						}
					}

					$pov->setProductOption($po);
					$pov->setProduct($p);
					$pov->setOption($o);

					foreach ($ovs as $ov) {
						$desc = $ov->getDescription()->first()->getName();
						if ($desc == $optionValueDescription['name']) {
							$pov->setOptionValue($ov);
						}
					}

					$po->addProductOptionValue($pov);

					$this->pService->updateEntity($pov);
				}

				$this->poService->updateEntity($po);

				$p->addOption($po);
			}
		}

		$this->pService->updateEntity($p);
	}

	protected function createProduct($data) {
		// TODO: Inject
		$sService = new \App\Resource\Store($this->em, 'OcStore');
		$pService = new \App\Resource\Product($this->em, 'OcProduct');
		$pdService = new \App\Resource\ProductDescription($this->em, 'OcProductDescription');
		$intlService = new \App\Resource\Language($this->em, 'OcLanguage');
		$ssService = new \App\Resource\StockStatus($this->em, 'OcStockStatus');
		$tcService = new \App\Resource\TaxClass($this->em, 'OcTaxClass');
		$wService = new \App\Resource\WeightClass($this->em, 'OcWeightClass');
		$lService = new \App\Resource\LengthClass($this->em, 'OcLengthClass');

		// TODO: Inject
		//$store = $sService->getEntity(1, false);
		//$stock_status = $this->getStockStatusByName('In Stock');
		$language = $intlService->getEntity(1, false);
		$stock = $ssService->getEntity(7, false); // In Stock
		$taxClass = $tcService->getEntity(13, false); // Taxable Goods
		$weightClass = $wService->getEntity(1, false); // Kilos
		$lengthClass = $lService->getEntity(3, false); // Inches

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

		$p->setStockStatus($stock); // Cannot be null
		$p->setWeightClass($weightClass); // Cannot be null
		$p->setLengthClass($lengthClass); // Cannot be null
		$p->setTaxClass($taxClass); // Cannot be null

		$p->setStatus(0);
	}
	
	public function fetch() {
		$this->init();

		$this->loadMetadata();
		// getMappings is used to map remote entities unlike mapDoctrineEntity and its wrappers which simply convert between OpenCart/OpenCart DB fields and their Doctrine entity equivalents
		$mappings = $this->getMappings($this->foreign); // Get the remote item mappings

		$p = null;
		$data = array();

		// TODO: Pretty sure this is redundant now that we've moved stuff to init
        // Test and delete if we're all good
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

		$file = new \SplFileObject(DIR_QC . 'data/caffetech-lists.iif');

		$reader = new CsvReader($file, "\t");

		// Any rows that contain fewer values than the column headers are simply padded with null values.
		// Any additional values in a row that contain more values than the column headers are ignored.
		$reader->setStrict(false);

		$columns = array();
		$headerOffset = 0;
		$invOffset = 0;
		$i = 0;
		foreach ($reader as $row) {
			if (is_array($row)) {
				if ($row[0] == '!INVITEM') {
					$columns = array_flip($row);
					$headerOffset = $i;
					$reader->setHeaderRowNumber($headerOffset, CsvReader::DUPLICATE_HEADERS_INCREMENT);
				}

				if (count($columns) > 0) {
					if ($row['INVITEMTYPE'] == 'INVENTORY') {
						var_dump($row['INVITEMTYPE']);
						$invOffset = ($i - 1) - $headerOffset;
						break;
					}
				}
			}

			$i++;
		}

		$partTotal = 0;
		$partCount = 0;
		$newPartCount = 0;
		$skipCount = 0;
		$errorCount = 0;
		$noPrefixCount = 0;

		$writer = new CallbackWriter(
			function ($item) use (&$partCount, &$noPrefixCount, &$newPartCount, &$skipCount, &$errorCount, &$partTotal, &$mappings, &$importedIds, &$p, &$pService, &$pdService, &$language, &$stock, &$taxClass, &$weightClass, &$lengthClass) {
				try {
					$fields = $mappings['fields'];
					$data = array();

					foreach (array_intersect_key($item, $fields) as $prop => $value) {
						if (array_key_exists($prop, $fields)) {
							$data[$fields[$prop]] = trim($value);
						}
					}

					// Non parts
					$import = [
						//'DISHWASHER PARTS/PIASTRE',
						'BFC',
						'IGF',
						'SILANOS'
					];

					$sysCodes = SysManufacturerCodes::getConstants();
					$codes = SourceManufacturerCodes::getConstants();;
					$filter = array_keys($sysCodes);

					/*$filter = [
						'BUNN',
						//'IGF',
						//'JURA',
						//'LA_SPAZIALE',
						//'LA_SAN_MARCO',
						//'LA_MARZOCCO',
						//'QUICK_MILL',
						//'RANCILIO',
						///'ROCKET',
						//'SILANOS',
						//'ELEKTRA'
					];*/

					$ignore = [
						'ACCESSORIES',
						'BEZZERA MACH',
						'BREW COF EQU',
						'BREW COF ACCE',
						'SAECO PARTS',
						'USED EQUIPMEN',
						'STEAM PITCHERS / SHOT GL. / CRE',
						'T&S TAPS',
						'SYROP DAVINC',
						'SYRUP CHOCOL',
						'SPOONS',
						'IZZO MACHINES',
						'ROSSI DIMENS',
						'SAECO MACHINE',
						'ELECTRONIC',
						'ELECTRICAL PA',
						'ELEKTRA ESPRESSO MACHINES',
						'FABA Panini Press',
						'FAMILIAR PART',
						'Fittings Green Line',
						'FORLIFE',
						'FLUIDO TECH P',
						'FRANKE COFFEE SYSTEMS',
						'MAZZER MACHINE',
						'MAHLKONIG',
						'KAHLKONIG',
						'MEC PRODUCT',
						'MUSSO GELATO',
						'PAPER CUPS',
						'NEW EQUIPMENT',
						'PASTA MACINES',
						'PAVONI MACHINES',
						'RANCILLIO DOM',
						'Stoelting',
						'SWEDLINGHAUS',
						'TAMPERS',
						'DITTING'
					];

					$ignore = array_map('strtolower', $ignore);

					if (isset($data['model'])) {

						//$partsExpr = '/part{0,1}s{0,1}$/';
						$partsExpr = '/parts$/i';
						// Explode QB name path
						$parts = explode(':', $data['model']);
						// DO PARTS!
						// Match parts
						// Is the current item a sub item of a parts category?
						//if (preg_match($partsExpr, strtolower($parts[0]))) {
						if (preg_match($partsExpr, strtolower($item['ACCNT'])) && !in_array(strtolower(str_replace('  ', ' ',trim($parts[0]))), $ignore)) {
							echo $data['model'] . "\r\n<br>";

							// OK let's filter this down to parts that have matching codes and part names
							$codeExpr = '/^\s{0,}(';


							$codeExpr = $codeExpr . implode('|', $codes);
							$codeExpr .= ')\s+(.+)$/i';

							$buildLikeExpr = function ($mapManf, $string) use ($codes, $sysCodes) {
								$codeExpr = '/^(\s{0,}' . $codes[$mapManf] . ')\s{0,}/i';
								$model = preg_replace($codeExpr, $sysCodes[$mapManf] . ' ', $string);

								$parts = explode(' ', $model, 2);

								$string = '%[' . implode(' ', [$parts[0], str_replace(' ', '%', trim($parts[1]))]) . ']%';
								$string = strtolower($string);

								return $string;
							};

							$getPartsMpn = function ($mapManf, $string) use ($codes, $sysCodes) {
								$codeExpr = '/^(\s{0,}' . $codes[$mapManf] . ')\s{0,}/i';
								$matchExpr = '/^(\s{0,}' . $sysCodes[$mapManf] . ')\s{0,}/i';
								$model = preg_replace($codeExpr, $sysCodes[$mapManf] . ' ', $string);

								if (preg_match($matchExpr, $model)) {
									$parts = explode(' ', $model, 2);
									$string = $parts[1];
								}

								return trim($string);
							};

							$matches = false;

							if (preg_match($codeExpr, $parts[1], $matches)) {
								$reverseCodes = array_flip($codes);
								if (isset($matches[1])) {
									$mapManf = (isset($reverseCodes[strtoupper($matches[1])])) ? $reverseCodes[strtoupper($matches[1])] : false;
									$manf = false;

									if ($mapManf && isset($sysCodes[$mapManf])) {
										$manf = $sysCodes[$mapManf];
									}

									if ($manf && in_array($mapManf, $filter)) {
										// Get all parts with a direct SKU match
										/*$sku = strtolower($manf . '%' . str_replace(' ', '%', $matches[2])); // Take spaces out of the match equation
										$exists = $this->like('product', 'sku', $sku); // TODO: Method defaults maybe?

										if ($exists) {
											//var_dump($sku);
											$data['sku'] = $sku;
											//var_dump($data);
											echo "------ exists -----\r\n<br>";
											var_dump($exists);1a
										} else {
											echo "----- no match ----\r\n<br>";
											//var_dump($data);
											$newPartCount++;
											$partTotal++;
										}*/

										$mpn = $getPartsMpn($mapManf, $parts[1]);
										$data['mpn'] = $mpn;
										$data['sku'] = $sysCodes[$mapManf] . ' ' . $mpn;
										$data['model'] = '[' . $data['sku'] . ']';

										if (preg_match('/^' . $codes[$mapManf] . '\s/i', $parts[1])) {
											// Build like expr, replace old manufacturer code with new code
											$model = $buildLikeExpr($mapManf, $parts[1]); // Take spaces out of the match equation
										} else {
											// Build like expr, replace old manufacturer code with new code
											$model = strtolower('%[' . $sysCodes[$mapManf] . ' ' . str_replace(' ', '%', trim($parts[1])) . '%]%'); // Take spaces out of the match equation
										}

										$exists = $this->like('product', 'model', $model); // TODO: Method defaults maybe?

										if (count($exists) > 1) {
											echo "more than one row returned by like expression\r\n<br>";
											foreach ($exists as $result) {
												var_dump($result['model']);
											}

											$skipCount++;
										} else {
											echo $model . "\r\n<br>";
											if (is_array($exists)) {
												//var_dump($sku);
												echo "manufacturer code prefix matched\r\n<br>";
												echo $item['DESC'] . "\r\n<br>";
												echo "------ exists -----\r\n<br>";

												$where = new \Doctrine\ORM\Query\Expr();
												$where = $where->eq('p.productId', (new \Doctrine\ORM\Query\Expr())->literal((int)$exists[0]['product_id']));

												// Assume a product and description exist
												$results = $this->pService->findWhere('p', $where);
												$p = (is_array($results) && count($results) > 0) ? $results[0] : null;

												if ($p instanceof OcProduct) {
													$params['copyInventoryFields'] = false;
													$this->mergeProduct($p, $data, $params);
												} else {
													// Create new
													$params['copyInventoryFields'] = true;
													$this->mergeProduct((new OcProduct()), $data, $params);
												}

												$partCount++;
												$partTotal++;
											} else {
												echo $item['desc'] . "\r\n<br>";
												echo "----- no match -----\r\n<br>";

												// Create new
												$params['copyInventoryFields'] = true;
												$this->mergeProduct((new OcProduct()), $data, $params);

												$newPartCount++;
												$partTotal++;
											}
										}

										echo "___________________________________________________________________________________________________________________________\r\n<br>";
									} else {
										echo "error! not in filter\r\n<br>";
										var_dump($matches);
										var_dump($reverseCodes[$matches[1]]);
										//var_dump($reverseCodes);
										echo "___________________________________________________________________________________________________________________________\r\n<br>";

										$skipCount++;
									}


									//#select * from oc2_product where model like '[%]%' and (sku is null or sku = '');
									//#update oc2_product set sku = TRIM(TRAILING ']' FROM TRIM(LEADING '[' FROM model)) where model like '[%]%' and (sku is null or sku = '');
									//select * from oc2_product where model like '[%]%';

								} else {
									//echo "no matches or reverse code matches\r\n<br>";
									//var_dump($matches);
									//var_dump($reverseCodes[$matches[1]]);
									//echo "___________________________________________________________________________________________________________________________\r\n<br>";

									$skipCount++;

								}
							} else {
								echo "no prefix detected\r\n<br>";

								if (trim($parts[1]) != '') {
									$model = strtolower('%[' . str_replace(' ', '%', trim($parts[1])) . ']%'); // Take spaces out of the match equation
									$exists = $this->like('product', 'model', $model); // TODO: Method defaults maybe?

									if (count($exists) > 1) {
										echo "more than one row returned by like expression\r\n<br>";
										foreach ($exists as $result) {
											var_dump($result['model']);
										}

										$skipCount++;
									} else {
										echo $model . "\r\n<br>";
										if (is_array($exists)) {
											//var_dump($sku);
											$data['model'] = $model;
											echo "manufacturer code prefix matched\r\n<br>";
											echo "------ exists -----\r\n<br>";

											$where = new \Doctrine\ORM\Query\Expr();
											$where = $where->eq('p.productId', (new \Doctrine\ORM\Query\Expr())->literal($exists[0]['product_id']));

											// Assume a product and description exist
											$results = $this->pService->findWhere('p', $where);
											$p = (is_array($results) && count($results) > 0) ? $results[0] : null;

											if ($p instanceof OcProduct) {
												$params['copyInventoryFields'] = false;
												$this->mergeProduct($p, $data, $params);
											} else {
												// Create new
												$params['copyInventoryFields'] = true;
												$this->mergeProduct((new OcProduct()), $data, $params);
											}

											$partCount++;
											$partTotal++;
										} else {
											echo "----- no match -----\r\n<br>";
											$newPartCount++;
											$partTotal++;
										}
									}

									$noPrefixCount++;

									echo "___________________________________________________________________________________________________________________________\r\n<br>";
								} else {
									echo "parent item or no sub - skip!\r\n<br>";

									echo "___________________________________________________________________________________________________________________________\r\n<br>";

									$skipCount++;
								}

								$noPrefixCount++;
							}

						} elseif (in_array(strtoupper($parts[0]), $import)) {
							// OK let's filter this down to parts that have matching codes and part names
							/*$codeExpr = '/(';

							$codes = SourceManufacturerCodes::getConstants();
							$codeExpr = $codeExpr . implode('|', $codes);
							$codeExpr .= ')\s+(.+)$/';

							$matches = false;

							// Is the product name prefixed with a manufacturer code?
							if (preg_match($codeExpr, $parts[1], $matches)) {
								$reverseCodes = array_flip($codes);

								if (isset($matches[1]) && isset($reverseCodes[$matches[1]])) {
									$sysCodes = SysManufacturerCodes::getConstants();

									$mapManf = $reverseCodes[$matches[1]];
									$manf = (isset($sysCodes[$mapManf])) ? $sysCodes[$mapManf] : null;

									$filter = array_keys($sysCodes);
									//var_dump('FILTER');
									//var_dump($filter);
									//exit;

									if (isset($manf) &&  in_array($mapManf, $filter)) {
										if (preg_match('/^' . $manf . '\s/i', $parts[1])) {
											$model = strtolower('%' . str_replace(' ', '%', trim($parts[1])) . '%'); // Take spaces out of the match equation
										} else {
											$model = strtolower('%' . $manf . '%' . str_replace(' ', '%', trim($parts[1])) . '%'); // Take spaces out of the match equation
										}

										$exists = $this->like('product', 'model', $model); // TODO: Method defaults maybe?

										echo $model . "\r\n<br>";
										if (is_array($exists)) {
											//var_dump($sku);
											$data['model'] = $model;
											echo "manufacturer code prefix matched\r\n<br>";
											echo "------ exists -----\r\n<br>";
											$partCount++;
											$partTotal++;
										} else {
											echo "----- no match -----\r\n<br>";
											$newPartCount++;
											$partTotal++;
										}
									} else {
										echo "manufacturer code " . $mapManf . " not in filter\r\n<br>";
										//var_dump($filter);

										$skipCount++;
									}

									echo "___________________________________________________________________________________________________________________________\r\n<br>";

									//#select * from oc2_product where model like '[%]%' and (sku is null or sku = '');
									//#update oc2_product set sku = TRIM(TRAILING ']' FROM TRIM(LEADING '[' FROM model)) where model like '[%]%' and (sku is null or sku = '');
									//select * from oc2_product where model like '[%]%';

								} else {
									echo "manufacturer code not mapped\r\n<br>";

									$skipCount++;

									echo "___________________________________________________________________________________________________________________________\r\n<br>";
								}
							} else {
								if (trim($parts[1]) != '') {
									$model = strtolower('%' . str_replace(' ', '%', trim($parts[1])) . '%'); // Take spaces out of the match equation
									$exists = $this->like('product', 'model', $model); // TODO: Method defaults maybe?

									echo $model . "\r\n<br>";
									if (is_array($exists)) {
										//var_dump($sku);
										$data['model'] = $model;
										echo "manufacturer code prefix matched\r\n<br>";
										echo "------ exists -----\r\n<br>";
										//var_dump($data);
										//var_dump($exists);
										$partCount++;
										$partTotal++;
									} else {

										echo "----- no match -----\r\n<br>";
										//var_dump($model);
										//var_dump($data);

										$newPartCount++;
										$partTotal++;
									}

									$noPrefixCount++;

									echo "___________________________________________________________________________________________________________________________\r\n<br>";
								} else {
									echo "parent item or no sub - skip!\r\n<br>";
									var_dump($parts);

									echo "___________________________________________________________________________________________________________________________\r\n<br>";

									$skipCount++;
								}
							}*/
						} else {

							//echo "item " . strtoupper($parts[0] . ':' . $parts[1]) . " was not in import list\r\n<br>";

							$skipCount++;

							//echo "___________________________________________________________________________________________________________________________\r\n<br>";
						}
					}

				} catch (Exception $e) {
					throw $e;
				}
			});

		$workflow = new Workflow($reader);
		//self::addDateConverters($workflow); // TODO: Where is this method?
		$workflow->addWriter($writer);
		
		$filter = new OffsetFilter($invOffset, 5000);
		//$filter = new OffsetFilter(0, 10);
		$workflow->addFilter($filter);
		
		$workflow->process();

		echo 'Parts with No Prefix (Currently Skipped):' . $noPrefixCount .  "\r\n";
		echo 'Parts to Update:' . $partCount .  "\r\n";
		echo 'Parts to Add:' . $newPartCount  . "\r\n";
		echo 'Parts Skipped:' . $skipCount  . "\r\n";
		echo 'Total Parts:' . $partTotal . "\r\n";

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
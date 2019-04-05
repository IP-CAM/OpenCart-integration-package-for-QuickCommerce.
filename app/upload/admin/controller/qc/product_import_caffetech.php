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

class ControllerQCProductImportCaffeTech extends ControllerQCProduct {
	protected $tableName = 'qcli_product';
	protected $joinTableName = 'product';
	protected $joinCol = 'product_id';
	protected $foreign = 'Item';
	
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
	
	protected function mergeProduct(OcProduct &$p, $productDb2, $params = array()) {
		$productDb2Id = $productDb2['product_id'];
		
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
		unset($productDb2['product_id']);

		if ($params['copyInventoryFields'] == false) {
			unset($productDb2['model']);
			unset($productDb2['sku']);
			unset($productDb2['upc']);
			unset($productDb2['ean']);
			unset($productDb2['jan']);
			unset($productDb2['isbn']);
			unset($productDb2['mpn']);
		}

		unset ($productDb2['seo_url']);

		$productDb2['date_added'] = new DateTime($productDb2['date_added']);
		$productDb2['date_modified'] = new DateTime($productDb2['date_modified']);
		$productDb2['date_available'] = new DateTime($productDb2['date_available']);

		$this->pService->fillEntity($productDb2, $p, false);
		
		$manufacturerDb2 = $this->model_catalog_manufacturer->getDb2Manufacturer($productDb2['manufacturer_id']);
		//var_dump($manufacturerDb2);

		if (isset($manufacturerDb2['name']) && !empty($manufacturerDb2['name'])) {
			$m = $this->mService->findEntityByName($manufacturerDb2['name'], false)[0];
			$p->setManufacturer($m);
		}

		//Debug::dump($m);

		// TODO: Remove hard-coded entity references! Set in admin instead...
		$productId = $p->getProductId();
		if ($productId == null || !is_int($productId)) {
			//$store = $sService->getEntity(1, false);
			//$stock_status = $this->getStockStatusByName('In Stock');

			$language = $this->intlService->getEntity(1, false);
			$stock = $this->ssService->getEntity(7, false); // In Stock
			$taxClass = $this->tcService->getEntity(1, false); // Taxable Goods
			$weightClass = $this->wService->getEntity(1, false); // Kilos
			$lengthClass = $this->lService->getEntity(3, false); // Inches

			// Create
			$p->setStockStatus($stock); // Cannot be null
			$p->setWeightClass($weightClass); // Cannot be null
			$p->setLengthClass($lengthClass); // Cannot be null
			$p->setTaxClass($taxClass); // Cannot be null

			$this->pService->updateEntity($p);

			$pd = new OcProductDescription();

			$this->pdService->fillEntity($productDb2, $pd, false);

			$pd->setProduct($p); // No multi-language support in QBO
			$pd->setLanguage($language); // No multi-language support in QBO

			$p->addDescription($pd); // No multi-language support in QBO

			$this->pdService->updateEntity($pd);
		} else {
			$pd = $p->getDescription()->first(); // English only for now
			$this->pdService->fillEntity($productDb2, $pd, true);

			$pd->setProduct($p); // Just in case
			$this->pdService->updateEntity($pd);
		}
		
		if ($params['category']) {
			// Categories
			$this->load->model('catalog/category');
			
			$p->getCategory()->clear(); // Clear associations
			// TODO: Unset categories before adding, or check to see if they exist - commenting this out just to get data transferred
			$categoriesDb2 = $this->model_catalog_product->getDb2ProductCategories($productDb2Id);
			foreach ($categoriesDb2 as $categoryDb2Id) {
				$categoryDb2 = $this->model_catalog_category->getDb2Category($categoryDb2Id);

				unset($categoryDb2['category_id']);
				$c = $this->cService->findEntityByDescriptionName($categoryDb2['name'], false)[0];
				$p->addCategory($c);
			}
		}		

		if ($params['images'] && isset($productId)) {
			$imagesDb2 = $this->model_catalog_product->getDb2ProductImages($productDb2Id);
			foreach ($imagesDb2 as $imageDb2) {
				unset($imageDb2['product_image_id']); // Unset PK
				$pi = $this->piService->findOrCreateItem($imageDb2);
				$pi->setProduct($p);
				$this->piService->fillEntity($imageDb2, $pi, true);
				$this->piService->updateEntity($pi);
			}
		}
		
		/*$discountsDb2 = $this->model_catalog_product->getDb2ProductDiscounts($productDb2Id);
		var_dump($discountsDb2);
		$specialsDb2 = $this->model_catalog_product->getDb2ProductSpecials($productDb2Id);
		var_dump($specialsDb2);
		$rewardsDb2 = $this->model_catalog_product->getDb2ProductRewards($productDb2Id);
		var_dump($rewardsDb2);
		$downloadsDb2 = $this->model_catalog_product->getDb2ProductDownloads($productDb2Id);
		var_dump($downloadsDb2);*/
		
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

			$productAttributesDb2 = $this->model_catalog_product->getDb2ProductAttributes($productDb2Id);
			//var_dump($productAttributesDb2);
			foreach ($productAttributesDb2 as $productAttributeDb2) {
				//unset($attributeDb2['product_attribute_id']); // Unset PK
				$attributeDb2Id = $productAttributeDb2['attribute_id'];
				$attributeDb2 = $this->model_catalog_attribute->getDb2Attribute($attributeDb2Id);
				
				$a = $this->aService->findEntityByDescriptionName($attributeDb2['name'], false)[0];
				$desc = $productAttributeDb2['product_attribute_description'];
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
			$productOptionsDb2 = $this->model_catalog_product->getDb2ProductOptions($productDb2Id);
			//var_dump($productOptionsDb2);
			foreach ($productOptionsDb2 as $productOptionDb2) {
				//unset($optionDb2['product_option_id']); // Unset PK
				$optionDb2Id = $productOptionDb2['option_id'];
				$optionDb2 = $this->model_catalog_option->getDb2Option($optionDb2Id);
				$o = $this->oService->findEntityByDescriptionName($optionDb2['name'], false)[0];
				
				$ovs = $o->getOptionValues();
				
				$po = new OcProductOption();

				unset($productOptionDb2['product_option_id']);
				$this->poService->fillEntity($productOptionDb2, $po, false);

				$po->setOption($o);
				$po->setProduct($p);

				$this->poService->updateEntity($po);
				
				$povs = $po->getProductOptionValues();
				
				$optionValueDescriptions = $this->model_catalog_option->getDb2OptionValueDescriptions($productOptionDb2['option_id']);
				foreach ($productOptionDb2['product_option_value'] as $productOptionValue) {
					$pov = new OcProductOptionValue();
					$this->povService->fillEntity($productOptionValue, $pov, false);
					
					$optionValueDb2Id = $productOptionValue['option_value_id'];
					$optionValueDb2 = null;
					
					foreach ($optionValueDescriptions as $optionValueDescription) {
						if ((int)$optionValueDescription['option_value_id'] == (int)$optionValueDb2Id) {
							$optionValueDescriptionDb2 = $optionValueDescription['option_value_description'][$this->language->getLanguageId()];
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
					
					$this->pService->updateEntity($pov);
				}
				
				$this->poService->updateEntity($po);
				
				$p->addOption($po);
			}
		}
		
		$this->pService->updateEntity($p);
	}
	
    /**
     * Merge an OpenCart product from another store with it's local counterpart, creating one if it doesn't exist.
     * This is not the same as the fetch method in ControllerQCProduct, which retrieves and merges in products from
     * QuickBooks Online.
     */
	public function merge() {
		$ids = null;
		$params = array();
		$params['categories'] = $this->request->post['categories'] ? (bool)$this->request->post['categories'] : false;
		$params['images'] = (isset($this->request->post['images'])) ? (bool)$this->request->post['images'] : false;
		$params['attributes'] = (isset($this->request->post['attributes'])) ? (bool)$this->request->post['attributes'] : false;
		$params['options'] = (isset($this->request->post['options'])) ? (bool)$this->request->post['options'] : false;
		$params['downloads'] = (isset($this->request->post['downloads'])) ? (bool)$this->request->post['downloads'] : false;

		//$productIds = $this->request->post['product_id'];
		//$selected = $this->request->post['selected'];
		$ids = $this->mapSelectedArray('product_id', 'selected');
		
		if (!$ids) return false; // No point in continuing yet
		
		$this->init();
		
		$this->loadMetadata();
		// getMappings is used to map remote entities unlike mapDoctrineEntity and its wrappers which simply convert between OpenCart/OpenCart DB fields and their Doctrine entity equivalents
		$mappings = $this->getMappings($this->foreign); // Get the remote item mappings

		$p = null;
		$data = array();

		$importedIds = array();

		//$store = $sService->getEntity(1, false);
		//$stock_status = $this->getStockStatusByName('In Stock');
		$this->language = $this->intlService->getEntity(1, false);
		$this->stock = $this->ssService->getEntity(7, false); // In Stock
		$this->taxClass = $this->tcService->getEntity(13, false); // Taxable Goods
		$this->weightClass = $this->wService->getEntity(1, false); // Kilos
		$this->lengthClass = $this->lService->getEntity(3, false); // Inches
		
		$export = false; // Saves a step later
		
		$this->load->model('catalog/product');
		$this->load->model('catalog/manufacturer');
		$this->load->model('catalog/attribute');
		$this->load->model('catalog/option');
		
		foreach ($ids as $id => $localId) {
			try {
				$productDb2 = $this->model_catalog_product->getDb2Product((int)$id);

				$where = new \Doctrine\ORM\Query\Expr();
				if (empty($localId)) {
					$where = $where->eq('p.model', (new \Doctrine\ORM\Query\Expr())->literal($productDb2['mpn']));
				} else {
					$where = $where->eq('p.productId', (new \Doctrine\ORM\Query\Expr())->literal($localId));
				}

				// Assume a product and description exist
				$results = $this->pService->findWhere('p', $where);
				$p = (is_array($results) && count($results) > 0) ? $results[0] : null;

				if ($p instanceof OcProduct) {
					$params['copyInventoryFields'] = false;
					$this->mergeProduct($p, $productDb2, $params);
				} else {
					// Create new
					$params['copyInventoryFields'] = true;
					$this->mergeProduct((new OcProduct()), $productDb2, $params);
				}

			} catch (Exception $e) {
				throw $e;
			}
		}
	}
	
	// TODO: Get this working
	private function mergeAll() {
		$this->init();
		
		$this->loadMetadata();
		// getMappings is used to map remote entities unlike mapDoctrineEntity and its wrappers which simply convert between OpenCart/OpenCart DB fields and their Doctrine entity equivalents
		$mappings = $this->getMappings($this->foreign); // Get the remote item mappings

		$p = null;
		$data = array();

		$importedIds = array();

		//$store = $sService->getEntity(1, false);
		//$stock_status = $this->getStockStatusByName('In Stock');
		$this->language = $this->intlService->getEntity(1, false);
		$this->stock = $this->ssService->getEntity(7, false); // In Stock
		$this->taxClass = $this->tcService->getEntity(13, false); // Taxable Goods
		$this->weightClass = $this->wService->getEntity(1, false); // Kilos
		$this->lengthClass = $this->lService->getEntity(3, false); // Inches
		
		// TODO: Real transactions and rollback!!! These actions are atomic right now
		$export = false; // Saves a step later
		
		$this->load->model('catalog/product');
		$this->load->model('catalog/manufacturer');
		$this->load->model('catalog/attribute');
		$this->load->model('catalog/option');
		
		$reader = new DoctrineReader($this->em, 'OcProduct');
		$writer = new CallbackWriter(
			function ($item) use (&$importedIds, &$p) {
					try {
						$productDb2 = $this->model_catalog_product->getDb2Products(array('filter_mpn' => $item['model']));
						
						if (is_array($productDb2) && count($productDb2) == 1) {
							$productDb2Id = $productDb2[0]['product_id'];
							$productDb2 = $this->model_catalog_product->getDb2Product($productDb2Id);
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
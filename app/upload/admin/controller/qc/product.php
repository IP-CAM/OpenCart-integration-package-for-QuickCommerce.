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

class ControllerQCProduct extends QCController {
	protected $tableName = 'qcli_product';
	protected $joinTableName = 'product';
	protected $joinCol = 'product_id';
	protected $foreign = 'Item';

	function __construct($registry) {
		parent::__construct($registry);
		parent::before();
	}
    
    private function loadMetadata() {
		$this->pMeta = $this->em->getClassMetadata('OcProduct');
		$this->pdMeta = $this->em->getClassMetadata('OcProductDescription');
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
	
	private static function explodeSubItem($item, $fullyQualified = false) {
		if (!$fullyQualified) {
			$item = explode(':', trim($item));
			return array_pop($item);
		}

		return $item;
	}
    
    protected function buildFilter() {
        if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = null;
		}

		if (isset($this->request->get['filter_price'])) {
			$filter_price = $this->request->get['filter_price'];
		} else {
			$filter_price = null;
		}

		if (isset($this->request->get['filter_quantity'])) {
			$filter_quantity = $this->request->get['filter_quantity'];
		} else {
			$filter_quantity = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['product_category'])) {
			$filter_category_id = $this->request->get['product_category'];
		} else {
			$filter_category_id = null;
		}

		if (isset($this->request->get['filter_match'])) {
			$filter_match = $this->request->get['filter_match'];
		} else {
			$filter_match = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.model';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$start = ($page - 1) * $this->config->get('config_limit_admin') + 1; // Default

		if (isset($this->request->get['records'])) {
			$records = $this->request->get['records'];
			$start = $records + 1; // Adjust start
		} else {
			$records = 0;
		}
        
        $filter_data = array(
			'filter_name'			=> $filter_name,
			'filter_model'			=> $filter_model,
			'filter_price'			=> $filter_price,
			'filter_quantity'		=> $filter_quantity,
			'filter_status'   		=> $filter_status,
			'filter_sub_category'	=> true,
			'filter_category_id'	=> (is_array($filter_category_id) && count($filter_category_id) > 0) ? $filter_category_id[0] : null, // Quick hack to make sure we only pick up one
			'filter_match'			=> $filter_match,
			'sort'					=> $sort,
			'order'					=> $order,
			'records'				=> $records,
			'start'					=> $start,
			'limit'					=> $this->config->get('config_limit_admin')
		);
        
        return $filter_data;
    }
    
    protected function buildQuery() {
        $url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_match'])) {
			$url .= '&filter_match=' . $this->request->get['filter_match'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
        
        return $url;
    }
    
    protected function getFieldNames(&$data = array()) {
        $this->load->language('catalog/product'); // Just in case
        
        $data['entry_name'] = $this->language->get('entry_name');

		$data['entry_qbname'] = $this->language->get('entry_qbname');
		$data['entry_parent'] = $this->language->get('entry_parent');

		$data['entry_model'] = $this->language->get('entry_model');
		$data['entry_price'] = $this->language->get('entry_price');
		$data['entry_quantity'] = $this->language->get('entry_quantity');
		$data['entry_status'] = $this->language->get('entry_status');
    }
    
    protected function getColumnNames(&$data = array()) {
        $this->load->language('catalog/product'); // Just in case
        
        $data['column_image'] = $this->language->get('column_image');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_action'] = $this->language->get('column_action');
    }
    
    /**
     * Note that this method displays a limited result set. 
     * We could, at a later time, implement a solution that leverages the iterateCollection method or something,
     * but there are no plans to do so at this time.
     */
    public function getImportList() {
        $this->init();
        
        $this->loadMetadata();
        
		$this->load->language('catalog/product');

		$this->load->model('catalog/product');

		$url = $this->buildQuery();
        $filter_data = $this->buildFilter();

		$data['add'] = $this->url->link('catalog/product/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['copy'] = $this->url->link('catalog/product/copy', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('catalog/product/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['products'] = array();

		$this->load->model('tool/image');

		$qb_product_total = $this->getCount($filter_data);
		$qb_results = array(); //$this->getCollection($filter_data['start'], $filter_data['limit']);
		$qb_processed = 0;
		// This is not the same as $importItem in fetch and other methods -- we check to see if the record exists before adding it

		$importItem = function (&$item, &$data, &$exclude) {
			$mappings = $this->getMappings($this->foreign); // They're already loaded, just getting the reference
			$p = array();
            $pMeta = $this->pMeta; // Will result in indirect modification of overloaded property notice if provided
			self::importEntity($item, $mappings, $pMeta, $p);

			$process = true;
			if (preg_match('/^PARTS:/', $p['qbname']) == false) { // TODO: Ignore all parts for now, I can't see what's going on there are way too many
				$process = $this->listItemExists('qbname', $p['qbname']);
			}

			if ($process == false) {
				$data[] = $p;
			} else {
				$exclude = true;
			}
		};

		$start = $filter_data['start'];

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$qb_processed = $this->iterateCollection($importItem, $qb_results, $start, $filter_data['limit'], $filter_data, 'Id DESC');
        
        $reader = new ArrayReader($qb_results);
		$writer = new CallbackWriter(
			function ($item) use (&$importedIds, &$data) {
                try {
                    // Product should be tested for a unique email address - we don't want duplicates in OpenCart
                    // That might not be the case in QuickBooks?
                    //$exists = $this->listItemExists('qbname', $item['qbname']); // TODO: Method defaults maybe?
                    // Check to see if a mapped product exists? I don't think it's necessary

                    //if ($exists == false) {
                        $data['db2_products'][] = array(
                            'local_id'   => (isset($result['local_id'])) ? $result['local_id'] : '',
                            'local_model'=> (isset($result['local_model'])) ? $result['local_model'] : '',
                            'product_id' => 0, //$result['product_id'],
                            'image'      => '', //$image,
                            //'name'       => $result['name'],
                            'model'      => $item['model'],
                            'price'      => $item['price'], // TODO: Convert to formatted price?
                            'special'    => '', //$special, // TODO: Convert to formatted price?
                            'quantity'   => $item['quantity'], // TODO: Convert to formatted price?
                            'status'     => '', //($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'), // Disabled products should be included in results anyway
                            //'edit'       => $this->url->link('catalog/qb_product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $result['product_id'] . $url, 'SSL')
                        );                        
                    /*} else {
                        // Product exists -- we need to update the totals and record count
						$exclude = true;
                    }*/
                } catch (Exception $e) {
                    throw $e;
                }
            }
        );
        
        $workflow = new Workflow($reader);
		//self::addDateConverters($workflow); // TODO: Where is this method?
		$workflow->addWriter($writer);
		$workflow->process();
        
        //var_dump($data);
        //exit;
        
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

		// There's no real way to make this paging 100% accurate without wasting a sh**-ton of resources
		// because we don't know if a record actually exists until it's been fetched.
		// To minimize the margin of error, we simply subtract the number of processed records...
		$qb_product_total = $qb_product_total - $qb_processed - $filter_data['records'];
		$qb_pagination = new Pagination();
		$qb_pagination->total = $qb_product_total;
		$qb_pagination->page = $page;
		$qb_pagination->limit = $this->config->get('config_limit_admin');
		$qb_pagination->url = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        // db2 instead of qb so we can reuse template
		$data['db2_pagination'] = $qb_pagination->render();

		$data['db2_results'] = sprintf($this->language->get('text_pagination'), ($qb_product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($qb_product_total - $this->config->get('config_limit_admin'))) ? $qb_product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $qb_product_total, ceil($qb_product_total / $this->config->get('config_limit_admin')));

		$data['db2_records'] = $qb_processed + $filter_data['records'];

		$this->getFieldNames($data);
        $this->getColumnNames($data);

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$data['sort'] = $filter_data['sort'];
		$data['order'] = $filter_data['order'];

		$data['token'] = $this->session->data['token'];

		// Categories
		/*$this->load->model('catalog/category');

		if (isset($this->request->post['product_category'])) {
			$categories = $this->request->post['product_category'];
		} elseif (isset($this->request->get['product_id'])) {
			$categories = $this->model_catalog_product->getDb2ProductCategories($this->request->get['product_id']);
		} else {
			$categories = array();
		}

		$data['product_categories'] = array();

		foreach ($categories as $category_id) {
			$category_info = $this->model_catalog_category->getDb2Category($category_id);

			if ($category_info) {
				$data['product_categories'][] = array(
					'category_id' => $category_info['category_id'],
					'name' => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
				);
			}
		}*/

		$this->response->setOutput($this->load->view('catalog/product_import_list.tpl', $data));
	}

	public function getRelinkList() {
		$this->init();
		
        $this->loadMetadata();

		$this->load->language('catalog/product');

		$this->load->model('catalog/product');

		$url = $this->buildQuery();
		$filter_data = $this->buildFilter();

		$data['add'] = $this->url->link('catalog/product/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['copy'] = $this->url->link('catalog/product/copy', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('catalog/product/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['products'] = array();

		$this->load->model('tool/image');

		$qb_product_total = $this->getCount($filter_data);
		$qb_results = array(); //$this->getCollection($filter_data['start'], $filter_data['limit']);
		$qb_processed = 0;
		// This is not the same as $importItem in fetch and other methods -- we check to see if the record exists before adding it

		$importItem = function (&$item, &$data, &$exclude) {
			$mappings = $this->getMappings($this->foreign); // They're already loaded, just getting the reference
			$p = array();
			$pMeta = $this->pMeta; // Will result in indirect modification of overloaded property notice if provided
			self::importEntity($item, $mappings, $pMeta, $p);

			$process = false;
			//if (preg_match('/^PARTS:/', $p['qbname']) == false) { // TODO: Ignore all parts for now, I can't see what's going on there are way too many
				//$process = $this->listItemExists('qbname', $p['qbname']);
			//}

			if ($process == false) {
				$data[] = $p;
			} else {
				$exclude = true;
			}
		};

		$start = $filter_data['start'];

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$qb_processed = $this->iterateCollection($importItem, $qb_results, $start, $filter_data['limit'], $filter_data, 'Id DESC');

		$reader = new ArrayReader($qb_results);
		$writer = new CallbackWriter(
			function ($item) use (&$importedIds, &$data) {
				try {
					// Product should be tested for a unique email address - we don't want duplicates in OpenCart
					// That might not be the case in QuickBooks?
					//$exists = $this->listItemExists('qbname', $item['qbname']); // TODO: Method defaults maybe?
					// Check to see if a mapped product exists? I don't think it's necessary

					$qbid = 0;
					if ($item['_entity'] instanceof QuickBooks_IPP_Object_Item) {
						$qbid = self::qbId($item['_entity']->getId());
					}

					//if ($exists == false) {
					$data['db2_products'][] = array(
						'local_id'   => (isset($result['local_id'])) ? $result['local_id'] : '',
						'local_model'=> (isset($result['local_model'])) ? $result['local_model'] : '',
						'qbid' 		 => $qbid,
						'image'      => '', //$image,
						//'name'       => $result['name'],
						'model'      => $item['model'],
						'price'      => $item['price'], // TODO: Convert to formatted price?
						'special'    => '', //$special, // TODO: Convert to formatted price?
						'quantity'   => $item['quantity'], // TODO: Convert to formatted price?
						'status'     => '', //($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'), // Disabled products should be included in results anyway
						//'edit'       => $this->url->link('catalog/qb_product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $result['product_id'] . $url, 'SSL')
					);
					/*} else {
                        // Product exists -- we need to update the totals and record count
                        $exclude = true;
                    }*/
				} catch (Exception $e) {
					throw $e;
				}
			}
		);

		$workflow = new Workflow($reader);
		//self::addDateConverters($workflow); // TODO: Where is this method?
		$workflow->addWriter($writer);
		$workflow->process();

		//var_dump($data);
		//exit;

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

		// There's no real way to make this paging 100% accurate without wasting a sh**-ton of resources
		// because we don't know if a record actually exists until it's been fetched.
		// To minimize the margin of error, we simply subtract the number of processed records...
		$qb_product_total = $qb_product_total - $qb_processed - $filter_data['records'];
		$qb_pagination = new Pagination();
		$qb_pagination->total = $qb_product_total;
		$qb_pagination->page = $page;
		$qb_pagination->limit = $this->config->get('config_limit_admin');
		$qb_pagination->url = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		// db2 instead of qb so we can reuse template
		$data['db2_pagination'] = $qb_pagination->render();

		$data['db2_results'] = sprintf($this->language->get('text_pagination'), ($qb_product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($qb_product_total - $this->config->get('config_limit_admin'))) ? $qb_product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $qb_product_total, ceil($qb_product_total / $this->config->get('config_limit_admin')));

		$data['db2_records'] = $qb_processed + $filter_data['records'];

		$this->getFieldNames($data);
		$this->getColumnNames($data);

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$data['sort'] = $filter_data['sort'];
		$data['order'] = $filter_data['order'];

		$data['token'] = $this->session->data['token'];

		// Categories
		/*$this->load->model('catalog/category');

		if (isset($this->request->post['product_category'])) {
			$categories = $this->request->post['product_category'];
		} elseif (isset($this->request->get['product_id'])) {
			$categories = $this->model_catalog_product->getDb2ProductCategories($this->request->get['product_id']);
		} else {
			$categories = array();
		}

		$data['product_categories'] = array();

		foreach ($categories as $category_id) {
			$category_info = $this->model_catalog_category->getDb2Category($category_id);

			if ($category_info) {
				$data['product_categories'][] = array(
					'category_id' => $category_info['category_id'],
					'name' => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
				);
			}
		}*/

		$this->response->setOutput($this->load->view('catalog/product_relink_list.tpl', $data));
	}

	public function getLocalProduct() {
		$this->load->language('catalog/product');
		$this->getColumnNames($data);

		$data['product'] = null;

		if (isset($this->request->get['product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$this->load->model('catalog/product');

			$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);

			if ($product_info != null) {
				$this->load->model('catalog/option');
				$this->load->model('tool/image');

				if (is_file(DIR_IMAGE . $product_info['image'])) {
					$image = $this->model_tool_image->resize($product_info['image'], 80, 80);
				} else {
					$image = $this->model_tool_image->resize('no_image.png', 80, 80);
				}

				$option_data = array();

				// TODO: This is working... might be nice somewhere along the line
				/*$product_options = $this->model_catalog_product->getProductOptions($product_info['product_id']);

				foreach ($product_options as $product_option) {
					$option_info = $this->model_catalog_option->getOption($product_option['option_id']);

					if ($option_info) {
						$product_option_value_data = array();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);

							if ($option_value_info) {
								$product_option_value_data[] = array(
									'product_option_value_id' => $product_option_value['product_option_value_id'],
									'option_value_id'         => $product_option_value['option_value_id'],
									'name'                    => $option_value_info['name'],
									'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
									'price_prefix'            => $product_option_value['price_prefix']
								);
							}
						}

						$option_data[] = array(
							'product_option_id'    => $product_option['product_option_id'],
							'product_option_value' => $product_option_value_data,
							'option_id'            => $product_option['option_id'],
							'name'                 => $option_info['name'],
							'type'                 => $option_info['type'],
							'value'                => $product_option['value'],
							'required'             => $product_option['required']
						);
					}
				}*/

				$special = false;

				$product_specials = $this->model_catalog_product->getProductSpecials($product_info['product_id']);

				foreach ($product_specials  as $product_special) {
					if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
						$special = $product_special['price'];

						break;
					}
				}

				$data['product'] = array(
					'product_id' => $product_info['product_id'],
					'name'       => strip_tags(html_entity_decode($product_info['name'], ENT_QUOTES, 'UTF-8')),
					'model'      => $product_info['model'],
					'option'     => $option_data,
					'price'      => $product_info['price'],
					'special'    => $special,
					'quantity'   => $product_info['quantity'],
					'status'     => ($product_info['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
					'quantity'   => $product_info['quantity'],
					'image'      => $image
				);
			}
		}

		$this->response->setOutput($this->load->view('catalog/product_local_item.tpl', $data));

		//$this->response->addHeader('Content-Type: application/json');
		//$this->response->setOutput(json_encode($json));
	}
	
	// TODO: I can make a generic one of these, just copying for now...

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
	
	public function getTaxStatuses($taxClassId)
	{
		$this->load->model('localisation/tax_class');
		return $this->model_localisation_tax_class->getTaxClasses();
	}
	
	// TODO: This shoud replace the version in the OC product controller?
	/*public function generateSeoUrls() {
		if (!isset($this->request->post['selected'])) {
			return false;
		}


		$aService = new \App\Resource\Account($this->em, 'OcAccount');

		$incomeAccountNum = $this->request->post['qc_income_account'];
		$expenseAccountNum = $this->request->post['qc_cogs_account'];
		$assetAccountNum = $this->request->post['qc_asset_account'];

		$ia = null;
		$ea = null;
		$aa = null;

		if (isset($incomeAccountNum) && is_numeric($incomeAccountNum)) {
			$ia = $aService->getEntity((int)$incomeAccountNum);
		}

		if (isset($expenseAccountNum) && is_numeric($expenseAccountNum)) {
			$ea = $aService->getEntity((int)$expenseAccountNum);
		}

		if (isset($assetAccountNum) && is_numeric($assetAccountNum)) {
			$aa = $aService->getEntity((int)$assetAccountNum);
		}

		foreach ($this->request->post['selected'] as $selected) {
			// Fetch product
			$pService = new \App\Resource\Product($this->em, 'OcProduct');
			$p = $pService->getEntity($selected, false);

			if ($p != null && $p instanceof OcProduct) {
				$this->setProductAccounts($p, null, $incomeAccountNum, $expenseAccountNum, $assetAccountNum);

				$p->setDateModified(new DateTime());
				$pService->updateEntity($p);
			}
		}
	}*/

	public function assignAccounts() {
		if (!isset($this->request->post['selected'])) {
			return false;
		}

		$aService = new \App\Resource\Account($this->em, 'OcAccount');

		$incomeAccountNum = $this->request->post['qc_income_account'];
		$expenseAccountNum = $this->request->post['qc_cogs_account'];
		$assetAccountNum = $this->request->post['qc_asset_account'];

		$ia = null;
		$ea = null;
		$aa = null;

		if (isset($incomeAccountNum) && is_numeric($incomeAccountNum)) {
			$ia = $aService->getEntity((int)$incomeAccountNum);
		}

		if (isset($expenseAccountNum) && is_numeric($expenseAccountNum)) {
			$ea = $aService->getEntity((int)$expenseAccountNum);
		}

		if (isset($assetAccountNum) && is_numeric($assetAccountNum)) {
			$aa = $aService->getEntity((int)$assetAccountNum);
		}

		foreach ($this->request->post['selected'] as $selected) {
			// Fetch product
			$pService = new \App\Resource\Product($this->em, 'OcProduct');
			$p = $pService->getEntity($selected, false);

			if ($p != null && $p instanceof OcProduct) {
				$this->setProductAccounts($p, null, $incomeAccountNum, $expenseAccountNum, $assetAccountNum);

				$p->setDateModified(new DateTime());
				$pService->updateEntity($p);
			}
		}
	}

	// TODO: Immediately refactor!
	protected function setProductAccounts(OcProduct &$product, $item, $incomeAccount = null, $expenseAccount = null, $assetAccount = null) {
		// TODO: I know it's a waste to initialize these over and over, but just trying to get this working for now
		$aService = new \App\Resource\Attribute($this->em, 'OcAttribute');
		$adService = new \App\Resource\AttributeDescription($this->em, 'OcAttributeDescription');
		//$agService = new \App\Resource\AttributeGroup($this->em, 'OcAttributeGroup');
		$paService = new \App\Resource\ProductAttribute($this->em, 'OcProductAttribute');
		$intlService = new \App\Resource\Language($this->em, 'OcLanguage');
		
		$language = $intlService->getEntity(1, false);
		
		$incomeAccount = ($incomeAccount != null) ? $incomeAccount : $item['_entity']->getIncomeAccountRef();
		$expenseAccount = ($expenseAccount != null) ? $expenseAccount : $item['_entity']->getExpenseAccountRef();
		$assetAccount = ($assetAccount != null) ? $assetAccount : $item['_entity']->getAssetAccountRef();
		
		$expenseAccount = (!empty($expenseAccount)) ? self::qbId($expenseAccount) : null;
		$assetAccount = (!empty($assetAccount)) ? self::qbId($assetAccount) : null;
		$incomeAccount = (!empty($incomeAccount)) ? self::qbId($incomeAccount) : null;
		
		$productAttributes = $product->getAttribute();

		$aIncomeAccount = null;
		$aExpenseAccount = null;
		$aAssetAccount = null;

		$ipa = null;
		$epa = null;
		$apa = null;

		// Does the product have attributes?
		if (!empty($productAttributes) && $productAttributes->count() > 0) {

			// Set our account references if they exist
			foreach ($productAttributes as $pa) {
				$a = $pa->getAttribute();
				$ad = $a->getDescription()[0]; // English only for now
				$name = $ad->getName();

				switch ($name) {
					case 'Income Account':
						$aIncomeAccount =& $a;

						// Set income account
						if (!empty($aIncomeAccount)) {
							$this->setProductAttribute($pa, $product, $aIncomeAccount, $language, $incomeAccount);
							$paService->updateEntity($pa);
						}

						break;

					case 'COGS Account':
						$aExpenseAccount =& $a;

						// Set expense account
						if (!empty($aExpenseAccount)) {
							$this->setProductAttribute($pa, $product, $aExpenseAccount, $language, $expenseAccount);
							$paService->updateEntity($pa);
						}

						break;

					case 'Asset Account':
						$aAssetAccount =& $a;

						// Set asset account
						if (!empty($aAssetAccount)) {
							$this->setProductAttribute($pa, $product, $aAssetAccount, $language, $assetAccount);
							$paService->updateEntity($pa);
						}

						break;
				}
			}

			if ($aIncomeAccount == null) {
				$aIncomeAccount = $this->getAttributeByName('Income Account');

				// Set income account
				if (!empty($incomeAccount)) {
					$ipa = new OcProductAttribute();
					$this->setProductAttribute($ipa, $product, $aIncomeAccount, $language, $incomeAccount);

					$product->addAttribute($ipa);
					$paService->updateEntity($ipa);
				}

			}

			if ($aExpenseAccount == null) {
				$aExpenseAccount = $this->getAttributeByName('COGS Account');

				// Set expense account
				if (!empty($expenseAccount)) {
					$epa = new OcProductAttribute();
					$this->setProductAttribute($epa, $product, $aExpenseAccount, $language, $expenseAccount);

					$product->addAttribute($epa);
					$paService->updateEntity($epa);
				}

			}


			if ($aAssetAccount == null) {
				$aAssetAccount = $this->getAttributeByName('Asset Account');

				// Set asset account
				if (!empty($assetAccount)) {
					$apa = new OcProductAttribute();
					$this->setProductAttribute($apa, $product, $aAssetAccount, $language, $assetAccount);

					$product->addAttribute($apa);
					$paService->updateEntity($apa);
				}

			}

		} else {
			$aIncomeAccount = $this->getAttributeByName('Income Account');
			$aExpenseAccount = $this->getAttributeByName('COGS Account');
			$aAssetAccount = $this->getAttributeByName('Asset Account');

			// Set income account
			if (!empty($incomeAccount)) {
				$ipa = new OcProductAttribute();
				$this->setProductAttribute($ipa, $product, $aIncomeAccount, $language, $incomeAccount);

				$product->addAttribute($ipa);
				$paService->updateEntity($ipa);
			}

			// Set expense account
			if (!empty($expenseAccount)) {
				$epa = new OcProductAttribute();
				$this->setProductAttribute($epa, $product, $aExpenseAccount, $language, $expenseAccount);

				$product->addAttribute($epa);
				$paService->updateEntity($epa);
			}

			// Set asset account
			if (!empty($assetAccount)) {
				$apa = new OcProductAttribute();
				$this->setProductAttribute($apa, $product, $aAssetAccount, $language, $assetAccount);

				$product->addAttribute($apa);
				$paService->updateEntity($apa);
			}
		}
	}
	
	protected function setProductAttribute(OcProductAttribute &$productAttribute, OcProduct &$product, OcAttribute &$attribute, OcLanguage &$language, &$value) {
		$productAttribute->setProduct($product);
		$productAttribute->setAttribute($attribute);
		$productAttribute->setLanguage($language);
		$productAttribute->setText($value);
	}
	
	// TODO: This could be moved to AttributeDescription or Attribute service?
	protected function getAttributeByName($name) {
		$adService = new \App\Resource\AttributeDescription($this->em, 'OcAttributeDescription');
		
		$criteria = Criteria::create()
			->where(Criteria::expr()->eq('name', $name)) // TODO: Add language to Criteria
			//->orderBy(array('text' => 'ASC')) // Cannot order by relationed field or this can crash...
			->setFirstResult(0)
			->setMaxResults(1);
			
		$ad = $adService->find($criteria);
		
		return $ad[0]->getAttribute();
	}
	
	/** 
	 * This fetch operation is suited for product import from an XML feed
	 * TODO: Replace product fetch from QBO with something like what's in customer
	 */
	/*public function fetch() {
		$output = [];
		
		$items = $this->getCollection();
		
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
		$mappings = $this->getMappings($this->foreign);
		
		// Returns properties of current node not including... ./*[not(name()=\'Network\')]
		$filtered = EntityMapper::filterEntities($xml, '*[name() = "Item"]');
		
		$data = XML2Array::createArray($filtered); // Just filters crap out
		$data = (!empty($data['entities'])) ? $data['entities']['Item'] : array();
		
		$reader = new ArrayReader($data); // OK for single level
		$workflow = new Workflow($reader);
		$output = [];
		
		$this->load->model('rest/restadmin');		
		
		$workflow->addWriter(new CallbackWriter(function ($row) use ($mappings) {
			$fields = $mappings['fields'];
			$data = array();
			
			foreach (array_intersect_key($row, $fields) as $prop => $value) {
				if (array_key_exists($prop, $fields)) {
					$data[$fields[$prop]] = $value;
				}
			}
			
			$p = ObjectFactory::createEntity($this->em, 'OcProduct', $data);
			$pd = ObjectFactory::createEntity($this->em, 'OcProductDescription', $data);
			
			// Shared value - reassign
			// TODO: Need to make a way to assign the same input to multiple fields... 
			// this is getting wiped out when I do the array flip
			$pd['language_id'] = 1; // No multi-language support in QBO
			$pd['name'] = $p['model'];
			$pd['meta_title'] = $pd['name'];
			$pd['meta_description'] = (isset($pd['description'])) ? $pd['description'] : '';
			$pd['meta_keyword'] = '';
			$pd['tag'] = '';
			
			$stock_status = $this->getStockStatusByName('In Stock');
			$p['stock_status_id'] = (int)$stock_status['stock_status_id']; // TODO: Set based on if quantity exists
			
			$taxClass = $this->getTaxClassByName('Taxable Goods');
			$p['tax_class_id'] = (int)$taxClass['tax_class_id']; // TODO: Set based on if quantity exists
			
			$p['product_description'] = array();
			array_push($p['product_description'], $pd);
			
			// TODO: A way to do this dynamically or at least generically?
			if (isset($row['ParentRef']) && isset($row['ParentRef_name'])) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE model = '" . $row['ParentRef_name'] . "'");
				if (count($query->rows) > 0) {
					$p['parent_id'] = (int)$query->row['product_id'];
				}
			}
			
			//var_dump($p);
			
			$productId = $this->model_rest_restadmin->addProduct($p);
		}));
		
		$workflow->process();
	}*/
    
    /**
     * Merge an OpenCart product from another QuickBooks Online with it's local counterpart, creating one if it doesn't exist.
     * This is not the same as the merge method in ControllerQCProductImport{Variant}, which retrieves and merges in products from
     * another OpenCart installation.
     */
	public function fetch() {
        $this->init();
        
		$this->loadMetadata();
		// getMappings is used to map remote entities unlike mapDoctrineEntity and its wrappers which simply convert between OpenCart/OpenCart DB fields and their Doctrine entity equivalents
		$mappings = $this->getMappings($this->foreign); // Get the remote item mappings

		$p = null;
		$data = array();

		//$items = $this->getCollection();

		$importItem = function (&$item, &$data) {
			$mappings = $this->getMappings($this->foreign); // They're already loaded, just getting the reference
			$p = array();
			self::importEntity($item, $mappings, $this->pMeta, $p);
			$data[] = $p;
		};

		$this->iterateCollection($importItem, $data);
		// TODO: Would be better if I didn't have to deal with huge arrays of data... maybe I can process on the fly?

		// Parent refs on the OC side need to be populated after all the products are imported
		// We can't do this in the same loop because we don't know what order the items are coming in from QuickBooks
		// Store the ids in an array for processing later

		$importedIds = array();

		//$store = $this->sService->getEntity(1, false);
		//$stock_status = $this->getStockStatusByName('In Stock');
		$language = $this->intlService->getEntity(1, false);
		$stock = $this->ssService->getEntity(7, false); // In Stock
		$taxClass = $this->tcService->getEntity(13, false); // Taxable Goods
		$weightClass = $this->wService->getEntity(1, false); // Kilos
		$lengthClass = $this->lService->getEntity(3, false); // Inches

		$reader = new ArrayReader($data);
		$writer = new CallbackWriter(
			function ($item) use (&$importedIds, &$p, &$language, &$stock, &$taxClass, &$weightClass, &$lengthClass) {
					try {
						// Product should be tested for a unique email address - we don't want duplicates in OpenCart
						// That might not be the case in QuickBooks?
						$exists = $this->listItemExists('qbname', $item['qbname']); // TODO: Method defaults maybe?

						if ($exists == false) {
							$date = new DateTime();
							$p = $this->pService->writeItem($item);
							$p->setDateAvailable($date); // TODO: Wtf to do with this? What is OC default? Can I null?
							$p->setDateAdded($date);
							$p->setDateModified($date);

							if (empty($p->getSku())) {
								$p->setSku(''); // Cannot be null
							}

							$p->setUpc(''); // Cannot be null
							$p->setEan(''); // Cannot be null
							$p->setJan(''); // Cannot be null
							$p->setIsbn(''); // Cannot be null
							$p->setMpn(''); // Cannot be null
							$p->setLocation(''); // Cannot be null

							$p->setStockStatus($stock); // Cannot be null
							$p->setWeightClass($weightClass); // Cannot be null
							$p->setLengthClass($lengthClass); // Cannot be null
							$p->setTaxClass($taxClass); // Cannot be null

							$this->pService->updateEntity($p);

							$pd = $this->pdService->writeItem($item);
							$pd->setName($p->getModel()); // Name in OC is not mapped to QB; we don't want store product titles tied to our QB item name

							if (empty($pd->getDescription())) {
								$pd->setDescription(''); // Cannot be null;
							}

							$pd->setMetaTitle($p->getModel());
							$pd->setMetaDescription($pd->getDescription());
							$pd->setMetaKeyword('');
							$pd->setTag('');

							$pd->setProduct($p); // No multi-language support in QBO
							$pd->setLanguage($language); // No multi-language support in QBO

							$p->addDescription($pd); // No multi-language support in QBO

							$this->pdService->updateEntity($pd);

							//$this->db->query("UPDATE " .  DB_PREFIX . "customer SET store_id = '0' WHERE store_id IS NULL"); // MySQL-only fix for "0" store id - no idea how to work around this in Doctrine
							//$this->db->query("UPDATE " .  DB_PREFIX . "customer SET address_id = '0' WHERE address_id IS NULL"); // MySQL-only fix for "0" store id - no idea how to work around this in Doctrine

							
							$this->setProductAccounts($p, $item);
							
							$item['_entity']->setOcId($p->getProductId());
							
							$this->_writeListItem($item['_entity']);

							$id = $p->getProductId();
							//$qbId = self::qbId($item['_entity']->getId()); // TODO: Not needed, remove?
							$parentRefId = self::qbId($item['_entity']->getParentRef());

							if ($parentRefId && $parentRefId > 0) {
								$importedIds[$id] = $parentRefId;
							}
						} else {
							// Update the list item
							$item['_entity']->setOcId($exists);
							$this->_writeListItem($item['_entity']);
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
		foreach ($importedIds as $id => $parentRefId) {
			// Update the parent reference
			$entity = $this->pService->getEntity($id, false);

			$sql = "SELECT oc_entity_id FROM " . DB_PREFIX . $this->tableName . " WHERE feed_id = '" . $parentRefId . "'";

			// Fast nasty and easy, should do this with Doctrine later though, or find a way to decorate the entity with the link table stuff
			$query = $this->db->query($sql);
			$parentId = $query->row['oc_entity_id'];

			if ($parentId) {
				$entity->setParentId($parentId);
				$this->pService->updateEntity($entity);
			}
		}
	}
	
	// Working on it...
	// This is alright for base OpenCart install
	// OC doesn't like nulls... need to set default vals somehow
	public function pull($product, $remote) {
        $this->init();
        
		$this->loadMetadata();
		// getMappings is used to map remote entities unlike mapDoctrineEntity and its wrappers which simply convert between OpenCart/OpenCart DB fields and their Doctrine entity equivalents
		$mappings = $this->getMappings($this->foreign); // Get the remote item mappings

		$data = array();

		// Import the remote entity
		self::importEntity($remote, $mappings, $this->pMeta, $data);

		try {
			$data = array_merge($this->pService->serializeEntity($product, false, array(), array(), false), $data);
		} catch (Exception $e) {
			var_dump($e);
		}

		//$store = $sService->getEntity(1, false);
		//$stock_status = $this->getStockStatusByName('In Stock');
		$language = $this->intlService->getEntity(1, false);
		$stock = $this->ssService->getEntity(7, false); // In Stock

		$taxSuccess = false;
		if ($remote->getSalesTaxCodeRef() != null) {
			try {
				$taxClassId = $this->getOcId(self::qbId($remote->getSalesTaxCodeRef()), 'qcli_tax_code');
				if ($taxClassId) {
					$taxClass = $this->tcService->getEntity($taxClassId, false);
					$taxSuccess = true;
				}
			} catch (Exception $e) {
				//var_dump($e);
			};
		}

		if (!$taxSuccess) {
			$taxClass = $this->tcService->getEntity(1, false); // Taxable Goods
		}

		$weightClass = $this->wService->getEntity(1, false); // Kilos
		$lengthClass = $this->lService->getEntity(3, false); // Inches

		$reader = new ArrayReader(array($data)); // Wrap in an array it so we drop it into the writer
		$writer = new CallbackWriter(
			function ($item) use (&$remote, &$p, &$language, &$stock, &$taxClass, &$weightClass, &$lengthClass) {
					try {
						// Product should be tested for a unique email address - we don't want duplicates in OpenCart
						// That might not be the case in QuickBooks?
						//$exists = $this->exists($this->tableName, 'feed_id', $item['email']); // TODO: Method defaults maybe?

						//if (!$exists) {
							$date = new DateTime();

							$p = $this->pService->writeItem($item, true); // Serialized data was tableized, so set the camelize flag
							$p->setDateAvailable($date); // TODO: Wtf to do with this? What is OC default? Can I null?
							
                            if (empty($p->getDateAdded())) {
                                $p->setDateAdded($date);
                            }
                            
                            if (empty($p->getDateModified())) {
                                $p->setDateModified($date);
                            }
							
							if (empty($p->getSku())) {
								$p->setSku(''); // Cannot be null
							}
							
							$p->setUpc(''); // Cannot be null
							$p->setEan(''); // Cannot be null
							$p->setJan(''); // Cannot be null
							$p->setIsbn(''); // Cannot be null
							
                            if (empty($p->getMpn())) {
								$p->setMpn(''); // Cannot be null
							}
                            
							$p->setLocation(''); // Cannot be null

							$p->setStockStatus($stock); // Cannot be null
							$p->setWeightClass($weightClass); // Cannot be null
							$p->setLengthClass($lengthClass); // Cannot be null
							$p->setTaxClass($taxClass); // Cannot be null

							// Try to get the description
							if ($p->getDescription()->count() > 0) {
								$pd = $p->getDescription()->get(0); // TODO: Multi-language
							} else {
								$pd = $this->pdService->writeItem($item);
							}

							$pd->setName($p->getModel()); // Name in OC is not mapped to QB; we don't want store product titles tied to our QB item name

							if (empty($pd->getDescription())) {
								$pd->setDescription(''); // Cannot be null;
							}

							$pd->setMetaTitle($pd->getName());
							$pd->setMetaDescription(substr($pd->getDescription(), 0, 254)); // TODO: Trim to words
							$pd->setMetaKeyword('');
							$pd->setTag('');

							$pd->setProduct($p); // No multi-language support in QBO
							$pd->setLanguage($language); // No multi-language support in QBO

							$this->pService->updateEntity($p);

							$p->addDescription($pd); // No multi-language support in QBO

							$this->pdService->updateEntity($pd);

							//$this->db->query("UPDATE " .  DB_PREFIX . "customer SET store_id = '0' WHERE store_id IS NULL"); // MySQL-only fix for "0" store id - no idea how to work around this in Doctrine
							//$this->db->query("UPDATE " .  DB_PREFIX . "customer SET address_id = '0' WHERE address_id IS NULL"); // MySQL-only fix for "0" store id - no idea how to work around this in Doctrine
							
							$item['_entity']->setOcId($p->getProductId());
							//$this->_writeListItem($item['_entity']); // TODO: _updateListItem - in case refs were updated
						//} else {
							// If the customer exists, maybe we can do an update instead, if the QBO record is more current than the OC record
							// Just ignore for now
						//}

					} catch (Exception $e) {
						throw $e;
					}
			});

		$workflow = new Workflow($reader);
		//self::addDateConverters($workflow); // TODO: Where is this method?
		$workflow->addWriter($writer);
		$workflow->process();
	}
	
	/**
	 * return QuickBooks_IPP_Object_Item
	 */
	public function get($id = 4, $data = array()) {
		$itemService = new QuickBooks_IPP_Service_Item();

		// Get the existing item
		$items = $itemService->query($this->Context, $this->realm, "SELECT * FROM Item WHERE Id = '" . $id . "'");
		$item = ($items && count($items) > 0) ? $items[0] : null;

		return $item;
	}
	
	/**
	 * @param $productId
     */
	public function add($productId) {
		$this->getMappings($this->foreign);
		$mappings = $this->mappings;

		$this->load->model('catalog/product');
		$this->load->model('localisation/tax_class');
		$this->load->model('localisation/stock_status');
		$data = $this->model_catalog_product->getProduct($productId);

		// TODO: Throw error, QuickBooks won't accept anything without a tax class
		if (!is_array($data) || !count($data) > 0) return false;

		$tax = $this->model_localisation_tax_class->getTaxClass($data['tax_class_id']);
		$stock = $this->model_localisation_stock_status->getStockStatus($data['stock_status_id']);

		$entityService = new QuickBooks_IPP_Service_Item();
		$entity = new QuickBooks_IPP_Object_Item();
		$entity->setOcId($productId);
		$pMeta = $this->em->getClassMetadata('OcProduct');
		$pdMeta = $this->em->getClassMetadata('OcProductDescription');

		$p = ObjectFactory::createEntity($this->em, 'OcProduct', $data, array('taxClass' => $tax, 'stockStatus' => $stock));
		$pd = ObjectFactory::createEntity($this->em, 'OcProductDescription', $data);

		$pd['description'] = (array_key_exists('description', $pd)) ? trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(strip_tags(html_entity_decode($pd['description'])))))) : '';

		// TODO: How am I going to manage conditional mappings?
		if ($p['taxClass']['title'] == 'Taxable Goods') $entity->setTaxable(true);
		//if ($p['stockStatus']['name'] == 'In Stock');
		$entity->setType('Inventory');
		$entity->setTrackQtyOnHand(true); // OpenCart products all have quantities

		$this->fillEntity($entity, $mappings['Item']['fields'], $pMeta, $p); // Populate entity data
		$this->fillEntity($entity, $mappings['Item']['fields'], $pdMeta, $pd); // Populate entity data
		$this->fillEntityRefs($entity, $mappings['Item']['refs'], $p); // Populate entity data

		// TODO: This is an improvement over the previous hardcoding, but there still isn't anything in the QC module admin that allows you to configure which attributes correlate to accounts
		$incomeAcct = $this->model_catalog_product->getProductAttributeByName($productId, 'Accounting', 'Income Account'); // TODO: Must be "Sales of Product Income" account
		$expenseAcct = $this->model_catalog_product->getProductAttributeByName($productId, 'Accounting', 'COGS Account'); // TODO: Must be "Cost of Goods Sold" account
		$assetAcct = $this->model_catalog_product->getProductAttributeByName($productId, 'Accounting', 'Asset Account'); // TODO: Yeah.... drawing a blank here

		// TODO: Validate!
		// If they aren't set, we need to ignore and/or log, because QuickBooks won't accept the entity
		if (isset($incomeAcct['text'])) {
			$entity->setIncomeAccountRef($incomeAcct['text']);
			$entity->setExpenseAccountRef($expenseAcct['text']);
			$entity->setAssetAccountRef($assetAcct['text']);

			$entity->setInvStartDate(date('Y-m-d', strtotime($p['date_added']))); // Quick fix to strip time stuff out which is preventing QBO from saving as Inventory entity

			// TODO: Extend services with export func.
			// I've isolated the code using static helpers right now
			// so it should be pretty easy to move around later
			$this->export($entityService, $entity);
		}

	}
	
	/**
	 * TODO: It's a good time to rewrite this using Doctrine
	 * @param int $productId
	 * @param array $data
     */
	public function edit($product = null, $feedEntity = false) {
		$this->getMappings($this->foreign);
		$mappings = $this->mappings;

		$productId = 0;

		if ($product instanceof OcProduct) {
			$productId = $product->getProductId();
		} elseif (is_numeric($product) && (int)$product > 0) {
			$productId = (int)$product;
		}

		// Load OpenCart models and fetch any required data
		$this->load->model('catalog/product');
		$this->load->model('localisation/tax_class');
		$this->load->model('localisation/stock_status');
		$data = $this->model_catalog_product->getProduct($productId); // TODO: Did I mod the model for this?
		$tax = $this->model_localisation_tax_class->getTaxClass($data['tax_class_id']);
		$stock = $this->model_localisation_stock_status->getStockStatus($data['stock_status_id']);

		// Create the service client
		$entityService = new QuickBooks_IPP_Service_Item();
		$entity = null; //new QuickBooks_IPP_Object_Item();
		$feedId = null;

		// Set our $feedId and $entity variables - this process is repeated in most QCController classes
		// This method handles the log
		$this->setRemoteEntityVars($feedId, $entity, $productId, $feedEntity);

		if ($entity) {
			$entity->setOcId($productId);
			// TODO: Move this metadata stuff to _before so it's always available
			$pMeta = $this->em->getClassMetadata('OcProduct');
			$pdMeta = $this->em->getClassMetadata('OcProductDescription');

			$p = ObjectFactory::createEntity($this->em, 'OcProduct', $data, array('taxClass' => $tax, 'stockStatus' => $stock));
			$pd = ObjectFactory::createEntity($this->em, 'OcProductDescription', $data);

			$pd['description'] = (array_key_exists('description', $pd)) ? trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(strip_tags(html_entity_decode($pd['description'])))))) : '';

			// TODO: How am I going to manage conditional mappings?
			//if ($p['taxClass']['title'] == 'Taxable Goods') $entity->setTaxable(true);
			//if ($p['stockStatus']['name'] == 'In Stock');
			$entity->setType('Inventory');
			$entity->setTrackQtyOnHand(true); // OpenCart products all have quantities

			$this->fillEntity($entity, $mappings['Item']['fields'], $pMeta, $p); // Populate entity data
			$this->fillEntity($entity, $mappings['Item']['fields'], $pdMeta, $pd); // Populate entity data
			$this->fillEntityRefs($entity, $mappings['Item']['refs'], $p);

			// TODO: This is an improvement over the previous hardcoding, but there still isn't anything in the QC module admin that allows you to configure which attributes correlate to accounts
			$incomeAcct = $this->model_catalog_product->getProductAttributeByName($productId, 'Accounting', 'Income Account'); // TODO: Must be "Sales of Product Income" account
			$expenseAcct = $this->model_catalog_product->getProductAttributeByName($productId, 'Accounting', 'COGS Account'); // TODO: Must be "Cost of Goods Sold" account
			$assetAcct = $this->model_catalog_product->getProductAttributeByName($productId, 'Accounting', 'Asset Account'); // TODO: Yeah.... drawing a blank here

			// TODO: Validate!
			// Temp - error!
			if (true) {
				// TODO: Have to detect type or this won't work right
				// If it's an inventory type of item
				/*if (!isset($incomeAcct['text']) || !isset($expenseAcct['text']) || !isset($assetAcct['text'])) {
					throw new Exception('Inventory items must have income, expense and asset accounts assigned!');
				}*/

			}

			if (isset($incomeAcct['text'])) {
				$entity->setIncomeAccountRef($incomeAcct['text']);
			}
			
			if (isset($expenseAcct['text'])) {
				$entity->setExpenseAccountRef($expenseAcct['text']);
			}
			
			if (isset($assetAcct['text'])) {
				$entity->setAssetAccountRef($assetAcct['text']);
			}


			if (empty($entity->getInvStartDate())) {
				$entity->setInvStartDate(date('Y-m-d', strtotime($p['date_added']))); // Quick fix to strip time stuff out which is preventing QBO from saving as Inventory entity
			}


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
			$this->add($productId);
		}
	}

	protected function getService() {
		$service = new \App\Resource\Product($this->em, 'OcProduct');
		return $service;
	}
    
    public function sync() {
		$this->__sync();
	}

	public function getSyncStatuses() {
		$this->__getSyncStatuses();
	}

	public function debugSyncStatuses() {
		$results = array();
		$this->__getSyncStatuses($results);

		$this->sendResponse($results);
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
	public function eventAfterEditProduct($productId) {
		if ($this->quickbooks_is_connected) {
			// Post changes to QBO
			$this->edit($productId);
		} else {
			$errorDetail = array(
				'error' => 'QuickBooks is not connected'
			);

			$this->session->data['ipp_error']['warning'] = $errorDetail;
		}
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
		if ($this->quickbooks_is_connected) {
			// Post product to QBO
			$this->add($productId);
		} else {
			$errorDetail = array(
				'error' => 'QuickBooks is not connected'
			);

			$this->session->data['ipp_error']['warning'] = $errorDetail;
		}
	}

	
	/*public function eventOnDeleteProduct() {
		
	}*/
}
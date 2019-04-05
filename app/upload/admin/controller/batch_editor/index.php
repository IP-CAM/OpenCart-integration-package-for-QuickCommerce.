<?php
class ControllerBatchEditorIndex extends Controller {
	private $error = array ();
	private $text = array ();
	private $json = array ('warning' => '', 'success' => '', 'value' => '', 'count' => '');
	
	public function index() {
		$this->load->model('batch_editor/setting');
		$this->load->model('batch_editor/list');
		
		$this_data['setting'] = $this->model_batch_editor_setting->get('table');
		$this_data['list'] = $this->model_batch_editor_setting->get('list');
		$this_data['option'] = $this->model_batch_editor_setting->get('option');
		unset ($this_data['option']['hash']);
		
		if (!$this_data['setting'] || !$this_data['list'] || !$this_data['option']) {
			if (VERSION < '2.0.0.0') {
				$this->redirect($this->url->link('batch_editor/setting', 'token=' . $this->session->data['token'], 'SSL'));
			} else {
				$this->response->redirect($this->url->link('batch_editor/setting', 'token=' . $this->session->data['token'], 'SSL'));
			}
		}
		
		$this->load->language('batch_editor/index');
		
		$title = str_replace ('{version}', $this->model_batch_editor_setting->getVersion(), $this->language->get('heading_title'));
		
		$this->document->setTitle($title);
		
		$this_data['heading_title'] = $title;
		
		$this_data['tab_general'] = $this->language->get('tab_general');
		$this_data['tab_link'] = $this->language->get('tab_link');
		$this_data['tab_tool'] = $this->language->get('tab_tool');
		
		$this_data['text_seo_generator'] = $this->language->get('text_seo_generator');
		$this_data['text_option_price'] = $this->language->get('text_option_price');
		$this_data['text_search_replace'] = $this->language->get('text_search_replace');
		$this_data['text_image_google'] = $this->language->get('text_image_google');
		$this_data['text_image_google_auto'] = $this->language->get('text_image_google_auto');
		$this_data['text_yandex_translate'] = $this->language->get('text_yandex_translate');
		$this_data['text_rounding_numbers'] = $this->language->get('text_rounding_numbers');
		$this_data['text_lost_image'] = $this->language->get('text_lost_image');
		
		$this_data['text_edit'] = $this->language->get('text_edit');
		$this_data['text_up'] = $this->language->get('text_up');
		$this_data['text_enabled'] = $this->language->get('text_enabled');
		$this_data['text_disabled'] = $this->language->get('text_disabled');
		$this_data['text_language_description'] = $this->language->get('text_language_description');
		$this_data['text_additional'] = $this->language->get('text_additional');
		$this_data['text_no_results'] = $this->language->get('text_no_results');
		
		$this_data['text_quantity_copies'] = $this->language->get('text_quantity_copies');
		$this_data['text_not_contain'] = $this->language->get('text_not_contain');
		$this_data['text_limit'] = $this->language->get('text_limit');
		$this_data['text_column'] = $this->language->get('text_column');
		
		$this_data['text_batch_edit'] = $this->language->get('text_batch_edit');
		$this_data['text_stop'] = $this->language->get('text_stop');
		$this_data['text_pause'] = $this->language->get('text_pause');
		$this_data['text_continue'] = $this->language->get('text_continue');
		$this_data['text_from'] = $this->language->get('text_from');
		
		$this_data['text_product_update'] = $this->language->get('text_product_update');
		$this_data['text_current_main'] = $this->language->get('text_current_main');
		$this_data['text_current_quickly'] = $this->language->get('text_current_quickly');
		
		$this_data['text_load_template'] = $this->language->get('text_load_template');
		$this_data['text_save_template'] = $this->language->get('text_save_template');
		
		$this_data['notice_html_editor'] = $this->language->get('notice_html_editor');
		
		$this_data['button_insert'] = $this->language->get('button_insert');
		$this_data['button_copy'] = $this->language->get('button_copy');
		$this_data['button_remove'] = $this->language->get('button_remove');
		$this_data['button_filter'] = $this->language->get('button_filter');
		$this_data['button_reset'] = $this->language->get('button_reset');
		$this_data['button_cancel'] = $this->language->get('button_cancel');
		$this_data['button_setting'] = $this->language->get('button_setting');
		$this_data['button_clear_cache'] = $this->language->get('button_clear_cache');
		
		$this_data['error_server'] = $this->language->get('error_server');
		$this_data['error_empty_product'] = $this->language->get('error_empty_product');
		
		$this_data['success_edit_product'] = $this->language->get('success_edit_product');
		
		$code = $this->config->get('config_admin_language');
		
		//////////////////////////////////////////////////////////
		$this_data['filter_product'] = array ('product_id');
		$this_data['filter_product_description'] = array ();
		$this_data['edit_field'] = array ();
		
		foreach ($this_data['filter_product'] as $field) {
			$this_data['text_' . $field] = $this->language->get('text_' . $field);
		}
		
		foreach ($this_data['setting'] as $field=>$setting) {
			if (isset ($setting['enable']['filter'])) {
				if ($setting['table'] == 'p' || $setting['table'] == 'ua') {
					$this_data['filter_product'][] = $field;
				}
				
				if ($setting['table'] == 'pd' || $setting['table'] == 'pt') {
					$this_data['filter_product_description'][] = $field;
				}
			}
			
			if (isset ($setting['enable']['main'])) {
				$this_data['edit_field'][$field] = $this_data['setting'][$field];
				
				if (isset ($this_data['list'][$field])) {
					$this_data['edit_field'][$field]['list'] = $this->model_batch_editor_list->{'get' . str_replace ('_', '', $field)}();
				}
			}
			
			if (isset ($setting['text'][$code]) && $setting['text'][$code]) {
				$this_data['text_' . $field] = $setting['text'][$code];
			} else {
				$this_data['text_' . $field] = $this->language->get('text_' . $field);
			}
		}
		$temp = $this->model_batch_editor_setting->get('filter');
		
		$this_data['filter_additional'] = array ();
		
		foreach ($temp as $table => $data) {
			foreach ($data['field'] as $field) {
				if (isset ($data['text'][$field][$code]) && $data['text'][$field][$code]) {
					$text = $data['text'][$field][$code];
				} else {
					$text = 'text_' . $field;
				}
				
				$this_data['filter_additional'][$table][$field] = $text;
			}
		}
		
		$this_data['filter_link'] = array ();
		$this_data['edit_link'] = array ();
		
		$temp = $this->model_batch_editor_setting->get('link');
		
		foreach ($temp as $link=>$setting) {
			if (isset ($setting['enable']['filter']) && $link != 'description') {
				$this_data['filter_link'][] = $link;
			}
			
			if (isset ($setting['enable']['link']) && $link != 'description') {
				$this_data['edit_link'][] = $link;
			}
			
			$this_data['text_' . $link] = $this->language->get('text_' . $link);
			
			foreach ($setting['text'] as $text) {
				$this_data['text_' . $text] = $this->language->get('text_' . $text);
			}
		}
		
		$temp = $this->model_batch_editor_setting->getAdditionalLink();
		
		foreach ($temp as $link=>$setting) {
			if (isset ($setting['enable']['filter'])) {
				$this_data['filter_link'][] = $link;
				
				if (isset ($setting['description'][$code]) && $setting['description'][$code]) {
					$this_data['text_' . $link] = $setting['description'][$code];
				} else {
					$this_data['text_' . $link] = 'text_' . $link;
				}
			}
			
			if (isset ($setting['enable']['link'])) {
				$this_data['edit_link'][] = $link;
				
				if (isset ($setting['description'][$code]) && $setting['description'][$code]) {
					$this_data['text_' . $link] = $setting['description'][$code];
				} else {
					$this_data['text_' . $link] = 'text_' . $link;
				}
			}
		}
		//////////////////////////////////////////////////////////
		
		$this_data['calculate'] = $this->model_batch_editor_list->getCalculate();
		$this_data['languages'] = $this->model_batch_editor_list->getLanguages();
		
		$this_data['breadcrumbs'] = array (
			array (
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link((VERSION < '2.0.0.0') ? 'common/home' : 'common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => ''
			),
			array (
				'text' => $title,
				'href' => $this->url->link('batch_editor/index', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => ' :: '
			)
		);
		
		if (isset ($this->request->post['quantity_copies_products'])) {
			$this_data['quantity_copies_products'] = abs ((int) $this->request->post['quantity_copies_products']);
		} else {
			$this_data['quantity_copies_products'] = 1;
		}
		
		if ($this_data['quantity_copies_products'] == 0) {
			$this_data['quantity_copies_products'] = 1;
		}
		
		$this->load->model('tool/image');
		
		if (VERSION < '2.0.0.0') {
			$no_image = 'no_image.jpg';
		} else {
			$no_image = 'no_image.png';
		}
		
		$this_data['no_image'] = $this->model_tool_image->resize($no_image, $this_data['option']['image']['width'], $this_data['option']['image']['height']);
		
		$this_data['token'] = $this->session->data['token'];
		
		$this_data['language_id'] = (int) $this->config->get('config_language_id');
		
		$this_data['url_cancel'] = $this->url->link((VERSION < '2.0.0.0') ? 'common/home' : 'common/dashboard', 'token=' . $this->session->data['token'], 'SSL');
		$this_data['url_setting'] = $this->url->link('batch_editor/setting', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset ($this->session->data['success'])) {
			$this_data['success'] = $this->session->data['success'];
			unset ($this->session->data['success']);
		} else {
			$this_data['success'] = FALSE;
		}
		
		$this_template = 'batch_editor/index.tpl';
		
		$this->setOutput($this_template, $this_data, true);
	}
	
	public function getFormProductAdd() {
		$this->load->model('batch_editor/list');
		$this->load->language('batch_editor/index');
		
		$this_data['text_none'] = $this->language->get('text_none');
		$this_data['text_edit'] = $this->language->get('text_edit');
		$this_data['text_path'] = $this->language->get('text_path');
		$this_data['text_clear'] = $this->language->get('text_clear');
		$this_data['text_product_add'] = $this->language->get('text_product_add');
		
		$this_data['text_image'] = $this->language->get('text_image');
		$this_data['text_name'] = $this->language->get('text_name');
		$this_data['text_model'] = $this->language->get('text_model');
		$this_data['text_price'] = $this->language->get('text_price');
		
		$this_data['text_manufacturer_id'] = $this->language->get('text_manufacturer_id');
		$this_data['text_tax_class_id'] = $this->language->get('text_tax_class_id');
		$this_data['text_stock_status_id'] = $this->language->get('text_stock_status_id');
		$this_data['text_weight_class_id'] = $this->language->get('text_weight_class_id');
		$this_data['text_length_class_id'] = $this->language->get('text_length_class_id');
		
		$this_data['button_insert'] = $this->language->get('button_insert');
		
		$this_data['languages'] = $this->model_batch_editor_list->getLanguages();
		$this_data['manufacturers'] = $this->model_batch_editor_list->getManufacturerId();
		$this_data['tax_classes'] = $this->model_batch_editor_list->getTaxClassId();
		$this_data['stock_statuses'] = $this->model_batch_editor_list->getStockStatusId();
		$this_data['weight_classes'] = $this->model_batch_editor_list->getWeightClassId();
		$this_data['length_classes'] = $this->model_batch_editor_list->getLengthClassId();
		
		$this->setOutput('batch_editor/ajax/product_add.tpl', $this_data);
	}
	
	public function productAddCopyDelete() {
		$this->load->language('batch_editor/index');
		
		if ($this->validate()) {
			if (isset ($this->request->post['action'])) {
				$action = $this->request->post['action'];
			} else {
				$action = '';
			}
			
			if ($action == 'add') {
				if (isset ($this->request->post['product'])) {
					$product = $this->request->post['product'];
				} else {
					$product = array ();
				}
				
				if (!isset ($product['model']) || !trim ($product['model'])) {
					$this->error['warning'] = $this->language->get('error_product_model');
				}
				
				if (isset ($product['name']) && is_array ($product['name'])) {
					foreach ($product['name'] as $language_id=>$name) {
						if (!trim ($name)) {
							$this->error['warning'] = $this->language->get('error_product_name');
							break;
						}
					}
				} else {
					$this->error['warning'] = $this->language->get('error_product_name');
				}
				
				if (!isset ($this->error['warning'])) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product SET model = '" . $this->db->escape($product['model']) . "', price = '" . (float) $product['price'] . "', image = '" . $this->db->escape($product['image']) . "', manufacturer_id = '" . (int) $product['manufacturer_id'] . "', tax_class_id = '" . (int) $product['tax_class_id'] . "', stock_status_id = '" . (int) $product['stock_status_id'] . "', weight_class_id = '" . (int) $product['weight_class_id'] . "', length_class_id = '" . (int) $product['length_class_id'] . "', date_added = NOW(), date_modified = NOW(), date_available = NOW()");
					$product_id = $this->db->getLastId();
					
					foreach ($product['name'] as $language_id=>$name) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int) $product_id . "', language_id = '" . (int) $language_id . "', name = '" . $this->db->escape($name) . "'");
					}
					
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store (product_id, store_id) VALUES ('" . (int) $product_id . "', '0')");
				}
			}
			
			if ($action == 'copy') {
				if (isset ($this->request->post['quantity'])) {
					$quantity = (int) $this->request->post['quantity'];
				} else {
					$quantity = 1;
				}
				
				if ($quantity < 1) {
					$quantity = 1;
				}
				
				if (isset ($this->request->post['selected']) && is_array ($this->request->post['selected'])) {
					$selected = $this->request->post['selected'];
				} else {
					$selected = array ();
				}
				
				if ($selected) {
					$this->load->model('catalog/product');
					
					for ($i = 0; $i < $quantity; $i++) {
						foreach ($selected as $product_id) {
							$this->model_catalog_product->copyProduct((int) $product_id);
						}
					}
				} else {
					$this->error['warning'] = $this->language->get('error_empty_product');
				}
			}
			
			if ($action == 'delete') {
				if (isset ($this->request->post['selected']) && is_array ($this->request->post['selected'])) {
					$selected = $this->request->post['selected'];
				} else {
					$selected = array ();
				}
				
				if ($selected) {
					$this->load->model('catalog/product');
					$this->load->model('batch_editor/setting');
					
					$option = $this->model_batch_editor_setting->get('option');
					
					$images = array ();
					
					if ($option['product_image_remove']) {
						$query = $this->db->query("SELECT `image` FROM `" . DB_PREFIX . "product` WHERE `product_id` IN ('" . implode ("','", $selected) . "')");
						
						foreach ($query->rows as $value) {
							$images[] = $value['image'];
						}
						
						$query = $this->db->query("SELECT `image` FROM `" . DB_PREFIX . "product_image` WHERE `product_id` IN ('" . implode ("','", $selected) . "')");
						
						foreach ($query->rows as $value) {
							$images[] = $value['image'];
						}
					}
					
					foreach ($selected as $product_id) {
						$this->model_catalog_product->deleteProduct((int) $product_id);
					}
					
					foreach ($images as $image) {
						$query = $this->db->query("SELECT `product_id` FROM `" . DB_PREFIX . "product` WHERE `image` = '" . $this->db->escape($image) . "'");
						
						if ($query->num_rows) {
							continue;
						}
						
						$query = $this->db->query("SELECT `product_id` FROM `" . DB_PREFIX . "product_image` WHERE `image` = '" . $this->db->escape($image) . "'");
						
						if ($query->num_rows) {
							continue;
						}
						
						if (is_file (DIR_IMAGE . $image)) {
							unlink (DIR_IMAGE . $image);
						}
					}
				} else {
					$this->error['warning'] = $this->language->get('error_empty_product');
				}
			}
		}
		
		if (isset ($this->error['warning'])) {
			$this->json['warning'] = $this->error['warning'];
		} else {
			$this->json['success'] = $this->language->get('success_product_' . $action);
		}
		
		echo json_encode ($this->json);
	}
	
	public function editProduct() {
		$this->load->model('batch_editor/setting');
		$this->load->language('batch_editor/index');
		
		if (isset ($this->request->post['selected'])) {
			$data['product_id'] = $this->request->post['selected'];
		} else {
			$data['product_id'] = array ();
		}
		
		if (isset ($this->request->post['batch_edit'])) {
			$data['product_id'] = $this->getEditProductId();
			
			$this->json['count'] = count ($data['product_id']);
		}
		
		if (isset ($this->request->post['field'])) {
			$data['field'] = $this->request->post['field'];
		} else {
			$data['field'] = '';
		}
		
		if (isset ($this->request->post['product_' . $data['field']])) {
			$data['value'] = $this->request->post['product_' . $data['field']];
		} else {
			$data['value'] = '';
		}
		
		if (!$data['product_id']) {
			$this->error['warning'] = $this->language->get('error_empty_product');
		}
		
		if ($data['field'] == 'name' && !$data['value']) {
			$this->error['warning'] = $this->language->get('error_product_name');
		}
		
		if ($data['field'] == 'model' && !$data['value']) {
			$this->error['warning'] = $this->language->get('error_product_model');
		}
		
		if ($this->validate()) {
			$table = $this->model_batch_editor_setting->get('table');
			
			if (isset ($table[$data['field']])) {
				$this->load->model('batch_editor/edit');
				
				$data['setting'] = $table[$data['field']];
				
				$data['value'] = $this->model_batch_editor_edit->Product($data);
			}
			
			$this->cache->delete('product');
		}
		
		$this->json['value'] = $data['value'];
		
		if (isset ($this->error['warning'])) {
			$this->json['warning'] = $this->error['warning'];
		} else {
			$this->json['success'] = $this->language->get('success_edit_product');
		}
		
		echo json_encode ($this->json);
	}
	
	public function getField() {
		$this->load->model('batch_editor/setting');
		
		$this_data['setting'] = array ();
		$this_data['list'] = array ();
		
		if (isset ($this->request->post['data'])) {
			$data = $this->request->post['data'];
		} else {
			$data = array ();
		}
		
		$array = explode ('|', $data);
		
		if (isset ($array[0]) && isset ($array[1])) {
			$table = $array[0];
			$field = $array[1];
		} else {
			return;
		}
		
		if ($table == 'product' && $field == 'url_alias') {
			$this_data['setting'] = $this->model_batch_editor_setting->getTableField('url_alias', 'keyword');
		} else {
			if ($table == 'product_description' && $field == 'tag' && VERSION < '1.5.4') {
				$this_data['setting'] = $this->model_batch_editor_setting->getTableField('product_tag', 'tag');
			} else {
				$this_data['setting'] = $this->model_batch_editor_setting->getTableField($table, $field);
			}
		}
		
		if ($this_data['setting']) {
			$list = $this->model_batch_editor_setting->get('list');
			
			if (isset ($list[$field])) {
				$this->load->model('batch_editor/list');
				
				$this_data['list'] = $this->model_batch_editor_list->{'get' . str_replace ('_', '', $field)}();
				
				if (isset ($list[$field]['zero'])) {
					array_unshift ($this_data['list'], array ($field => 0, 'name' => $this->language->get('text_none')));
				}
			}
			
			unset ($list);
			
			$this_data['table'] = $table;
			$this_data['field'] = $field;
			
			$this->load->language('batch_editor/index');
			
			$this_data['text_range'] = $this->language->get('text_range');
			$this_data['text_value'] = utf8_strtolower ($this->language->get('text_value'));
			$this_data['text_value_by_space'] = $this->language->get('text_value_by_space');
			$this_data['text_duplicate'] = $this->language->get('text_duplicate');
			
			if ($this_data['setting']['type'] == 'tinyint') {
				$this_data['text_no'] = $this->language->get('text_no');
				$this_data['text_yes'] = $this->language->get('text_yes');
				$this_data['text_disabled'] = $this->language->get('text_disabled');
				$this_data['text_enabled'] = $this->language->get('text_enabled');
			}
			
			$this->setOutput('batch_editor/ajax/field.tpl', $this_data);
		}
	}
	
	public function getProduct() {
		$this->load->model('batch_editor/list');
		$this->load->model('batch_editor/setting');
		$this->load->language('batch_editor/index');
		
		if (isset ($this->request->post['filter_template'])) {
			$filter_template = (int) $this->request->post['filter_template'];
			
			$this->request->post = $this->model_batch_editor_setting->get('template/filter/' . $filter_template);
		}
		
		$this_data['setting'] = $this->model_batch_editor_setting->get('table');
		$this_data['option'] = $this->model_batch_editor_setting->get('option');
		$this_data['link'] = $this->model_batch_editor_setting->get('link');
		$this_data['no_edit'] = $this->model_batch_editor_setting->get('no_edit');
		
		$list = $this->model_batch_editor_setting->get('list');
		
		if ($this_data['option']['quick_filter']) {
			if ($this_data['option']['column_categories']) {
				if (isset ($this->request->post['category'][-1]['category_id'])) {
					$this_data['category'][-1]['category_id'] = $this->request->post['category'][-1]['category_id'];
				}
				
				if ($this_data['option']['category']) {
					if (isset ($this->request->post['category'][-1]['name'])) {
						$this_data['category'][-1]['name'] = $this->request->post['category'][-1]['name'];
					}
				} else {
					$this_data['categories'] = $this->model_batch_editor_list->getCategories();
				}
			}
			
			if ($this_data['option']['column_attributes']) {
				if (isset ($this->request->post['attribute'][-1]['attribute_id'])) {
					$this_data['attribute'][-1]['attribute_id'] = $this->request->post['attribute'][-1]['attribute_id'];
				}
				
				if (isset ($this->request->post['attribute'][-1]['name'])) {
					$this_data['attribute'][-1]['name'] = $this->request->post['attribute'][-1]['name'];
				}
			}
			
			if ($this_data['option']['column_options']) {
				if (isset ($this->request->post['option'][-1]['option_id'])) {
					$this_data['option'][-1]['option_id'] = (int) $this->request->post['option'][-1]['option_id'];
				}
				
				if (isset ($this->request->post['option'][-1]['name'])) {
					$this_data['option'][-1]['name'] = $this->request->post['option'][-1]['name'];
				}
			}
			
			$this_data['list'] = array ();
		}
		
		if (isset ($this->request->post['table'])) {
			$this_data['post'] = $this->request->post['table'];
		}
		
		$this_data['text_related_to_product'] = $this->language->get('text_related_to_product');
		$this_data['text_image_google'] = $this->language->get('text_image_google');
		$this_data['text_image_manager'] = $this->language->get('text_image_manager');
		$this_data['text_view'] = $this->language->get('text_view');
		$this_data['text_edit'] = $this->language->get('text_edit');
		$this_data['text_enabled'] = $this->language->get('text_enabled');
		$this_data['text_disabled'] = $this->language->get('text_disabled');
		$this_data['text_yes'] = $this->language->get('text_yes');
		$this_data['text_no'] = $this->language->get('text_no');
		$this_data['text_no_results'] = $this->language->get('text_no_results');
		$this_data['text_path'] = $this->language->get('text_path');
		
		$this_data['button_remove'] = $this->language->get('button_remove');
		$this_data['button_reset'] = $this->language->get('button_reset');
		
		foreach ($this_data['link'] as $link => $data) {
			$this_data['text_' . $link] = $this->language->get('text_' . $link);
		}
		
		$code = $this->config->get('config_admin_language');
		
		$additional_link = $this->model_batch_editor_setting->getAdditionalLink();
		
		foreach ($additional_link as $link => $data) {
			if (isset ($data['description'][$code]) && $data['description'][$code]) {
				$this_data['text_' . $link] = $data['description'][$code];
			} else {
				$this_data['text_' . $link] = 'text_' . $link;
			}
			
			$this_data['link'][$link] = $data;
		}
		
		if (isset ($this->request->post['filter_language_id'])) {
			$this_data['language_id'] = (int) $this->request->post['filter_language_id'];
		} else {
			$this_data['language_id'] = (int) $this->config->get('config_language_id');
		}
		
		$this_data['filter_column'] = array ('name');
		
		if (isset ($this->request->post['filter_column']) && is_array ($this->request->post['filter_column'])) {
			$this_data['filter_column'] = array_merge ($this_data['filter_column'], $this->request->post['filter_column']);
		}
		
		$sql_fields_ = '';
		$sql_tables_ = '';
		$sorts_ = array ();
		
		foreach ($this_data['setting'] as $field => $data) {
			if (in_array ($field, $this_data['filter_column'])) {
				if (isset ($data['text'][$code]) && $data['text'][$code]) {
					$this_data['text_' . $field] = $data['text'][$code];
				} else {
					$this_data['text_' . $field] = $this->language->get('text_' . $field);
				}
				
				if (isset ($list[$field])) {
					if ($this_data['option']['quick_filter']) {
						$this_data['list'][$field] = $this->model_batch_editor_list->{'get' . str_replace ('_', '', $field)}();
						
						if (isset ($list[$field]['zero'])) {
							array_unshift ($this_data['list'][$field], array ($field => 0, 'name' => $this->language->get('text_none')));
						}
					}
					
					$this_data['sort_' . $field] = $sorts_[] = $list[$field]['name'] . '.' . $list[$field]['field'];
					
					$sql_fields_ .= $list[$field]['name'] . "." . $list[$field]['field'] . " AS " . $field . ", ";
					$sql_tables_ .= "LEFT JOIN " . DB_PREFIX . $list[$field]['table'] . " " . $list[$field]['name'] . " ON (p." . $field . " = " . $list[$field]['name'] . "." . $field;
					
					if (isset ($list[$field]['lang'])) {
						$sql_tables_ .= " AND " . $list[$field]['name'] . ".language_id = '" . (int) $this->config->get('config_language_id') . "') ";
					} else {
						$sql_tables_ .= ") ";
					}
					
					$this_data['setting'][$field]['list'] = 1;
				} else {
					if ($field == 'url_alias') {
						if ($this_data['option']['url_alias']) {
							$this_data['sort_' . $field] = $sorts_[] = 'url_alias';
							$sql_fields_ .= "(SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = CONCAT('product_id=', p.product_id) LIMIT 1) AS url_alias, ";
						}
					} else if ($field == 'tag' && VERSION < '1.5.4') {
						$this_data['sort_' . $field] = $sorts_[] = 'tag';
						$sql_fields_ .= "(SELECT GROUP_CONCAT(pt.tag) FROM " . DB_PREFIX . "product_tag pt WHERE pt.product_id = p.product_id AND pt.language_id='" . $this_data['language_id'] . "') AS tag, ";
					} else {
						$this_data['sort_' . $field] = $sorts_[] = $data['table'] . "." . $field;
						$sql_fields_ .= $data['table'] . "." . $field . " AS " . $field . ", ";
					}
				}
			} else {
				unset ($this_data['setting'][$field]);
			}
		}
		
		if (isset ($this->request->post['selected'])) {
			$this_data['product_id'] = $this->request->post['selected'];
		} else {
			$this_data['product_id'] = array ();
		}
		
		if (isset ($this->request->post['limit'])) {
			$this_data['limit'] = abs ((int) $this->request->post['limit']);
		} else {
			$this_data['limit'] = 10;
		}
		
		if (!$this_data['limit']) {
			$this_data['limit'] = 10;
		}
		
		if (isset ($this->request->post['page'])) {
			$this_data['page'] = abs ((int) $this->request->post['page']);
		} else {
			$this_data['page'] = 1;
		}
		
		$this_data['start'] = ($this_data['page'] - 1) * $this_data['limit'];
		
		if ($this_data['start'] < 0) {
			$this_data['start'] = 0;
		}
		
		if (isset ($this->request->post['sort'])) {
			$this_data['sort'] = $this->request->post['sort'];
		} else {
			$this_data['sort'] = 'p.product_id';
		}
		
		if (!in_array ($this_data['sort'], $sorts_)) {
			$this_data['sort'] = 'p.product_id';
		}
		
		if (isset ($this->request->post['order'])) {
			$this_data['order'] = $this->request->post['order'];
		} else {
			$this_data['order'] = 'ASC';
		}
		
		if ($this_data['order'] != 'ASC' && $this_data['order'] != 'DESC') {
			$this_data['order'] = 'ASC';
		}
		
		$data = array (
			'sort'        => $this_data['sort'],
			'order'       => $this_data['order'],
			'start'       => $this_data['start'],
			'limit'       => $this_data['limit'],
			'language_id' => $this_data['language_id'],
			'sql_fields'  => $sql_fields_,
			'sql_tables'  => $sql_tables_
		);
		
		$this->load->model('batch_editor/product');
		
		$this_data['product_total'] = $this->model_batch_editor_product->getTotalProducts($data);
		
		$results = $this->model_batch_editor_product->getProducts($data);
		
		$this->load->model('tool/image');
		
		if (VERSION < '2.0.0.0') {
			$no_image = 'no_image.jpg';
		} else {
			$no_image = 'no_image.png';
		}
		
		$this_data['no_image'] = $this->model_tool_image->resize($no_image, $this_data['option']['image']['width'], $this_data['option']['image']['height']);
		
		$this_data['token'] = $this->session->data['token'];
		
		$this_data['products'] = array ();
		
		$i = 0;
		
		foreach ($results as $result) {
			$query = $this->db->query("SELECT s.url AS `url` FROM `" . DB_PREFIX . "store` s LEFT JOIN `" . DB_PREFIX . "product_to_store` p2s ON (p2s.store_id = s.store_id) WHERE p2s.product_id = '" . $result['product_id'] . "' LIMIT 1");
			
			if ($query->num_rows) {
				$product_url = $query->row['url'] . 'index.php?route=product/product&product_id=' . $result['product_id'];
			} else {
				$product_url = HTTP_CATALOG . 'index.php?route=product/product&product_id=' . $result['product_id'];
			}
			
			$this_data['products'][$i] = array (
				'selected' => in_array ($result['product_id'], $this_data['product_id']),
				'product_id' => $result['product_id'],
				'date_added' => $result['date_added'],
				'date_modified' => $result['date_modified'],
				'product-url' => $product_url
			);
			
			foreach ($this_data['setting'] as $field=>$data) {
				if ($field == 'image') {
					$this_data['products'][$i]['image'] = $result['image'];
					
					if ($result['image'] && file_exists (DIR_IMAGE . $result['image'])) {
						$this_data['products'][$i]['thumb'] = $this->model_tool_image->resize($result['image'], $this_data['option']['image']['width'], $this_data['option']['image']['height']);
					} else {
						$this_data['products'][$i]['thumb'] = $this_data['no_image'];
					}
				} else if ($field == 'url_alias') {
					if ($this_data['option']['url_alias']) {
						$this_data['products'][$i][$field] = $result[$field];
					} else {
						$query = $this->db->query("SELECT `keyword` FROM `" . DB_PREFIX . "url_alias` WHERE `query` = 'product_id=" . $result['product_id'] . "' LIMIT 1");
						
						if ($query->num_rows) {
							$this_data['products'][$i][$field] = $query->row['keyword'];
						} else {
							$this_data['products'][$i][$field] = '';
						}
					}
				} else {
					if ($data['type'] == 'char' || $data['type'] == 'varchar' || $data['type'] == 'text') {
						$this_data['products'][$i][$field] = html_entity_decode ($result[$field], ENT_QUOTES, 'UTF-8');
					} else {
						$this_data['products'][$i][$field] = $result[$field];
					}
				}
			}
			
			$i++;
		}
		
		$pagination = new Pagination();
		$pagination->total = $this_data['product_total'];
		$pagination->page = $this_data['page'];
		$pagination->limit = $this_data['limit'];
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = '{page}';
		
		$this_data['pagination'] = $pagination->render();
		
		if (VERSION >= '2.0.0.0') {
			$this_data['results'] = sprintf($this->language->get('text_pagination'), ($this_data['product_total']) ? (($this_data['page'] - 1) * $this_data['limit']) + 1 : 0, ((($this_data['page'] - 1) * $this_data['limit']) > ($this_data['product_total'] - $this_data['limit'])) ? $this_data['product_total'] : ((($this_data['page'] - 1) * $this_data['limit']) + $this_data['limit']), $this_data['product_total'], ceil($this_data['product_total'] / $this_data['limit']));
		}
		
		$this->setOutput('batch_editor/ajax/product.tpl', $this_data);
	}
	
	public function getLink() {
		$this->load->model('batch_editor/setting');
		$this->load->model('batch_editor/data');
		$this->load->model('batch_editor/list');
		
		$this->load->language('batch_editor/index');
		
		$this_data['text_no_results'] = $this->language->get('text_no_results');
		$this_data['text_save_template'] = $this->language->get('text_save_template');
		$this_data['text_load_template'] = $this->language->get('text_load_template');
		
		$this_data['button_insert'] = $this->language->get('button_insert');
		$this_data['button_remove'] = $this->language->get('button_remove');
		$this_data['button_save'] = $this->language->get('button_save');
		
		$this_data['button_insert_sel'] = $this->language->get('button_insert_sel');
		$this_data['button_delete_sel'] = $this->language->get('button_delete_sel');
		
		$this_data['button_add_to_filter'] = $this->language->get('button_add_to_filter');
		$this_data['button_remove_from_filter'] = $this->language->get('button_remove_from_filter');
		
		$this_data['button_next'] = $this->language->get('button_next');
		$this_data['button_prev'] = $this->language->get('button_prev');
		$this_data['button_close'] = $this->language->get('button_close');
		$this_data['button_copy'] = $this->language->get('button_copy');
		
		$this_data['text_yes'] = $this->language->get('text_yes');
		$this_data['text_no'] = $this->language->get('text_no');
		$this_data['text_enabled'] = $this->language->get('text_enabled');
		$this_data['text_disabled'] = $this->language->get('text_disabled');
		$this_data['text_none'] = str_replace ('-', '', $this->language->get('text_none'));
		$this_data['text_insert'] = $this->language->get('text_insert');
		$this_data['text_edit'] = $this->language->get('text_edit');
		$this_data['text_main'] = $this->language->get('text_main');
		$this_data['text_not_contain'] = $this->language->get('text_not_contain');
		$this_data['text_strictly_selected'] = $this->language->get('text_strictly_selected');
		$this_data['text_with_regard_number'] = $this->language->get('text_with_regard_number');
		
		$this_data['notice_empty_field'] = $this->language->get('notice_empty_field');
		
		if (isset ($this->request->post['link'])) {
			$link = $this->request->post['link'];
		} else {
			$link = '';
		}
		
		if (isset ($this->request->post['product_id'])) {
			$this_data['product_id'] = (int) $this->request->post['product_id'];
		} else {
			$this_data['product_id'] = 0;
		}
		
		$this_data['data'] = array ();
		$this_data['names'] = array ();
		
		$this_data['setting']['option'] = $this->model_batch_editor_setting->get('option');
		
		if ($this_data['product_id'] > 0) {
			$this->load->model('tool/image');
			
			$product_image = $this->model_batch_editor_data->getProductImage_($this_data['product_id']);
			$this_data['product_name'] = $this->model_batch_editor_data->getProductName_($this_data['product_id']);
			$this_data['product_image'] = $this->model_tool_image->resize($product_image, $this_data['setting']['option']['image']['width'], $this_data['setting']['option']['image']['height']);
		}
		
		// Временный костыль
		if ($link == 'category') {
			$this_data['text_category'] = $this->language->get('text_category');
			$this_data['field_category_id'] = $this->language->get('text_category');
			$this_data['field_main_category'] = $this->language->get('text_main_category');
			
			$this_data['fields'] = $this->model_batch_editor_setting->getTableField('product_to_category');
			
			$array_id = array ();
			$data_temp = array ();
			$this_data['main_category'] = 0;
			
			if ($this_data['product_id'] > 0) {
				$this_data['data'] = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = " . (int) $this_data['product_id'])->rows;
				
				foreach ($this_data['data'] as $key => $array) {
					foreach ($array as $field=>$value) {
						if ($field != 'product_id' && $this_data['fields'][$field]['key'] == 'PRI') {
							$array_id[] = (int) $value;
						}
						
						if ($field == 'main_category' && $array['main_category']) {
							$this_data['main_category'] = $array['category_id'];
						}
					}
					
					$data_temp[$array['category_id']] = $array;
				}
				
				$this_data['names'] = $this->model_batch_editor_list->getCategoryName(array ('array_id' => $array_id));
			}
			
			$this_data['link'] = $link;
			
			if (isset ($this_data['setting']['option']['category']) && $this_data['setting']['option']['category']) {
				$this_template = 'batch_editor/link/autocomplete.tpl';
			} else {
				$this_data['data'] = $data_temp;
				$this_data['list'] = $this->model_batch_editor_list->getCategories();
				$this_template = 'batch_editor/link/list.tpl';
			}
			
			$this->setOutput($this_template, $this_data);
			
			return;
		}
		// Временный костыль
		
		$code = $this->config->get('config_admin_language');
		
		$_link_ = $this->model_batch_editor_setting->get('link');
		
		if (isset ($_link_[$link]) && $link != 'special' && $link != 'discount') {
			$this_data['main_category'] = 0;
			
			if ($this_data['product_id'] > 0) {
				if (isset ($_link_[$link]['func'])) {
					$this_data['data'] = $this->model_batch_editor_data->{'getProduct' . $link}($this_data['product_id']);
					
					if ($link == 'description') {
						$this_data['table'] = $this->model_batch_editor_setting->get('table');
						
						foreach ($this_data['table'] as $field=>$data) {
							if ($data['table'] == 'pd' || $data['table'] == 'pt') {
								if (isset ($data['text'][$code]) && $data['text'][$code]) {
									$this_data['text_' . $field] = $data['text'][$code];
								} else {
									$this_data['text_' . $field] = $this->language->get('text_' . $field);
								}
							} else {
								unset ($this_data['table'][$field]);
							}
						}
					}
				} else {
					$this->load->model('catalog/product');
					
					if ($link == 'category') {
						$this_data['data'] = $this->model_catalog_product->getProductCategories($this_data['product_id']);
						
						if ($this_data['setting']['option']['main_category']) {
							$data = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id = " . $this_data['product_id'] . " AND main_category = 1")->row;
							
							if (isset ($data['category_id'])) {
								$this_data['main_category'] = $data['category_id'];
							} else {
								$this_data['main_category'] = 0;
							}
						}
					} else {
						$this_data['data'] = $this->model_catalog_product->{'getProduct' . $link . 's'}($this_data['product_id']);
					}
				}
			}
			
			$this_data['text_' . $link] = $this->language->get('text_' . $link);
			
			foreach ($_link_[$link]['text'] as $text) {
				$this_data['text_' . $text] = $this->language->get('text_' . $text);
			}
			
			if ($_link_[$link]['list']) {
				$this->load->model('batch_editor/list');
				
				foreach ($_link_[$link]['list'] as $list) {
					$this_data[$list] = $this->model_batch_editor_list->{'get' . str_replace ('_', '', $list)} ();
				}
				
				$this_data['discount_actions'] = $this->model_batch_editor_list->getDiscountActions();
			}
			
			if ($link == 'option') {
				$fields = $this->model_batch_editor_setting->getTableField('product_option_value');
				
				if (isset ($fields['base_price'])) {
					$this_data['base_price'] = true;
					
					$this_data['text_base_price'] = $this->language->get('text_base_price');
				} else {
					$this_data['base_price'] = false;
				}
				
				if (isset ($fields['quantity_foo_rashod'])) {
					$this_data['quantity_foo_rashod'] = true;
					
					$this_data['text_quantity_foo_rashod'] = $this->language->get('text_quantity_foo_rashod');
				} else {
					$this_data['quantity_foo_rashod'] = false;
				}
				
				$this_data['option_price_prefix'] = $this_data['setting']['option']['option_price_prefix'];
				$this_data['option_type'] = $this_data['setting']['option']['option_type'];
			}
			
			$this_template = 'batch_editor/catalog/' . $link . '.tpl';
		} else {
			unset ($_link_);
			
			$additional_link = $this->model_batch_editor_setting->getAdditionalLink();
			
			// Временный костыль
			if ($link == 'special' || $link == 'discount') {
				$additional_link['special'] = array ('table' => 'product_special');
				$additional_link['discount'] = array ('table' => 'product_discount');
				
				$this_data['list']['customer_group_id'] = $this->model_batch_editor_list->getCustomerGroups();
			}
			
			if ($link == 'product_shipping') {
				$this->load->model('module/productshipping');
				
				$this_data['list']['extension_id'] = $this->model_module_productshipping->get_shippings_with_names();
			}
			
			// Временный костыль
			
			if (!isset ($additional_link[$link])) {
				return;
			}
			
			$data = $additional_link[$link];
			unset ($additional_link);
			
			if (isset ($data['description'][$code]) && $data['description'][$code]) {
				$this_data['text_' . $link] = $data['description'][$code];
			} else {
				$this_data['text_' . $link] = $this->language->get('text_' . $link);
			}
			
			$this_data['fields'] = $this->model_batch_editor_setting->getTableField($data['table']);
			
			if (isset ($this_data['fields']['language_id'])) {
				$this_data['languages'] = $this->model_batch_editor_list->getLanguages();
			}
			
			if ($this_data['product_id'] > 0) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . $data['table'] . " WHERE product_id = " . (int) $this_data['product_id']);
				
				$this_data['data'] = $query->rows;
			}
			
			$integer = array ();
			$primary_key = array ();
			$auto_increment = true;
			$template = 'standart';
			
			$this->text = $this->model_batch_editor_setting->get('language/' . $code . '/field');
			
			foreach ($this_data['fields'] as $field => $setting) {
				$this_data['field_' . $field] = $this->getLanguage('field_' . $field);
				
				// Временный костыль
				if ($this_data['field_' . $field] == 'field_' . $field) {
					$this_data['field_' . $field] = $this->language->get('field_' . $field);
				}
				// Временный костыль
				
				if ($setting['type'] == 'int') {
					$integer[] = $field;
				}
				
				if ($setting['key'] == 'PRI') {
					$primary_key[] = $field;
				}
				
				if ($setting['extra'] == 'auto_increment') {
					$auto_increment = true;
				}
			}
			
			if (count ($primary_key) == 2 && in_array ('language_id', $primary_key)) {
				$template = 'language';
				
				$data = array ();
				
				foreach ($this_data['data'] as $array) {
					foreach ($array as $field=>$value) {
						$data[$array['language_id']][$field] = $value;
					}
				}
				
				$this_data['data'] = $data;
			}
			
			if ((count ($primary_key) == 2 && count ($this_data['fields']) == 2) || $link == 'coupon_product') {
				$template = 'autocomplete';
				$array_id = array ();
				
				foreach ($this_data['data'] as $key => $array) {
					foreach ($array as $field => $value) {
							if (($field != 'product_id') && ($this_data['fields'][$field]['key'] == 'PRI' || $this_data['fields'][$field]['extra'] != 'auto_increment')) {
								$array_id[] = (int) $value;
							}
						
					}
				}
				
				if ($array_id) {
					// Временный костыль
					if ($link == 'category') {
						$this_data['names'] = $this->model_batch_editor_list->getCategoryName(array ('array_id' => $array_id));
					} else if ($link == 'coupon_product') {
						$this_data['names'] = $this->model_batch_editor_list->getCouponName(array ('array_id' => $array_id));
					} else if ($link == 'sizechart_to_product') {
						$this_data['names'] = $this->model_batch_editor_list->getSizeChartName(array ('array_id' => $array_id));
					} else {
						$this_data['names'] = $this->model_batch_editor_list->getProductName(array ('array_id' => $array_id));
					}
					// Временный костыль
				}
			}
			
			if ($template == 'standart' && isset ($this_data['fields']['price'])) {
				$this_data['actions'] = $this->model_batch_editor_list->getDiscountActions();
			}
			
			$this_template = 'batch_editor/link/' . $template . '.tpl';
		}
		
		$this_data['link'] = $link;
		
		$this->setOutput($this_template, $this_data);
	}
	
	public function editLink() {
		$this->load->language('batch_editor/index');
		$this->load->model('batch_editor/setting');
		
		$_link_ = $this->model_batch_editor_setting->get('link');
		
		if (isset ($this->request->post['link'])) {
			$link = $this->request->post['link'];
		} else {
			$link = '';
		}
		
		if (isset ($this->request->post[$link])) {
			$data['data'] = $this->request->post[$link];
		} else {
			$data['data'] = array ();
		}
		
		if (isset ($this->request->post['action'])) {
			$data['action'] = $this->request->post['action'];
		} else {
			$data['action'] = '';
		}
		
		if (isset ($this->request->post['selected'])) {
			$data['product_id'] = $this->request->post['selected'];
		} else {
			$data['product_id'] = array ();
		}
		
		if (isset ($this->request->post['batch_edit'])) {
			$data['product_id'] = $this->getEditProductId();
			
			$this->json['count'] = count ($data['product_id']);
		}
		
		if (!$data['product_id']) {
			$this->error['warning'] = $this->language->get('error_empty_product');
		}
		
		if ($link == 'attribute') {
			foreach ($data['data'] as $attribute) {
				if (!isset ($attribute['name']) || !$attribute['name']) {
					$this->error['warning'] = $this->language->get('error_empty_attribute_name');
					break;
				}
			}
		}
		
		if ((!$data['data']) && ($data['action'] == 'add' || $data['action'] == 'del')) {
			if ($link == 'related') {
				$this->error['warning'] = $this->language->get('error_empty_related');
			} else {
				$this->error['warning'] = $this->language->get('error_empty_data_' . $data['action']);
			}
		}
		
		if (!$data['data'] && $link == 'ocfilter') {
			$this->error['warning'] = $this->language->get('error_empty_option');
		}
		
		if ($link == 'description') {
			$validate = 'validateForm';
			$data['table'] = $this->model_batch_editor_setting->get('table');
			
			foreach ($data['table'] as $field=>$value) {
				if ($value['table'] != 'pd') {
					unset ($data['table'][$field]);
				}
			}
		} else {
			$validate = 'validate';
			$data['table'] = array ();
		}
		
		if ($this->{$validate}()) {
			$this->load->model('batch_editor/edit');
			
			if ($link == 'special' || $link == 'discount') {
				// Временный костыль
				$data['table'] = 'product_' . $link;
				$this->model_batch_editor_edit->link($data);
			} else if ($link == 'category') {
				$data['table'] = 'product_to_' . $link;
				$this->model_batch_editor_edit->link($data);
				// Временный костыль
			} else {
				if (isset ($_link_[$link]) || $link == 'copy' || $link == 'delete') {
					$this->model_batch_editor_edit->{$link}($data);
				} else {
					$data['table'] = $link;
					$this->model_batch_editor_edit->link($data);
				}
			}
			
			$this->cache->delete('product');
		}
		
		if (isset ($this->error['warning'])) {
			$this->json['warning'] = $this->error['warning'];
		} else {
			$this->json['success'] = $this->language->get('success_edit_product');
		}
		
		echo json_encode ($this->json);
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'batch_editor/index')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		//$this->validateDomain();
		return (!$this->error) ? TRUE : FALSE;
	}
	
	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'batch_editor/index')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		foreach ($this->request->post['description'] as $language_id=>$value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_product_name');
			}
		}
		
		if ($this->error && !isset ($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_validate_form');
		}
		
		//$this->validateDomain();
		return (!$this->error) ? TRUE : FALSE;
	}
	
	private function validateDomain() {
		$this->load->model('batch_editor/setting');
		
		$option = $this->model_batch_editor_setting->get('option');
		
		if (!isset ($option['hash']) || $option['hash'] != $this->model_batch_editor_setting->getHash()) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
	}
	
	private function getLanguage($text) {
		if (isset ($this->text[$text])) {
			return $this->text[$text];
		} else {
			return $text;
		}
	}
	
	public function setEditProductId() {
		$this->load->model('batch_editor/product');
		$this->load->model('batch_editor/setting');
		
		$product_id = array ();
		
		$count = 0;
		
		if (isset ($this->request->post['type'])) {
			$type = $this->request->post['type'];
		} else {
			$type = 'filter';
		}
		
		if ($type == 'filter') {
			if (isset ($this->request->post['filter_language_id'])) {
				$data['language_id'] = (int) $this->request->post['filter_language_id'];
			} else {
				$data['language_id'] = (int) $this->config->get('config_language_id');
			}
			
			$array = $this->model_batch_editor_product->getProductId($data);
			
			foreach ($array as $value) {
				$product_id[] = $value['product_id'];
				
				$count++;
			}
		} else {
			if (isset ($this->request->post['selected']) && is_array ($this->request->post['selected'])) {
				$product_id = $this->request->post['selected'];
				
				$count = count ($product_id);
			}
		}
		
		$this->model_batch_editor_setting->set('temp/product_id.' . $this->session->data['token'], $product_id);
		
		$this->json['count'] = $count;
		
		echo json_encode ($this->json);
	}
	
	public function productCopyData() {
		$this->load->model('batch_editor/edit');
		$this->load->model('batch_editor/setting');
		$this->load->language('batch_editor/index');
		
		if (isset ($this->request->post['link'])) {
			$link = (string) $this->request->post['link'];
		} else {
			return false;
		}
		
		$link_array = $this->model_batch_editor_setting->get('link');
		
		if (isset ($link_array[$link])) {
			$data['table'] = $link_array[$link]['table'];
		} else {
			$link_array = $this->model_batch_editor_setting->get('link/' . $link);
			
			if ($link_array) {
				$data['table'] = $link;
			} else {
				return false;
			}
		}
		
		if (isset ($this->request->post['copy_product_id'])) {
			$data['copy_product_id'] = abs ((int) $this->request->post['copy_product_id']);
		} else {
			$data['copy_product_id'] = 0;
		}
		
		if (isset ($this->request->post['selected']) && is_array ($this->request->post['selected'])) {
			$data['product_id'] = $this->request->post['selected'];
		} else {
			$data['product_id'] = array ();
		}
		
		if (!$data['product_id']) {
			$this->json['warning'] = $this->language->get('error_empty_product');
		}
		
		if (!$data['copy_product_id']) {
			$this->json['warning'] = $this->language->get('error_empty_product_copy');
		}
		
		if (!$this->json['warning'] && $this->validate()) {
			if ($link == 'option') {
				$this->model_batch_editor_edit->copyProductOption($data);
			} else {
				$this->model_batch_editor_edit->copyProductData($data);
			}
			
			$this->json['success'] = $this->language->get('success_edit_product');
		}
		
		echo json_encode ($this->json);
	}
	
	private function getEditProductId($limit = 100) {
		$this->load->model('batch_editor/setting');
		
		$product_id = array ();
		
		$data = $this->model_batch_editor_setting->get('temp/product_id.' . $this->session->data['token']);
		
		$count = 0;
		
		foreach ($data as $index=>$value) {
			$product_id[] = $value;
			
			unset ($data[$index]);
			
			$count++;
			
			if ($count == $limit) {
				break;
			}
		}
		
		$this->model_batch_editor_setting->set('temp/product_id.' . $this->session->data['token'], $data);
		
		return $product_id;
	}
	
	private function setOutput($template, $data, $children = false) {
		if (VERSION < '2.0.0.0') {
			$this->data = $data;
			$this->template = $template;
			
			if ($children) {
				$this->children = array ('common/header', 'common/footer');
			}
			
			$this->response->setOutput($this->render());
		} else {
			if ($children) {
				$data['header'] = $this->load->controller('common/header');
				$data['column_left'] = $this->load->controller('common/column_left');
				$data['footer'] = $this->load->controller('common/footer');
			}
			
			$this->response->setOutput($this->load->view($template, $data));
		}
	}
}
?>
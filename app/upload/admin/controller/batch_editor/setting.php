<?php
class ControllerBatchEditorSetting extends Controller {
	private $error = array ();
	private $json = array ('success' => '', 'warning' => '', 'value' => '');
	
	private $list = array (
		'manufacturer_id' => array ('table' => 'manufacturer', 'name' => 'm', 'field' => 'name', 'zero' => 1),
		'tax_class_id' => array ('table' => 'tax_class', 'name' => 'tc', 'field' => 'title', 'zero' => 1),
		'weight_class_id' => array ('table' => 'weight_class_description', 'name' => 'wcd', 'field' => 'title', 'lang' => 1),
		'length_class_id' => array ('table' => 'length_class_description', 'name' => 'lcd', 'field' => 'title', 'lang' => 1),
		'stock_status_id' => array ('table' => 'stock_status', 'name' => 'ss', 'field' => 'name', 'lang' => 1)
	);
	
	private $link = array (
		'description' => array (
			'table' => 'product_description',
			'list' => array ('languages'),
			'text' => array ('name', 'meta_description', 'meta_keyword', 'description', 'seo_title', 'seo_h1', 'tag'),
			'lang' => 1,
			'func' => 1
		),
		'category' => array (
			'table' => 'product_to_category',
			'list' => array ('categories'),
			'text' => array ('categories', 'main_category', 'none')
		),
		'attribute' => array (
			'table' => 'product_attribute',
			'list' => array ('languages', 'attributes'),
			'text' => array ('group', 'name', 'value', 'none'),
			'lang' => 1,
			'func' => 1
		),
		'option' => array (
			'table' => 'product_option',
			'list' => array (),
			'text' => array ('required', 'value', 'quantity', 'subtract', 'price', 'point', 'weight', 'yes','no'),
			'func' => 1
		),
		'special' => array (
			'table' => 'product_special',
			'list' => array ('customer_groups'),
			'text' => array ('customer_group', 'priority', 'discount', 'date_start', 'date_end')
		),
		'discount' => array (
			'table' => 'product_discount',
			'list' => array ('customer_groups'),
			'text' => array ('customer_group', 'quantity', 'priority', 'discount', 'date_start', 'date_end')
		),
		'related' => array (
			'table' => 'product_related',
			'list' => array (),
			'text' => array (),
			'func' => 1
		),
		'store' => array (
			'table' => 'product_to_store',
			'list' => array ('stores'),
			'text' => array ('default')
		),
		'download' => array (
			'table' => 'product_to_download',
			'list' => array ('downloads'),
			'text' => array ()
		),
		'image' => array (
			'table' => 'product_image',
			'list' => array ('no_image'),
			'text' => array ('image_manager', 'sort_order', 'clear', 'path'),
			'func' => 1
		),
		'reward' => array (
			'table' => 'product_reward',
			'list' => array ('customer_groups'),
			'text' => array ('customer_group', 'points')
		),
		'layout' => array (
			'table' => 'product_to_layout',
			'list' => array ('layouts', 'stores'),
			'text' => array ('layout', 'store', 'default')
		)
	);
	
	private $no_edit = array (
		'product' => array (
			'product_id' => array ('type' => 'int', 'size' => 11, 'table' => 'p'),
			'date_added' => array ('type' => 'date', 'table' => 'p'),
			'date_modified' => array ('type' => 'datetime', 'table' => 'p')
		),
		'product_description' => array (
			'product_id' => array ('type' => 'int', 'size' => 11, 'table' => 'pd'),
			'language_id' => array ('type' => 'int', 'size' => 11, 'table' => 'pd')
		)
	);
	
	public function index() {
		$this->load->language('batch_editor/index');
		$this->load->language('batch_editor/setting');
		
		$this->load->model('batch_editor/list');
		$this->load->model('batch_editor/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$width = (int) $this->request->post['option']['image']['width'];
			$height = (int) $this->request->post['option']['image']['height'];
			
			if ($width < 10 || $height < 10) {
				$width = $height = 40;
			}
			
			$this->request->post['option']['image']['width'] = $width;
			$this->request->post['option']['image']['height'] = $height;
			
			$main_category = $this->model_batch_editor_setting->getTableField('product_to_category', 'main_category');
			
			if ($main_category) {
				$this->request->post['option']['main_category'] = 1;
			} else {
				$this->request->post['option']['main_category'] = 0;
			}
			
			if (isset ($this->request->post['option']['limit']) && is_array ($this->request->post['option']['limit'])) {
				foreach ($this->request->post['option']['limit'] as $key=>$limit) {
					$limit = (int) $limit;
					
					if ($limit > 0) {
						$this->request->post['option']['limit'][$key] = $limit;
					} else {
						unset ($this->request->post['option']['limit'][$key]);
					}
				}
				
				if (!$this->request->post['option']['limit']) {
					$this->request->post['option']['limit'] = array (10);
				}
			} else {
				$this->request->post['option']['limit'] = array (10);
			}
			
			$option = $this->model_batch_editor_setting->get('option');
			
			if (isset ($option['hash'])) {
				$this->request->post['option']['hash'] = $option['hash'];
			}
			
			if (isset ($this->request->post['table']['asticker_id'])) {
				$this->list['asticker_id'] = array ('table' => 'astickers', 'name' => 'ast', 'field' => 'name', 'zero' => 1);
			}
			
			$this->model_batch_editor_setting->set('table', $this->request->post['table']);
			$this->model_batch_editor_setting->set('option', $this->request->post['option']);
			$this->model_batch_editor_setting->set('list', $this->list);
			$this->model_batch_editor_setting->set('no_edit', $this->no_edit);
			
			if (isset ($this->request->post['seo_generator'])) {
				$this->model_batch_editor_setting->set('tool/seo_generator', $this->request->post['seo_generator']);
			} else {
				$this->model_batch_editor_setting->set('tool/seo_generator', array ());
			}
			
			if (isset ($this->request->post['search_replace'])) {
				$this->model_batch_editor_setting->set('tool/search_replace', $this->request->post['search_replace']);
			} else {
				$this->model_batch_editor_setting->set('tool/search_replace', array ());
			}
			
			if (isset ($this->request->post['rounding_numbers'])) {
				$this->model_batch_editor_setting->set('tool/rounding_numbers', $this->request->post['rounding_numbers']);
			} else {
				$this->model_batch_editor_setting->set('tool/rounding_numbers', array ());
			}
			
			if (isset ($this->request->post['image_google'])) {
				$this->model_batch_editor_setting->set('tool/image_google', $this->request->post['image_google']);
			} else {
				$this->model_batch_editor_setting->set('tool/image_google', array ());
			}
			
			if (isset ($this->request->post['filter']) && is_array ($this->request->post['filter'])) {
				foreach ($this->request->post['filter'] as $table=>$data) {
					if (isset ($data['field']) && is_array ($data['field'])) {
						foreach ($data['field'] as $key=>$field) {
							if (!$field) {
								unset ($data['field'][$key]);
							}
						}
						
						if (!$data['field']) {
							unset ($this->request->post['filter'][$table]);
						}
					} else {
						unset ($this->request->post['filter'][$table]);
					}
				}
				
				$this->model_batch_editor_setting->set('filter', $this->request->post['filter']);
			} else {
				$this->model_batch_editor_setting->set('filter', array ());
			}
			
			if (isset ($this->request->post['multilanguage']['field']) && is_array ($this->request->post['multilanguage']['field'])) {
				foreach ($this->request->post['multilanguage']['field'] as $code => $data) {
					$this->model_batch_editor_setting->delete('language/' . $code . '/field');
					
					if (is_array ($data)) {
						$temp = array ();
						
						foreach ($data as $variable=>$text) {
							$variable = htmlspecialchars_decode ($variable);
							$variable = preg_replace ('%[^a-zA-Z0-9\_]%', '', $variable);
							
							$temp[$variable] = $text;
						}
						
						$path = DIR_APPLICATION . 'view/batch_editor/setting/language/' . $code;
						
						if (!is_dir ($path)) {
							mkdir ($path, 0755);
						}
						
						$this->model_batch_editor_setting->set('language/' . $code . '/field', $temp);
						
						unset ($temp);
					}
				}
			}
			
			$this->validateLink();
			
			foreach ($this->link as $link=>$data) {
				if (isset ($this->request->post['link'][$link]['enable']['filter'])) {
					$this->link[$link]['enable']['filter'] = 1;
				}
				
				if (isset ($this->request->post['link'][$link]['enable']['link'])) {
					$this->link[$link]['enable']['link'] = 1;
				}
				
				if (isset ($this->request->post['link'][$link]['enable']['product'])) {
					$this->link[$link]['enable']['product'] = 1;
				}
			}
			
			$additional_link = $this->model_batch_editor_setting->getAdditionalLink();
			
			foreach ($additional_link as $link => $data) {
				unset ($additional_link[$link]['enable']);
				
				if (isset ($this->request->post['link'][$link]['enable']['filter'])) {
					$additional_link[$link]['enable']['filter'] = 1;
				}
				
				if (isset ($this->request->post['link'][$link]['enable']['link'])) {
					$additional_link[$link]['enable']['link'] = 1;
				}
				
				if (isset ($this->request->post['link'][$link]['enable']['product'])) {
					$additional_link[$link]['enable']['product'] = 1;
				}
				
				$fields = $this->model_batch_editor_setting->getTableField($link);
				
				$type = 'standart';
				
				$primary_key = array ();
				
				foreach ($fields as $field => $field_setting) {
					if ($field_setting['key'] == 'PRI') {
						$primary_key[] = $field;
					}
				}
				
				if (count ($primary_key) == 2 && in_array ('language_id', $primary_key)) {
					$type = 'language';
				}
				
				$additional_link[$link]['type'] = $type;
				
				$this->model_batch_editor_setting->set('link/' . $link, $additional_link[$link]);
			}
			
			$this->model_batch_editor_setting->set('link', $this->link);
			
			$this->session->data['success'] = $this->language->get('success_edit_setting');
			
			if (VERSION < '2.0.0.0') {
				$this->redirect($this->url->link('batch_editor/index', 'token=' . $this->session->data['token'], 'SSL'));
			} else {
				$this->response->redirect($this->url->link('batch_editor/index', 'token=' . $this->session->data['token'], 'SSL'));
			}
		}
		
		$title = str_replace ('{version}', $this->model_batch_editor_setting->getVersion(), $this->language->get('heading_title'));
		
		$this->document->setTitle($title);
		
		$this_data['heading_title'] = $this->language->get('button_setting');
		
		$this_data['tab_general'] = $this->language->get('tab_general');
		$this_data['tab_option'] = $this->language->get('tab_option');
		$this_data['tab_link'] = $this->language->get('tab_link');
		$this_data['tab_filter'] = $this->language->get('tab_filter');
		
		$this_data['text_visible'] = $this->language->get('text_visible');
		$this_data['text_name'] = $this->language->get('text_name');
		$this_data['text_table'] = $this->language->get('text_table');
		$this_data['text_field'] = $this->language->get('text_field');
		$this_data['text_type'] = $this->language->get('text_type');
		$this_data['text_size'] = $this->language->get('text_size');
		$this_data['text_image_size'] = $this->language->get('text_image_size');
		$this_data['text_add_related'] = $this->language->get('text_add_related');
		$this_data['text_del_related'] = $this->language->get('text_del_related');
		$this_data['text_counter'] = $this->language->get('text_counter');
		$this_data['text_add'] = $this->language->get('text_add');
		$this_data['text_delete'] = $this->language->get('text_delete');
		$this_data['text_link'] = $this->language->get('text_link');
		$this_data['text_product'] = $this->language->get('text_product');
		$this_data['text_additional'] = $this->language->get('text_additional');
		$this_data['text_variable'] = $this->language->get('text_variable');
		$this_data['text_value'] = $this->language->get('text_value');
		$this_data['text_language'] = $this->language->get('text_language');
		$this_data['text_variables'] = $this->language->get('text_variables');
		$this_data['text_limit'] = $this->language->get('text_limit');
		$this_data['text_filter'] = $this->language->get('text_filter');
		$this_data['text_quick_filter'] = $this->language->get('text_quick_filter');
		$this_data['text_view_categories'] = $this->language->get('text_view_categories');
		$this_data['text_price_prefix'] = $this->language->get('text_price_prefix');
		
		$this_data['text_column_categories'] = $this->language->get('text_column_categories');
		$this_data['text_column_attributes'] = $this->language->get('text_column_attributes');
		$this_data['text_column_options'] = $this->language->get('text_column_options');
		
		$this_data['text_product_image_remove'] = $this->language->get('text_product_image_remove');
		
		$this_data['text_yes'] = $this->language->get('text_yes');
		$this_data['text_no'] = $this->language->get('text_no');
		
		$this_data['text_enabled'] = $this->language->get('text_enabled');
		$this_data['text_disabled'] = $this->language->get('text_disabled');
		
		$this_data['text_one_side'] = $this->language->get('text_one_side');
		$this_data['text_two_side'] = $this->language->get('text_two_side');
		
		$this_data['text_tab'] = $this->language->get('text_tab');
		$this_data['text_list'] = $this->language->get('text_list');
		$this_data['text_data'] = $this->language->get('text_data');
		$this_data['text_apply_to'] = $this->language->get('text_apply_to');
		$this_data['text_autocomplete'] = $this->language->get('text_autocomplete');
		$this_data['text_multilanguage'] = $this->language->get('text_multilanguage');
		$this_data['text_url_alias'] = $this->language->get('text_url_alias');
		$this_data['text_keyword'] = $this->language->get('text_keyword');
		
		$this_data['text_seo_generator'] = $this->language->get('text_seo_generator');
		$this_data['text_search_replace'] = $this->language->get('text_search_replace');
		$this_data['text_yandex_translate'] = $this->language->get('text_yandex_translate');
		$this_data['text_rounding_numbers'] = $this->language->get('text_rounding_numbers');
		$this_data['text_image_google'] = $this->language->get('text_image_google');
		
		$this_data['button_save'] = $this->language->get('button_save');
		$this_data['button_cancel'] = $this->language->get('button_cancel');
		$this_data['button_setting'] = $this->language->get('button_setting');
		$this_data['button_insert'] = $this->language->get('button_insert');
		$this_data['button_remove'] = $this->language->get('button_remove');
		
		$this_data['breadcrumbs'] = array (
			array (
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link((VERSION < '2.0.0.0') ? 'common/home' : 'common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => FALSE
			),
			array (
				'text' => $title,
				'href' => $this->url->link('batch_editor/index', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => ' :: '
			),
			array (
				'text' => $this->language->get('button_setting'),
				'href' => $this->url->link('batch_editor/setting', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => ' :: '
			)
		);
		
		$exclude = array ('product_id', 'language_id');
		
		$default = array ('model', 'sku', 'upc', 'location', 'quantity', 'stock_status_id', 'image', 'manufacturer_id', 'shipping', 'price', 'points', 'tax_class_id', 'date_available', 'weight', 'weight_class_id', 'length', 'width', 'height', 'length_class_id', 'subtract', 'minimum', 'sort_order', 'status', 'viewed', 'url_alias', 'date_added', 'date_modified', 'name', 'description', 'meta_description', 'meta_keyword');
		
		if (VERSION >= '1.5.4') {
			$default[] = 'tag';
		}
		
		$this_data['option'] = $this->model_batch_editor_setting->get('option');
		
		$product = $this->model_batch_editor_setting->table('product', $exclude);
		$product_description = $this->model_batch_editor_setting->table('product_description', $exclude);
		
		foreach ($product as $key=>$data) {
			$product[$key]['table'] = 'p';
		}
		
		foreach ($product_description as $key=>$data) {
			$product_description[$key]['table'] = 'pd';
		}
		
		$this_data['table'] = array_merge ($product, $product_description);
		
		$this_data['table']['url_alias'] = array ('type' => 'varchar', 'size' => 255, 'table' => 'ua');
		
		if (VERSION < '1.5.4') {
			$this_data['table']['tag'] = array ('type' => 'varchar', 'size' => 32, 'table' => 'pt');
		}
		
		$setting = $this->model_batch_editor_setting->get('table');
		
		$this_data['table'] = array_merge ($setting, $this_data['table']);
		
		$this_data['languages'] = $this->model_batch_editor_list->getLanguages();
		
		foreach ($this_data['table'] as $field => $data) {
			if (in_array ($field, $default)) {
				$this_data['table'][$field]['text'] = $this->language->get('text_' . $field);
			} else {
				foreach ($this_data['languages'] as $code => $language) {
					if (isset ($setting[$field]['text'][$code])) {
						$this_data['table'][$field]['text'][$code] = $setting[$field]['text'][$code];
					} else {
						$this_data['table'][$field]['text'][$code] = $field;
					}
				}
			}
			
			if (($data['type'] == 'int' || $data['type'] == 'decimal')) {
				if (!isset ($this->list[$field])) {
					$this_data['table'][$field]['calc'] = 1;
				}
			}
			
			if (isset ($setting[$field]['enable'])) {
				$this_data['table'][$field]['enable'] = $setting[$field]['enable'];
			} else {
				$this_data['table'][$field]['enable'] = 0;
			}
		}
		
		$this_data['url_action'] = $this->url->link('batch_editor/setting', 'token=' . $this->session->data['token'], 'SSL');
		$this_data['url_cancel'] = $this->url->link('batch_editor/index', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset ($this->error['warning'])) {
			$this_data['warning'] = $this->error['warning'];
		} else {
			$this_data['warning'] = '';
		}
		
		$this_data['setting']['link'] = $this->model_batch_editor_setting->get('link');
		
		if (!$this_data['setting']['link']) {
			$this->validateLink();
			
			$this_data['setting']['link'] = $this->link;
		}
		
		foreach ($this_data['setting']['link'] as $link => $data) {
			$this_data['text_' . $link] = $this->language->get('text_' . $link);
		}
		
		$this_data['setting']['additional_link'] = array ();
		
		$admin_language = $this->config->get('config_admin_language');
		
		$this_data['setting']['additional_link'] = $this->model_batch_editor_setting->getAdditionalLink();
		
		foreach ($this_data['setting']['additional_link'] as $link => $data) {
			if (isset ($data['description'][$admin_language])) {
				$this_data['text_' . $link] = $data['description'][$admin_language];
			} else {
				$this_data['text_' . $link] = 'text_' . $link;
			}
		}
		
		$this_data['tables'] = $this->model_batch_editor_setting->getTableWithProductId();
		$this_data['filter'] = $this->model_batch_editor_setting->get('filter');
		
		$this_data['multilanguage'] = array ();
		$this_data['variables'] = array ();
		
		foreach ($this_data['languages'] as $code => $language) {
			$this_data['multilanguage']['field'][$code] = $this->model_batch_editor_setting->get('language/' . $code . '/field');
			
			if ($this_data['multilanguage']['field'][$code]) {
				foreach ($this_data['multilanguage']['field'][$code] as $variable => $text) {
					$this_data['variables'][$variable] = $variable;
				}
			}
		}
		
		// Activate Start
		$this_data['button_activate'] = $this->language->get('button_activate');
		
		if (isset ($this_data['option']['hash']) && $this_data['option']['hash'] == $this->model_batch_editor_setting->getHash()) {
			$this_data['activate'] = true;
		} else {
			$this_data['activate'] = false;
		}
		
		$this_data['success_activate_extension'] = $this->language->get('success_activate_extension');
		$this_data['error_activate_extension'] = $this->language->get('error_activate_extension');
		
		unset ($this_data['option']['hash']);
		// Activate End
		
		$this_data['seo_generator'] = $this->model_batch_editor_setting->get('tool/seo_generator');
		
		if (!isset ($this_data['seo_generator']['field'])) {
			$this_data['seo_generator']['field'] = array ();
		}
		
		if (!isset ($this_data['seo_generator']['apply_to'])) {
			$this_data['seo_generator']['apply_to'] = array ();
		}
		
		$this_data['search_replace'] = $this->model_batch_editor_setting->get('tool/search_replace');
		
		if (!isset ($this_data['search_replace']['apply_to'])) {
			$this_data['search_replace']['apply_to'] = array ();
		}
		
		if (!isset ($this_data['search_replace']['field'])) {
			$this_data['search_replace']['field'] = array ();
		}
		
		$this_data['rounding_numbers'] = $this->model_batch_editor_setting->get('tool/rounding_numbers');
		
		if (!isset ($this_data['rounding_numbers']['apply_to'])) {
			$this_data['rounding_numbers']['apply_to'] = array ();
		}
		
		$this_data['image_google'] = $this->model_batch_editor_setting->get('tool/image_google');
		
		if (!isset ($this_data['image_google']['keyword'])) {
			$this_data['image_google']['keyword'] = array ();
		}
		
		$this_data['token'] = $this->session->data['token'];
		
		$this_template = 'batch_editor/setting.tpl';
		
		$this->setOutput($this_template, $this_data, true);
	}
	
	public function addLink() {
		$this->load->language('batch_editor/setting');
		
		$this_data['text_description'] = $this->language->get('text_description');
		$this_data['text_table'] = $this->language->get('text_table');
		$this_data['text_save'] = $this->language->get('text_save');
		$this_data['text_none'] = $this->language->get('text_none');
		
		$this->load->model('batch_editor/setting');
		
		$this->load->model('batch_editor/list');
		
		$this_data['languages'] = $this->model_batch_editor_list->getLanguages();
		
		$exclude_tables = array (
			DB_PREFIX . 'ocfilter_option_value_to_product',
			DB_PREFIX . 'ocfilter_option_value_to_product_description',
			DB_PREFIX . 'product',
			DB_PREFIX . 'product_filter',
			DB_PREFIX . 'product_option_value',
			DB_PREFIX . 'order_product',
			DB_PREFIX . 'return'
		);
		
		foreach ($this->link as $value) {
			$exclude_tables[] = DB_PREFIX . $value['table'];
		}
		
		$additional_link = $this->model_batch_editor_setting->getAdditionalLink();
		
		foreach ($additional_link as $value) {
			$exclude_tables[] = DB_PREFIX . $value['table'];
		}
		
		$tables = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "%'")->rows;
		
		$this_data['tables'] = array ();
		
		foreach ($tables as $data) {
			foreach ($data as $table) {
				if (!in_array ($table, $exclude_tables)) {
					$product_id = $this->db->query("SHOW COLUMNS FROM `" . $table . "` LIKE 'product_id'")->rows;
					
					if ($product_id) {
						$this_data['tables'][] = preg_replace ('/^' . DB_PREFIX . '/', '', $table);
					}
				}
			}
		}
		
		$this->setOutput('batch_editor/link/form.tpl', $this_data);
	}
	
	public function saveLink() {
		$this->load->language('batch_editor/setting');
		$this->load->model('batch_editor/list');
		
		if (isset ($this->request->post['link'])) {
			$link = $this->request->post['link'];
		} else {
			$link = array ();
		}
		
		$languages = $this->model_batch_editor_list->getLanguages();
		
		foreach ($languages as $code => $language) {
			if (!isset ($link['description'][$code]) || (isset ($link['description'][$code]) && !$link['description'][$code])) {
				$this->error['warning'] = $this->language->get('error_link_description');
			}
		}
		
		if (!isset ($link['table'])) {
			$this->error['warning'] = $this->language->get('error_link_table');
		} else {
			if (!$link['table']) {
				$this->error['warning'] = $this->language->get('error_link_table');
			} else {
				foreach ($this->link as $data) {
					if ($link['table'] == $data['table']) {
						$this->error['warning'] = $this->language->get('error_link_table');
						
						break;
					}
				}
			}
		}
		
		if ($this->validate()) {
			$this->load->model('batch_editor/setting');
			
			$this->model_batch_editor_setting->set('link/' . $link['table'], $link);
		}
		
		if (isset ($this->error['warning'])) {
			$this->json['warning'] = $this->error['warning'];
		} else {
			$this->json['value'] = $this->config->get('config_admin_language');
			$this->json['success'] = $this->language->get('success_edit_link');
		}
		
		echo json_encode ($this->json);
	}
	
	public function deleteLink() {
		$this->load->language('batch_editor/setting');
		
		if ($this->validate()) {
			if (isset ($this->request->post['link'])) {
				$link = $this->request->post['link'];
			} else {
				$link = '';
			}
			
			$file = DIR_APPLICATION . 'view/batch_editor/setting/link/' . $link . '.ini';
			
			if (file_exists ($file)) {
				unlink ($file);
			}
		}
		
		if (isset ($this->error['warning'])) {
			$this->json['warning'] = $this->error['warning'];
		} else {
			$this->json['success'] = $this->language->get('success_delete_link');
		}
		
		echo json_encode ($this->json);
	}
	
	public function getFilterField() {
		$fields = array ();
		
		if (isset ($this->request->post['table'])) {
			$table = $this->request->post['table'];
		} else {
			$table = '';
		}
		
		if ($table) {
			$this->load->model('batch_editor/setting');
			
			$temp = $this->model_batch_editor_setting->getTableField($table);
			
			foreach ($temp as $field=>$setting) {
				$fields[] = $field;
			}
			
			unset ($temp);
		}
		
		echo json_encode ($fields);
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'batch_editor/setting')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		return (!$this->error) ? TRUE : FALSE;
	}
	
	private function validateLink() {
		if (VERSION >= '1.5.5') {
			$this->link['filter'] = array (
				'table' => 'product_filter',
				'list' => array (),
				'text' => array (),
				'func' => 1
			);
		}
		
		$result = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "setting` WHERE `Field` = 'group'");
		
		if ($result->num_rows) {
			$query = $this->db->query("SELECT s.setting_id FROM `" . DB_PREFIX . "setting` s WHERE s.group = 'ocfilter'");
		} else {
			$query = $this->db->query("SELECT s.setting_id FROM `" . DB_PREFIX . "setting` s WHERE s.code = 'ocfilter'");
		}
		
		if ($query->num_rows) {
			$this->link['ocfilter'] = array (
				'table' => 'ocfilter_option_value_to_product',
				'list' => array ('languages', 'categories'),
				'text' => array ('none'),
				'func' => 1
			);
		}
		
		if (VERSION >= '2.0.0.0') {
			$this->link['recurring'] = array (
				'table' => 'product_recurring',
				'list' => array ('customer_groups', 'recurring_id'),
				'text' => array ('customer_group', 'recurring'),
				'func' => 1
			);
		}
	}
	
	public function activate() {
		$this->load->language('batch_editor/setting');
		
		if ($this->validate()) {
			/*if (function_exists ('curl_init')) {
				$this->load->model('batch_editor/setting');
				
				$curl = curl_init ();
				curl_setopt ($curl, CURLOPT_URL, 'http://opencart-ocstore.ru/index.php?route=business/validate/domain' . $this->model_batch_editor_setting->getRequest());
				curl_setopt ($curl, CURLOPT_RETURNTRANSFER, TRUE);
				$response = curl_exec ($curl);
				curl_close ($curl);
				
				$option = $this->model_batch_editor_setting->get('option');
				
				$option['hash'] = $response;
				
				if (!$response) {
					$this->error['warning'] = $this->language->get('error_activate_extension');
				}
				
				$this->model_batch_editor_setting->set('option', $option);
			} else {
				$this->error['warning'] = 'Function <em>curl_init</em> does not exist!';
			}*/
		}
		
		if (isset ($this->error['warning'])) {
			$this->json['warning'] = $this->error['warning'];
		} else {
			$this->json['success'] = $this->language->get('success_activate_extension');
		}
		
		echo json_encode ($this->json);
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
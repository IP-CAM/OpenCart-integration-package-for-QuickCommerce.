<?php
class ControllerBatchEditorTool extends Controller {
	private $error = array ();
	private $json = array ('success' => '', 'attention' => '', 'warning' => '', 'value' => '');
	private $tool = array (
		'seo_generator' => array (
			'get' => array (
				'text' => array ('template', 'apply_to', 'languages', 'text', 'data', 'space', 'edit', 'add', 'delete', 'optional', 'synonymizer', 'translit', 'attribute', 'attributes_all', 'value', 'main', 'description', 'load_template', 'save_template')
			),
			'edit' => array (
				'validate' => array (
					'variable' => array ('product_id' => 'empty_product', 'data' => 'empty_parameter', 'apply_to' => 'empty_parameter', 'language_id' => 'empty_parameter')
				)
			)
		),
		'search_replace' => array (
			'get' => array (
				'text' => array ('what', 'on_what', 'apply_to', 'languages', 'replace', 'main' , 'description', 'add', 'delete', 'text', 'data', 'template', 'load_template', 'save_template')
			),
			'edit' => array (
				'validate' => array (
					'variable' => array ('product_id' => 'empty_product', 'what' => 'empty_parameter', 'apply_to' => 'empty_parameter', 'language_id' => 'empty_parameter')
				)
			)
		),
		'option_price' => array (
			'get' => array (
				'text' => array ('edit', 'yes', 'no', 'none', 'add', 'delete', 'action', 'condition')
			),
			'edit' => array (
				'validate' => array (
					'variable' => array ('product_id' => 'empty_product', 'data' => 'empty_parameter')
				)
			)
		),
		'image_google' => array (
			'get' => array (
				'text' => array ('main', 'additional', 'add', 'delete', 'reset', 'more', 'search', 'file_type', 'image_type', 'color', 'colorization', 'size', 'folder')
			),
			'edit' => array (
				'validate' => array (
					'variable' => array ('product_id' => 'empty_product', 'data' => 'empty_image'),
					'function' => array ('curl_init' => 'curl')
				)
			)
		),
		'image_google_auto' => array (
			'get' => array (
				'text' => array ('add', 'delete', 'search', 'file_type', 'color', 'colorization', 'size', 'image_type', 'main', 'number_images', 'folder', 'main_category', 'category', 'default', 'existing', 'translit', 'by_priority', 'keyword', 'optional')
			),
			'edit' => array (
				'validate' => array (
					'variable' => array ('product_id' => 'empty_product'),
					'function' => array ('curl_init' => 'curl')
				)
			)
		),
		'yandex_translate' => array (
			'get' => array (
				'text' => array ('description', 'direction', 'apply_to', 'optional', 'rewrite', 'edit')
			),
			'edit' => array (
				'validate' => array (
					'variable' => array ('product_id' => 'empty_product', 'from' => 'empty_parameter', 'to' => 'empty_parameter', 'apply_to' => 'empty_parameter'),
					'function' => array ('curl_init' => 'curl')
				)
			)
		),
		'rounding_numbers' => array (
			'get' => array (
				'text' => array ('rounding', 'apply_to', 'main', 'optional', 'under_rule', 'in_big_way', 'edit')
			),
			'edit' => array (
				'validate' => array (
					'variable' => array ('product_id' => 'empty_product', 'rule' => 'empty_parameter', 'apply_to' => 'empty_parameter')
				)
			)
		),
		'lost_image' => array (
			'get' => array (
				'text' => array ('search', 'delete', 'reset', 'delete_entries')
			),
			'edit' => array (
				'validate' => array (
					'variable' => array ('product_id' => 'empty_product')
				)
			)
		),
		'image_manager' => array (
			'get' => array (
				'text' => array ('image_name', 'product_name', 'original', 'translit', 'folder', 'main', 'sort_order', 'load', 'add', 'delete', 'reset', 'close', 'drag_image', 'success_upload', 'error_image')
			),
			'edit' => array (
				'validate' => array (
					'variable' => array ('product_id' => 'empty_product', 'directory' => 'empty_parameter')
				)
			)
		)
	);
	
	public function getTool() {
		if (isset ($this->request->post['tool'])) {
			$tool = $this->request->post['tool'];
		} else {
			$tool = '';
		}
		
		if (!isset ($this->tool[$tool])) {
			return false;
		}
		
		$this->load->model('batch_editor/setting');
		
		$this->load->language('batch_editor/tool');
		$this->load->language('batch_editor/field');
		
		if (isset ($this->tool[$tool]['get']['function'])) {
			$this->load->model('batch_editor/function');
			
			foreach ($this->tool[$tool]['get']['function'] as $index => $function) {
				if (is_string ($index)) {
					$this_data[$index] = $this->model_batch_editor_function->{$function}();
				} else {
					$this_data = $this->model_batch_editor_function->{$function}();
				}
			}
		}
		
		if (isset ($this->request->post['product_id'])) {
			$this_data['product_id'] = (int) $this->request->post['product_id'];
		} else {
			$this_data['product_id'] = 0;
		}
		
		if ($this_data['product_id'] > 0) {
			$this->load->model('tool/image');
			$this->load->model('batch_editor/data');
			
			$this_data['product_name'] = $this->model_batch_editor_data->getProductName_($this_data['product_id']);
			$this_data['product_image'] = $this->model_batch_editor_data->getProductImage_($this_data['product_id']);
			
			$this_data['image'] = $this->model_batch_editor_setting->get('option', 'image');
			
			$this_data['product_image'] = $this->model_tool_image->resize($this_data['product_image'], $this_data['image']['width'], $this_data['image']['height']);
		}
		
		if ($tool == 'image_google' || $tool == 'image_manager') {
			$this->load->model('batch_editor/function');
			
			$this_data['directories'] = array ();
			
			if ($this_data['product_id'] > 0) {
				$setting = $this->model_batch_editor_setting->get('tool/' . $tool);
				
				$this_data['keyword'] = $this->model_batch_editor_setting->getKeywordFromField($this_data['product_id'], $setting['keyword']);
				
				$this_data['directories'] = $this->getProductImageFolder($this_data['product_id']);
			}
			
			if (!$this_data['directories']) {
				$this_data['directories'] = $this->model_batch_editor_function->getImageDirectories();
			}
		}
		
		if ($tool == 'image_google_auto') {
			if (VERSION < '2.0.0.0') {
				$this_data['directory'] = 'data/';
			} else {
				$this_data['directory'] = 'catalog/';
			}
			
			$this_data['main_category'] = $this->model_batch_editor_setting->getTableField('product_to_category', 'main_category');
			
			$setting = $this->model_batch_editor_setting->get('tool/image_google');
			
			$keyword_field = array ();
			
			foreach ($setting['keyword'] as $field) {
				$keyword_field[] = '{' . $this->language->get('field_' . $field) . '}';
			}
			
			$this_data['keyword_field'] = implode (' ', $keyword_field);
		}
		
		if ($tool == 'search_replace') {
			$code = $this->config->get('config_admin_language');
			
			$table = $this->model_batch_editor_setting->get('table');
			$search_replace = $this->model_batch_editor_setting->get('tool/search_replace');
			
			$this_data['apply_to']['p'] = array ();
			$this_data['apply_to']['pd'] = array ();
			
			if (isset ($search_replace['apply_to'])) {
				foreach ($search_replace['apply_to'] as $field) {
					if ($table[$field]['table'] == 'pd' || $table[$field]['table'] == 'pt') {
						$this_data['apply_to']['pd'][] = $field;
					} else {
						$this_data['apply_to']['p'][] = $field;
					}
					
					if (isset ($table[$field]['text'][$code]) && $table[$field]['text'][$code]) {
						$this_data['field_' . $field] = $table[$field]['text'][$code];
					} else {
						$this_data['field_' . $field] = $this->language->get('field_' . $field);
					}
				}
			}
			
			$this_data['apply_to']['pd'][] = 'attribute';
			$this_data['field_attribute'] = $this->language->get('text_attribute') . "&nbsp;(" . $this->language->get('text_value') . ")";
			
			$this->load->model('batch_editor/list');
			
			$this_data['languages'] = $this->model_batch_editor_list->getLanguages();
			$this_data['language_id'] = $this->config->get('config_language_id');
			
			if (isset ($search_replace['field'])) {
				$this_data['fields'] = $search_replace['field'];
			} else {
				$this_data['fields'] = array ();
			}
			
			foreach ($this_data['fields'] as $field) {
				if (isset ($table[$field]['text'][$code]) && $table[$field]['text'][$code]) {
					$this_data['field_' . $field] = $table[$field]['text'][$code];
				} else {
					$this_data['field_' . $field] = $this->language->get('field_' . $field);
				}
			}
		}
		
		if ($tool == 'seo_generator') {
				$code = $this->config->get('config_admin_language');
				
				$table = $this->model_batch_editor_setting->get('table');
				$seo_generator = $this->model_batch_editor_setting->get('tool/seo_generator');
				
				$this_data['apply_to']['p'] = array ();
				$this_data['apply_to']['pd'] = array ();
				
				if (isset ($seo_generator['apply_to'])) {
					foreach ($seo_generator['apply_to'] as $field) {
						if ($table[$field]['table'] == 'pd' || $table[$field]['table'] == 'pt') {
							$this_data['apply_to']['pd'][] = $field;
						} else {
							$this_data['apply_to']['p'][] = $field;
						}
						
						if (isset ($table[$field]['text'][$code]) && $table[$field]['text'][$code]) {
							$this_data['field_' . $field] = $table[$field]['text'][$code];
						} else {
							$this_data['field_' . $field] = $this->language->get('field_' . $field);
						}
					}
				}
				
				$this->load->model('batch_editor/list');
				
				$this_data['languages'] = $this->model_batch_editor_list->getLanguages();
				$this_data['language_id'] = (int) $this->config->get('config_language_id');
				
				if (isset ($seo_generator['field'])) {
					$this_data['fields'] = $seo_generator['field'];
				} else {
					$this_data['fields'] = array ();
				}
				
				foreach ($this_data['fields'] as $field) {
					if (isset ($table[$field]['text'][$code]) && $table[$field]['text'][$code]) {
						$this_data['field_' . $field] = $table[$field]['text'][$code];
					} else {
						$this_data['field_' . $field] = $this->language->get('field_' . $field);
					}
				}
				
				$this_data['options'] = array ('attribute', 'attributes_all');
				
				$this->load->model('localisation/currency');
				
				$this_data['currencies'] = $this->model_localisation_currency->getCurrencies();
			}
		
		if ($tool == 'option_price') {
				$this->load->model('batch_editor/list');
				
				$this_data['actions'] = $this_data['calculate'] = $this->model_batch_editor_list->getCalculate();
				$this_data['options'] = $this->model_batch_editor_list->getOptions();
				
				$setting = $this->model_batch_editor_setting->get('option');
				
				$this_data['option_price_prefix'] = $setting['option_price_prefix'];
				
				$this_data['field_option_id'] = $this->language->get('field_option_id');
				$this_data['field_option_value_id'] = $this->language->get('field_option_value_id');
				$this_data['field_subtract'] = $this->language->get('field_subtract');
				$this_data['field_quantity'] = $this->language->get('field_quantity');
				$this_data['field_price'] = $this->language->get('field_price');
				$this_data['field_points'] = $this->language->get('field_points');
				$this_data['field_weight'] = $this->language->get('field_weight');
			}
		
		if ($tool == 'yandex_translate') {
			$code = $this->config->get('config_admin_language');
			$table = $this->model_batch_editor_setting->get('table');
			
			$this_data['apply_to']['pd'] = array ();
			
			foreach ($table as $field => $setting) {
				if ($setting['table'] == 'pd' || $setting['table'] == 'pt') {
					$this_data['apply_to']['pd'][] = $field;
					
					if (isset ($setting['text'][$code]) && $setting['text'][$code]) {
						$this_data['field_' . $field] = $setting['text'][$code];
					} else {
						$this_data['field_' . $field] = $this->language->get('field_' . $field);
					}
				}
			}
			
			$yandex_languages = array ('sq', 'en', 'ar', 'hy', 'az', 'be', 'bg', 'bs', 'vi', 'hu', 'nl', 'el', 'ka', 'da', 'he', 'id', 'it', 'is', 'es', 'ca', 'zh', 'lv', 'lt', 'ms', 'mt', 'mk', 'de', 'no', 'pl', 'pt', 'ro', 'ru', 'en', 'sr', 'sk', 'sl', 'th', 'tr', 'uk', 'fi', 'fr', 'hr', 'cs', 'sv', 'et');
			
			$this->load->model('batch_editor/list');
			
			$this_data['languages'] = $this->model_batch_editor_list->getLanguages();
			
			foreach ($this_data['languages'] as $code => $language) {
				if (!in_array ($code, $yandex_languages)) {
					unset ($this_data['languages'][$code]);
				}
			}
		}
		
		if ($tool == 'rounding_numbers') {
			$code = $this->config->get('config_admin_language');
			
			$setting = $this->model_batch_editor_setting->get('table');
			$rounding_numbers = $this->model_batch_editor_setting->get('tool/rounding_numbers');
			
			$this_data['apply_to']['product'] = array ();
			
			if (isset ($rounding_numbers['apply_to'])) {
				foreach ($rounding_numbers['apply_to'] as $field) {
					$this_data['apply_to']['product'][] = $field;
					
					if (isset ($setting[$field]['text'][$code]) && $setting[$field]['text'][$code]) {
						$this_data['field_' . $field] = $setting[$field]['text'][$code];
					} else {
						$this_data['field_' . $field] = $this->language->get('field_' . $field);
					}
				}
			}
			
			$this_data['text_option_price'] = $this->language->get('field_price') . ' (' . $this->language->get('text_options') . ')';
			$this_data['text_special_price'] = $this->language->get('field_price') . ' (' . $this->language->get('text_specials') . ')';
			$this_data['text_discount_price'] = $this->language->get('field_price') . ' (' . $this->language->get('text_discounts') . ')';
		}
		
		$this_data['text_' . $tool] = $this->language->get('text_' . $tool);
		
		foreach ($this->tool[$tool]['get']['text'] as $text) {
			$this_data['text_' . $text] = $this->language->get('text_' . $text);
		}
		
		$this->setOutput('batch_editor/tool/' . $tool . '.tpl', $this_data);
	}
	
	public function editTool() {
		if (isset ($this->request->post['tool']) && is_string ($this->request->post['tool'])) {
			$tool = $this->request->post['tool'];
		} else {
			$tool = '';
		}
		
		if (!isset ($this->tool[$tool])) {
			return false;
		}
		
		$this->load->language('batch_editor/index');
		
		if (isset ($this->request->post[$tool]) && is_array ($this->request->post[$tool])) {
			$data = $this->request->post[$tool];
		} else {
			$data = array ();
		}
		
		if (isset ($this->request->post['selected']) && is_array ($this->request->post['selected'])) {
			$data['product_id'] = $this->request->post['selected'];
		} else {
			$data['product_id'] = array ();
		}
		
		if (isset ($this->request->post['batch_edit'])) {
			if ($tool == 'image_google_auto') {
				$data['product_id'] = $this->getEditProductId(1);
			} else {
				$data['product_id'] = $this->getEditProductId();
			}
			
			$this->json['count'] = count ($data['product_id']);
		}
		
		if (isset ($this->request->post['action']) && is_string ($this->request->post['action'])) {
			$data['action'] = $this->request->post['action'];
		} else {
			$data['action'] = 'add';
		}
		
		if (isset ($this->tool[$tool]['edit']['validate'])) {
			foreach ($this->tool[$tool]['edit']['validate'] as $type=>$value) {
				if ($type == 'variable') {
					foreach ($value as $variable => $error) {
						if (!isset ($data[$variable]) || !$data[$variable]) {
							$this->json['warning'] = $this->language->get('error_' . $error);
						}
					}
				} else if ($type == 'function') {
					foreach ($value as $function => $error) {
						if (!function_exists ($function)) {
							$this->json['warning'] = $this->language->get('error_' . $error);
						}
					}
				}
			}
		}
		
		if ($this->validate()) {
			$this->load->language('batch_editor/attention');
			$this->load->model('batch_editor/tool');
			
			$result = $this->model_batch_editor_tool->{str_replace ('_', '', $tool)}($data);
			
			if (isset ($result['attention'])) {
				$this->json['attention'] = $result['attention'];
			}
			
			if (isset ($result['value'])) {
				$this->json['value'] = $result['value'];
			}
		}
		
		if (!$this->json['warning'] && !$this->json['attention']) {
			$this->json['success'] = $this->language->get('success_edit_product');
		}
		
		echo json_encode ($this->json);
	}
	
	public function clearCache() {
		$this->load->language('batch_editor/index');
		
		if ($this->validate()) {
			$this->cache->delete('batch_editor');
		}
		
		$files = glob (DIR_APPLICATION . 'view/batch_editor/setting/temp/*');
		
		if ($files) {
			foreach ($files as $file) {
				if (file_exists ($file)) {
					unlink ($file);
				}
			}
		}
		
		
		if (!$this->json['warning']) {
			$this->json['success'] = $this->language->get('success_clear_cache');
		}
		
		echo json_encode ($this->json);
	}
	
	public function imageResize() {
		$this->load->model('tool/image');
		$this->load->model('batch_editor/setting');
		
		$option = $this->model_batch_editor_setting->get('option');
		
		if (isset ($this->request->post['image'])) {
			$image = $this->request->post['image'];
		} else {
			$image = '';
		}
		
		if (VERSION < '2.0.0.0') {
			$no_image = 'no_image.jpg';
		} else {
			$no_image = 'no_image.png';
		}
		
		if ($image && file_exists (DIR_IMAGE . $image)) {
			$image = $this->model_tool_image->resize($image, $option['image']['width'], $option['image']['height']);
		} else {
			$image = $this->model_tool_image->resize($no_image, $option['image']['width'], $option['image']['height']);
		}
		
		echo $image;
	}
	
	public function getImageDirectories() {
		$this->load->model('batch_editor/function');
		
		$directories = $this->model_batch_editor_function->getImageDirectories();
		
		echo json_encode ($directories);
	}
	
	private function getProductImageFolder($product_id) {
		$directories = array ();
		
		$query = $this->db->query("SELECT `image` FROM `" . DB_PREFIX . "product` WHERE `product_id` = '" . $product_id . "' LIMIT 1");
		
		foreach ($query->rows as $value) {
			if (trim ($value['image'])) {
				$directories[] = $this->extractImageDirectory($value['image']);
			}
		}
		
		$query = $this->db->query("SELECT `image` FROM `" . DB_PREFIX . "product_image` WHERE `product_id` = '" . $product_id . "'");
		
		foreach ($query->rows as $value) {
			if (trim ($value['image'])) {
				$directories[] = $this->extractImageDirectory($value['image']);
			}
		}
		
		return array_unique ($directories);
	}
	
	private function getEditProductId($limit = 100) {
		$this->load->model('batch_editor/setting');
		
		$product_id = array ();
		
		$data = $this->model_batch_editor_setting->get('temp/product_id.' . $this->session->data['token']);
		
		$count = 0;
		
		foreach ($data as $index => $value) {
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
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'batch_editor/tool')) {
			$this->json['warning'] = $this->language->get('error_permission');
		}
		
		//$this->validateDomain();
		return (!$this->json['warning']) ? TRUE : FALSE;
	}
	
	private function validateDomain() {
		$this->load->model('batch_editor/setting');
		
		$option = $this->model_batch_editor_setting->get('option');
		
		if (!isset ($option['hash']) || $option['hash'] != $this->model_batch_editor_setting->getHash()) {
			$this->json['warning'] = $this->language->get('error_permission');
		}
	}
	
	private function extractImageDirectory($image) {
		$directory = '';
		
		$array = explode ('/', $image);
		
		$index = count ($array) - 1;
		
		unset ($array[$index]);
		
		$directory = implode ('/', $array) . '/';
		
		if ($directory) {
			return $directory;
		} else {
			return 'data/';
		}
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
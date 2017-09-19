<?php
class ModelBatchEditorSetting extends Model {
	public function get($file, $parameter = false) {
		$data = array ();
		
		$path = DIR_APPLICATION . 'view/batch_editor/setting/' . $file . '.ini';
		
		if (file_exists ($path)) {
			$content = file_get_contents ($path);
			
			$data = unserialize ($content);
		}
		
		if ($file == 'option') {
			$data = $this->validateOption($data);
		}
		
		if ($file == 'table') {
			$data = $this->validateTable($data);
		}
		
		if (($file == 'tool/image_google' || $file == 'tool/image_manager') && !isset ($data['keyword'])) {
			$data['keyword'] = array ('name');
		}
		
		if ($parameter && isset ($data[$parameter])) {
			$data = $data[$parameter];
		}
		
		return $data;
	}
	
	public function set($file, $data) {
		file_put_contents (DIR_APPLICATION . 'view/batch_editor/setting/' . $file . '.ini', serialize ($data));
	}
	
	public function delete($file) {
		$path = DIR_APPLICATION . 'view/batch_editor/setting/' . $file . '.ini';
		
		if (file_exists ($path)) {
			unlink ($path);
		}
	}
	
	public function table($name, $exclude = array ()) {
		$result = array ();
		
		$table = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . $name . "`")->rows;
		
		foreach ($table as $data) {
			preg_match_all ('/^(?P<type>[a-zA-Z]*)[(]{0,1}(?P<size>[0-9]*){0,1}[,]{0,1}(?P<size_2>[0-9]*){0,1}[)]{0,1}$/', $data['Type'], $array, PREG_SET_ORDER);
			
			if (!in_array ($data['Field'], $exclude)) {
				foreach ($array as $value) {
					$result[$data['Field']]['type'] = $value['type'];
					
					if ($value['size']) {
						$result[$data['Field']]['size'] = $value['size'];
					}
					
					if ($value['size_2']) {
						$result[$data['Field']]['size_2'] = $value['size_2'];
					}
				}
			}
		}
		
		return $result;
	}
	
	public function getTableField($table, $field = false) {
		$result = array ();
		
		$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . $table . "'");
		
		if ($query->rows) {
			$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . $table . "`");
			
			foreach ($query->rows as $key => $data) {
				foreach ($data as $parametr => $value) {
					if ($parametr != 'Field') {
						if ($parametr == 'Type') {
							preg_match_all ('/^(?P<type>[a-zA-Z]*)[(]{0,1}(?P<size>[0-9]*){0,1}[,]{0,1}(?P<size_2>[0-9]*){0,1}[)]{0,1}/', $value, $array, PREG_SET_ORDER);
							
							foreach ($array as $array_value) {
								$result[$data['Field']]['type'] = $array_value['type'];
								
								if ($array_value['size']) {
									$result[$data['Field']]['size'] = $array_value['size'];
								}
								
								if ($array_value['size_2']) {
									$result[$data['Field']]['size_2'] = $array_value['size_2'];
								}
							}
						} else {
							$result[$data['Field']][strtolower ($parametr)] = $value;
						}
					}
				}
			}
		}
		
		if ($field) {
			if (isset ($result[$field])) {
				$result = $result[$field];
			} else {
				$result = array ();
			}
		}
		
		return $result;
	}
	
	public function getTableWithProductId() {
		$tables = $this->cache->get('batch_editor.table_with_product_id');
		
		if (!$tables) {
			$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "%'");
			
			foreach ($query->rows as $data) {
				foreach ($data as $table) {
					$product_id = $this->db->query("SHOW COLUMNS FROM `" . $table . "` LIKE 'product_id'")->rows;
					
					if ($product_id) {
						$tables[] = preg_replace ('/^' . DB_PREFIX . '/', '', $table);
					}
				}
			}
			
			$this->cache->set('batch_editor.table_with_product_id', $tables);
		}
		
		return $tables;
	}
	
	public function getAdditionalLink() {
		$result = array ();
		
		$data = scandir (DIR_APPLICATION . 'view/batch_editor/setting/link/');
		
		foreach ($data as $value) {
			if ($value != '.' && $value != '..') {
				$name = str_replace ('.ini', '', $value);
				
				$result[$name] = $this->get('link/' . $name);
			}
		}
		
		return $result;
	}
	
	public function getRequest() {
		return '&module=batch_editor&version=' . $this->getVersion() . '&domain=' . preg_replace ('/^www\./', '', $_SERVER['HTTP_HOST']);
	}
	
	public function getHash() {
		return sha1 (sha1 ( sha1 ($this->getRequest())));
	}
	
	public function getVersion() {
		return '0.4.7';
	}
	
	private function validateOption($option) {
		$setting = array (
			'counter'                  => 0,
			'category'                 => 0,
			'limit'                    => array (10),
			'related'                  => array ('add' => 1, 'del' => 1),
			'image'                    => array ('width' => 40, 'height' => 40),
			'quick_filter'             => 0,
			'url_alias'                => 0,
			'column_categories'        => 0,
			'column_attributes'        => 0,
			'column_options'           => 0,
			'product_image_remove'     => 0,
			'yandex_translate_key_api' => false,
			'option_price_prefix'      => array (array ('value' => '+', 'name' => '+'), array ('value' => '-', 'name' => '-')),
			'option_type'              => array ('select', 'radio', 'checkbox', 'image')
		);
		
		foreach ($setting as $parameter => $default) {
			if (!isset ($option[$parameter])) {
				$option[$parameter] = $default;
			}
		}
		
		return $option;
	}
	
	private function validateTable($table) {
		$product = $this->getTableField('product');
		
		$product_description = $this->getTableField('product_description');
		
		foreach ($table as $field => $parameter) {
			if (!isset ($product[$field]) && !isset ($product_description[$field]) && $field != 'url_alias' && $field != 'tag') {
				unset ($table[$field]);
			}
		}
		
		return $table;
	}
	
	public function getKeywordFromField($product_id, $fields) {
		$keyword = '';
		
		$product = $this->getTableField('product');
		$product_description = $this->getTableField('product_description');
		
		$concat = array ();
		
		foreach ($fields as $field) {
			if (isset ($product[$field])) {
				$concat[] = "p." . $field;
			}
			
			if (isset ($product_description[$field])) {
				$concat[] = "pd." . $field;
			}
		}
		
		if ($concat) {
			$query = $this->db->query("SELECT GROUP_CONCAT(" . implode (",' ',", $concat) . ") AS keyword FROM `" . DB_PREFIX . "product` p LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (pd.product_id = p.product_id AND pd.language_id = '" . $this->config->get('config_language_id') . "') AND p.product_id = '" . $product_id . "'");
			
			if ($query->num_rows) {
				$keyword = $query->row['keyword'];
			}
		}
		
		return $keyword;
	}
}
?>
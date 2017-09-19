<?php
class ModelBatchEditorList extends Model {
	public function getNoImage() {
		$this->load->model('batch_editor/setting');
		$this->load->model('tool/image');
		
		if (VERSION < '2.0.0.0') {
			$no_image = 'no_image.jpg';
		} else {
			$no_image = 'no_image.png';
		}
		
		$option = $this->model_batch_editor_setting->get('option');
		
		return $this->model_tool_image->resize($no_image, $option['image']['width'], $option['image']['height']);
	}
	
	public function getAttributes() {
		$attributes = $this->cache->get('batch_editor.attributes_all.' . (int) $this->config->get('config_language_id'));
		
		if (!$attributes) {
			$query = $this->db->query("SELECT ad.attribute_id AS attribute_id, ad.name AS attribute_name, agd.attribute_group_id AS attribute_group_id, agd.name AS attribute_group_name FROM " . DB_PREFIX . "attribute a, " . DB_PREFIX . "attribute_description ad, " . DB_PREFIX . "attribute_group_description agd, " . DB_PREFIX . "attribute_group ag WHERE a.attribute_id = ad.attribute_id AND a.attribute_group_id = agd.attribute_group_id AND ag.attribute_group_id = agd.attribute_group_id AND agd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND ad.language_id = '" . (int) $this->config->get('config_language_id') . "' ORDER BY ag.sort_order, a.sort_order ASC");
			
			$attributes = array ();
			
			foreach ($query->rows as $row) {
				$attributes[$row['attribute_group_id']]['attribute_group_id'  ] = $row['attribute_group_id'  ];
				$attributes[$row['attribute_group_id']]['attribute_group_name'] = $row['attribute_group_name'];
				
				$attributes[$row['attribute_group_id']]['attributes'][$row['attribute_id']] = array (
					'attribute_id'   => $row['attribute_id'],
					'attribute_name' => $row['attribute_name']
				);
			}
			
			$this->cache->set('batch_editor.attributes_all.' . (int) $this->config->get('config_language_id'), $attributes);
		}
		
		return $attributes;
	}
	
	public function getAttributesByGroupId($group_id) {
		$attributes = $this->cache->get('batch_editor.attributes.' . (int) $group_id . '.' . (int) $this->config->get('config_language_id'));
		
		if (!$attributes) {
			$attributes = $this->db->query("SELECT ad.attribute_id AS attribute_id, ad.name AS attribute_name FROM " . DB_PREFIX . "attribute a LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (ad.attribute_id = a.attribute_id) WHERE a.attribute_group_id = '" . (int) $group_id . "' AND ad.language_id = '" . (int) $this->config->get('config_language_id') . "' ORDER BY a.sort_order, ad.name ASC")->rows;
			
			$this->cache->set('batch_editor.attributes.' . (int) $group_id . '.' . (int) $this->config->get('config_language_id'), $attributes);
		}
		
		return $attributes;
	}
	
	public function getStatus() {
		return array (
			array ('status' => 0, 'name' => $this->language->get('text_disabled')),
			array ('status' => 1, 'name' => $this->language->get('text_enabled')),
		);
	}
	
	public function getShipping() {
		return array (
			array ('shipping' => 0, 'name' => $this->language->get('text_no')),
			array ('shipping' => 1, 'name' => $this->language->get('text_yes')),
		);
	}
	
	public function getSubtract() {
		return array (
			array ('subtract' => 0, 'name' => $this->language->get('text_no')),
			array ('subtract' => 1, 'name' => $this->language->get('text_yes')),
		);
	}
	
	public function getTinyintList($field) {
		if ($field == 'status') {
			return array (
				array ('status' => 0, 'name' => $this->language->get('text_disabled')),
				array ('status' => 1, 'name' => $this->language->get('text_enabled')),
			);
		} else {
			return array (
				array ($field => 0, 'name' => $this->language->get('text_no')),
				array ($field => 1, 'name' => $this->language->get('text_yes')),
			);
		}
	}
	
	public function getManufacturerId() {
		$manufacturers = $this->cache->get('batch_editor.manufacturers_all');
		
		if (!$manufacturers) {
			$manufacturers = $this->db->query("SELECT manufacturer_id, name FROM " . DB_PREFIX . "manufacturer ORDER BY name")->rows;
			
			$this->cache->set('batch_editor.manufacturers_all', $manufacturers);
		}
		
		return $manufacturers;
	}
	
	public function getStockStatusId() {
		$stock_statuses = $this->cache->get('batch_editor.stock_statuses_all.' . (int)$this->config->get('config_language_id'));
		
		if (!$stock_statuses) {
			$stock_statuses = $this->db->query("SELECT stock_status_id, name FROM " . DB_PREFIX . "stock_status WHERE language_id = '" . (int) $this->config->get('config_language_id') . "' ORDER BY name")->rows;
			
			$this->cache->set('batch_editor.stock_statuses_all.' . (int)$this->config->get('config_language_id'), $stock_statuses);
		}
		
		return $stock_statuses;
	}
	
	public function getTaxClassId() {
		$tax_classes = $this->cache->get('batch_editor.tax_classes_all');
		
		if (!$tax_classes) {
			$tax_classes = $this->db->query("SELECT tax_class_id AS tax_class_id, title AS name FROM " . DB_PREFIX . "tax_class")->rows;
			
			$this->cache->set('batch_editor.tax_classes_all', $tax_classes);
		}
		
		return $tax_classes;
	}
	
	public function getLengthClassId() {
		$length_classes = $this->cache->get('batch_editor.length_classes_all.' . (int)$this->config->get('config_language_id'));
		
		if (!$length_classes) {
				$length_classes = $this->db->query("SELECT lc.length_class_id AS length_class_id, lcd.title AS name FROM " . DB_PREFIX . "length_class lc LEFT JOIN " . DB_PREFIX . "length_class_description lcd ON (lc.length_class_id = lcd.length_class_id) WHERE lcd.language_id = '" . (int)$this->config->get('config_language_id') . "'")->rows;
				
				$this->cache->set('batch_editor.length_classes_all.' . (int)$this->config->get('config_language_id'), $length_classes);
			}
			
			return $length_classes;
	}
	
	public function getWeightClassId() {
		$weight_classes = $this->cache->get('batch_editor.weight_classes_all.' . (int) $this->config->get('config_language_id'));
		
		if (!$weight_classes) {
			$weight_classes = $this->db->query("SELECT wc.weight_class_id as weight_class_id, wcd.title AS name FROM " . DB_PREFIX . "weight_class wc LEFT JOIN " . DB_PREFIX . "weight_class_description wcd ON (wc.weight_class_id = wcd.weight_class_id) WHERE wcd.language_id = '" . (int) $this->config->get('config_language_id') . "'")->rows;
			
			$this->cache->set('batch_editor.weight_classes_all.' . (int) $this->config->get('config_language_id'), $weight_classes);
		}
		
		return $weight_classes;
	}
	
	public function getLanguages() {
		$languages = $this->cache->get('batch_editor.languages_all');
		
		if (!$languages) {
			$languages = array ();
			
			$query = $this->db->query("SELECT language_id, name, image, code FROM " . DB_PREFIX . "language ORDER BY sort_order, name");
			
			foreach ($query->rows as $result) {
				$languages[$result['code']] = array (
					'language_id' => $result['language_id'],
					'name'        => $result['name'],
					'image'       => $result['image']
				);
			}
			
			$this->cache->set('batch_editor.languages_all', $languages);
		}
		
		return $languages;
	}
	
	public function getCustomerGroups() {
		if (VERSION < '1.5.3') {
			return $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_group ORDER BY name ASC")->rows;
		} else {
			return $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_group_description WHERE language_id = '" . (int) $this->config->get('config_language_id') . "' ORDER BY name ASC")->rows;
		}
	}
	
	public function getStores() {
		$stores = $this->cache->get('batch_editor.stores_all');
		
		if (!$stores) {
			$stores = $this->db->query("SELECT store_id, name FROM " . DB_PREFIX . "store ORDER BY url")->rows;
			
			$this->cache->set('batch_editor.stores_all', $stores);
		}
		
		return $stores;
	}
	
	public function getDownloads() {
		$downloads = $this->cache->get('batch_editor.downloads_all.' . (int) $this->config->get('config_language_id'));
		
		if (!$downloads) {
			$downloads = $this->db->query("SELECT download_id, name FROM " . DB_PREFIX . "download_description WHERE language_id = '" . (int) $this->config->get('config_language_id') . "' ORDER BY name ASC")->rows;
			
			$this->cache->set('batch_editor.downloads_all.' . (int) $this->config->get('config_language_id'), $downloads);
		}
		
		return $downloads;
	}
	
	public function getLayouts() {
		$layouts = $this->cache->get('batch_editor.layouts_all');
		
		if (!$layouts) {
			$layouts = $this->db->query("SELECT layout_id, name FROM " . DB_PREFIX . "layout ORDER BY name ASC")->rows;
			
			$this->cache->set('batch_editor.layouts_all.', $layouts);
		}
		
		return $layouts;
	}
	
	public function getFilter() {
		$filters = $this->cache->get('batch_editor.filters_all.' . (int) $this->config->get('config_language_id'));
		
		if (!$filters) {
			$filters = $this->db->query("SELECT filter_id, name FROM " . DB_PREFIX . "filter_description WHERE language_id = '" . (int) $this->config->get('config_language_id') . "' ORDER BY name ASC")->rows;
			
			$this->cache->set('batch_editor.filters_all.' . (int) $this->config->get('config_language_id'), $filters);
		}
		
		return $filters;
	}
	
	public function getAStickerId() {
		$astickers = $this->cache->get('batch_editor.astickers_all.' . (int) $this->config->get('config_language_id'));
		
		if (!$astickers) {
			$astickers = $this->db->query("SELECT ast.asticker_id as asticker_id, ast.name AS name FROM " . DB_PREFIX . "astickers ast")->rows;
			
			$this->cache->set('batch_editor.astickers_all', $astickers);
		}
		
		return $astickers;
	}
	
	public function getCategories() {
		$categories = $this->cache->get('batch_editor.categories_all.' . (int) $this->config->get('config_language_id'));
		
		if (!$categories) {
			$categories = $this->getCategory();
			
			$this->cache->set('batch_editor.categories_all.' . (int) $this->config->get('config_language_id'), $categories);
		}
		
		return $categories;
	}
	
	private function getCategory($parent_id = 0) {
		$category_data = array ();
		
		$sql = "SELECT c.category_id, cd.name FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE c.parent_id = " . (int) $parent_id . " AND cd.language_id = " . (int) $this->config->get('config_language_id') . " ORDER BY c.sort_order, cd.name ASC";
		
		$query = $this->db->query($sql);
		
		foreach ($query->rows as $result) {
			$temp = array ();
			
			foreach ($result as $field => $value) {
				if ($field == 'name') {
					$temp['name'] = $this->getPath($result['category_id']);
				} else {
					$temp[$field] = $value;
				}
			}
			
			$category_data[] = $temp;
			
			$category_data = array_merge ($category_data, $this->getCategory($result['category_id']));
		}
		
		return $category_data;
	}
	
	private function getPath($category_id) {
		$query = $this->db->query("SELECT name, parent_id FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE c.category_id = '" . (int) $category_id . "' AND cd.language_id = '" . (int) $this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name ASC");
		
		if ($query->row['parent_id']) {
			return $this->getPath($query->row['parent_id'], $this->config->get('config_language_id')) . ' > ' . $query->row['name'];
		} else {
			return $query->row['name'];
		}
	}
	
	public function getDiscountActions() {
		$this->load->language('batch_editor/calculate');
		
		return array (
			array ('action' => 'equal_number' , 'name' => $this->language->get('text_equal_number')),
			array ('action' => 'plus_number' , 'name' => $this->language->get('text_price_plus_number')),
			array ('action' => 'minus_number' , 'name' => $this->language->get('text_price_minus_number')),
			array ('action' => 'plus_percent', 'name' => $this->language->get('text_price_plus_percent')),
			array ('action' => 'minus_percent', 'name' => $this->language->get('text_price_minus_percent'))
		);
	}
	
	public function getCalculate() {
		$this->load->language('batch_editor/calculate');
		
		return array (
			array ('action' => 'equal_number'   , 'name' => $this->language->get('text_equal_number')),
			array ('action' => 'plus_number'    , 'name' => $this->language->get('text_plus_number')),
			array ('action' => 'minus_number'   , 'name' => $this->language->get('text_minus_number')),
			array ('action' => 'multiply_number', 'name' => $this->language->get('text_multiply_number')),
			array ('action' => 'divide_number'  , 'name' => $this->language->get('text_divide_number')),
			array ('action' => 'plus_percent'   , 'name' => $this->language->get('text_plus_percent')),
			array ('action' => 'minus_percent'  , 'name' => $this->language->get('text_minus_percent'))
		);
	}
	
	public function getOptions() {
		$this->load->model('batch_editor/setting');
		
		$option_type = $this->model_batch_editor_setting->get('option', 'option_type');
		
		$query = $this->db->query("SELECT o.option_id AS option_id, od.name AS name FROM `" . DB_PREFIX . "option` o LEFT JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id AND od.language_id = '" . (int) $this->config->get('config_language_id') . "') WHERE o.type IN ('" . implode ("','", $option_type) . "')");
		
		return $query->rows;
	}
	
	public function getOptionValues($option_id) {
		$query = $this->db->query("SELECT option_value_id, name FROM " . DB_PREFIX . "option_value_description WHERE option_id = '" . (int) $option_id . "' AND language_id = '" . (int) $this->config->get('config_language_id') . "'");
		
		return $query->rows;
	}
	
	public function getRecurringId() {
		$recurring = $this->cache->get('batch_editor.recurring_all');
		
		if (!$recurring) {
			$query = $this->db->query("SELECT r.recurring_id, rd.name FROM `" . DB_PREFIX . "recurring` r LEFT JOIN " . DB_PREFIX . "recurring_description rd ON (r.recurring_id = rd.recurring_id AND rd.language_id = '" . $this->config->get('config_language_id') . "') ORDER BY r.sort_order ASC");
			
			$recurring = $query->rows;
			
			$this->cache->set('batch_editor.recurring_all', $recurring);
		}
		
		return $recurring;
	}
	///////////////////////////////////////////////////////////////////
	public function getCategoryName($data = array ()) {
		$category_data = array ();
		$sql = '';
		
		if (isset ($data['keyword'])) {
			$query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "category_description WHERE LCASE(name) LIKE '%" . $this->db->escape(utf8_strtolower($data['keyword'])) . "%' AND language_id = " . (int) $this->config->get('config_language_id') . " LIMIT 10");
			
			foreach ($query->rows as $result) {
				$result['name'] = html_entity_decode ($this->getPath($result['category_id']), ENT_QUOTES, 'UTF-8');
				$category_data[] = $result;
			}
			
			array_multisort ($category_data);
		}
		
		if (isset ($data['array_id'])) {
			foreach ($data['array_id'] as $category_id) {
				$category_id = (int) $category_id;
				
				$category_data[$category_id] = html_entity_decode ($this->getPath($category_id), ENT_QUOTES, 'UTF-8');
			}
		}
		
		return $category_data;
	}
	
	public function getProductName($data = array ()) {
		$product_data = array ();
		$sql = '';
		
		if (isset ($data['keyword'])) {
			$product_data = $this->db->query("SELECT product_id, name FROM " . DB_PREFIX . "product_description WHERE LCASE(name) LIKE '%" . $this->db->escape(utf8_strtolower($data['keyword'])) . "%' AND language_id=" . (int) $this->config->get('config_language_id') . " ORDER BY name LIMIT 10")->rows;
			
			foreach ($product_data as $key=>$array) {
				foreach ($array as $field=>$value) {
					if ($field == 'name') {
						$product_data[$key][$field] = html_entity_decode ($value, ENT_QUOTES, 'UTF-8');
					}
				}
			}
		}
		
		if (isset ($data['array_id'])) {
			$query = $this->db->query("SELECT product_id, name FROM " . DB_PREFIX . "product_description WHERE product_id IN (" . implode (',', $data['array_id']) . ") AND language_id=" . (int) $this->config->get('config_language_id'));
			
			foreach ($query->rows as $value) {
				$product_data[$value['product_id']] = html_entity_decode ($value['name'], ENT_QUOTES, 'UTF-8');
			}
		}
		
		return $product_data;
	}
	
	public function getCouponName($data = array ()) {
		$coupons = array ();
		$sql = '';
		
		if (isset ($data['keyword'])) {
			$query = $this->db->query("SELECT coupon_id, name FROM " . DB_PREFIX . "coupon WHERE LCASE(name) LIKE '%" . $this->db->escape(utf8_strtolower($data['keyword'])) . "%' ORDER BY name LIMIT 10");
			
			$coupons = $query->rows;
		}
		
		if (isset ($data['array_id'])) {
			$query = $this->db->query("SELECT coupon_id, name FROM " . DB_PREFIX . "coupon WHERE coupon_id IN (" . implode (',', $data['array_id']) . ")");
			
			foreach ($query->rows as $value) {
				$coupons[$value['coupon_id']] = html_entity_decode ($value['name'], ENT_QUOTES, 'UTF-8');
			}
		}
		
		return $coupons;
	}
	
	public function getSizeChartName($data = array ()) {
		$sizecharts = array ();
		$sql = '';
		
		if (isset ($data['keyword'])) {
			$query = $this->db->query("SELECT `sizechart_id`, `name` FROM `" . DB_PREFIX . "sizechart_description` WHERE LCASE(`name`) LIKE '%" . $this->db->escape(utf8_strtolower($data['keyword'])) . "%' AND `language_id` = '" . $this->config->get('config_language_id') . "' ORDER BY `name` LIMIT 10");
			
			$sizecharts = $query->rows;
		}
		
		if (isset ($data['array_id'])) {
			$query = $this->db->query("SELECT `sizechart_id`, `name` FROM `" . DB_PREFIX . "sizechart_description` WHERE `sizechart_id` IN (" . implode (',', $data['array_id']) . ")");
			
			foreach ($query->rows as $value) {
				$sizecharts[$value['sizechart_id']] = html_entity_decode ($value['name'], ENT_QUOTES, 'UTF-8');
			}
		}
		
		return $sizecharts;
	}
	
	public function getCoupons() {
		$coupons = $this->cache->get('batch_editor.coupons_all');
		
		if (!$coupons) {
			$coupons = $this->db->query("SELECT coupon_id, name FROM " . DB_PREFIX . "coupon ORDER BY name ASC")->rows;
			
			$this->cache->set('batch_editor.coupons_all', $coupons);
		}
		
		return $coupons;
	}
}
?>
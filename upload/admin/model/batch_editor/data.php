<?php
class ModelBatchEditorData extends Model {
	public function getProductName_($product_id) {
		if (isset ($this->request->post['language_id'])) {
			$language_id = (int) $this->request->post['language_id'];
		} else {
			$language_id = (int) $this->config->get('config_language_id');
		}
		
		$result = $this->db->query('SELECT name FROM ' . DB_PREFIX . 'product_description WHERE product_id = ' . (int) $product_id . ' AND language_id = ' . $language_id)->row;
		
		//return (isset ($result['name'])) ? html_entity_decode ($result['name'], ENT_QUOTES, 'UTF-8') : FALSE;
		return (isset ($result['name'])) ? $result['name'] : '';
	}
	
	public function getProductImage_($product_id) {
		if (VERSION < '2.0.0.0') {
			$no_image = 'no_image.jpg';
		} else {
			$no_image = 'no_image.png';
		}
		
		$result = $this->db->query('SELECT image FROM ' . DB_PREFIX . 'product WHERE product_id = "' . (int) $product_id . '"')->row;
		
		return (isset ($result['image']) && $result['image'] && file_exists (DIR_IMAGE . $result['image'])) ? $result['image'] : $no_image;
	}
	
	public function getProductDescription($product_id) {
		$this->load->model('batch_editor/setting');
		$table = $this->model_batch_editor_setting->get('table');
		
		$data = array ();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int) $product_id . "'");
		
		foreach ($query->rows as $result) {
			foreach ($table as $field => $value) {
				if ($value['table'] == 'pd') {
					$data[$result['language_id']][$field] = $result[$field];
				} else {
					unset ($table[$field]);
				}
			}
		}
		
		if (VERSION < '1.5.4') {
			$this->load->model('catalog/product');
			$tags = $this->model_catalog_product->getProductTags($product_id);
			
			foreach ($tags as $language_id=>$value) {
				$data[$language_id]['tag'] = $value;
			}
		}
		
		return $data;
	}
	
	public function getProductImage($product_id) {
		$this->load->model('batch_editor/setting');
		$this->load->model('tool/image');
		
		if (VERSION < '2.0.0.0') {
			$no_image = 'no_image.jpg';
		} else {
			$no_image = 'no_image.png';
		}
		
		$data = array ();
		
		$option = $this->model_batch_editor_setting->get('option');
		
		$query = $this->db->query("SELECT `image`, `sort_order` FROM `" . DB_PREFIX . "product_image` WHERE `product_id` = '" . (int) $product_id . "'");
		
		foreach ($query->rows as $array) {
			if ($array['image'] && file_exists (DIR_IMAGE . $array['image'])) {
				$thumb = $this->model_tool_image->resize($array['image'], $option['image']['width'], $option['image']['height']);
			} else {
				$thumb = $this->model_tool_image->resize($no_image, $option['image']['width'], $option['image']['height']);
			}
			
			$data[] = array ('image' => $array['image'], 'thumb' => $thumb, 'sort_order' => $array['sort_order']);
		}
		
		return $data;
	}
	
	public function getProductOption($product_id) {
		$this->load->model('batch_editor/setting');
		
		$option_type = $this->model_batch_editor_setting->get('option', 'option_type');
		
		$product_options = array ();
		$option_values = array ();
		
		$sql = "SELECT * FROM `" . DB_PREFIX . "product_option` po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE od.language_id = '" . (int) $this->config->get('config_language_id') . "'";
		
		if (is_array ($product_id)) {
			$sql .= " AND po.product_id IN ('" . implode ("','", $product_id) . "')";
		} else {
			$sql .= " AND po.product_id = '" . (int) $product_id . "'";
		}
		
		$query = $this->db->query($sql);
		
		$product_options = $query->rows;
		
		foreach ($product_options as $key => $option) {
			if (in_array ($option['type'], $option_type)) {
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option_value` WHERE `product_option_id` = '" . $option['product_option_id'] . "'");
				
				$product_options[$key]['product_option_value'] = $query->rows;
				
				$option_values[$option['option_id']] = array ();
			}
		}
		
		foreach ($option_values as $option_id => $array) {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "option_value` ov LEFT JOIN `" . DB_PREFIX . "option_value_description` ovd ON (ov.option_value_id = ovd.option_value_id) WHERE ov.option_id = '" . (int) $option_id . "' AND ovd.language_id = '" . (int) $this->config->get('config_language_id') . "' ORDER BY ov.sort_order ASC");
			
			$option_values[$option_id] = $query->rows;
		}
		
		if (is_array ($product_id)) {
			$data = array ();
			
			foreach ($product_options as $array) {
				$data[$array['product_id']][] = $array;
			}
			
			$product_options = $data;
		}
		
		return array ('product_options' => $product_options, 'option_values' => $option_values);
	}
	
	public function getProductAttribute($product_id) {
		$data = array();
		
		$sql = "SELECT pa.*, ad.name FROM `" . DB_PREFIX . "product_attribute` pa LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (pa.attribute_id = ad.attribute_id AND ad.language_id = '" . (int) $this->config->get('config_language_id') . "') WHERE ";
		
		if (is_array ($product_id)) {
			$sql .= "pa.product_id IN ('" . implode ("','", $product_id) . "')";
		} else {
			$sql .= "pa.product_id = '" . (int) $product_id . "'";
		}
		
		$query = $this->db->query($sql);
		
		if (is_array ($product_id)) {
			foreach ($query->rows as $array) {
				$data[$array['product_id']][$array['attribute_id']]['attribute_id'] = $array['attribute_id'];
				$data[$array['product_id']][$array['attribute_id']]['name'] = $array['name'];
				$data[$array['product_id']][$array['attribute_id']]['attribute_description'][$array['language_id']]['text'] = $array['text'];
			}
		} else {
			foreach ($query->rows as $array) {
				$data[$array['attribute_id']]['attribute_id'] = $array['attribute_id'];
				$data[$array['attribute_id']]['name'] = $array['name'];
				$data[$array['attribute_id']]['attribute_description'][$array['language_id']]['text'] = $array['text'];
			}
		}
		
		return $data;
	}
	
	public function getProductRelated($product_id) {
		$related = array ();
		
		$this->load->model('catalog/product');
		
		$products = $this->model_catalog_product->getProductRelated($product_id);
		
		if ($products) {
			$related = $this->db->query("SELECT product_id AS product_id, name AS name FROM " . DB_PREFIX . "product_description WHERE product_id IN (" . implode (', ', $products) . ") AND language_id = '" . (int) $this->config->get('config_language_id') . "'")->rows;
		}
		
		return $related;
	}
	
	public function getProductFilter($product_id) {
		$product_filter_data = array();
		
		$product_filter_query = $this->db->query("SELECT pf.filter_id AS filter_id, fd.name AS name, fgd.name AS group_name FROM " . DB_PREFIX . "product_filter pf LEFT JOIN " . DB_PREFIX . "filter_description fd ON (fd.filter_id = pf.filter_id) LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON (fgd.filter_group_id = fd.filter_group_id) WHERE pf.product_id = '" . (int) $product_id . "' AND fd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND fgd.language_id = '" . (int) $this->config->get('config_language_id') . "'");
		
		foreach ($product_filter_query->rows as $product_filter) {
			$product_filter_data[] = array ('filter_id' => $product_filter['filter_id'], 'name' => $product_filter['group_name'] . ' &gt; ' . $product_filter['name']);
		}
		
		return $product_filter_data;
	}
	
	public function getProductOcFilter($product_id) {
		$data = array ();
		$product = array ();
		$option_id = array ();
		
		$query = $this->db->query("SELECT oo.option_id, oo.type, ood.name FROM " . DB_PREFIX . "ocfilter_option oo LEFT JOIN " . DB_PREFIX . "ocfilter_option_to_category oo2c ON (oo2c.option_id = oo.option_id) LEFT JOIN " . DB_PREFIX . "ocfilter_option_description ood ON (ood.option_id = oo.option_id) LEFT JOIN " . DB_PREFIX . "ocfilter_option_to_store oo2s ON (oo2s.option_id = oo.option_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (oo2c.category_id = p2c.category_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p2s.store_id = oo2s.store_id) WHERE ood.language_id = " . (int) $this->config->get('config_language_id') . " AND p2s.product_id = " . (int) $product_id . " AND p2c.product_id = " . (int) $product_id . " GROUP BY oo.option_id");
		
		foreach ($query->rows as $value) {
			$data[$value['option_id']] = array ('name' => $value['name'], 'type' => $value['type']);
			$option_id[] = $value['option_id'];
		}
		
		if ($option_id) {
			$query = $this->db->query("SELECT option_id, value_id, name FROM " . DB_PREFIX . "ocfilter_option_value_description WHERE option_id IN (" . implode (',', $option_id) . ") AND language_id = " . (int) $this->config->get('config_language_id'));
			
			foreach ($query->rows as $value) {
				$type = $data[$value['option_id']]['type'];
				
				if ($type == 'checkbox' || $type == 'radio' || $type == 'select') {
					$data[$value['option_id']]['value'][$value['value_id']] = $value['name'];
				}
			}
		}
		
		$query = $this->db->query("SELECT oov2p.option_id, oov2p.value_id, oov2p.slide_value_min, oov2p.slide_value_max, oo.type FROM " . DB_PREFIX . "ocfilter_option_value_to_product oov2p LEFT JOIN " . DB_PREFIX . "ocfilter_option oo ON (oo.option_id = oov2p.option_id) WHERE oov2p.product_id = " . (int) $product_id);
		
		foreach ($query->rows as $value) {
			$product[$value['option_id']][$value['value_id']] = array ();
			
			if ($value['type'] == 'slide' || $value['type'] == 'slide_dual') {
				$product[$value['option_id']][$value['value_id']] = array ('slide_value_min' => $value['slide_value_min'], 'slide_value_max' => $value['slide_value_max']);
			}
		}
		
		$query = $this->db->query("SELECT option_id, value_id, language_id, description FROM " . DB_PREFIX . "ocfilter_option_value_to_product_description WHERE product_id = " . (int) $product_id);
		
		foreach ($query->rows as $value) {
			$product[$value['option_id']][$value['value_id']][$value['language_id']] = $value['description'];
		}
		
		return array ('data' => $data, 'product' => $product);
	}
	
	public function getProductCategory($product_id) {
		$sql = "SELECT p2c.*, cd.name FROM `" . DB_PREFIX . "product_to_category` p2c LEFT JOIN `" . DB_PREFIX . "category_description` cd ON (p2c.category_id = cd.category_id AND cd.language_id = '" . (int) $this->config->get('config_language_id') . "') WHERE ";
		
		if (is_array ($product_id)) {
			$sql .= "p2c.product_id IN ('" . implode ("','", $product_id) . "')";
		} else {
			$sql .= "p2c.product_id = '" . (int) $product_id . "'";
		}
		
		$query = $this->db->query($sql);
		
		if (is_array ($product_id)) {
			$data = array ();
			
			foreach ($query->rows as $array) {
				$data[$array['product_id']][] = $array;
			}
		} else {
			$data = $query->rows;
		}
		
		return $data;
	}
	
	public function getProductRecurring($product_id) {
		$query = $this->db->query("SELECT `recurring_id`, `customer_group_id` FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = '" . (int) $product_id . "'");
		
		return $query->rows;
	}
	
	public function getLinkToColumn($data) {
		$sql = '';
		$result = '';
		
		if ($data['link'] == 'product_to_category') {
			$sql = "SELECT CONCAT('<table>', GROUP_CONCAT('<tr><td>', cd.name, '</td></tr>' SEPARATOR ''), '</table>') AS `html` FROM `" . DB_PREFIX . "product_to_category` p2c LEFT JOIN `" . DB_PREFIX . "category_description` cd ON (p2c.category_id = cd.category_id AND cd.language_id = '" . (int) $this->config->get('config_language_id') . "') WHERE p2c.product_id = '" . (int) $data['product_id'] . "'";
		} else if ($data['link'] == 'product_attribute') {
			$sql = "SELECT CONCAT('<table>', GROUP_CONCAT('<tr><td>', ad.name, '</td><td>', pa.text, '</td>' SEPARATOR ''), '</table>') AS `html` FROM `" . DB_PREFIX . "product_attribute` pa LEFT JOIN `" . DB_PREFIX . "attribute_description` ad ON (ad.attribute_id = pa.attribute_id AND ad.language_id = '" . (int) $this->config->get('config_language_id') . "') WHERE pa.language_id = '" . (int) $this->config->get('config_language_id') . "' AND pa.product_id = '" . (int) $data['product_id'] . "'";
		} else if ($data['link'] == 'product_option') {
			$sql = "SELECT GROUP_CONCAT('<div>', od.name, IFNULL((SELECT CONCAT('<table>', GROUP_CONCAT('<tr><td>', ovd.name, '</td><td>', pov.quantity, '</td><td>', pov.subtract, '</td><td>', pov.price_prefix, pov.price, '</td><td>', pov.points_prefix, pov.points, '</td><td>', pov.weight_prefix, pov.weight, '</td></tr>' SEPARATOR ''), '</table>') FROM `" . DB_PREFIX . "product_option_value` pov LEFT JOIN `" . DB_PREFIX . "option_value_description` ovd ON (ovd.option_value_id = pov.option_value_id AND ovd.language_id = '" . (int) $this->config->get('config_language_id') . "') WHERE pov.product_option_id = po.product_option_id AND pov.product_id = '" . (int) $data['product_id'] . "' ), ''), '</div>' SEPARATOR '') AS `html` FROM `" . DB_PREFIX . "product_option` po LEFT JOIN `" . DB_PREFIX . "option_description` od ON (od.option_id = po.option_id AND od.language_id = '" . (int) $this->config->get('config_language_id') . "') WHERE po.product_id = '" . (int) $data['product_id'] . "'";
		}
		
		if ($sql) {
			$query = $this->db->query($sql);
			
			foreach ($query->rows as $value) {
				$result = $value['html'];
			}
		}
		
		return $result;
	}
}
?>
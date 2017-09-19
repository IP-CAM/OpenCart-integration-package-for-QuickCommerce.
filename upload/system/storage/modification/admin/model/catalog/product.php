<?php
class ModelCatalogProduct extends Model {
	public function addProduct($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "product SET model = '" . $this->db->escape($data['model']) . "', 
            sku = '" . $this->db->escape($data['sku']) . "', qbname = '" . $this->db->escape($data['qbname']) . "', parent_id = '" . $this->db->escape($data['parent_id']) . "',
             upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_added = NOW()");

		$product_id = $this->db->getLastId();


		$this->db->query("UPDATE " . DB_PREFIX . "product SET po_cost = '" . (float)$data['po_cost'] . "', po_model = '" . $this->db->escape($data['po_model']) . "', po_title = '" . $this->db->escape($data['po_title']) . "' WHERE product_id = '" . (int)$product_id . "'");
			

		$this->db->query("UPDATE " . DB_PREFIX . "product SET display_mode = '" . (float)$data['display_mode'] . "' WHERE product_id = '" . (int)$product_id . "'");
			
		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					// Removes duplicates
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "' AND language_id = '" . (int)$language_id . "'");

						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
				}
			}
		}

		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
			}
		}

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $product_reward) {
				if ((int)$product_reward['points'] > 0) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$product_reward['points'] . "'");
				}
			}
		}

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		if ($data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}

		if (isset($data['product_recurring'])) {
			foreach ($data['product_recurring'] as $recurring) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int)$product_id . ", customer_group_id = " . (int)$recurring['customer_group_id'] . ", `recurring_id` = " . (int)$recurring['recurring_id']);
			}
		}

		$this->cache->delete('product');

		return $product_id;
	}

	public function editProduct($product_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "product SET model = '" . $this->db->escape($data['model']) . "', 
            sku = '" . $this->db->escape($data['sku']) . "', qbname = '" . $this->db->escape($data['qbname']) . "', parent_id = '" . $this->db->escape($data['parent_id']) . "',
             upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");


		$this->db->query("UPDATE " . DB_PREFIX . "product SET po_cost = '" . (float)$data['po_cost'] . "', po_model = '" . $this->db->escape($data['po_model']) . "', po_title = '" . $this->db->escape($data['po_title']) . "' WHERE product_id = '" . (int)$product_id . "'");
			

		$this->db->query("UPDATE " . DB_PREFIX . "product SET display_mode = '" . (float)$data['display_mode'] . "' WHERE product_id = '" . (int)$product_id . "'");
			
		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");

		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");

		if (!empty($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					// Removes duplicates
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "', product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $value) {
				if ((int)$value['points'] > 0) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$value['points'] . "'");
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "'");

		if ($data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = " . (int)$product_id);

		if (isset($data['product_recurring'])) {
			foreach ($data['product_recurring'] as $product_recurring) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int)$product_id . ", customer_group_id = " . (int)$product_recurring['customer_group_id'] . ", `recurring_id` = " . (int)$product_recurring['recurring_id']);
			}
		}

		$this->cache->delete('product');
	}

	public function copyProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p WHERE p.product_id = '" . (int)$product_id . "'");

		if ($query->num_rows) {
			$data = $query->row;

			$data['sku'] = '';
			$data['upc'] = '';
			$data['viewed'] = '0';
			$data['keyword'] = '';
			$data['status'] = '0';

			$data['product_attribute'] = $this->getProductAttributes($product_id);
			$data['product_description'] = $this->getProductDescriptions($product_id);
			$data['product_discount'] = $this->getProductDiscounts($product_id);
			$data['product_filter'] = $this->getProductFilters($product_id);
			$data['product_image'] = $this->getProductImages($product_id);
			$data['product_option'] = $this->getProductOptions($product_id);
			$data['product_related'] = $this->getProductRelated($product_id);
			$data['product_reward'] = $this->getProductRewards($product_id);
			$data['product_special'] = $this->getProductSpecials($product_id);
			$data['product_category'] = $this->getProductCategories($product_id);
			$data['product_download'] = $this->getProductDownloads($product_id);
			$data['product_layout'] = $this->getProductLayouts($product_id);
			$data['product_store'] = $this->getProductStores($product_id);
			$data['product_recurrings'] = $this->getRecurrings($product_id);

			$this->addProduct($data);
		}
	}

	public function deleteProduct($product_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_recurring WHERE product_id = " . (int)$product_id);
		$this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_product WHERE product_id = '" . (int)$product_id . "'");

		$this->cache->delete('product');
	}


	public function getDb2Product($product_id) {
		$query = $this->db2->query("SELECT DISTINCT *, (SELECT keyword FROM " . DB2_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "') AS keyword FROM " . DB2_PREFIX . "product p LEFT JOIN " . DB2_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}
			
	public function getProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "') AS keyword FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}


    public function getQueryFilters() {
        $sql  = "SELECT f.filter_id AS `filter_id`, fd.name AS `name`, fgd.name AS `group` FROM " . DB_PREFIX . "filter f";
		$sql .= " LEFT JOIN " . DB_PREFIX . "filter_description fd ON (f.filter_id = fd.filter_id)";
		$sql .= " LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON (f.filter_group_id = fgd.filter_group_id)";
		$sql .= " LEFT JOIN " . DB_PREFIX . "filter_group fg ON (f.filter_group_id = fg.filter_group_id)";
		$sql .= " WHERE fd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		$sql .= " AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		$sql .= " ORDER BY fg.sort_order, fgd.name, f.sort_order, fd.name";

		$query = $this->db->query($sql);
		return $query->rows;
    }
    
    public function getQueryOptions() {
        $sql  = "SELECT od.option_id, od.name";
		$sql .= " FROM " . DB_PREFIX . "option_description od";
		$sql .= " WHERE od.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY od.name";
        
        $query = $this->db->query($sql);
		return $query->rows;
    }
    
    public function getQueryOptionValues() {
        $sql  = "SELECT ovd.option_value_id, ovd.name AS ov_name, od.name AS o_name";
		$sql .= " FROM " . DB_PREFIX . "option_value_description ovd";
		$sql .= " LEFT JOIN " . DB_PREFIX . "option_description od ON (ovd.option_id = od.option_id)";
		$sql .= " WHERE ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY ovd.option_value_id ORDER BY od.name, ovd.name";
        
        $query = $this->db->query($sql);
		return $query->rows;
    }
    
    public function getTotalQueryProducts($data = array()) {
		$prefix = '';
		$join = '';
		$where = '';

		$buildWhere = function (&$where, $condition) {
			$prefix = (empty($where)) ? " WHERE " : " AND ";
			return $where .= $prefix . $condition;
		};

		if (count($data['product_category']) > 0) {
			$join = " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)";

			if (in_array(0,$data['product_category'])) {
				$join .= " LEFT JOIN " . DB_PREFIX . "product_to_category p2c0x ON (p.product_id = p2c0x.product_id)";
				$buildWhere($where, "(p2c.category_id IN ('" .implode("', '", $data['product_category']). "') OR p2c0x.category_id IS NULL)");
			} else {
				$buildWhere($where, "p2c.category_id IN ('" .implode("', '", $data['product_category']). "')");
			}
		}

		if (count($data['manufacturer_ids']) > 0) {
			$buildWhere($where, "p.manufacturer_id IN ('" .implode("', '", $data['manufacturer_ids']). "')");
		}

		if (count($data['filters_ids']) > 0) {
			$join .= " LEFT JOIN " . DB_PREFIX . "product_filter prfil ON (p.product_id = prfil.product_id)";

			if (in_array(0,$data['filters_ids'])) {
				$join.=" LEFT JOIN " . DB_PREFIX . "product_filter pf0x ON (p.product_id = pf0x.product_id)";
				$buildWhere($where, "(prfil.filter_id IN ('" .implode("', '", $data['filters_ids']). "') OR pf0x.filter_id IS NULL)");
			} else {
				$buildWhere($where, "prfil.filter_id IN ('" .implode("', '", $data['filters_ids']). "')");
			}
		}

		if ($data['price_min'] != '') {
			$buildWhere($where, "p.price >= '" . (float)$data['price_min'] . "'");
		}

		if ($data['price_max'] != '') {
			$buildWhere($where, "p.price <= '" . (float)$data['price_max'] . "'");
		}

		// Discounts
		if ($data['d_price_min'] != '' OR $data['d_price_max'] != '' OR $data['d_cust_group_filter'] != 'any') {
			$join .= " LEFT JOIN " . DB_PREFIX . "product_discount pdisc ON (p.product_id = pdisc.product_id)";
		}
		if ($data['d_cust_group_filter'] != 'any') {
			$buildWhere($where, "pdisc.customer_group_id = '" . (int)$data['d_cust_group_filter'] . "'");
		}
		if ($data['d_price_min'] != '') {
			$buildWhere($where, "pdisc.price >= '" . (float)$data['d_price_min'] . "'");
		}

		if ($data['d_price_max'] != '') {
			$buildWhere($where, "pdisc.price <= '" . (float)$data['d_price_max'] . "'");
		}

		// Specials
		if ($data['s_price_min'] != '' OR $data['s_price_max'] != '' OR $data['s_cust_group_filter'] != 'any') {
			$join .= " LEFT JOIN " . DB_PREFIX . "product_special pspec ON (p.product_id = pspec.product_id)";
		}

		if ($data['s_cust_group_filter'] != 'any') {
			$buildWhere($where, "pspec.customer_group_id = '" . (int)$data['s_cust_group_filter'] . "'");
		}
		if ($data['s_price_min'] != '') {
			$buildWhere($where, "pspec.price >= '" . (float)$data['s_price_min'] . "'");
		}

		if ($data['s_price_max'] != '') {
			$buildWhere($where, "pspec.price <= '" . (float)$data['s_price_max'] . "'");
		}

		if ($data['tax_class_filter'] != 'any') {
			$buildWhere($where, "p.tax_class_id = '" . (int)$data['tax_class_filter'] . "'");
		}

		if ($data['stock_min'] != '') {
			$buildWhere($where, "p.quantity >= '" . (int)$data['stock_min'] . "'");
		}

		if ($data['stock_max'] != '') {
			$buildWhere($where, "p.quantity <= '" . (int)$data['stock_max'] . "'");
		}

		if ($data['min_q_min'] != '') {
			$buildWhere($where, "p.minimum >= '" . (int)$data['min_q_min'] . "'");
		}

		if ($data['min_q_max'] != '') {
			$buildWhere($where, "p.minimum <= '" . (int)$data['min_q_max'] . "'");
		}

		if ($data['stock_status_filter'] != 'any') {
			$buildWhere($where, "p.stock_status_id = '" . (int)$data['stock_status_filter'] . "'");
		}

		if ($data['subtract_filter'] != 'any') {
			$buildWhere($where, "p.subtract = '" . (int)$data['subtract_filter'] . "'");
		}

		if ($data['shipping_filter'] != 'any') {
			$buildWhere($where, "p.shipping = '" . (int)$data['shipping_filter'] . "'");
		}

		if ($data['date_min'] != '') {
			$buildWhere($where, "p.date_available >= '" . $this->db->escape($data['date_min']) . "'");
		}

		if ($data['date_max'] != '') {
			$buildWhere($where, "p.date_available <= '" . $this->db->escape($data['date_max']) . "'");
		}

		if ($data['date_added_min'] != '') {
			$buildWhere($where, "p.date_added >= '" . $this->db->escape($data['date_added_min']) . "'");
		}

		if ($data['date_added_max'] != '') {
			$buildWhere($where, "p.date_added <= '" . $this->db->escape($data['date_added_max']) . "'");
		}

		if ($data['date_modified_min'] != '') {
			$buildWhere($where, "p.date_modified >= '" . $this->db->escape($data['date_modified_min']) . "'");
		}

		if ($data['date_modified_max'] != '') {
			$buildWhere($where, "p.date_modified <= '" . $this->db->escape($data['date_modified_max']) . "'");
		}

		if ($data['filter_status'] != 'any') {
			$buildWhere($where, "p.status = '" . (int)$data['filter_status'] . "'");
		}

		if ($data['store_filter'] != 'any') {
			$join .= " LEFT JOIN " . DB_PREFIX . "product_to_store pts ON (p.product_id = pts.product_id)";
			$buildWhere($where, "pts.store_id = '" . (int)$data['store_filter'] . "'");
		}

		if ($data['filter_attr'] != 'any') {
            $join .= " LEFT JOIN " . DB_PREFIX . "product_attribute pattr ON (p.product_id = pattr.product_id)";
			$buildWhere($where, "pattr.attribute_id = '" . (int)$data['filter_attr'] . "'");
		}

		if ($data['filter_opti'] != 'any') {
			$join.=" LEFT JOIN " . DB_PREFIX . "product_option po ON (p.product_id = po.product_id)";
			$buildWhere($where, "po.option_id = '" . (int)$data['filter_opti'] . "'");
		}

		if ($data['filter_attr_val'] != '') {
			if ($data['filter_attr']=="any") {
				$join .= " LEFT JOIN " . DB_PREFIX . "product_attribute pattr ON (p.product_id = pattr.product_id)";
			}

			$buildWhere($where, "pattr.text LIKE '%" . $this->db->escape($data['filter_attr_val']) . "%'");
		}

		if ($data['filter_opti_val'] != 'any') {
			$join .= " LEFT JOIN " . DB_PREFIX . "product_option_value pov ON (p.product_id = pov.product_id)";
			$buildWhere($where, "pov.option_value_id = '" . (int)$data['filter_opti_val'] . "'");
		}

		if ($data['filter_name']!= '') {
			if (version_compare(VERSION, '1.5.4.1', '>')) {
				$buildWhere($where, "pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'");
			}
		}

		if ($data['filter_model'] != '') {
			if (version_compare(VERSION, '1.5.4.1', '>')) {
				$buildWhere($where, "p.model LIKE '%" . $this->db->escape($data['filter_model']) . "%'");
			} elseif (version_compare(VERSION, '1.5.1.2', '>')) {
				$buildWhere($where, "LCASE(p.model) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_model'])) . "%'");
			} else {
				$buildWhere($where, "LCASE(p.model) LIKE '%" . $this->db->escape(strtolower($data['filter_model'])) . "%'");
			}
		}

		if ($data['filter_tag'] != '') {
			if (version_compare(VERSION, '1.5.3.1', '>')) {
				$buildWhere($where, "LCASE(pd.tag) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_tag'])) . "%'");
			}
		}

		$buildWhere($where, "pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		$sql  = "SELECT COUNT(DISTINCT p.product_id) AS total";
		$sql .= " FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";
		$sql .= $join . $where;
        
		$query = $this->db->query($sql);

		return $query->row['total'];
	}
            

    public function getQueryProducts($data = array()) {
		$prefix = '';
		$join = '';
		$where = '';

		$buildWhere = function (&$where, $condition) {
			$prefix = (empty($where)) ? " WHERE " : " AND ";
			return $where .= $prefix . $condition;
		};

		if (count($data['product_category']) > 0) {
			$join = " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)";

			if (in_array(0,$data['product_category'])) {
				$join .= " LEFT JOIN " . DB_PREFIX . "product_to_category p2c0x ON (p.product_id = p2c0x.product_id)";
				$buildWhere($where, "(p2c.category_id IN ('" .implode("', '", $data['product_category']). "') OR p2c0x.category_id IS NULL)");
			} else {
				$buildWhere($where, "p2c.category_id IN ('" .implode("', '", $data['product_category']). "')");
			}
		}

		if (count($data['manufacturer_ids']) > 0) {
			$buildWhere($where, "p.manufacturer_id IN ('" .implode("', '", $data['manufacturer_ids']). "')");
		}

		if (count($data['filters_ids']) > 0) {
			$join .= " LEFT JOIN " . DB_PREFIX . "product_filter prfil ON (p.product_id = prfil.product_id)";

			if (in_array(0,$data['filters_ids'])) {
				$join.=" LEFT JOIN " . DB_PREFIX . "product_filter pf0x ON (p.product_id = pf0x.product_id)";
				$buildWhere($where, "(prfil.filter_id IN ('" .implode("', '", $data['filters_ids']). "') OR pf0x.filter_id IS NULL)");
			} else {
				$buildWhere($where, "prfil.filter_id IN ('" .implode("', '", $data['filters_ids']). "')");
			}
		}

		if ($data['price_min'] != '') {
			$buildWhere($where, "p.price >= '" . (float)$data['price_min'] . "'");
		}

		if ($data['price_max'] != '') {
			$buildWhere($where, "p.price <= '" . (float)$data['price_max'] . "'");
		}

		// Discounts
		if ($data['d_price_min'] != '' OR $data['d_price_max'] != '' OR $data['d_cust_group_filter'] != 'any') {
			$join .= " LEFT JOIN " . DB_PREFIX . "product_discount pdisc ON (p.product_id = pdisc.product_id)";
		}
		if ($data['d_cust_group_filter'] != 'any') {
			$buildWhere($where, "pdisc.customer_group_id = '" . (int)$data['d_cust_group_filter'] . "'");
		}
		if ($data['d_price_min'] != '') {
			$buildWhere($where, "pdisc.price >= '" . (float)$data['d_price_min'] . "'");
		}

		if ($data['d_price_max'] != '') {
			$buildWhere($where, "pdisc.price <= '" . (float)$data['d_price_max'] . "'");
		}

		// Specials
		if ($data['s_price_min'] != '' OR $data['s_price_max'] != '' OR $data['s_cust_group_filter'] != 'any') {
			$join .= " LEFT JOIN " . DB_PREFIX . "product_special pspec ON (p.product_id = pspec.product_id)";
		}

		if ($data['s_cust_group_filter'] != 'any') {
			$buildWhere($where, "pspec.customer_group_id = '" . (int)$data['s_cust_group_filter'] . "'");
		}
		if ($data['s_price_min'] != '') {
			$buildWhere($where, "pspec.price >= '" . (float)$data['s_price_min'] . "'");
		}

		if ($data['s_price_max'] != '') {
			$buildWhere($where, "pspec.price <= '" . (float)$data['s_price_max'] . "'");
		}

		if ($data['tax_class_filter'] != 'any') {
			$buildWhere($where, "p.tax_class_id = '" . (int)$data['tax_class_filter'] . "'");
		}

		if ($data['stock_min'] != '') {
			$buildWhere($where, "p.quantity >= '" . (int)$data['stock_min'] . "'");
		}

		if ($data['stock_max'] != '') {
			$buildWhere($where, "p.quantity <= '" . (int)$data['stock_max'] . "'");
		}

		if ($data['min_q_min'] != '') {
			$buildWhere($where, "p.minimum >= '" . (int)$data['min_q_min'] . "'");
		}

		if ($data['min_q_max'] != '') {
			$buildWhere($where, "p.minimum <= '" . (int)$data['min_q_max'] . "'");
		}

		if ($data['stock_status_filter'] != 'any') {
			$buildWhere($where, "p.stock_status_id = '" . (int)$data['stock_status_filter'] . "'");
		}

		if ($data['subtract_filter'] != 'any') {
			$buildWhere($where, "p.subtract = '" . (int)$data['subtract_filter'] . "'");
		}

		if ($data['shipping_filter'] != 'any') {
			$buildWhere($where, "p.shipping = '" . (int)$data['shipping_filter'] . "'");
		}

		if ($data['date_min'] != '') {
			$buildWhere($where, "p.date_available >= '" . $this->db->escape($data['date_min']) . "'");
		}

		if ($data['date_max'] != '') {
			$buildWhere($where, "p.date_available <= '" . $this->db->escape($data['date_max']) . "'");
		}

		if ($data['date_added_min'] != '') {
			$buildWhere($where, "p.date_added >= '" . $this->db->escape($data['date_added_min']) . "'");
		}

		if ($data['date_added_max'] != '') {
			$buildWhere($where, "p.date_added <= '" . $this->db->escape($data['date_added_max']) . "'");
		}

		if ($data['date_modified_min'] != '') {
			$buildWhere($where, "p.date_modified >= '" . $this->db->escape($data['date_modified_min']) . "'");
		}

		if ($data['date_modified_max'] != '') {
			$buildWhere($where, "p.date_modified <= '" . $this->db->escape($data['date_modified_max']) . "'");
		}

		if ($data['filter_status'] != 'any') {
			$buildWhere($where, "p.status = '" . (int)$data['filter_status'] . "'");
		}

		if ($data['store_filter'] != 'any') {
			$join .= " LEFT JOIN " . DB_PREFIX . "product_to_store pts ON (p.product_id = pts.product_id)";
			$buildWhere($where, "pts.store_id = '" . (int)$data['store_filter'] . "'");
		}

		if ($data['filter_attr'] != 'any') {
            $join .= " LEFT JOIN " . DB_PREFIX . "product_attribute pattr ON (p.product_id = pattr.product_id)";
			$buildWhere($where, "pattr.attribute_id = '" . (int)$data['filter_attr'] . "'");
		}

		if ($data['filter_opti'] != 'any') {
			$join.=" LEFT JOIN " . DB_PREFIX . "product_option po ON (p.product_id = po.product_id)";
			$buildWhere($where, "po.option_id = '" . (int)$data['filter_opti'] . "'");
		}

		if ($data['filter_attr_val'] != '') {
			if ($data['filter_attr']=="any") {
				$join .= " LEFT JOIN " . DB_PREFIX . "product_attribute pattr ON (p.product_id = pattr.product_id)";
			}

			$buildWhere($where, "pattr.text LIKE '%" . $this->db->escape($data['filter_attr_val']) . "%'");
		}

		if ($data['filter_opti_val'] != 'any') {
			$join .= " LEFT JOIN " . DB_PREFIX . "product_option_value pov ON (p.product_id = pov.product_id)";
			$buildWhere($where, "pov.option_value_id = '" . (int)$data['filter_opti_val'] . "'");
		}

		if ($data['filter_name']!= '') {
			if (version_compare(VERSION, '1.5.4.1', '>')) {
				$buildWhere($where, "pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'");
			}
		}

		if ($data['filter_model'] != '') {
			if (version_compare(VERSION, '1.5.4.1', '>')) {
				$buildWhere($where, "p.model LIKE '%" . $this->db->escape($data['filter_model']) . "%'");
			} elseif (version_compare(VERSION, '1.5.1.2', '>')) {
				$buildWhere($where, "LCASE(p.model) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_model'])) . "%'");
			} else {
				$buildWhere($where, "LCASE(p.model) LIKE '%" . $this->db->escape(strtolower($data['filter_model'])) . "%'");
			}
		}

		if ($data['filter_tag'] != '') {
			if (version_compare(VERSION, '1.5.3.1', '>')) {
				$buildWhere($where, "LCASE(pd.tag) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_tag'])) . "%'");
			}
		}

		$buildWhere($where, "pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		$sql  = "SELECT p.*, pd.name";
		$sql .= " FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";
		$sql .= $join . $where . " GROUP BY p.product_id";
        
        if (isset($data['sort'])) {
			$sort = $data['sort'];
		} else {
			$sort = 'pd.name';
		}
		if (isset($data['order'])) {
			$order = $data['order'];
		} else {
			$order = 'ASC';
		}
		if (isset($data['page'])) {
			$page = $data['page'];
		} else {
			$page = 1;
		}
        if (isset($data['max_results'])) {
            $max_results = $data['max_results'];
        } else {
            $max_results = 150; // Default from config should be passed in anyway
        }
        
        $settings = array(
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $max_results,
			'limit'           => $max_results
		);

        $sort_data = array(
            'pd.name',
            'p.model',
            'p.price',
            'p.quantity',
            'p.status',
            'p.product_id',
            'p.date_added',
            'p.date_modified',
            'p.viewed',
            'p.sort_order'
        );

        if (isset($settings['sort']) && in_array($settings['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $settings['sort'];	
        } else {
            $sql .= " ORDER BY pd.name";	
        }
        
        if (isset($settings['order']) && ($settings['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }
    
        if (isset($settings['start']) || isset($settings['limit'])) {
            if ($settings['start'] < 0) {
                $settings['start'] = 0;
            }				

            if ($settings['limit'] < 1) {
                $settings['limit'] = 20;
            }	
        
            $sql .= " LIMIT " . (int)$settings['start'] . "," . (int)$settings['limit'];
        }
        
		$query = $this->db->query($sql);

		return $query->rows;
	}
            

	public function getDb2Products($data = array()) {
		$sql = "SELECT db2p.*, db2pd.*, p.product_id AS local_id, p.model AS local_model";

		if (!empty($data['filter_category_id'])) {
			$sql .= " FROM " . DB2_DATABASE . "." . DB2_PREFIX . "category_path db2cp LEFT JOIN " . DB2_DATABASE . "." . DB2_PREFIX . "product_to_category db2p2c ON (db2cp.category_id = db2p2c.category_id)";
			$sql .= " LEFT JOIN " . DB2_DATABASE . "." . DB2_PREFIX . "product db2p ON (db2p2c.product_id = db2p.product_id)";
		} else {
			$sql .= " FROM " . DB2_DATABASE . "." . DB2_PREFIX . "product db2p";
		}
		
		$sql .= " LEFT JOIN " . DB2_DATABASE . "." . DB2_PREFIX . "product_description db2pd ON (db2p.product_id = db2pd.product_id)";

		if (!isset($data['filter_match']) || empty($data['filter_match'])) {
			$sql .= " LEFT OUTER JOIN " . DB_DATABASE . "." . DB_PREFIX . "product p ON (db2p.mpn = p.model)";
		} else {
			$sql .= " INNER JOIN " . DB_DATABASE . "." . DB_PREFIX . "product p ON (db2p.mpn = p.model)";
		}
		
		$sql .= " WHERE db2pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		
		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND db2cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND db2p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}
		}

		if (!empty($data['filter_name'])) {
			$sql .= " AND db2pd.name LIKE '" . $this->db2->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND db2p.model LIKE '" . $this->db2->escape($data['filter_model']) . "%'";
		}

		if (!empty($data['filter_mpn'])) {
			$sql .= " AND db2p.mpn LIKE '" . $this->db2->escape($data['filter_mpn']) . "%'";
		}

		if (!empty($data['filter_sku'])) {
			$sql .= " AND db2p.sku LIKE '" . $this->db->escape($data['filter_sku']) . "%'";
		}

		/*if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$sql .= " AND db2p.price LIKE '" . $this->db2->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " AND db2p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND db2p.status = '" . (int)$data['filter_status'] . "'";
		}*/
		
		$sql .= " GROUP BY db2p.product_id";

		$sort_data = array(
			'db2pd.name',
			'db2p.model',
			'db2p.price',
			'db2p.quantity',
			'db2p.status',
			'db2p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY db2pd.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db2->query($sql);

		return $query->rows;
	}
			
			

	public function editUrlAlias($product_id = null, $keyword = '') {
		if (!is_numeric($product_id) || $product_id < 1) {
			return false;
		}
		
		$keyword = (!empty($keyword)) ? $keyword : '';
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($keyword) . "'");
	}
	
	public function getProductsByCategory($data = array()) {
		$sql = 'SELECT *';		
		if (!empty($data['filter_category_id'])) {
			$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
		} else {
			$sql .= " FROM " . DB_PREFIX . "product p";
		}
		
		$sql .=  " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		
		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}
		}

		if (!empty($data['filter_mpn'])) {
			$sql .= " AND p.mpn LIKE '" . $this->db->escape($data['filter_mpn']) . "%'";
		}
				
			
		if (!empty($data['filter_name'])) {
			
		$sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";	
			
		}

		if (!empty($data['filter_model'])) {
			
		$sql .= " AND p.model LIKE '%" . $this->db->escape($data['filter_model']) . "%'";	
			
		}

		/*if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}*/
		
		$sql .= " GROUP BY p.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.price',
			'p.quantity',
			'p.status',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pd.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}
			
	public function getProducts($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";


		if (!empty($data['filter_mpn'])) {
			$sql .= " AND p.mpn LIKE '" . $this->db->escape($data['filter_mpn']) . "%'";
		}
				
			
		if (!empty($data['filter_name'])) {
			
		$sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";	
			
		}

		if (!empty($data['filter_model'])) {
			
		$sql .= " AND p.model LIKE '%" . $this->db->escape($data['filter_model']) . "%'";	
			
		}

		if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_image']) && !is_null($data['filter_image'])) {
			if ($data['filter_image'] == 1) {
				$sql .= " AND (p.image IS NOT NULL AND p.image <> '' AND p.image <> 'no_image.png')";
			} else {
				$sql .= " AND (p.image IS NULL OR p.image = '' OR p.image = 'no_image.png')";
			}
		}

		$sql .= " GROUP BY p.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.price',
			'p.quantity',
			'p.status',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pd.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getProductsByCategoryId($category_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2c.category_id = '" . (int)$category_id . "' ORDER BY pd.name ASC");

		return $query->rows;
	}

	public function getProductDescriptions($product_id) {
		$product_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'tag'              => $result['tag']
			);
		}

		return $product_description_data;
	}


	public function getDb2ProductCategories($product_id) {
		$product_category_data = array();

		$query = $this->db2->query("SELECT * FROM " . DB2_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_category_data[] = $result['category_id'];
		}

		return $product_category_data;
	}
			
	public function getProductCategories($product_id) {
		$product_category_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_category_data[] = $result['category_id'];
		}

		return $product_category_data;
	}

	public function getProductFilters($product_id) {
		$product_filter_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_filter_data[] = $result['filter_id'];
		}

		return $product_filter_data;
	}


	public function getDb2ProductAttributes($product_id) {
		$product_attribute_data = array();

		$product_attribute_query = $this->db2->query("SELECT attribute_id FROM " . DB2_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' GROUP BY attribute_id");

		foreach ($product_attribute_query->rows as $product_attribute) {
			$product_attribute_description_data = array();

			$product_attribute_description_query = $this->db2->query("SELECT * FROM " . DB2_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

			foreach ($product_attribute_description_query->rows as $product_attribute_description) {
				$product_attribute_description_data[$product_attribute_description['language_id']] = array('text' => $product_attribute_description['text']);
			}

			$product_attribute_data[] = array(
				'attribute_id'                  => $product_attribute['attribute_id'],
				'product_attribute_description' => $product_attribute_description_data
			);
		}

		return $product_attribute_data;
	}
			
	public function getProductAttributes($product_id) {
		$product_attribute_data = array();

		$product_attribute_query = $this->db->query("SELECT attribute_id FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' GROUP BY attribute_id");

		foreach ($product_attribute_query->rows as $product_attribute) {
			$product_attribute_description_data = array();

			$product_attribute_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

			foreach ($product_attribute_description_query->rows as $product_attribute_description) {
				$product_attribute_description_data[$product_attribute_description['language_id']] = array('text' => $product_attribute_description['text']);
			}

			$product_attribute_data[] = array(
				'attribute_id'                  => $product_attribute['attribute_id'],
				'product_attribute_description' => $product_attribute_description_data
			);
		}

		return $product_attribute_data;
	}


	// QC Mod
	public function getProductAttributeByName($product_id, $group_name, $attribute_name) {
		$product_attribute_data = array();

		$product_attribute_query = $this->db->query("SELECT DISTINCT a.attribute_id, ad.name, agd.name, pa.text FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_group_description agd ON (a.attribute_group_id = agd.attribute_group_id) WHERE pa.product_id = '" . (int)$product_id . "' AND ad.name = '" . $attribute_name . "' AND agd.name = '" . $group_name . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pa.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY a.sort_order, ad.name LIMIT 1");
		
		if (count($product_attribute_query->rows) > 0) {
			$product_attribute_data = $product_attribute_query->rows[0];
		}
		
		return $product_attribute_data;
	}
			

	public function getDb2ProductOptions($product_id) {
		$product_option_data = array();

		$product_option_query = $this->db2->query("SELECT * FROM `" . DB2_PREFIX . "product_option` po LEFT JOIN `" . DB2_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN `" . DB2_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($product_option_query->rows as $product_option) {
			$product_option_value_data = array();

			$product_option_value_query = $this->db2->query("SELECT * FROM " . DB2_PREFIX . "product_option_value WHERE product_option_id = '" . (int)$product_option['product_option_id'] . "'");

			foreach ($product_option_value_query->rows as $product_option_value) {
				$product_option_value_data[] = array(
					'product_option_value_id' => $product_option_value['product_option_value_id'],
					'option_value_id'         => $product_option_value['option_value_id'],
					'quantity'                => $product_option_value['quantity'],
					'subtract'                => $product_option_value['subtract'],
					'price'                   => $product_option_value['price'],
					'price_prefix'            => $product_option_value['price_prefix'],
					'points'                  => $product_option_value['points'],
					'points_prefix'           => $product_option_value['points_prefix'],
					'weight'                  => $product_option_value['weight'],
					'weight_prefix'           => $product_option_value['weight_prefix']
				);
			}

			$product_option_data[] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => $product_option['value'],
				'required'             => $product_option['required']
			);
		}

		return $product_option_data;
	}
			
	public function getProductOptions($product_id) {
		$product_option_data = array();

		$product_option_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option` po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($product_option_query->rows as $product_option) {
			$product_option_value_data = array();

			$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON(pov.option_value_id = ov.option_value_id) WHERE pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' ORDER BY ov.sort_order ASC");

			foreach ($product_option_value_query->rows as $product_option_value) {
				$product_option_value_data[] = array(
					'product_option_value_id' => $product_option_value['product_option_value_id'],
					'option_value_id'         => $product_option_value['option_value_id'],
					'quantity'                => $product_option_value['quantity'],
					'subtract'                => $product_option_value['subtract'],
					'price'                   => $product_option_value['price'],
					'price_prefix'            => $product_option_value['price_prefix'],
					'points'                  => $product_option_value['points'],
					'points_prefix'           => $product_option_value['points_prefix'],
					'weight'                  => $product_option_value['weight'],
					'weight_prefix'           => $product_option_value['weight_prefix']
				);
			}

			$product_option_data[] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => $product_option['value'],
				'required'             => $product_option['required']
			);
		}

		return $product_option_data;
	}

	public function getProductOptionValue($product_id, $product_option_value_id) {
		$query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}


	public function getDb2ProductImages($product_id) {
		$query = $this->db2->query("SELECT * FROM " . DB2_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}
			
	public function getProductImages($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}


	public function getDb2ProductDiscounts($product_id) {
		$query = $this->db2->query("SELECT * FROM " . DB2_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' ORDER BY quantity, priority, price");

		return $query->rows;
	}
			
	public function getProductDiscounts($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' ORDER BY quantity, priority, price");

		return $query->rows;
	}


	public function getDb2ProductSpecials($product_id) {
		$query = $this->db2->query("SELECT * FROM " . DB2_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' ORDER BY priority, price");

		return $query->rows;
	}
			
	public function getProductSpecials($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' ORDER BY priority, price");

		return $query->rows;
	}


	public function getDb2ProductRewards($product_id) {
		$product_reward_data = array();

		$query = $this->db2->query("SELECT * FROM " . DB2_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_reward_data[$result['customer_group_id']] = array('points' => $result['points']);
		}

		return $product_reward_data;
	}
			
	public function getProductRewards($product_id) {
		$product_reward_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_reward_data[$result['customer_group_id']] = array('points' => $result['points']);
		}

		return $product_reward_data;
	}


	public function getDb2ProductDownloads($product_id) {
		$product_download_data = array();

		$query = $this->db2->query("SELECT * FROM " . DB2_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_download_data[] = $result['download_id'];
		}

		return $product_download_data;
	}
			
	public function getProductDownloads($product_id) {
		$product_download_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_download_data[] = $result['download_id'];
		}

		return $product_download_data;
	}

	public function getProductStores($product_id) {
		$product_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_store_data[] = $result['store_id'];
		}

		return $product_store_data;
	}

	public function getProductLayouts($product_id) {
		$product_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $product_layout_data;
	}

	public function getProductRelated($product_id) {
		$product_related_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_related_data[] = $result['related_id'];
		}

		return $product_related_data;
	}

	public function getRecurrings($product_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = '" . (int)$product_id . "'");

		return $query->rows;
	}


	public function getDb2TotalProducts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT db2p.product_id) AS total";

		if (!empty($data['filter_category_id'])) {
			$sql .= " FROM " . DB2_DATABASE . "." . DB2_PREFIX . "category_path db2cp LEFT JOIN " . DB2_DATABASE . "." . DB2_PREFIX . "product_to_category db2p2c ON (db2cp.category_id = db2p2c.category_id)";
			$sql .= " LEFT JOIN " . DB2_DATABASE . "." . DB2_PREFIX . "product db2p ON (db2p2c.product_id = db2p.product_id)";
		} else {
			$sql .= " FROM " . DB2_DATABASE . "." . DB2_PREFIX . "product db2p";
		}

		$sql .= " LEFT JOIN " . DB2_DATABASE . "." . DB2_PREFIX . "product_description db2pd ON (db2p.product_id = db2pd.product_id)";

		if (!isset($data['filter_match']) || empty($data['filter_match'])) {
			$sql .= " LEFT OUTER JOIN " . DB_DATABASE . "." . DB_PREFIX . "product p ON (db2p.mpn = p.model)";
		} else {
			$sql .= " INNER JOIN " . DB_DATABASE . "." . DB_PREFIX . "product p ON (db2p.mpn = p.model)";
		}

		$sql .= " WHERE db2pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND db2cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND db2p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}
		}

		if (!empty($data['filter_name'])) {
			$sql .= " AND db2pd.name LIKE '" . $this->db2->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND db2p.model LIKE '" . $this->db2->escape($data['filter_model']) . "%'";
		}

		if (!empty($data['filter_mpn'])) {
			$sql .= " AND db2p.mpn LIKE '" . $this->db2->escape($data['filter_mpn']) . "%'";
		}

		if (!empty($data['filter_sku'])) {
			$sql .= " AND db2p.sku LIKE '" . $this->db->escape($data['filter_sku']) . "%'";
		}

		/*if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$sql .= " AND db2p.price LIKE '" . $this->db2->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " AND db2p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND db2p.status = '" . (int)$data['filter_status'] . "'";
		}*/

		$query = $this->db2->query($sql);

		return $query->row['total'];
	}
			
	public function getTotalProducts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";

		$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";


		if (!empty($data['filter_mpn'])) {
			$sql .= " AND p.mpn LIKE '" . $this->db->escape($data['filter_mpn']) . "%'";
		}
				
			
		if (!empty($data['filter_name'])) {
			
		$sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";	
			
		}

		if (!empty($data['filter_model'])) {
			
		$sql .= " AND p.model LIKE '%" . $this->db->escape($data['filter_model']) . "%'";	
			
		}

		if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_image']) && !is_null($data['filter_image'])) {
			if ($data['filter_image'] == 1) {
				$sql .= " AND (p.image IS NOT NULL AND p.image <> '' AND p.image <> 'no_image.png')";
			} else {
				$sql .= " AND (p.image IS NULL OR p.image = '' OR p.image = 'no_image.png')";
			}
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalProductsByTaxClassId($tax_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE tax_class_id = '" . (int)$tax_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByStockStatusId($stock_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE stock_status_id = '" . (int)$stock_status_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByWeightClassId($weight_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE weight_class_id = '" . (int)$weight_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByLengthClassId($length_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE length_class_id = '" . (int)$length_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByDownloadId($download_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_download WHERE download_id = '" . (int)$download_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByManufacturerId($manufacturer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByAttributeId($attribute_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_attribute WHERE attribute_id = '" . (int)$attribute_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByOptionId($option_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_option WHERE option_id = '" . (int)$option_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByProfileId($recurring_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_recurring WHERE recurring_id = '" . (int)$recurring_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}
}

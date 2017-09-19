<?php 
class ModelCatalogPurchaseOrderVendor extends Model {
	public function addVendor($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "purchase_order_vendor SET name = '" . $this->db->escape($data['name']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "'");
	
		$purchase_order_vendor_id = $this->db->getLastId();
	
		if (isset($data['manufacturer_id'])) {
			foreach ($data['manufacturer_id'] as $manufacturer_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "purchase_order_vendor_to_manufacturer SET purchase_order_vendor_id = '" . (int)$purchase_order_vendor_id . "', manufacturer_id = '" . (int)$manufacturer_id . "'");
			}
		}
	}

	public function editVendor($purchase_order_vendor_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "purchase_order_vendor SET name = '" . $this->db->escape($data['name']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "' WHERE purchase_order_vendor_id = '" . (int)$purchase_order_vendor_id . "'");
	
		$this->db->query("DELETE FROM " . DB_PREFIX . "purchase_order_vendor_to_manufacturer WHERE purchase_order_vendor_id = '" . (int)$purchase_order_vendor_id . "'");
	
		if (isset($data['manufacturer_id'])) {
			foreach ($data['manufacturer_id'] as $manufacturer_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "purchase_order_vendor_to_manufacturer SET purchase_order_vendor_id = '" . (int)$purchase_order_vendor_id . "', manufacturer_id = '" . (int)$manufacturer_id . "'");
			}
		}
	}
	
	public function deleteVendor($purchase_order_vendor_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "purchase_order_vendor WHERE purchase_order_vendor_id = '" . (int)$purchase_order_vendor_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "purchase_order_vendor_to_manufacturer WHERE purchase_order_vendor_id = '" . (int)$purchase_order_vendor . "'");
	}
		
	public function getVendor($purchase_order_vendor_id) {
		$query = $this->db->query("SELECT *, (SELECT name FROM " . DB_PREFIX . "country c WHERE c.country_id = pov.country_id) AS country, (SELECT name FROM " . DB_PREFIX . "zone z WHERE z.zone_id = pov.zone_id) AS zone FROM " . DB_PREFIX . "purchase_order_vendor pov WHERE pov.purchase_order_vendor_id = '" . (int)$purchase_order_vendor_id . "'");
		
		$manufacturer_query = $this->db->query("SELECT manufacturer_id FROM " . DB_PREFIX . "purchase_order_vendor_to_manufacturer WHERE purchase_order_vendor_id = '" . (int)$purchase_order_vendor_id . "'");
		
		$manufacturers['manufacturer_id'] = array();
		
		foreach ($manufacturer_query->rows as $result) {
			$manufacturers['manufacturer_id'][] = $result['manufacturer_id'];
		}
		
		return array_merge($query->row, $manufacturers);
	}
		
	public function getVendors($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "purchase_order_vendor";
		
		if (isset($data['filter_name'])) {
			$sql .= " WHERE name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
		
		$sql .= " ORDER BY name";	
		
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
	
	public function getTotalVendors() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "purchase_order_vendor");
		
		return $query->row['total'];
	}
	
	public function getProducts($manufacturer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON pd.product_id = p.product_id WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.manufacturer_id = '" . (int)$manufacturer_id . "'");
		
		$products = array();
		
		foreach ($query->rows as $result) {
			$has_option = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$result['product_id'] . "'");

			if ($has_option->num_rows) {
				$options_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$result['product_id'] . "'");
				
				$group = array();
				
				foreach ($options_query->rows as $value) {
					if(!array_key_exists($value['product_option_id'], $group)) {
						$group[$value['product_option_id']] = array();
					}
					
					$group[$value['product_option_id']][] = $value['product_option_value_id'];
				}
				
				$product_option_ids = array();
				
				$regroup = array();
				
				$i = 0;
				
				foreach ($group as $key => $value) {
					$product_option_ids[$i] = $key;
					
					$regroup[$i] = $value;
					
					$i++;
				}
				
				$final_group = array();
				
				if (!empty($regroup)) {
					foreach ($regroup[0] as $value0) {
						if (!empty($regroup[1])) {
							foreach ($regroup[1] as $value1) {
								if (!empty($regroup[2])) {
									foreach ($regroup[2] as $value2) {
										$final_group[] = $value0 . ':' . $value1 . ':' . $value2;
									}
								} else {
									$final_group[] = $value0 . ':' . $value1;
								}
							}
						} else {
							$final_group[] = $value0;
						}
					}
				} else {
					$sold_query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_product WHERE product_id = '" . (int)$result['product_id'] . "'");

					$products[] = array(
						'product_id'	=> $result['product_id'],
						'name'			=> $result['po_title'] ? $result['po_title'] : $result['name'],
						'model'			=> $result['po_model'] ? $result['po_model'] : $result['model'],
						'price'			=> (float)$result['po_cost'] ? $result['po_cost'] : number_format($result['price'] * (100 - $this->config->get('purchase_order_price')) / 100, 4),
						'stock'			=> $result['quantity'],
						'sold'			=> $sold_query->row['total'],
						'options'		=> array(),
						'hasOption'		=> $has_option->num_rows
					);
				}
				
				foreach ($final_group as $combination) {
					$options = array();
					
					$values = explode(':', $combination);
					
					$i = 0;
					
					$conditions = array();
					
					foreach ($values as $value) {
						$options[] = array(
							'product_option_id'			=> $product_option_ids[$i],
							'product_option_value_id'	=> $value
						);
						
						$conditions[] = $value;
						
						$i++;
					}
				
					$sold_query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_product WHERE product_id = '" . (int)$result['product_id'] . "'");
					
					/* Takes up too much resources to calculate per combination
					foreach ($sold_query->rows as $sold) {
						$sold_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_product_id = '" . (int)$sold['order_product_id'] . "'");
						
						$valid = false;
						
						foreach ($sold_option_query->rows as $sold_option) {
							if (in_array($sold_option['product_option_value_id'], $conditions)) {
								$valid = true;
							} else {
								$valid = false;
							}
						}
						
						if ($valid) {
							$total_purchased++;
						}
					}*/
					
					$products[] = array(
						'product_id'	=> $result['product_id'],
						'name'			=> $result['po_title'] ? $result['po_title'] : $result['name'],
						'model'			=> $result['po_model'] ? $result['po_model'] : $result['model'],
						'price'			=> (float)$result['po_cost'] ? $result['po_cost'] : number_format($result['price'] * (100 - $this->config->get('purchase_order_price')) / 100, 4),
						'stock'			=> $result['quantity'],
						'sold'			=> $sold_query->row['total'],
						'options'		=> $options,
						'hasOption'		=> $has_option->num_rows
					);
				}
			} else {
				$sold_query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_product WHERE product_id = '" . (int)$result['product_id'] . "'");

				$products[] = array(
					'product_id'	=> $result['product_id'],
					'name'			=> $result['po_title'] ? $result['po_title'] : $result['name'],
					'model'			=> $result['po_model'] ? $result['po_model'] : $result['model'],
					'price'			=> (float)$result['po_cost'] ? $result['po_cost'] : number_format($result['price'] * (100 - $this->config->get('purchase_order_price')) / 100, 4),
					'stock'			=> $result['quantity'],
					'sold'			=> $sold_query->row['total'],
					'options'		=> array(),
					'hasOption'		=> $has_option->num_rows
				);
			}
		}
		
		return $products;
	}
	
	public function getProduct($filter_name) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON pd.product_id = p.product_id WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pd.name LIKE '%" . $this->db->escape($filter_name) . "%'");
		
		$products = array();
		
		foreach ($query->rows as $result) {
			$sold_query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_product WHERE product_id = '" . (int)$result['product_id'] . "'");
		
			$options = array();
			
			$options_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$result['product_id'] . "'");
		
			$products[] = array(
				'product_id'	=> $result['product_id'],
				'name'			=> $result['po_title'] ? $result['po_title'] : $result['name'],
				'model'			=> $result['po_model'] ? $result['po_model'] : $result['model'],
				'price'			=> (float)$result['po_cost'] ? $result['po_cost'] : number_format($result['price'] * (100 - $this->config->get('purchase_order_price')) / 100, 4),
				'stock'			=> $result['quantity'],
				'sold'			=> $sold_query->row['total'],
				'options'		=> $options,
				'hasOption'		=> $options_query->num_rows
			);
		}
		
		return $products;
	}
	
	public function getOptions($product_id) {
		$options_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON o.option_id = po.option_id LEFT JOIN " . DB_PREFIX . "option_description od ON od.option_id = o.option_id WHERE od.language_id = '" . (int)$this->config->get('config_language_id') . "' AND product_id = '" . (int)$product_id . "' AND o.type != 'file'");
		
		$options = array();
		
		foreach ($options_query->rows as $result) {
			$options[] = array(
				'product_option_id'	=> $result['product_option_id'],
				'name'				=> $result['name'],
				'type'				=> $result['type']
			);
		}
		
		return $options;
	}
	
	public function getOptionValues($product_option_id) {
		$values_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON ov.option_value_id = pov.option_value_id LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON ovd.option_value_id = ov.option_value_id WHERE ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pov.product_option_id = '" . (int)$product_option_id . "'");
	
		$values = array();
		
		foreach ($values_query->rows as $result) {
			$values[] = array(
				'product_option_value_id'	=> $result['product_option_value_id'],
				'name'						=> $result['name'],
				'price'						=> $result['price'],
				'price_prefix'				=> $result['price_prefix']
			);
		}
		
		return $values;
	}
}
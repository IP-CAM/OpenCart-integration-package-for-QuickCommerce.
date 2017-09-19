<?php 
class ModelCatalogPurchaseOrderShipping extends Model {
	public function addShipping($data) {
		foreach ($data['shipping'] as $language_id => $value) {
			if (isset($purchase_order_shipping_id)) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "purchase_order_shipping SET purchase_order_shipping_id = '" . (int)$purchase_order_shipping_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			} else {
				$this->db->query("INSERT INTO " . DB_PREFIX . "purchase_order_shipping SET language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
				
				$purchase_order_shipping_id = $this->db->getLastId();
			}
		}
	}

	public function editShipping($purchase_order_shipping_id, $data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "purchase_order_shipping WHERE purchase_order_shipping_id = '" . (int)$purchase_order_shipping_id . "'");

		foreach ($data['shipping'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "purchase_order_shipping SET purchase_order_shipping_id = '" . (int)$purchase_order_shipping_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}
	}
	
	public function deleteShipping($purchase_order_shipping_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "purchase_order_shipping WHERE purchase_order_shipping_id = '" . (int)$purchase_order_shipping_id . "'");
	}
		
	public function getShipping($purchase_order_shipping_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purchase_order_shipping WHERE purchase_order_shipping_id = '" . (int)$purchase_order_shipping_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		return $query->row;
	}
		
	public function getShippings($data = array()) {
      	if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "purchase_order_shipping WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";
			
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
		} else {
			$query = $this->db->query("SELECT purchase_order_shipping_id, name FROM " . DB_PREFIX . "purchase_order_shipping WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");
		}	
	
		return $query->rows;				
	}
	
	public function getShippingDescriptions($purchase_order_shipping_id) {
		$purchase_order_shipping_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purchase_order_shipping WHERE purchase_order_shipping_id = '" . (int)$purchase_order_shipping_id . "'");
		
		foreach ($query->rows as $result) {
			$purchase_order_shipping_data[$result['language_id']] = array('name' => $result['name']);
		}
		
		return $purchase_order_shipping_data;
	}
	
	public function getTotalShippings() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "purchase_order_shipping WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		return $query->row['total'];
	}	
}
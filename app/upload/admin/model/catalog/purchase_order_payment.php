<?php 
class ModelCatalogPurchaseOrderPayment extends Model {
	public function addPayment($data) {
		foreach ($data['payment'] as $language_id => $value) {
			if (isset($purchase_order_payment_id)) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "purchase_order_payment SET purchase_order_payment_id = '" . (int)$purchase_order_payment_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			} else {
				$this->db->query("INSERT INTO " . DB_PREFIX . "purchase_order_payment SET language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
				
				$purchase_order_payment_id = $this->db->getLastId();
			}
		}
	}

	public function editPayment($purchase_order_payment_id, $data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "purchase_order_payment WHERE purchase_order_payment_id = '" . (int)$purchase_order_payment_id . "'");

		foreach ($data['payment'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "purchase_order_payment SET purchase_order_payment_id = '" . (int)$purchase_order_payment_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}
	}
	
	public function deletePayment($purchase_order_payment_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "purchase_order_payment WHERE purchase_order_payment_id = '" . (int)$purchase_order_payment_id . "'");
	}
		
	public function getPayment($purchase_order_payment_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purchase_order_payment WHERE purchase_order_payment_id = '" . (int)$purchase_order_payment_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		return $query->row;
	}
		
	public function getPayments($data = array()) {
      	if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "purchase_order_payment WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";
			
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
			$query = $this->db->query("SELECT purchase_order_payment_id, name FROM " . DB_PREFIX . "purchase_order_payment WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");
		}	
	
		return $query->rows;				
	}
	
	public function getPaymentDescriptions($purchase_order_payment_id) {
		$purchase_order_payment_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purchase_order_payment WHERE purchase_order_payment_id = '" . (int)$purchase_order_payment_id . "'");
		
		foreach ($query->rows as $result) {
			$purchase_order_payment_data[$result['language_id']] = array('name' => $result['name']);
		}
		
		return $purchase_order_payment_data;
	}
	
	public function getTotalPayments() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "purchase_order_payment WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		return $query->row['total'];
	}	
}
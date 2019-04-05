<?php 
class ModelCatalogPurchaseOrder extends Model {
	public function addOrder($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "purchase_order SET order_name = '" . $this->db->escape($data['order_name']) . "', status_id = '" . (int)$data['status_id'] . "', purchase_order_vendor_id = '" . (int)$data['purchase_order_vendor_id'] . "', purchase_order_payment_id = '" . (int)$data['purchase_order_payment_id'] . "', purchase_order_shipping_id = '" . (int)$data['purchase_order_shipping_id'] . "', total = '" . (float)$data['total'] . "', comment = '" . $this->db->escape($data['comment']) . "', date_arrival = '" . $this->db->escape($data['date_arrival']) . "', date_added = NOW()");
	
		$purchase_order_id = $this->db->getLastId();
	
		if (!isset($data['received'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "purchase_order SET received = '0' AND date_received = '0000-00-00' WHERE purchase_order_id = '" . (int)$purchase_order_id . "'");
		} else {
			$this->db->query("UPDATE " . DB_PREFIX . "purchase_order SET received = '1' AND date_received = '" . $this->db->escape($data['date_received']) . "' WHERE purchase_order_id = '" . (int)$purchase_order_id . "'");
		}
		
		if (isset($data['products'])) {
			foreach ($data['products'] as $product) {
				if ($product['quantity'] > 0) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "purchase_order_product SET purchase_order_id = '" . (int)$purchase_order_id . "', product_id = '" . (int)$product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int)$product['quantity'] . "', price = '" . (float)$product['price'] . "', total = '" . (float)$product['total'] . "'");
				
					$purchase_order_product_id = $this->db->getLastId();
					
					if (isset($product['options'])) {
						foreach ($product['options'] as $option) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "purchase_order_option SET purchase_order_id = '" . (int)$purchase_order_id . "', purchase_order_product_id = '" . (int)$purchase_order_product_id . "', product_option_id = '" . (int)$option['product_option_id'] . "', product_option_value_id = '" . (int)$option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', value = '" . $this->db->escape($option['value']) . "'");
						}
					}
				}
			}
		}
		
		if (isset($data['totals'])) {
			foreach ($data['totals'] as $key => $total) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "purchase_order_total SET purchase_order_id = '" . (int)$purchase_order_id . "', name = '" . $this->db->escape($total['name']) . "', value = '" . (float)$total['value'] . "', sort_order = '" . (int)$key . "'");
			}
		}
		
		return $purchase_order_id;
	}

	public function editOrder($purchase_order_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "purchase_order SET order_name = '" . $this->db->escape($data['order_name']) . "', status_id = '" . (int)$data['status_id'] ."', purchase_order_vendor_id = '" . (int)$data['purchase_order_vendor_id'] . "', purchase_order_payment_id = '" . (int)$data['purchase_order_payment_id'] . "', purchase_order_shipping_id = '" . (int)$data['purchase_order_shipping_id'] . "', total = '" . (float)$data['total'] . "', comment = '" . $this->db->escape($data['comment']) . "', date_arrival = '" . $this->db->escape($data['date_arrival']) . "' WHERE purchase_order_id = '" . (int)$purchase_order_id . "'");
	
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purchase_order WHERE purchase_order_id = '" . (int)$purchase_order_id . "'");
	
		if (!isset($data['received'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "purchase_order SET received = '0' AND date_received = '0000-00-00' WHERE purchase_order_id = '" . (int)$purchase_order_id . "'");
		} else {
			if ($query->row['received']) {
				$this->db->query("UPDATE " . DB_PREFIX . "purchase_order SET received = '1' AND date_received = '" . $this->db->escape($data['date_received']) . "' WHERE purchase_order_id = '" . (int)$purchase_order_id . "'");
			} else {
				$this->received($purchase_order_id, $data['date_received']);
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "purchase_order_total WHERE purchase_order_id = '" . (int)$purchase_order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "purchase_order_product WHERE purchase_order_id = '" . (int)$purchase_order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "purchase_order_option WHERE purchase_order_id = '" . (int)$purchase_order_id . "'");
		
		if (isset($data['products'])) {
			foreach ($data['products'] as $product) {
				if ($product['quantity'] > 0) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "purchase_order_product SET purchase_order_id = '" . (int)$purchase_order_id . "', product_id = '" . (int)$product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int)$product['quantity'] . "', price = '" . (float)$product['price'] . "', total = '" . (float)$product['total'] . "'");
				
					$purchase_order_product_id = $this->db->getLastId();
					
					if (isset($product['options'])) {
						foreach ($product['options'] as $option) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "purchase_order_option SET purchase_order_id = '" . (int)$purchase_order_id . "', purchase_order_product_id = '" . (int)$purchase_order_product_id . "', product_option_id = '" . (int)$option['product_option_id'] . "', product_option_value_id = '" . (int)$option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', value = '" . $this->db->escape($option['value']) . "'");
						}
					}
				}
			}
		}
		
		if (isset($data['totals'])) {
			foreach ($data['totals'] as $key => $total) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "purchase_order_total SET purchase_order_id = '" . (int)$purchase_order_id . "', name = '" . $this->db->escape($total['name']) . "', value = '" . (float)$total['value'] . "', sort_order = '" . (int)$key . "'");
			}
		}
	}
	
	public function deleteOrder($purchase_order_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "purchase_order WHERE purchase_order_id = '" . (int)$purchase_order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "purchase_order_history WHERE purchase_order_id = '" . (int)$purchase_order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "purchase_order_total WHERE purchase_order_id = '" . (int)$purchase_order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "purchase_order_product WHERE purchase_order_id = '" . (int)$purchase_order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "purchase_order_option WHERE purchase_order_id = '" . (int)$purchase_order_id . "'");
	}
		
	public function getOrder($purchase_order_id) {
		$query = $this->db->query("SELECT *, (SELECT name FROM " . DB_PREFIX . "purchase_order_vendor pov WHERE pov.purchase_order_vendor_id = po.purchase_order_vendor_id) AS vendor, (SELECT name FROM " . DB_PREFIX . "purchase_order_payment pop WHERE pop.purchase_order_payment_id = po.purchase_order_payment_id AND pop.language_id = '" . (int)$this->config->get('config_language_id') . "') AS purchase_order_payment, (SELECT name FROM " . DB_PREFIX . "purchase_order_shipping pos WHERE pos.purchase_order_shipping_id = po.purchase_order_shipping_id AND pos.language_id = '" . (int)$this->config->get('config_language_id') . "') AS purchase_order_shipping FROM " . DB_PREFIX . "purchase_order po WHERE purchase_order_id = '" . (int)$purchase_order_id . "'");
		
		if ($query->num_rows) {
			$products_query = $this->db->query("SELECT *, pop.name AS name, pop.model AS model, pop.quantity AS quantity, p.quantity AS stock, pop.price AS price, pop.total AS total FROM " . DB_PREFIX . "purchase_order_product pop LEFT JOIN " . DB_PREFIX . "product p ON p.product_id = pop.product_id WHERE pop.purchase_order_id = '" . (int)$purchase_order_id . "'");
			
			$products = array();
			
			foreach ($products_query->rows as $result) {
				$options_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purchase_order_option WHERE purchase_order_id = '" . (int)$purchase_order_id . "' AND purchase_order_product_id = '" . (int)$result['purchase_order_product_id'] . "'");
				
				$options = array();
				
				if ($options_query->num_rows) {
					foreach ($options_query->rows as $option) {
						$values_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON ov.option_value_id = pov.option_value_id LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON ovd.option_value_id = ov.option_value_id WHERE ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pov.product_option_id = '" . (int)$option['product_option_id'] . "'");

						$options[] = array(
							'product_option_id'			=> $option['product_option_id'],
							'product_option_value_id'	=> $option['product_option_value_id'],
							'name'						=> $option['name'],
							'value'						=> $option['value'],
							'values'					=> $values_query->rows
						);
					}
				}
				
				$sold_query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_product WHERE product_id = '" . (int)$result['product_id'] . "'");
				
				$has_option = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$result['product_id'] . "'");
				
				$product_options_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON o.option_id = po.option_id LEFT JOIN " . DB_PREFIX . "option_description od ON od.option_id = o.option_id WHERE od.language_id = '" . (int)$this->config->get('config_language_id') . "' AND product_id = '" . (int)$result['product_id'] . "' AND o.type != 'file'");
					
				$products[] = array(
					'product_id'		=> $result['product_id'],
					'name'				=> $result['name'],
					'model'				=> $result['model'],
					'quantity'			=> $result['quantity'],
					'price'				=> $result['price'],
					'total'				=> $result['total'],
					'stock'				=> $result['stock'],
					'sold'				=> $sold_query->row['total'],
					'hasOption'			=> $has_option->num_rows,
					'options'			=> $options,
					'product_options'	=> $product_options_query->rows
				);
			}
			
			$histories_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purchase_order_history WHERE purchase_order_id = '" . (int)$purchase_order_id . "' ORDER BY date_added DESC");
			$totals_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purchase_order_total WHERE purchase_order_id = '" . (int)$purchase_order_id . "' ORDER BY sort_order");
			
			return array(
				'purchase_order_id'				=> $query->row['purchase_order_id'],
				'order_name'					=> $query->row['order_name'],
				'vendor'						=> $query->row['vendor'],
				'status_id'						=> $query->row['status_id'],
				'purchase_order_vendor_id'		=> $query->row['purchase_order_vendor_id'],
				'purchase_order_payment_id'		=> $query->row['purchase_order_payment_id'],
				'purchase_order_shipping_id'	=> $query->row['purchase_order_shipping_id'],
				'purchase_order_payment'		=> $query->row['purchase_order_payment'],
				'purchase_order_shipping'		=> $query->row['purchase_order_shipping'],
				'total'							=> $query->row['total'],
				'comment'						=> $query->row['comment'],
				'date_arrival'					=> $query->row['date_arrival'],
				'date_received'					=> $query->row['date_received'],
				'date_added'					=> $query->row['date_added'],
				'received'						=> $query->row['received'],
				'products'						=> $products,
				'histories'						=> $histories_query->rows,
				'totals'						=> $totals_query->rows
			);
		} else {
			return false;
		}
	}
		
	public function getOrders($data = array()) {
		$sql = "SELECT *, pov.name AS vendor, pop.name AS payment, pos.name AS shipping, (SELECT name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = po.status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status FROM " . DB_PREFIX . "purchase_order po LEFT JOIN " . DB_PREFIX . "purchase_order_vendor pov ON pov.purchase_order_vendor_id = po.purchase_order_vendor_id LEFT JOIN " . DB_PREFIX . "purchase_order_payment pop ON pop.purchase_order_payment_id = po.purchase_order_payment_id LEFT JOIN " . DB_PREFIX . "purchase_order_shipping pos ON pos.purchase_order_shipping_id = po.purchase_order_shipping_id";
		
		$implode = array();
		
		if ($this->config->get('purchase_order_hide')) {
			$implode[] .= "received = '0'";
		}
		
		if (!empty($data['filter_purchase_order_id'])) {
			$implode[] .= "po.purchase_order_id = '" . (int)$data['filter_purchase_order_id'] . "'";
		}
		
		if (!empty($data['filter_order_name'])) {
			$implode[] .= "LOWER(po.order_name) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_order_name'])) . "%'";
		}
		
		if (!empty($data['filter_purchase_order_vendor_id'])) {
			$implode[] .= "po.purchase_order_vendor_id = '" . (int)$data['filter_purchase_order_vendor_id'] . "'";
		}
		
		if (!empty($data['filter_status_id'])) {
			$implode[] .= "po.status_id = '" . (int)$data['filter_status_id'] . "'";
		}
		
		$implode[] .= "pos.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		
		$implode[] .= "pop.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		
		$sort_array = array(
			'purchase_order_id',
			'order_name',
			'vendor',
			'status',
			'payment',
			'shipping',
			'total',
			'date_arrival',
			'date_received',
			'date_added'
		);
		
		if (isset($data['sort']) && in_array($data['sort'], $sort_array)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY order_name";	
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
	
	public function getTotalOrders() {
      	$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "purchase_order po";
		
		$implode = array();
		
		if ($this->config->get('purchase_order_hide')) {
			$implode[] .= "received = '0'";
		}
		
		if (!empty($data['filter_purchase_order_id'])) {
			$implode[] .= "po.purchase_order_id = '" . (int)$data['filter_purchase_order_id'] . "'";
		}
		
		if (!empty($data['filter_order_name'])) {
			$implode[] .= "LOWER(po.order_name) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_order_name'])) . "%'";
		}
		
		if (!empty($data['filter_purchase_order_vendor_id'])) {
			$implode[] .= "po.purchase_order_vendor_id = '" . (int)$data['filter_purchase_order_vendor_id'] . "'";
		}
		
		if (!empty($data['filter_status_id'])) {
			$implode[] .= "po.status_id = '" . (int)$data['filter_status_id'] . "'";
		}
		
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		
		$query = $this->db->query($sql);
		
		return $query->row['total'];
	}
	
	public function received($purchase_order_id, $date = false) {
		if (!$date) {
			$date = date('Y-m-d H:i:s');
		}
	
		$query = $this->db->query("SELECT received FROM " . DB_PREFIX . "purchase_order WHERE purchase_order_id = '" . (int)$purchase_order_id . "' AND received = '0'");
		
		if ($query->num_rows) {
			$this->db->query("UPDATE " . DB_PREFIX . "purchase_order SET received = '1', date_received = '" . $this->db->escape($date) . "' WHERE purchase_order_id = '" . (int)$purchase_order_id . "'");
		
			$order_info = $this->getOrder($purchase_order_id);
		
			if ($this->config->get('purchase_order_add_stock')) {
				foreach ($order_info['products'] as $product) {
					$this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = quantity + " . (int)$product['quantity'] . " WHERE product_id = '" . (int)$product['product_id'] . "'");
					
					foreach ($product['options'] as $option) {
						$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = quantity + " . (int)$product['quantity'] . " WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "'");
					}
				}
			}
			
			// Notify Vendor
			if ($this->config->get('purchase_order_receive_email')) {
				$this->load->model('catalog/purchase_order_vendor');
				
				$vendor_info = $this->model_catalog_purchase_order_vendor->getVendor($order_info['purchase_order_vendor_id']);
				
				$this->load->language('catalog/purchase_order');
				
				$subject = sprintf($this->language->get('mail_subject'), $this->config->get('config_name'), $order_info['order_name'], $order_info['purchase_order_id']);
				
				$message  = $this->language->get('mail_greeting') . "\n\n";
				$message .= sprintf($this->language->get('mail_received'), $order_info['order_name'], $order_info['purchase_order_id']) . "\n\n";
				$message .= $this->language->get('mail_queries') . "\n\n";
				$message .= $this->language->get('mail_thanks') . "\n";
				$message .= $this->config->get('config_name');
				
				$mail = new Mail($this->config->get('config_mail'));
				$mail->setTo($vendor_info['email']);
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender($this->config->get('config_name'));
				$mail->setSubject(html_entity_decode($subject, ENT_QUOTES));
				$mail->setText(strip_tags(html_entity_decode($message, ENT_QUOTES, 'UTF-8')));
				$mail->send();
			}
			
			return true;
		} else {
			return false;
		}
	}
}
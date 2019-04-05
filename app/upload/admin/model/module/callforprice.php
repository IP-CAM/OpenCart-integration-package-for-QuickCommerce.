<?php 
class ModelModuleCallForPrice extends Model {
	public function install(){
		$query = $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "callforprice`
		(`callforprice_id` INT(11) NOT NULL AUTO_INCREMENT, 
		 `customer_phone` VARCHAR(200) NULL DEFAULT NULL,
		 `customer_name` VARCHAR(100) NULL DEFAULT NULL,
		 `product_id` INT(11) NULL DEFAULT '0',
 		 `notes` VARCHAR(255) NULL DEFAULT NULL,
		 `anotes` VARCHAR(255) NULL DEFAULT NULL,
		 `date_created` DATETIME  NOT NULL DEFAULT '0000-00-00 00:00:00',
		 `customer_notified` TINYINT(1) NOT NULL DEFAULT '0',
		 `store_id` int(11) NOT NULL DEFAULT 0,
		  PRIMARY KEY (`callforprice_id`));");	
	  
		$this->db->query("UPDATE `" . DB_PREFIX . "modification` SET status=1 WHERE `name` LIKE'%CallForPrice by iSenseLabs%'");
		$modifications = $this->load->controller('extension/modification/refresh');
	}
	
	public function uninstall()	{
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "callforprice`");
		  
		$this->db->query("UPDATE `" . DB_PREFIX . "modification` SET status=0 WHERE `name` LIKE'%CallForPrice by iSenseLabs%'");
		$modifications = $this->load->controller('extension/modification/refresh');
	}
	
	public function viewcustomers($store_id=0, $filter_name='', $page=1, $limit=8, $sort="id", $order="DESC") {		
		if ($page) {
			$start = ($page - 1) * $limit;
		}
		
		$query = "SELECT cfp.*, product.name as product_name FROM `" . DB_PREFIX . "callforprice` cfp 
		JOIN `" . DB_PREFIX . "product_description` product on cfp.product_id = product.product_id
		WHERE customer_notified=0";
		
		if (!empty($filter_name)) {
			$query .= " AND product.name LIKE '" . $this->db->escape($filter_name) . "%'";
		}
		
		$query .=" and language_id = " . (int)$this->config->get('config_language_id') . " AND store_id='".$store_id."'
		ORDER BY `date_created` DESC LIMIT ".$start.", ".$limit;
		
		$query = $this->db->query($query);
		return $query->rows;
	}
	
	public function viewnotifiedcustomers($store_id=0, $filter_name='', $page=1, $limit=8, $sort="id", $order="DESC") {	
		if ($page) {
			$start = ($page - 1) * $limit;
		}
		
		$query = "SELECT cfp.*, product.name as product_name FROM `" . DB_PREFIX . "callforprice` cfp 
		JOIN `" . DB_PREFIX . "product_description` product on cfp.product_id = product.product_id
		WHERE customer_notified=1";
		
		if (!empty($filter_name)) {
			$query .= " AND product.name LIKE '" . $this->db->escape($filter_name) . "%'";
		}
		
		$query .=" and language_id = " . (int)$this->config->get('config_language_id') . " AND store_id='".$store_id."'
		ORDER BY `date_created` DESC LIMIT ".$start.", ".$limit;
		
		$query = $this->db->query($query);
		return $query->rows; 
	}
	
	public function getTotalCustomers($store_id=0, $filter_name=''){
		$query = "SELECT COUNT(*) as `count` FROM `" . DB_PREFIX . "callforprice` cfp 
		JOIN `" . DB_PREFIX . "product_description` product on cfp.product_id = product.product_id
		WHERE customer_notified=0";
		
		if (!empty($filter_name)) {
			$query .= " AND product.name LIKE '" . $this->db->escape($filter_name) . "%'";
		}
		
		$query .=" and language_id = " . (int)$this->config->get('config_language_id') . " AND store_id='".$store_id."'";
		
		$query = $this->db->query($query);
		return $query->row['count']; 
	}
	
	public function getTotalNotifiedCustomers($store_id=0, $filter_name=''){
		$query = "SELECT COUNT(*) as `count` FROM `" . DB_PREFIX . "callforprice` cfp 
		JOIN `" . DB_PREFIX . "product_description` product on cfp.product_id = product.product_id
		WHERE customer_notified=1";
		
		if (!empty($filter_name)) {
			$query .= " AND product.name LIKE '" . $this->db->escape($filter_name) . "%'";
		}
		
		$query .=" and language_id = " . (int)$this->config->get('config_language_id') . " AND store_id='".$store_id."'";
		
		$query = $this->db->query($query);
		return $query->row['count']; 
		
	}
	
	public function sendToArchive($product_id) {
		$update_customers = $this->db->query("UPDATE `" . DB_PREFIX . "callforprice` SET customer_notified=1 WHERE product_id = ".$product_id."");	
	}
}
?>
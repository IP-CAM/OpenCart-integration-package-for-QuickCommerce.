<?php

class ModelSalePaymentProcessor extends Model {

	public function getPayments($order_id) {
		$query = $this->db->query("SELECT `amount` FROM `" . DB_PREFIX . "oe_payment_processor` WHERE `order_id` = '" . (int)$order_id . "'");
		return $query->rows;
	}

	public function install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oe_payment_processor` (
			`oe_payment_processor_id` int(11) NOT NULL auto_increment,
			`order_id` int(11) NOT NULL,
			`cardholder` varchar(255) COLLATE utf8_general_ci NOT NULL,
			`last_four` int(4) NOT NULL,
			`expiration` varchar(7) COLLATE utf8_general_ci NOT NULL,
			`amount` decimal(15,4) NOT NULL,
			`transaction_id` varchar(255) COLLATE utf8_general_ci NOT NULL,
			`type` int(1) NOT NULL,
			`process_date` int(11) NOT NULL,
			`new_order` tinyint(1) NOT NULL,
			PRIMARY KEY (`oe_payment_processor_id`)
		);");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "oe_modules` SET `module_name` = 'Payment Processor', `module_code` = 'payment_processor'");
		$orders = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE `order_status_id` > '0'");
		if ($orders->num_rows) {
			foreach ($orders->rows as $row) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "oe_payment_processor` SET `order_id` = '" . (int)$row['order_id'] . "', `amount` = '" . (float)$row['total'] . "', `process_date` = '" . strtotime($row['date_added']) . "', `new_order` = '0'");
			}
		}
		return;
	}
	
	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "oe_payment_processor`");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "oe_modules` WHERE `module_code` = 'payment_processor'");
		return;
	}

}

?>
<?php

class ModelSaleOrderEntry extends Model {

	public function checkEmail($email) {
		$query = $this->db->query("SELECT `email` FROM `" . DB_PREFIX . "customer` WHERE LCASE(`email`) = '" . $this->db->escape(strtolower($email)) . "'");
		if ($query->num_rows) {
			return 1;
		} else {
			return 0;
		}
	}

	public function getModules() {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "oe_modules` ORDER BY `module_name`");
		return $query->rows;
	}

	public function enable($module_id) {
		$module_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "oe_modules` WHERE `oe_module_id` = '" . (int)$module_id . "'");
		if ($module_query->num_rows) {
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `value` = '1' WHERE `key` = '" . $module_query->row['module_code'] . "_status'");
			return 1;
		} else {
			return 0;
		}
	}
	
	public function disable($module_id) {
		$module_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "oe_modules` WHERE `oe_module_id` = '" . (int)$module_id . "'");
		if ($module_query->num_rows) {
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `value` = '0' WHERE `key` = '" . $module_query->row['module_code'] . "_status'");
			return 1;
		} else {
			return 0;
		}
	}

	public function updateTables() {
		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "oe_order_product` LIKE 'order_product_id'");
		if ($query->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "oe_order_product` ADD COLUMN `order_product_id` int(11) NOT NULL");
		}
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "modification` MODIFY COLUMN `xml` longtext COLLATE utf8_general_ci NOT NULL");
		return;
	}

	public function install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oe_order` (
			`oe_order_id` int(11) NOT NULL auto_increment,
			`order_id` int(11) NOT NULL UNIQUE,
			PRIMARY KEY (`oe_order_id`)
		);");
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oe_order_product` (
			`oe_order_product_id` int(11) NOT NULL auto_increment,
			`oe_order_id` int(11) NOT NULL,
			`order_product_id` int(11) NOT NULL,
			`order_id` int(11) NOT NULL,
			`product_id` int(11) NOT NULL,
			`notax` tinyint(1) NOT NULL,
			PRIMARY KEY (`oe_order_product_id`)
		);");
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oe_modules` (
			`oe_module_id` int(11) NOT NULL auto_increment,
			`module_name` varchar(255) COLLATE utf8_general_ci NOT NULL,
			`module_code` varchar(255) COLLATE utf8_general_ci NOT NULL,
			PRIMARY KEY (`oe_module_id`)
		);");
		return;
	}
	
	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "oe_order`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "oe_order_product`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "oe_modules`");
		return;
	}

}

?>
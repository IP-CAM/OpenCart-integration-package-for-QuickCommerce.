<?php

class ModelApiPaymentProcessor extends Model {

	public function getTempOrderId() {
		$order_id = 1;
		$query = $this->db->query("SELECT MAX(`order_id`) AS `last_order_id` FROM `" . DB_PREFIX . "order`");
		if ($query->num_rows) {
			$order_id = $query->row['last_order_id'] + 1;
		}
		return $order_id;
	}

	public function addPayment($data) {
		$last_four = str_replace("-", "", $data['cc_number']);
		$last_four = substr($data['cc_number'], -4);
		if (isset($data['payment_amount'])) {
			$amount = $data['payment_amount'];
		} else {
			$amount = $data['order_balance'];
		}
		if (isset($this->session->data['oe']['transaction_id'])) {
			$transaction_id = $this->session->data['oe']['transaction_id'];
		} else {
			$transaction_id = '';
		}
		$this->db->query("INSERT INTO `" . DB_PREFIX . "oe_payment_processor` SET `order_id` = '" . (int)$this->session->data['oe']['order_id'] . "', `cardholder` = '" . $this->db->escape($this->session->data['payment_address']['firstname'] . ' ' . $this->session->data['payment_address']['lastname']) . "', `last_four` = '" . (int)$last_four . "', `expiration` = '" . $this->db->escape($data['cc_expire_date_month'] . '/' . $data['cc_expire_date_year']) . "', `amount` = '" . (float)$amount . "', `transaction_id` = '" . $this->db->escape($transaction_id) . "', `type` = '1', `process_date` = '" . (int)time() . "', `new_order` = '1'");
		return;
	}

}

?>
<?php
/**
 * Class TransactionSalesReceipt
 */
class TransactionSalesReceipt extends QcResource {
	/**
	 * @param $id
     */
	public function load($id) {
		$this->context->load->model('sale/sale'); // Load model
		$this->model = &$this->context->model_sale_sale; // Assign model reference
	}

	/**
	 * @param $id
     */
	public function getTransaction($id) {
		//return $this->model->getOrder($id);a
	}

	/**
	 *
     */
	public function getTransactions() {
		//return $this->model->getOrders();
	}
}
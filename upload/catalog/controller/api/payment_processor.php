<?php

class ControllerApiPaymentProcessor extends Controller {
	private $error = array();

	public function getTempOrderId() {
		$json = array();
		$this->load->model('api/payment_processor');
		$this->session->data['order_id'] = $this->model_api_payment_processor->getTempOrderId();
		$this->session->data['oe']['order_id'] = $this->session->data['order_id'];
		$json['oe_order_id'] = $this->session->data['order_id'];
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}

?>
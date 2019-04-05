<?php
require_once(DIR_QC . 'vendor/autoload.php');

use Doctrine\Common\Util\Inflector;
use Doctrine\Common\Util\Debug;

class ModelTotalSubTotal extends Model {
	// TODO: vqmod or ocmod this
	public function getTotal(&$total_data, &$total, &$taxes, $lines = false) {
		$cart = ($lines) ? $this->lines : $this->cart;
		$this->load->language('total/sub_total');
		
		$sub_total = $cart->getSubTotal();

		if (isset($this->session->data['vouchers']) && $this->session->data['vouchers']) {
			foreach ($this->session->data['vouchers'] as $voucher) {
				$sub_total += $voucher['amount'];
			}
		}

		$total_data[] = array(
			'code'       => 'sub_total',
			'title'      => $this->language->get('text_sub_total'),
			'value'      => $sub_total,
			'sort_order' => $this->config->get('sub_total_sort_order')
		);

		$total += $sub_total;
	}
	
	/*public function getTotal(&$total_data, &$total, &$taxes) {
		$this->load->language('total/sub_total');

		$sub_total = $this->cart->getSubTotal();

		if (isset($this->session->data['vouchers']) && $this->session->data['vouchers']) {
			foreach ($this->session->data['vouchers'] as $voucher) {
				$sub_total += $voucher['amount'];
			}
		}

		$total_data[] = array(
			'code'       => 'sub_total',
			'title'      => $this->language->get('text_sub_total'),
			'value'      => $sub_total,
			'sort_order' => $this->config->get('sub_total_sort_order')
		);

		$total += $sub_total;
	}*/
}
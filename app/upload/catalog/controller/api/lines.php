<?php
require_once(DIR_SYSTEM . 'library/quickcommerce/lines.php');


class ControllerApiLines extends Controller {
	public function __construct($registry) {
		$this->registry = $registry;
		$this->registry->set('lines', new Lines($registry)); // For session ops
	}

	public function testAdd() {
		//$this->lines->addDescription('Don\'t say Motherfucker', 1, null);

		foreach ($this->session->data['lines'] as $key => $qty) {
			var_dump(unserialize(base64_decode($key)));
		}
	}
	
	public function add() {
		$this->load->language('api/cart');

		$json = array();

		if (false) {
		//if (!isset($this->session->data['api_id'])) {
			$json['error']['warning'] = $this->language->get('error_permission');
		} else {
			if (isset($this->request->post['product'])) {
				$this->lines->clear();

				foreach ($this->request->post['product'] as $product) {
					if (isset($product['option'])) {
						$option = $product['option'];
					} else {
						$option = array();
					}

					$this->lines->add($product['product_id'], $product['quantity'], $option);
				}
			}
			
			$product_id = $this->request->post['product_id'];
			if (isset($product_id) && $product_id != null) {
				$this->load->model('catalog/product');

				$product_info = $this->model_catalog_product->getProduct($this->request->post['product_id']);

				if ($product_info) {
					if (isset($this->request->post['quantity'])) {
						$quantity = $this->request->post['quantity'];
					} else {
						$quantity = 1;
					}

					if (isset($this->request->post['option'])) {
						$option = array_filter($this->request->post['option']);
					} else {
						$option = array();
					}

					$product_options = $this->model_catalog_product->getProductOptions($this->request->post['product_id']);

					foreach ($product_options as $product_option) {
						if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
							$json['error']['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
						}
					}

					if (!isset($json['error']['option'])) {
						$this->lines->add($this->request->post['product_id'], $quantity, $option);

						$json['success'] = $this->language->get('text_success');

						$this->clearSessionVars();
					}
				} else {
					$json['error']['store'] = $this->language->get('error_store');
				}
			} elseif ($this->request->post['description'] != '') {
				if (isset($this->request->post['quantity'])) {
					$quantity = $this->request->post['quantity'];
				} else {
					$quantity = 1;
				}

				$this->lines->addDescription($this->request->post['description'], $quantity);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	private function clearSessionVars() {
		unset($this->session->data['shipping_method']);
		unset($this->session->data['shipping_methods']);
		unset($this->session->data['payment_method']);
		unset($this->session->data['payment_methods']);
	}

	public function edit() {
		$this->load->language('api/cart');

		$json = array();

		if (false) {
		//if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->lines->update($this->request->post['key'], $this->request->post['quantity']);

			$json['success'] = $this->language->get('text_success');

			$this->clearSessionVars();
			unset($this->session->data['reward']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function remove()
	{
		$this->load->language('api/cart');

		$json = array();

		if (false) {
			//if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			// Remove
			if (isset($this->request->post['key'])) {
				$this->lines->remove($this->request->post['key']);

				unset($this->session->data['vouchers'][$this->request->post['key']]);

				$json['success'] = $this->language->get('text_success');

				$this->clearSessionVars();
				unset($this->session->data['reward']);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	// Testing
	public function linesFromCart() {
		$this->session->data['lines'] = $this->session->data['cart'];
		foreach ($this->session->data['lines'] as $key => $quantity) {
			$line = unserialize(base64_decode($key));
			//var_dump($line);
		}
	}
	
	public function createTestLines() {
		$this->lines->add(3542, 1, array(317 => 184));
		$this->lines->add(3549, 1, array(321 => 192));
		$this->lines->add(3517, 1, array(310 => 171));
	}
	
	/**
	 * Called by admin invoice controller
	 */
	public function lines() {
		$this->load->language('api/cart');

		$json = array();

		if (false) {
		//if (!isset($this->session->data['api_id'])) {
			$json['error']['warning'] = $this->language->get('error_permission');
		} else {
			// Stock
			/*if (!$this->lines->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
				$json['error']['stock'] = $this->language->get('error_stock');
			}*/

			// lines
			$json['lines'] = array();
			
			//$this->linesFromCart(); // If the cart is set, convert products to line items
			//$this->createTestLines();
			$lines = $this->lines->getLines(); // Gets lines

			foreach ($lines as $line) {

				$line_total = 0;

				if (isset($line['product_id']) && $line['product_id'] > 0) {
					foreach ($lines as $line_2) {
						if (isset($line_2['product_id']) && ($line_2['product_id'] == $line['product_id'])) {
							$line_total += $line_2['quantity'];
						}
					}

					if ($line['minimum'] > $line_total) {
						$json['error']['minimum'][] = sprintf($this->language->get('error_minimum'), $line['name'], $line['minimum']);
					}

					$option_data = array();

					foreach ($line['option'] as $option) {
						$option_data[] = array(
							'product_option_id'       => $option['product_option_id'],
							'product_option_value_id' => $option['product_option_value_id'],
							'name'                    => $option['name'],
							//'value'                   => $option['value'],
							'type'                    => $option['type']
						);
					}
				}

				$json['lines'][] = array(
					'key'        => $line['key'],
					'rawkey'	 => unserialize(base64_decode($line['key'])),
					'product_id' => (isset($line['product_id'])) ? $line['product_id'] : null,
					//'name'       => $line['name'],
					'name'       => (isset($line['product_id'])) ? $line['model'] : null,
					'model'      => (isset($line['model'])) ? $line['model'] : null,
					'option'     => (isset($line['product_id'])) ? $option_data : null,
					'quantity'   => (isset($line['quantity'])) ? $line['quantity'] : 0,
					//'stock'      => $line['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
					'shipping'   => (isset($line['product_id'])) ? $line['shipping'] : false,
					//'price'      => $this->currency->format($this->tax->calculate($line['price'], $line['tax_class_id'], $this->config->get('config_tax'))),
					//'total'      => $this->currency->format($this->tax->calculate($line['price'], $line['tax_class_id'], $this->config->get('config_tax')) * $line['quantity']),
					//'reward'     => $line['reward']
				);
			}

			// Voucher
			$json['vouchers'] = array();

			if (!empty($this->session->data['vouchers'])) {
				foreach ($this->session->data['vouchers'] as $key => $voucher) {
					$json['vouchers'][] = array(
						'code'             => $voucher['code'],
						'description'      => $voucher['description'],
						'from_name'        => $voucher['from_name'],
						'from_email'       => $voucher['from_email'],
						'to_name'          => $voucher['to_name'],
						'to_email'         => $voucher['to_email'],
						'voucher_theme_id' => $voucher['voucher_theme_id'],
						'message'          => $voucher['message'],
						'amount'           => $this->currency->format($voucher['amount'])
					);
				}
			}

			// Totals
			$this->load->model('extension/extension');

			$total_data = array();
			$total = 0;
			$taxes = $this->lines->getTaxes();

			$sort_order = array();

			$results = $this->model_extension_extension->getExtensions('total');

			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
			}

			array_multisort($sort_order, SORT_ASC, $results);

			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('total/' . $result['code']);

					$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
				}
			}

			$sort_order = array();

			foreach ($total_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $total_data);

			$json['totals'] = array();

			foreach ($total_data as $total) {
				$json['totals'][] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'])
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
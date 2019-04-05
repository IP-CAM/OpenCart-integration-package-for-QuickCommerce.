<?php
require_once(DIR_SYSTEM . 'library/quickcommerce/lines.php');


class ControllerApiLines extends Controller {
	public function __construct($registry) {
		$this->registry = $registry;
		$this->registry->set('tax', new Tax($registry));
		$this->registry->set('lines', new Lines($registry)); // Lines requires tax, load in order
	}

	public function testAdd() {
		foreach ($this->session->data['lines'] as $key => $qty) {
			var_dump(unserialize(base64_decode($key)));
		}
	}

	protected function getPostVar($key, $default = null) {
		return $this->getRequestVar($key, $default, 'post');
	}

	protected function getRequestVar($key, $default = null, $type = 'get') {
		$types = array('get', 'post');
		if (!in_array($type, $types)) {
			throw new Exception('Invalid request type');
		}

		if (isset($this->request->{$type}[$key])) {
			if (isset($this->request->{$type}[$key])) {
				return $this->request->{$type}[$key];
			}
		}

		return $default;
	}

	private function addCommissions() {
		// Batch add
		$this->lines->clear();

		foreach ($this->request->post['product_sales'] as $sales) {
			$params = array(); // Doubt I need to do this but tired... reset the var just in case

			if (isset($sales['option'])) {
				$option = $sales['option'];
			} else {
				$option = array();
			}

			$params['qty'] = (isset($sales['quantity'])) ? (int)$sales['quantity'] : 1;
			$params['revenue'] = (isset($sales['revenue'])) ? (int)$sales['revenue'] : false;
			$params['price'] = (isset($sales['price'])) ? (float)$sales['price'] : 0.00;
			$params['mod'] = (isset($sales['price_prefix'])) ? $sales['price_prefix'] : '=';
			$params['rate'] = (isset($sales['rate_factor'])) ? (float)$sales['rate_factor'] : 0.00;
			$params['description'] = (isset($sales['description'])) ? $sales['description'] : null;

			// I don't think sales are broken down by options... I'll have to test this
			//$this->lines->addCommission($sales['product_id'], $sales['quantity'], $option);
			$this->lines->addCommission($sales['product_id'], $params['qty'], $params);
			// We could calculate and just pass a price in, except for the fact that we need to store the mods in session
		}
	}
	
	public function add() {
		$this->load->language('api/cart');

		$json = array();

		$types = array(
			'SalesItemLineDetail',
			'DescriptionItemLineDetail',
			'DescriptionLineDetail',
			'ServiceItemLineDetail',
			'CommissionLineDetail'
		);

		if (isset($this->request->post['product_sales'])) {
			// Delegate processing
			$this->addCommissions();
		}

		$product = false;
		$line['product_id'] = $this->getPostVar('product_id');
		$line['description'] = $this->getPostVar('description');

		$quantity = $this->getPostVar('quantity');
		$price = $this->getPostVar('price');
		$taxable = $this->getPostVar('taxable');
		$taxClassId = $this->getPostVar('tax_class_id');

		$this->addLine($line, $quantity, $price, $taxClassId);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function addLine($data = null, $quantity = null, $price = null, $taxClassId = null) {
		// Determine appropriate line type
		$productId = null;
		$product = null;

		if ($data != null) {
			$this->load->model('catalog/product');

			if (is_numeric($data)) {
				$productId = (int)$productId;
			} elseif (is_array($data)) {
				$productId = (isset($data['product_id'])) ? $data['product_id'] : $productId;
			}

			$product = $this->model_catalog_product->getProduct($productId);

			if ($product != null) {
				$price = (empty($price)) ? $product['price'] : $price;
				$quantity = (empty($quantity)) ? 1 : $quantity;

				if (is_numeric($taxClassId)) {
					$product['tax_class_id'] = $taxClassId;
				}

				$this->lines->add($product, $quantity, array(), $price);
			} else {
				if (!empty($data['description'])) {
					$tax = false;

					if ($quantity == null && $price == null) {
						// If no price or quantity necessary, create a DescriptionOnly line item
						$this->lines->addDescription($data['description']);
					} else {
						if (isset($data['tax'])) { // TODO: Might be obsolete - I think this was for the taxable flag and the old freeform tax field in the 'Add Line Item' UI
							if (is_numeric($data['tax']) || is_bool($data['tax'])) {
								if ((float)$data['tax'] > 0  || $data['tax'] == true) {
									$tax = $data['tax'];
								}
							}
						}

						$this->lines->addDescriptionItem($data['description'], $quantity, $price, $taxClassId, $tax);
					}
				}
			}
		}
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
	
	public function remove() {
		$this->load->language('api/cart');

		$json = array();

		// Batch delete
		$key = null;
		if (isset($this->request->post['key'])) {
			$key = $this->request->post['key'];
		}
		
		if (is_array($key)) {
			foreach ($key as $hash) {
				$this->lines->remove($hash);
				
				unset($this->session->data['vouchers'][$hash]);

				$json['success'] = $this->language->get('text_success');

				$this->clearSessionVars();
				unset($this->session->data['reward']);
			}
		} elseif ($key != null) {
			$this->lines->remove($this->request->post['key']);

			unset($this->session->data['vouchers'][$this->request->post['key']]);

			$json['success'] = $this->language->get('text_success');

			$this->clearSessionVars();
			unset($this->session->data['reward']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	/*public function remove() {
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
	}*/
	
	public function dumpSession() {
		var_dump($this->session->data['customer']);
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
	public function lines($args = array()) {
		$export = (isset($args['export']) && is_bool($args['export'])) ? $args['export'] : true; // Not quite sure how OC feeds args when invoking $this->load->controller($route, $args) this'll do the trick anyway
		$children = (isset($args['children']) && is_array($args['children'])) ? $args['children'] : false;
		
		$this->load->language('api/lines');

		$this->load->model('localisation/tax_class');

		$tax_classes = $this->model_localisation_tax_class->getTaxClasses(); // TODO: Could alternatively use Doctrine now
		$tax_class_ids = array();
		foreach ($tax_classes as $tax_class) {
			$tax_class_ids[$tax_class['tax_class_id']] = $tax_class['title'];
		}

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
			$lines = $this->lines->getLines($children); // Gets lines

			foreach ($lines as $line) {

				$line_total = 0;

				$option_data = array();

				if (isset($line['product_id']) && $line['product_id'] > 0) {
					foreach ($lines as $line_2) {
						if (isset($line_2['product_id']) && ($line_2['product_id'] == $line['product_id'])) {
							$line_total += $line_2['quantity'];
						}
					}

					if ($line['minimum'] > $line_total) {
						$json['error']['minimum'][] = sprintf($this->language->get('error_minimum'), $line['name'], $line['minimum']);
					}

					if (isset($line['option']) && is_array($line['option'])) {
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
				}
				
				$raw = unserialize(base64_decode($line['key']));

				$price = (isset($line['price'])) ? (float)$line['price'] : '';
				if (isset($raw['mod'])) {
					$total = (float)$this->lines->adjustValue($raw['mod'], $raw['revenue'], $raw['rate']);
				} else {
					if (isset($line['total'])) {
						$total = $line['total'];
					} else {
						$total = $line['quantity'] * $price;
					}
				}
				
				// TODO: VEST Specific dump this in a mod!
				$vest = null;
				
				$revenue = null;
				$royalty = null; // VEST
				
				$cost = (isset($line['cost'])) ? $line['cost'] : null; // DRF etc.
				
				if (isset($raw['revenue'])) {
					$revenue = (float)$raw['revenue'];
					$royalty = $revenue * 0.25;
					$vest = $revenue;

					$vest -= (isset($total)) ? $total : 0;
					$vest -= (isset($royalty)) ? $royalty : 0;
					
					//$royalty = $this->currency->format($royalty);
					//$vest = $this->currency->format($vest);
				} elseif (isset($line['revenue'])) {
					$revenue = $line['revenue'];
					$royalty = $revenue * 0.25;
					$vest = $revenue;

					$vest -= (isset($total)) ? $total : 0;
					$vest -= (isset($royalty)) ? $royalty : 0;
				}

				$tax = $this->lines->getSalesItemTaxTotal($line);
				$item = array_merge($line, $raw);
				/*echo '<pre>';
				var_dump($item);
				echo '</pre>';*/

				$tax_class = '';
				if (isset($item['tax_class_id'])) {
					if (!empty($item['tax_class_id'])) {
						if (array_key_exists($item['tax_class_id'], $tax_class_ids)) {
							$tax_class = $tax_class_ids[$item['tax_class_id']];
						}
					}
				}

				$json['lines'][] = array(
					'key'        	=> $item['key'],
					'rawkey'	 	=> $raw,
					'product_id' 	=> (isset($item['product_id'])) ? $item['product_id'] : null,
					'product'	 	=> (isset($item['product'])) ? $item['product'] : null,
					'name'       	=> (!empty($item['name'])) ? $item['name'] : $item['description'], // TODO: Description?
					'model'      	=> (isset($item['model'])) ? $item['model'] : '',
					'option'     	=> (isset($item['product_id'])) ? $option_data : '',
					'quantity'   	=> (!empty($item['quantity'])) ? $item['quantity'] : '',
					'revenue'    	=> $revenue,
					'royalty'    	=> $royalty,
					'cost'       	=> $cost,
					//'stock'      => $item['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
					'shipping'   	=> (isset($item['shipping'])) ? $item['shipping'] : false,
					'price'      	=> $price, //$this->currency->format($price),
					'total'      	=> ($total > 0) ? $total : '', //$this->currency->format($total)
					'tax_class_id'	=> (isset($item['tax_class_id'])) ? $item['tax_class_id'] : null, //$this->currency->format($total)
					'tax_class'		=> $tax_class, //$this->currency->format($total)
					'tax'      	 	=> $tax, //$this->currency->format($total)
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
					
					//var_dump($this->{'model_total_' . $result['code']});

					$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes, true);
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

		/*$export = false;
		echo '<pre>';
		print_r($json);
		echo '</pre>';*/
		
		if ($export) {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		} else {
			return $json;
		}
		
	}
}
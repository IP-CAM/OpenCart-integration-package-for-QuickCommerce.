<?php 
/**
 * download.php
 *
 * Downloads management
 *
 * @author     Lucas Lopatka
 * @copyright  2015
 * @version    1.0
 */
require_once(DIR_SYSTEM . 'engine/restcontroller.php');

class ControllerRestDownload extends RestController {
	
	private $error = array();

	public function listDownloads() {

		$json = array('success' => true);
		
		if (!$this->customer->isLogged()) {
			$json["error"] = "User is not logged in";
			$json["success"] = false;
		}

		$this->language->load('account/download');

		$this->load->model('account/download');
		$this->load->model('catalog/product');

		if($json["success"]){
			$page = 1;

			$data['downloads'] = array();

			$download_total = $this->model_account_download->getTotalDownloads();

			$results = $this->model_account_download->getDownloads(($page - 1) * 10, 1000);

			foreach ($results as $result) {
				if (file_exists(DIR_DOWNLOAD . $result['filename'])) {
					$size = filesize(DIR_DOWNLOAD . $result['filename']);

					$i = 0;

					$suffix = array(
						'B',
						'KB',
						'MB',
						'GB',
						'TB',
						'PB',
						'EB',
						'ZB',
						'YB'
					);

					while (($size / 1024) > 1) {
						$size = $size / 1024;
						$i++;
					}
					
					$products = $this->model_catalog_product->getProductsByIds(array($result['product_id']), $this->customer);

					$data['downloads'][] = array(
						'download_id'   => $result['download_id'],
						'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
						'name'       => $result['name'],
						'size'       => round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i],
						'href'       => $this->url->link('account/download/download', 'download_id=' . $result['download_id'], 'SSL'),
						'product'	 => ($products != null) ? $this->getProductInfo(reset($products)) : null
					);
				}
				
				if (count($data['downloads']) > 0) {
					$json["data"] = $data;
				} else {
					$json["success"] = false;
					$json["error"] = "No customer download found";
				}
			}
		}

		if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}					
	}
	
	private function getProductInfo($product){

        $this->load->model('tool/image');
        $this->load->model('catalog/category');

        //product image
        if (isset($product['image']) && file_exists(DIR_IMAGE . $product['image'])) {
            $image = $this->model_tool_image->resize($product['image'], 500, 500);
        } else {
            $image = $this->model_tool_image->resize('no_image.jpg', 500, 500);
        }

        //additional images
        $additional_images = $this->model_catalog_product->getProductImages($product['product_id']);

        $images = array();

        foreach ($additional_images as $additional_image) {
            if (isset($additional_image['image']) && file_exists(DIR_IMAGE . $additional_image['image'])) {
                $images[] = $this->model_tool_image->resize($additional_image['image'], 500, 500);
            } else {
                $images[] = $this->model_tool_image->resize('no_image.jpg', 500, 500);
            }
        }

        //special
        if ((float)$product['special']) {
            $special = $this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax'));
            $special_formated = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')));
        } else {
            $special = "";
            $special_formated = "";
        }

        //discounts
        $discounts = array();
        $data_discounts = $this->model_catalog_product->getProductDiscounts($product['product_id']);

        foreach ($data_discounts as $discount) {
            $discounts[] = array(
                'quantity' => $discount['quantity'],
                'price' => $this->tax->calculate($discount['price'], $product['tax_class_id'], $this->config->get('config_tax')),
                'price_formated' => $this->currency->format($this->tax->calculate($discount['price'], $product['tax_class_id'], $this->config->get('config_tax')))
            );
        }


		//options
		$options = array();

		foreach ($this->model_catalog_product->getProductOptions($product['product_id']) as $option) {
			if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
				$option_value_data = array();
				if(!empty($option['product_option_value'])){
					foreach ($option['product_option_value'] as $option_value) {
						if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
							if ((($this->customer->isLogged() && $this->config->get('config_customer_price')) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
								$price = $this->tax->calculate($option_value['price'], $product['tax_class_id'], $this->config->get('config_tax'));
								$price_formated = $this->currency->format($this->tax->calculate($option_value['price'], $product['tax_class_id'], $this->config->get('config_tax')));
							} else {
								$price = false;
								$price_formated = false;
							}

							if (isset($option_value['image']) && file_exists(DIR_IMAGE . $option_value['image'])) {
								$option_image = $this->model_tool_image->resize($option_value['image'], 100, 100);
							} else {
								$option_image = $this->model_tool_image->resize('no_image.jpg', 100, 100);
							}

							$option_value_data[] = array(
								'image'					=> $option_image,
								'price'					=> $price,
								'price_formated'		=> $price_formated,
								'price_prefix'			=> $option_value['price_prefix'],
								'product_option_value_id'=> $option_value['product_option_value_id'],
								'option_value_id'		=> $option_value['option_value_id'],
								'name'					=> $option_value['name'],
								'quantity'	=> !empty($option_value['quantity']) ? $option_value['quantity'] : 0
							);
						}
					}
				}
				$options[] = array(
					'name'				=> $option['name'],
					'type'				=> $option['type'],
					'option_value'		=> $option_value_data,
					'required'			=> $option['required'],
					'product_option_id' => $option['product_option_id'],
					'option_id'			=> $option['option_id'],

				);

			} elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
				$option_value  = array();
				if(!empty($option['product_option_value'])){
					$option_value = $option['product_option_value'];
				}
				$options[] = array(
					'name'				=> $option['name'],
					'type'				=> $option['type'],
					'option_value'		=> $option_value,
					'required'			=> $option['required'],
					'product_option_id' => $option['product_option_id'],
					'option_id'			=> $option['option_id'],
				);
			}
		}


        $productCategories = array();
        $product_category  = $this->model_catalog_product->getCategories($product['product_id']);

        foreach ($product_category as $prodcat) {
            $category_info = $this->model_catalog_category->getCategory($prodcat['category_id']);
            if ($category_info) {
                $productCategories[] = array(
                    'name' => $category_info['name'],
                    'id' => $category_info['category_id']
                );
            }
        }

		/*reviews*/
		$this->load->model('catalog/review');
		
		$reviews = array();

		$reviews["review_total"] = $this->model_catalog_review->getTotalReviewsByProductId($product['product_id']);

		$reviewList = $this->model_catalog_review->getReviewsByProductId($product['product_id'], 0, 1000);

		foreach ($reviewList as $review) {
			$reviews['reviews'][] = array(
				'author'     => $review['author'],
				'text'       => nl2br($review['text']),
				'rating'     => (int)$review['rating'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($review['date_added']))
			);
		}

		return array(
			'id'				=> $product['product_id'],
			'seo_h1'			=> (!empty($product['seo_h1']) ? $product['seo_h1'] : "") ,
			'name'				=> $product['name'],
			'manufacturer'		=> $product['manufacturer'],
			'sku'				=> (!empty($product['sku']) ? $product['sku'] : "") ,
			'model'				=> $product['model'],
			'image'				=> $image,
			'images'			=> $images,
			'price'				=> $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')),
			'price_formated'    => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))),
			'rating'			=> (int)$product['rating'],
			'description'		=> html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8'),
			'attribute_groups'	=> $this->model_catalog_product->getProductAttributes($product['product_id']),
			'special'			=> $special,
			'special_formated'  => $special_formated,
			'special_start_date'	=> (!empty($product['special_start_date']) ? $product['special_start_date'] : "") ,
			'special_end_date'	=> (!empty($product['special_end_date']) ? $product['special_end_date'] : "") ,
			'discounts'			=> $discounts,
			'options'			=> $options,
			'minimum'			=> $product['minimum'] ? $product['minimum'] : 1,
			'meta_title'     => $product['meta_title'],
			'meta_description'     => $product['meta_description'],
			'meta_keyword'     => $product['meta_keyword'],
			'tag'              => $product['tag'],
			'upc'              => $product['upc'],
			'ean'              => $product['ean'],
			'jan'              => $product['jan'],
			'isbn'             => $product['isbn'],
			'mpn'              => $product['mpn'],
			'location'         => $product['location'],
			'stock_status'     => $product['stock_status'],
			'manufacturer_id'  => $product['manufacturer_id'],
			'tax_class_id'     => $product['tax_class_id'],
			'date_available'   => $product['date_available'],
			'weight'           => $product['weight'],
			'weight_class_id'  => $product['weight_class_id'],
			'length'           => $product['length'],
			'width'            => $product['width'],
			'height'           => $product['height'],
			'length_class_id'  => $product['length_class_id'],
			'subtract'         => $product['subtract'],
			'sort_order'       => $product['sort_order'],
			'status'           => $product['status'],
			'date_added'       => $product['date_added'],
			'date_modified'    => $product['date_modified'],
			'viewed'           => $product['viewed'],
			'weight_class'     => $product['weight_class'],
			'length_class'     => $product['length_class'],
			'reward'			=> $product['reward'],
			'points'			=> $product['points'],
			'category'			=> $productCategories,
			'quantity'			=> !empty($product['quantity']) ? $product['quantity'] : 0,
			'reviews' => $reviews
        );
    }

	public function getDownload($download_id) { 
		
		$json = array('success' => true);

		$this->language->load('account/download');


		if (!$this->customer->isLogged()) {
			$json["error"] = "User is not logged in";
			$json["success"] = false;
		}
		
		if($json["success"]){
			/*$this->load->model('account/download');

			$download_info = $this->model_account_download->getDownload($download_id);

			if ($download_info) {

				if ($order_info['invoice_no']) {
					$data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
				} else {
					$data['invoice_no'] = '';
				}

				$data['order_id'] = $order_id;
				$data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));

				if ($order_info['payment_address_format']) {
					$format = $order_info['payment_address_format'];
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}

				$find = array(
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					'{zone_code}',
					'{country}'
				);

				$replace = array(
					'firstname' => $order_info['payment_firstname'],
					'lastname'  => $order_info['payment_lastname'],
					'company'   => $order_info['payment_company'],
					'address_1' => $order_info['payment_address_1'],
					'address_2' => $order_info['payment_address_2'],
					'city'      => $order_info['payment_city'],
					'postcode'  => $order_info['payment_postcode'],
					'zone'      => $order_info['payment_zone'],
					'zone_code' => $order_info['payment_zone_code'],
					'country'   => $order_info['payment_country']
				);

				$data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

				$data['payment_method'] = $order_info['payment_method'];

				if ($order_info['shipping_address_format']) {
					$format = $order_info['shipping_address_format'];
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}

				$find = array(
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					'{zone_code}',
					'{country}'
				);

				$replace = array(
					'firstname' => $order_info['shipping_firstname'],
					'lastname'  => $order_info['shipping_lastname'],
					'company'   => $order_info['shipping_company'],
					'address_1' => $order_info['shipping_address_1'],
					'address_2' => $order_info['shipping_address_2'],
					'city'      => $order_info['shipping_city'],
					'postcode'  => $order_info['shipping_postcode'],
					'zone'      => $order_info['shipping_zone'],
					'zone_code' => $order_info['shipping_zone_code'],
					'country'   => $order_info['shipping_country']
				);

				$data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

				$data['shipping_method'] = $order_info['shipping_method'];

				$data['products'] = array();

				$products = $this->model_account_download->getOrderProducts($order_id);

				foreach ($products as $product) {
					$option_data = array();

					$options = $this->model_account_download->getOrderOptions($order_id, $product['order_product_id']);

					foreach ($options as $option) {
						if ($option['type'] != 'file') {
							$value = $option['value'];
						} else {
							$value = utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.'));
						}

						$option_data[] = array(
							'name'  => $option['name'],
							'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
						);					
					}

					$data['products'][] = array(
						'name'     => $product['name'],
						'model'    => $product['model'],
						'option'   => $option_data,
						'quantity' => $product['quantity'],
						'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
						'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
						'return'   => $this->url->link('account/return/insert', 'order_id=' . $order_info['order_id'] . '&product_id=' . $product['product_id'], 'SSL')
					);
				}

				// Voucher
				$data['vouchers'] = array();

				$vouchers = $this->model_account_download->getOrderVouchers($order_id);

				foreach ($vouchers as $voucher) {
					$data['vouchers'][] = array(
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
					);
				}

				$data['totals'] = $this->model_account_download->getOrderTotals($order_id);

				$data['comment'] = nl2br($order_info['comment']);

				$data['histories'] = array();

				$results = $this->model_account_download->getOrderHistories($order_id);

				foreach ($results as $result) {
					$data['histories'][] = array(
						'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
						'status'     => $result['status'],
						'comment'    => nl2br($result['comment'])
					);
				}

				$json["data"] = $data;
			}else {
					$json['success']     = false;
					$json['error']       = "The specified order does not exist.";
			}

			if ($this->debugIt) {
				echo '<pre>';
				print_r($json);
				echo '</pre>';
			} else {
				$this->response->setOutput(json_encode($json));
			}*/		
		} else {
            $this->response->setOutput(json_encode($json));
        }
    }
	
	private function addDownload () {
		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}
	}

	/*
	* DOWNLOADS FUNCTIONS
	*/	
	public function downloads() {

		$this->checkPlugin();
		

		if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
			//get order details
			if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
				$this->getDownload($this->request->get['id']);
			}else {
				//get order list
				$this->listDownloads();
			}
		}else if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
			//add download
			$requestjson = file_get_contents('php://input');
		
			$requestjson = json_decode($requestjson, true);

			if (!empty($requestjson)) {
				$this->saveDownload($requestjson);
			} else {
				$this->response->setOutput(json_encode(array('success' => false)));
			} 

		}

    }
}
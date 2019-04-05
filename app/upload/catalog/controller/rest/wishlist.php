<?php 
/**
 * wishlist.php
 *
 * wishlist management
 *
 * @author     Makai Lajos
 * @copyright  2015
 * @license    License.txt
 * @version    2.0
 * @link       http://opencart-api.com/product/opencart-restful-api-pro-v2-0/
 * @see        http://newapi2.opencart-api.com/demo/
 */
require_once(DIR_SYSTEM . 'engine/restcontroller.php');

class ControllerRestWishlist extends RestController {
	
	/*
	* Get wishlist
	*/
  	public function loadWishlist() {

		$json = array('success' => true);

		if(!isset($json["error"])){

			$this->load->language('account/wishlist');

			$this->load->model('catalog/product');

			$this->load->model('tool/image');

			if (!isset($this->session->data['wishlist'])) {
				$this->session->data['wishlist'] = array();
			}

			$json["data"]['products'] = array();

			foreach ($this->session->data['wishlist'] as $key => $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);

				if ($product_info) {
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_wishlist_width'), $this->config->get('config_image_wishlist_height'));
					} else {
						$image = false;
					}

					if ($product_info['quantity'] <= 0) {
						$stock = $product_info['stock_status'];
					} elseif ($this->config->get('config_stock_display')) {
						$stock = $product_info['quantity'];
					} else {
						$stock = $this->language->get('text_instock');
					}

					if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
					} else {
						$price = false;
					}

					if ((float)$product_info['special']) {
						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
					} else {
						$special = false;
					}

					$json["data"]['products'][] = array(
						'product_id' => $product_info['product_id'],
						'thumb'      => $image,
						'name'       => $product_info['name'],
						'model'      => $product_info['model'],
						'stock'      => $stock,
						'price'      => $price,
						'special'    => $special
					);
				} else {
					unset($this->session->data['wishlist'][$key]);
				}
			}
		}

        $this->sendResponse($json);
	}

	/* 
	* delete wishlist
	*/
	public function deleteWishlist($productId) {

		$this->load->language('account/wishlist');

		$json = array('success' => true);		

		$key = array_search($productId, $this->session->data['wishlist']);

		if ($key !== false) {
			unset($this->session->data['wishlist'][$key]);
			$json["data"]['success'] = $this->language->get('text_remove');
		} else {
			$json["error"] = "Product not found!";	
			$json["success"] = false;		
		}

        $this->sendResponse($json);
	}

	/* 
	* add to wishlist
	*/
	public function addWishlist($productId) {
		
		$json = array('success' => true);		
		
		$this->load->language('account/wishlist');

		if (!isset($this->session->data['wishlist'])) {
			$this->session->data['wishlist'] = array();
		}

		if (!empty($productId)) {
			$product_id = $productId;
		} else {
			$product_id = 0;
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {
			if (!in_array($product_id, $this->session->data['wishlist'])) {
				$this->session->data['wishlist'][] = (int)$product_id;
			} else {
				$json['info'] = "Already exists";
				$json['success'] = false;
			}

			$json['total'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}

        $this->sendResponse($json);
	}

	/*
	* WISHLIST FUNCTIONS
	*/	
	public function wishlist() {

		$this->checkPlugin();

		if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
			//get wishlist
			$this->loadWishlist();
		} else if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
			//add item to wishlist
			 if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
				$this->addWishlist($this->request->get['id']);
			}else {
                 $this->sendResponse(array('success' => false));
			}
		}else if ( $_SERVER['REQUEST_METHOD'] === 'DELETE' ){
			//delete item from wishlist
			 if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
				$this->deleteWishlist($this->request->get['id']);
			}else {
                $this->sendResponse(array('success' => false));
			}
		}

    }
}
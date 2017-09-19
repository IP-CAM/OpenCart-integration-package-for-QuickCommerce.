<?php
class ControllerQCLandingCatalog extends Controller {
    public function index() {
        $this->document->setTitle($this->config->get('config_meta_title'));
        $this->document->setDescription($this->config->get('config_meta_description'));
        $this->document->setKeywords($this->config->get('config_meta_keyword'));

        if (isset($this->request->get['route'])) {
            $this->document->addLink(HTTP_SERVER, 'canonical');
        }
        
        // Load everything
        $this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
        
        
        $categories = $this->model_catalog_category->getCategories(0);
        
        $limit = 20; // Set a hard limit so we don't bog everything down...
        $products = array();
        
        foreach ($categories as $category) {
            $category_id = $category['category_id'];
            
            $filter_data = array(
				'filter_category_id' => $category_id,
				'order'              => 'ASC',
				'start'              => 0,
				'limit'              => $limit
			);

			$data['categories'][$category_id] = $category;
            
            // Load all products within limit...
            $products = $this->model_catalog_product->getProducts($filter_data);
            $category_products = array();
            
            foreach ($products as $product) {
				if ($product['image']) {
					$image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
				}

				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$price = false;
				}

				if ((float)$product['special']) {
					$special = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$product['special'] ? $product['special'] : $product['price']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$product['rating'];
				} else {
					$rating = false;
				}

				$category_products[] = array(
					'product_id'  => $product['product_id'],
					'thumb'       => $image,
					'name'        => $product['name'],
					'description' => utf8_substr(strip_tags(html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $product['minimum'] > 0 ? $product['minimum'] : 1,
					'rating'      => $product['rating'],
					//'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $product['product_id'] . $url)
				);
			}
            
            $data['categories'][$category_id]['products'] = $category_products;
        }

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/quickcommerce/landing/menu.tpl')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/quickcommerce/landing/menu.tpl', $data));
        } else {
            $this->response->setOutput($this->load->view('quickcommerce/template/landing/menu.tpl', $data)); // TODO: Reset to default!
        }
    }
}
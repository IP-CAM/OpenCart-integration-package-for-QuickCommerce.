<?php 
class ControllerCatalogPurchaseOrderVendor extends Controller { 
	private $error = array();
   
  	public function index() {
		$this->language->load('catalog/purchase_order_vendor');
	
    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/purchase_order_vendor');
		
    	$this->getList();
  	}
              
  	public function insert() {
		$this->language->load('catalog/purchase_order_vendor');
	
    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/purchase_order_vendor');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
      		$this->model_catalog_purchase_order_vendor->addVendor($this->request->post);
		  	
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
						
      		$this->response->redirect($this->url->link('catalog/purchase_order_vendor', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
	
    	$this->getForm();
  	}

  	public function update() {
		$this->language->load('catalog/purchase_order_vendor');
	
    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/purchase_order_vendor');
		
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
	  		$this->model_catalog_purchase_order_vendor->editVendor($this->request->get['purchase_order_vendor_id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->response->redirect($this->url->link('catalog/purchase_order_vendor', 'token=' . $this->session->data['token'] . $url, 'SSL'));
    	}
	
    	$this->getForm();
  	}

  	public function delete() {
		$this->language->load('catalog/purchase_order_vendor');
	
    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/purchase_order_vendor');
		
    	if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $purchase_order_vendor) {
				$this->model_catalog_purchase_order_vendor->deleteVendor($purchase_order_vendor);
			}
			      		
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->response->redirect($this->url->link('catalog/purchase_order_vendor', 'token=' . $this->session->data['token'] . $url, 'SSL'));
   		}
	
    	$this->getList();
  	}
    
  	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}
		
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
				
		$url = '';
			
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('catalog/purchase_order_vendor', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);
							
		$data['insert'] = $this->url->link('catalog/purchase_order_vendor/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('catalog/purchase_order_vendor/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');	

		$data['purchase_order_vendors'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		
		$purchase_order_vendor_total = $this->model_catalog_purchase_order_vendor->getTotalVendors();
	
		$results = $this->model_catalog_purchase_order_vendor->getVendors($filter_data);
 
    	foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('catalog/purchase_order_vendor/update', 'token=' . $this->session->data['token'] . '&purchase_order_vendor_id=' . $result['purchase_order_vendor_id'] . $url, 'SSL')
			);
						
			$data['purchase_order_vendors'][] = array(
				'purchase_order_vendor_id' 	=> $result['purchase_order_vendor_id'],
				'name'           	 		=> $result['name'],
				'selected'        			=> isset($this->request->post['selected']) && in_array($result['purchase_order_vendor_id'], $this->request->post['selected']),
				'action'          			=> $action
			);
		}	
	
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_action'] = $this->language->get('column_action');		
		
		$data['button_insert'] = $this->language->get('button_insert');
		$data['button_delete'] = $this->language->get('button_delete');
 
 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data['sort_name'] = $this->url->link('catalog/purchase_order_vendor', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $purchase_order_vendor_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('catalog/purchase_order_vendor', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
			
		$data['pagination'] = $pagination->render();
		
		$data['results'] = sprintf($this->language->get('text_pagination'), ($purchase_order_vendor_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($purchase_order_vendor_total - $this->config->get('config_limit_admin'))) ? $purchase_order_vendor_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $purchase_order_vendor_total, ceil($purchase_order_vendor_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/purchase_order_vendor_list.tpl', $data));
  	}
  
  	protected function getForm() {
     	$data['heading_title'] = $this->language->get('heading_title');

    	$data['entry_name'] = $this->language->get('entry_name');
    	$data['entry_manufacturer'] = $this->language->get('entry_manufacturer');
    	$data['entry_email'] = $this->language->get('entry_email');
    	$data['entry_telephone'] = $this->language->get('entry_telephone');
    	$data['entry_fax'] = $this->language->get('entry_fax');
    	$data['entry_address_1'] = $this->language->get('entry_address_1');
    	$data['entry_address_2'] = $this->language->get('entry_address_2');
    	$data['entry_city'] = $this->language->get('entry_city');
    	$data['entry_postcode'] = $this->language->get('entry_postcode');
    	$data['entry_country'] = $this->language->get('entry_country');
    	$data['entry_zone'] = $this->language->get('entry_zone');
		
    	$data['text_none'] = $this->language->get('text_none');

    	$data['button_save'] = $this->language->get('button_save');
    	$data['button_cancel'] = $this->language->get('button_cancel');
    
 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

 		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}
		
		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}
		
		$url = '';
			
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
      	 	'text'      => $this->language->get('text_home'),	
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('catalog/purchase_order_vendor', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);
		
		if (!isset($this->request->get['purchase_order_vendor_id'])) {
			$data['action'] = $this->url->link('catalog/purchase_order_vendor/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('catalog/purchase_order_vendor/update', 'token=' . $this->session->data['token'] . '&purchase_order_vendor_id=' . $this->request->get['purchase_order_vendor_id'] . $url, 'SSL');
		}
			
		$data['cancel'] = $this->url->link('catalog/purchase_order_vendor', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (!isset($this->request->get['purchase_order_vendor_id'])) {
			$vendor_info = array();
		} else {
			$vendor_info = $this->model_catalog_purchase_order_vendor->getVendor($this->request->get['purchase_order_vendor_id']);
		}
		
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif ($vendor_info) {
			$data['name'] = $vendor_info['name'];
		} else {
			$data['name'] = '';
		}
		
		if (isset($this->request->post['manufacturer_id'])) {
			$data['manufacturer_id'] = $this->request->post['manufacturer_id'];
		} elseif ($vendor_info) {
			$data['manufacturer_id'] = $vendor_info['manufacturer_id'];
		} else {
			$data['manufacturer_id'] = array();
		}
		
		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif ($vendor_info) {
			$data['email'] = $vendor_info['email'];
		} else {
			$data['email'] = '';
		}
		
		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} elseif ($vendor_info) {
			$data['telephone'] = $vendor_info['telephone'];
		} else {
			$data['telephone'] = '';
		}
		
		if (isset($this->request->post['fax'])) {
			$data['fax'] = $this->request->post['fax'];
		} elseif ($vendor_info) {
			$data['fax'] = $vendor_info['fax'];
		} else {
			$data['fax'] = '';
		}
		
		if (isset($this->request->post['address_1'])) {
			$data['address_1'] = $this->request->post['address_1'];
		} elseif ($vendor_info) {
			$data['address_1'] = $vendor_info['address_1'];
		} else {
			$data['address_1'] = '';
		}
		
		if (isset($this->request->post['address_2'])) {
			$data['address_2'] = $this->request->post['address_2'];
		} elseif ($vendor_info) {
			$data['address_2'] = $vendor_info['address_2'];
		} else {
			$data['address_2'] = '';
		}
		
		if (isset($this->request->post['city'])) {
			$data['city'] = $this->request->post['city'];
		} elseif ($vendor_info) {
			$data['city'] = $vendor_info['city'];
		} else {
			$data['city'] = '';
		}
		
		if (isset($this->request->post['postcode'])) {
			$data['postcode'] = $this->request->post['postcode'];
		} elseif ($vendor_info) {
			$data['postcode'] = $vendor_info['postcode'];
		} else {
			$data['postcode'] = '';
		}
		
		if (isset($this->request->post['country_id'])) {
			$data['country_id'] = $this->request->post['country_id'];
		} elseif ($vendor_info) {
			$data['country_id'] = $vendor_info['country_id'];
		} else {
			$data['country_id'] = '';
		}
		
		if (isset($this->request->post['zone_id'])) {
			$data['zone_id'] = $this->request->post['zone_id'];
		} elseif ($vendor_info) {
			$data['zone_id'] = $vendor_info['zone_id'];
		} else {
			$data['zone_id'] = '';
		}
		
		$this->load->model('catalog/manufacturer');
		
		$data['manufacturers'] = $this->model_catalog_manufacturer->getManufacturers();
		
		$this->load->model('localisation/country');
		
		$data['countries'] = $this->model_localisation_country->getCountries();
		
		$data['token'] = $this->session->data['token'];
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/purchase_order_vendor_form.tpl', $data));
  	}
  	
	protected function validateForm() {
    	if (!$this->user->hasPermission('modify', 'catalog/purchase_order_vendor')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}
	
    	
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}
		
		if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
      		$this->error['email'] = $this->language->get('error_email');
    	}
		
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}

  	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/purchase_order_vendor')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}
		
		if (!$this->error) { 
	  		return true;
		} else {
	  		return false;
		}
  	}
	
	public function autocomplete() {
		$json = array();
		
		$this->load->model('catalog/purchase_order_vendor');
		
		$data = array(
			'filter_name'	=> $this->request->get['filter_name']
		);
		
		$vendors = $this->model_catalog_purchase_order_vendor->getVendors($data);
		
		foreach ($vendors as $vendor) {
			$json[] = array(
				'purchase_order_vendor_id'		=> $vendor['purchase_order_vendor_id'],
				'name'							=> html_entity_decode($vendor['name'], ENT_QUOTES)
			);
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function populate() {
		$this->load->language('catalog/purchase_order_vendor');
		
		$json = array();
		
		if (empty($this->request->get['purchase_order_vendor_id'])) {
			$json['error'] = $this->language->get('error_vendor');
		} else {
			$this->load->model('catalog/purchase_order_vendor');
			
			$vendor_info = $this->model_catalog_purchase_order_vendor->getVendor($this->request->get['purchase_order_vendor_id']);
			
			if ($vendor_info) {
				$product_id = array();
				
				if ($vendor_info['manufacturer_id']) {
					$json['products'] = array();
					
					foreach ($vendor_info['manufacturer_id'] as $manufacturer_id) {
						$products = $this->model_catalog_purchase_order_vendor->getProducts($manufacturer_id);
						
						foreach ($products as $product) {
							$json['products'][] = $product;
						}
					}
				} else {
					$json['error'] = $this->language->get('error_maufacturer');
				}
			} else {
				$json['error'] = $this->langauge->get('error_not_found');
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function product() {
		$json = array();
		
		$this->load->model('catalog/purchase_order_vendor');
		
		$products = $this->model_catalog_purchase_order_vendor->getProduct($this->request->get['filter_name']);
		
		if ($products) {
			foreach ($products as $product) {
				$json[] = array(
					'product_id'	=> $product['product_id'],
					'name'			=> html_entity_decode($product['name'], ENT_QUOTES),
					'model'			=> $product['model'],
					'sold'			=> $product['sold'],
					'stock'			=> $product['stock'],
					'price'			=> $product['price'],
					'hasOption'		=> $product['hasOption']
				);
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function option() {
		$json = array();
		
		$this->load->model('catalog/purchase_order_vendor');
		
		$options = $this->model_catalog_purchase_order_vendor->getOptions($this->request->get['product_id']);
		
		if ($options) {
			$json['options'] = $options;
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function optionvalue() {
		$json = array();
		
		$this->load->model('catalog/purchase_order_vendor');
		
		$values = $this->model_catalog_purchase_order_vendor->getOptionValues($this->request->get['product_option_id']);
		
		if ($values) {
			$json['values'] = $values;
		}
		
		$this->response->setOutput(json_encode($json));
	}
}
?>
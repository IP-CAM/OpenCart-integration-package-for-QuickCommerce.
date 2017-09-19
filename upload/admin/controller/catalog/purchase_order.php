<?php 
class ControllerCatalogPurchaseOrder extends Controller { 
	private $error = array();
   
  	public function index() {
		$this->language->load('catalog/purchase_order');
	
    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/purchase_order');
		
    	$this->getList();
  	}
              
  	public function insert() {
		$this->language->load('catalog/purchase_order');
	
    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/purchase_order');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			/*if (!$this->config->get('purchase_order' . base64_decode('X29yZGVyX2lk'))) { // base64 decoded = _order_id
				$this->response->redirect($this->url->link('catalog/purchase_order', 'token=' . $this->session->data['token'], 'SSL'));
			}*/
		
      		$purchase_order_id = $this->model_catalog_purchase_order->addOrder($this->request->post);
		  	
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
			
			if ($this->config->get('purchase_order_insert_email')) {
				$this->email($purchase_order_id);
			}
						
      		$this->response->redirect($this->url->link('catalog/purchase_order', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
	
    	$this->getForm();
  	}

  	public function update() {
		$this->language->load('catalog/purchase_order');
	
    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/purchase_order');
		
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
	  		$this->model_catalog_purchase_order->editOrder($this->request->get['purchase_order_id'], $this->request->post);
			
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
			
			if ($this->config->get('purchase_order_update_email')) {
				$this->email($this->request->get['purchase_order_id'], true);
			}
			
			$this->response->redirect($this->url->link('catalog/purchase_order', 'token=' . $this->session->data['token'] . $url, 'SSL'));
    	}
	
    	$this->getForm();
  	}

  	public function delete() {
		$this->language->load('catalog/purchase_order');
	
    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/purchase_order');
		
    	if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $purchase_order) {
				$this->model_catalog_purchase_order->deleteOrder($purchase_order);
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
			
			$this->response->redirect($this->url->link('catalog/purchase_order', 'token=' . $this->session->data['token'] . $url, 'SSL'));
   		}
	
    	$this->getList();
  	}
    
  	protected function getList() {
		if (isset($this->request->get['filter_purchase_order_id'])) {
			$filter_purchase_order_id = $this->request->get['filter_purchase_order_id'];
		} else {
			$filter_purchase_order_id = '';
		}
		
		if (isset($this->request->get['filter_order_name'])) {
			$filter_order_name = $this->request->get['filter_order_name'];
		} else {
			$filter_order_name = '';
		}
		
		if (isset($this->request->get['filter_purchase_order_vendor_id'])) {
			$filter_purchase_order_vendor_id = $this->request->get['filter_purchase_order_vendor_id'];
		} else {
			$filter_purchase_order_vendor_id = '';
		}
		
		if (isset($this->request->get['filter_status_id'])) {
			$filter_status_id = $this->request->get['filter_status_id'];
		} else {
			$filter_status_id = '';
		}
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'purchase_order_id';
		}
		
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
				
		$url = '';
		
		if (isset($this->request->get['filter_purchase_order_id'])) {
			$url .= '&filter_purchase_order_id=' . $this->request->get['filter_purchase_order_id'];
		}
		
		if (isset($this->request->get['filter_order_name'])) {
			$url .= '&filter_order_name=' . $this->request->get['filter_order_name'];
		}
		
		if (isset($this->request->get['filter_purchase_order_vendor_id'])) {
			$url .= '&filter_purchase_order_vendor_id=' . $this->request->get['filter_purchase_order_vendor_id'];
		}
		
		if (isset($this->request->get['filter_status_id'])) {
			$url .= '&filter_status_id=' . $this->request->get['filter_status_id'];
		}
			
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
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('catalog/purchase_order', 'token=' . $this->session->data['token'] . $url, 'SSL')
   		);
							
		$data['print'] = $this->url->link('catalog/purchase_order/view', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['insert'] = $this->url->link('catalog/purchase_order/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('catalog/purchase_order/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');	

		$data['purchase_orders'] = array();

		$filter_data = array(
			'filter_purchase_order_id'	       => $filter_purchase_order_id,
			'filter_order_name'                => $filter_order_name,
			'filter_purchase_order_vendor_id'  => $filter_purchase_order_vendor_id,
			'filter_status_id'	               => $filter_status_id,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		
		$purchase_order_total = $this->model_catalog_purchase_order->getTotalOrders();
	
		$results = $this->model_catalog_purchase_order->getOrders($filter_data);
 
    	foreach ($results as $result) {		
			$data['purchase_orders'][] = array(
				'purchase_order_id' 		=> $result['purchase_order_id'],
				'order_name'       	 		=> $result['order_name'],
				'vendor'       	 			=> $result['vendor'],
				'status'       	 			=> $result['status'],
				'payment'       	 		=> $result['payment'],
				'shipping'       	 		=> $result['shipping'],
				'total'       	 			=> $result['total'],
				'date_arrival'       	 	=> date('d M Y H:i', strtotime($result['date_arrival'])),
				'date_received'       	 	=> $result['received'] ? date('d M Y H:i', strtotime($result['date_received'])) : $this->language->get('text_na'),
				'date_added'       	 		=> date('d M Y H:i', strtotime($result['date_added'])),
				'selected'        			=> isset($this->request->post['selected']) && in_array($result['purchase_order_id'], $this->request->post['selected']),
				'edit'          			=> $this->url->link('catalog/purchase_order/update', 'token=' . $this->session->data['token'] . '&purchase_order_id=' . $result['purchase_order_id'] . $url, 'SSL'),
				'view'						=> $this->url->link('catalog/purchase_order/view', 'token=' . $this->session->data['token'] . '&purchase_order_id=' . $result['purchase_order_id'], 'SSL')
			);
		}	
	
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_view'] = $this->language->get('text_view');
		$data['text_received'] = $this->language->get('text_received');
		$data['text_resend'] = $this->language->get('text_resend');
		$data['text_confirm_received'] = $this->language->get('text_confirm_received');
		$data['text_confirm_resend'] = $this->language->get('text_confirm_resend');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_purchase_order_id'] = $this->language->get('column_purchase_order_id');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_vendor'] = $this->language->get('column_vendor');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_payment'] = $this->language->get('column_payment');
		$data['column_shipping'] = $this->language->get('column_shipping');
		$data['column_total'] = $this->language->get('column_total');
		$data['column_date_arrival'] = $this->language->get('column_date_arrival');
		$data['column_date_received'] = $this->language->get('column_date_received');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_action'] = $this->language->get('column_action');		
		
		$data['button_filter'] = $this->language->get('button_filter');
		$data['button_insert'] = $this->language->get('button_insert');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_print'] = $this->language->get('button_print');
 
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
		
		if (isset($this->request->get['filter_purchase_order_id'])) {
			$url .= '&filter_purchase_order_id=' . $this->request->get['filter_purchase_order_id'];
		}
		
		if (isset($this->request->get['filter_order_name'])) {
			$url .= '&filter_order_name=' . $this->request->get['filter_order_name'];
		}
		
		if (isset($this->request->get['filter_purchase_order_vendor_id'])) {
			$url .= '&filter_purchase_order_vendor_id=' . $this->request->get['filter_purchase_order_vendor_id'];
		}
		
		if (isset($this->request->get['filter_status_id'])) {
			$url .= '&filter_status_id=' . $this->request->get['filter_status_id'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data['sort_purchase_order_id'] = $this->url->link('catalog/purchase_order', 'token=' . $this->session->data['token'] . '&sort=purchase_order_id' . $url, 'SSL');
		$data['sort_name'] = $this->url->link('catalog/purchase_order', 'token=' . $this->session->data['token'] . '&sort=order_name' . $url, 'SSL');
		$data['sort_vendor'] = $this->url->link('catalog/purchase_order', 'token=' . $this->session->data['token'] . '&sort=vendor' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('catalog/purchase_order', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');
		$data['sort_payment'] = $this->url->link('catalog/purchase_order', 'token=' . $this->session->data['token'] . '&sort=payment' . $url, 'SSL');
		$data['sort_shipping'] = $this->url->link('catalog/purchase_order', 'token=' . $this->session->data['token'] . '&sort=shipping' . $url, 'SSL');
		$data['sort_total'] = $this->url->link('catalog/purchase_order', 'token=' . $this->session->data['token'] . '&sort=total' . $url, 'SSL');
		$data['sort_date_arrival'] = $this->url->link('catalog/purchase_order', 'token=' . $this->session->data['token'] . '&sort=date_arrival' . $url, 'SSL');
		$data['sort_date_received'] = $this->url->link('catalog/purchase_order', 'token=' . $this->session->data['token'] . '&sort=date_received' . $url, 'SSL');
		$data['sort_date_added'] = $this->url->link('catalog/purchase_order', 'token=' . $this->session->data['token'] . '&sort=date_added' . $url, 'SSL');
		
		$url = '';
		
		if (isset($this->request->get['filter_purchase_order_id'])) {
			$url .= '&filter_purchase_order_id=' . $this->request->get['filter_purchase_order_id'];
		}
		
		if (isset($this->request->get['filter_order_name'])) {
			$url .= '&filter_order_name=' . $this->request->get['filter_order_name'];
		}
		
		if (isset($this->request->get['filter_purchase_order_vendor_id'])) {
			$url .= '&filter_purchase_order_vendor_id=' . $this->request->get['filter_purchase_order_vendor_id'];
		}
		
		if (isset($this->request->get['filter_status_id'])) {
			$url .= '&filter_status_id=' . $this->request->get['filter_status_id'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $purchase_order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('catalog/purchase_order', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
			
		$data['pagination'] = $pagination->render();
		
		$data['filter_purchase_order_id'] = $filter_purchase_order_id;
		$data['filter_order_name'] = $filter_order_name;
		$data['filter_purchase_order_vendor_id'] = $filter_purchase_order_vendor_id;
		$data['filter_status_id'] = $filter_status_id;
		
		$data['results'] = sprintf($this->language->get('text_pagination'), ($purchase_order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($purchase_order_total - $this->config->get('config_limit_admin'))) ? $purchase_order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $purchase_order_total, ceil($purchase_order_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;
		
		$data['token'] = $this->session->data['token'];
		
		$this->load->model('catalog/purchase_order_vendor');
		
		$data['vendors'] = $this->model_catalog_purchase_order_vendor->getVendors();
		
		$this->load->model('localisation/order_status');
		
		$data['statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/purchase_order_list.tpl', $data));
  	}
  
  	protected function getForm() {
     	$data['heading_title'] = $this->language->get('heading_title');

    	$data['entry_name'] = $this->language->get('entry_name');
    	$data['entry_vendor'] = $this->language->get('entry_vendor');
    	$data['entry_payment'] = $this->language->get('entry_payment');
    	$data['entry_shipping'] = $this->language->get('entry_shipping');
    	$data['entry_date_arrival'] = $this->language->get('entry_date_arrival');
    	$data['entry_status'] = $this->language->get('entry_status');
    	$data['entry_comment'] = $this->language->get('entry_comment');
    	$data['entry_received'] = $this->language->get('entry_received');
    	$data['entry_date_received'] = $this->language->get('entry_date_received');
    	$data['entry_product'] = $this->language->get('entry_product');
    	$data['entry_sold'] = $this->language->get('entry_sold');
    	$data['entry_stock'] = $this->language->get('entry_stock');
    	$data['entry_model'] = $this->language->get('entry_model');
    	$data['entry_quantity'] = $this->language->get('entry_quantity');
    	$data['entry_price'] = $this->language->get('entry_price');
    	$data['entry_total'] = $this->language->get('entry_total');
    	$data['entry_value'] = $this->language->get('entry_value');
		
    	$data['text_yes'] = $this->language->get('text_yes');
    	$data['text_no'] = $this->language->get('text_no');

    	$data['button_save'] = $this->language->get('button_save');
    	$data['button_cancel'] = $this->language->get('button_cancel');
    	$data['button_populate'] = $this->language->get('button_populate');
    	$data['button_add_product'] = $this->language->get('button_add_product');
    	$data['button_add_total'] = $this->language->get('button_add_total');
    
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
		
		if (isset($this->error['vendor'])) {
			$data['error_vendor'] = $this->error['vendor'];
		} else {
			$data['error_vendor'] = '';
		}
		
		if (isset($this->error['payment'])) {
			$data['error_payment'] = $this->error['payment'];
		} else {
			$data['error_payment'] = '';
		}
		
		if (isset($this->error['shipping'])) {
			$data['error_shipping'] = $this->error['shipping'];
		} else {
			$data['error_shipping'] = '';
		}
		
		if (isset($this->error['date_arrival'])) {
			$data['error_date_arrival'] = $this->error['date_arrival'];
		} else {
			$data['error_date_arrival'] = '';
		}
		
		if (isset($this->error['status'])) {
			$data['error_status'] = $this->error['status'];
		} else {
			$data['error_status'] = '';
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
			'href'      => $this->url->link('catalog/purchase_order', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);
		
		if (!isset($this->request->get['purchase_order_id'])) {
			$data['action'] = $this->url->link('catalog/purchase_order/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('catalog/purchase_order/update', 'token=' . $this->session->data['token'] . '&purchase_order_id=' . $this->request->get['purchase_order_id'] . $url, 'SSL');
		}
			
		$data['cancel'] = $this->url->link('catalog/purchase_order', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (!isset($this->request->get['purchase_order_id'])) {
			$order_info = array();
		} else {
			$order_info = $this->model_catalog_purchase_order->getOrder($this->request->get['purchase_order_id']);
		}
		
		if (isset($this->request->post['order_name'])) {
			$data['order_name'] = $this->request->post['order_name'];
		} elseif ($order_info) {
			$data['order_name'] = $order_info['order_name'];
		} else {
			$data['order_name'] = '';
		}
		
		if (isset($this->request->post['vendor'])) {
			$data['vendor'] = $this->request->post['vendor'];
		} elseif ($order_info) {
			$data['vendor'] = $order_info['vendor'];
		} else {
			$data['vendor'] = '';
		}
		
		if (isset($this->request->post['purchase_order_vendor_id'])) {
			$data['purchase_order_vendor_id'] = $this->request->post['purchase_order_vendor_id'];
		} elseif ($order_info) {
			$data['purchase_order_vendor_id'] = $order_info['purchase_order_vendor_id'];
		} else {
			$data['purchase_order_vendor_id'] = '';
		}
		
		if (isset($this->request->post['purchase_order_payment_id'])) {
			$data['purchase_order_payment_id'] = $this->request->post['purchase_order_payment_id'];
		} elseif ($order_info) {
			$data['purchase_order_payment_id'] = $order_info['purchase_order_payment_id'];
		} else {
			$data['purchase_order_payment_id'] = '';
		}
		
		if (isset($this->request->post['purchase_order_shipping_id'])) {
			$data['purchase_order_shipping_id'] = $this->request->post['purchase_order_shipping_id'];
		} elseif ($order_info) {
			$data['purchase_order_shipping_id'] = $order_info['purchase_order_shipping_id'];
		} else {
			$data['purchase_order_shipping_id'] = '';
		}
		
		if (isset($this->request->post['date_arrival'])) {
			$data['date_arrival'] = $this->request->post['date_arrival'];
		} elseif ($order_info) {
			$data['date_arrival'] = $order_info['date_arrival'];
		} else {
			$data['date_arrival'] = '';
		}
		
		if (isset($this->request->post['status_id'])) {
			$data['status_id'] = $this->request->post['status_id'];
		} elseif ($order_info) {
			$data['status_id'] = $order_info['status_id'];
		} else {
			$data['status_id'] = '';
		}
		
		if (isset($this->request->post['comment'])) {
			$data['comment'] = $this->request->post['comment'];
		} elseif ($order_info) {
			$data['comment'] = $order_info['comment'];
		} else {
			$data['comment'] = '';
		}
		
		if (isset($this->request->post['received'])) {
			$data['received'] = $this->request->post['received'];
		} elseif ($order_info) {
			$data['received'] = $order_info['received'];
		} else {
			$data['received'] = '';
		}
		
		if (isset($this->request->post['date_received'])) {
			$data['date_received'] = $this->request->post['date_received'];
		} elseif ($order_info) {
			$data['date_received'] = $order_info['date_received'];
		} else {
			$data['date_received'] = '';
		}
		
		if (isset($this->request->post['total'])) {
			$data['total'] = $this->request->post['total'];
		} elseif ($order_info) {
			$data['total'] = $order_info['total'];
		} else {
			$data['total'] = '';
		}
		
		if (isset($this->request->post['products'])) {
			$data['products'] = $this->request->post['products'];
		} elseif ($order_info) {
			$data['products'] = $order_info['products'];
		} else {
			$data['products'] = array();
		}
		
		if (isset($this->request->post['totals'])) {
			$data['totals'] = $this->request->post['totals'];
		} elseif ($order_info) {
			$data['totals'] = $order_info['totals'];
		} else {
			$data['totals'] = array();
		}
		
		$this->load->model('localisation/order_status');
		
		$data['statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		$this->load->model('catalog/purchase_order_payment');
		
		$data['payments'] = $this->model_catalog_purchase_order_payment->getPayments();
		
		$this->load->model('catalog/purchase_order_shipping');
		
		$data['shippings'] = $this->model_catalog_purchase_order_shipping->getShippings();
		
		$data['token'] = $this->session->data['token'];
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/purchase_order_form.tpl', $data));
  	}
	
	public function view() {
		$this->language->load('catalog/purchase_order');
		
		$this->load->model('catalog/purchase_order');
		
		$data['title'] = $this->language->get('heading_title');

		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$data['base'] = HTTPS_SERVER;
		} else {
			$data['base'] = HTTP_SERVER;
		}
		
		$data['direction'] = $this->language->get('direction');
		$data['language'] = $this->language->get('code');
		
		$data['text_telephone'] = $this->language->get('text_telephone');
		$data['text_fax'] = $this->language->get('text_fax');
		$data['text_date_added'] = $this->language->get('text_date_added');
		$data['text_order'] = $this->language->get('text_order');
		$data['text_payment_method'] = $this->language->get('text_payment_method');
		$data['text_shipping_method'] = $this->language->get('text_shipping_method');
		$data['text_to'] = $this->language->get('text_to');
		
    	$data['entry_date_arrival'] = $this->language->get('entry_date_arrival');
    	$data['entry_comment'] = $this->language->get('entry_comment');
    	$data['entry_product'] = $this->language->get('entry_product');
    	$data['entry_model'] = $this->language->get('entry_model');
    	$data['entry_quantity'] = $this->language->get('entry_quantity');
    	$data['entry_price'] = $this->language->get('entry_price');
    	$data['entry_total'] = $this->language->get('entry_total');
    	$data['entry_value'] = $this->language->get('entry_value');
		
		$data['store_name'] = $this->config->get('config_name');
		$data['store_address'] = $this->config->get('config_address');
		$data['store_telephone'] = $this->config->get('config_telephone');
		$data['store_fax'] = $this->config->get('config_fax');
		$data['store_email'] = $this->config->get('config_email');
			
		$order_id = array();
		
		if (isset($this->request->get['purchase_order_id'])) {
			$order_ids[] = $this->request->get['purchase_order_id'];
		} elseif (isset($this->request->post['selected'])) {
			$order_ids = $this->request->post['selected'];
		} else {
			$order_ids[] = 0;
		}
		
		$this->load->model('catalog/purchase_order_vendor');

		$data['orders'] = array();
		
		foreach ($order_ids as $order_id) {
			$order_info = $this->model_catalog_purchase_order->getOrder($order_id);
		
			if ($order_info) {
				$vendor_info = $this->model_catalog_purchase_order_vendor->getVendor($order_info['purchase_order_vendor_id']);
			
				$products = array();
			
				foreach ($order_info['products'] as $result) {
					$products[] = array(
						'name'		=> $result['name'],
						'model'		=> $result['model'],
						'quantity'	=> $result['quantity'],
						'price'		=> $this->currency->format($result['price'], $this->config->get('config_currency_code')),
						'total'		=> $this->currency->format($result['total'], $this->config->get('config_currency_code')),
						'options'	=> $result['options']
					);
				}
				
				$totals = array();
				
				foreach ($order_info['totals'] as $result) {
					$totals[] = array(
						'name'		=> $result['name'],
						'value'		=> $this->currency->format($result['value'], $this->config->get('config_currency_code'))
					);
				}
			
				$data['orders'][] = array(
					'order_name' 				=> $order_info['order_name'],
					'vendor' 					=> $order_info['vendor'],
					'purchase_order_payment' 	=> $order_info['purchase_order_payment'],
					'purchase_order_shipping' 	=> $order_info['purchase_order_shipping'],
					'date_arrival' 				=> $order_info['date_arrival'],
					'comment' 					=> $order_info['comment'],
					'received' 					=> $order_info['received'],
					'date_received' 			=> $order_info['date_received'],
					'order_total' 				=> $this->currency->format($order_info['total'], $this->config->get('config_currency_code')),
					'products' 					=> $products,
					'totals' 					=> $totals,
					'address_1'					=> $vendor_info['address_1'],
					'address_2'					=> $vendor_info['address_2'],
					'city'						=> $vendor_info['city'],
					'postcode'					=> $vendor_info['postcode'],
					'country'					=> $vendor_info['country'],
					'zone'						=> $vendor_info['zone'],
					'email'						=> $vendor_info['email'],
					'telephone'					=> $vendor_info['telephone'],
					'fax'						=> $vendor_info['fax'],
					'date_added'				=> date('d M Y', strtotime($order_info['date_added']))
				);
			}
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/purchase_order_view.tpl', $data));
  	}
	
	public function received() {
		$json = array();
		
		$this->language->load('catalog/purchase_order');
		
		if (!$this->user->hasPermission('modify', 'catalog/purchase_order')) {
      		$json['error'] = $this->language->get('error_permission');
    	} else {
			$purchase_order_id = $this->request->get['purchase_order_id'];
			
			$this->load->model('catalog/purchase_order');
			
			if (!$this->model_catalog_purchase_order->received($purchase_order_id)) {
				$json['error'] = $this->language->get('error_received');
			}
			
			$json['success'] = $this->language->get('text_success');
			$json['date'] = date('d M Y H:i');
		}
		
		$this->response->setOutput(json_encode($json));
  	}
	
	public function resend() {
		$json = array();
		
		$this->language->load('catalog/purchase_order');
		
		if (!$this->user->hasPermission('modify', 'catalog/purchase_order')) {
      		$json['error'] = $this->language->get('error_permission');
    	} else {
			$purchase_order_id = $this->request->get['purchase_order_id'];
			
			$this->email($purchase_order_id);
			
			$json['success'] = $this->language->get('text_success');
		}
		
		$this->response->setOutput(json_encode($json));
  	}
	
	private function email($purchase_order_id, $update = false) {
		$this->language->load('catalog/purchase_order');
		
		$this->load->model('catalog/purchase_order');
		
		$data['mail_greeting'] = $this->language->get('mail_greeting');
		
		if ($update) {
			$data['mail_order'] = $this->language->get('mail_update');
		} else {
			$data['mail_order'] = $this->language->get('mail_order');
		}
		
		$data['text_telephone'] = $this->language->get('text_telephone');
		$data['text_fax'] = $this->language->get('text_fax');
		$data['text_date_added'] = $this->language->get('text_date_added');
		$data['text_order'] = $this->language->get('text_order');
		$data['text_payment_method'] = $this->language->get('text_payment_method');
		$data['text_shipping_method'] = $this->language->get('text_shipping_method');
		$data['text_to'] = $this->language->get('text_to');
		
    	$data['entry_date_arrival'] = $this->language->get('entry_date_arrival');
    	$data['entry_comment'] = $this->language->get('entry_comment');
    	$data['entry_product'] = $this->language->get('entry_product');
    	$data['entry_model'] = $this->language->get('entry_model');
    	$data['entry_quantity'] = $this->language->get('entry_quantity');
    	$data['entry_price'] = $this->language->get('entry_price');
    	$data['entry_total'] = $this->language->get('entry_total');
    	$data['entry_value'] = $this->language->get('entry_value');
		
		$data['store_name'] = $this->config->get('config_name');
		$data['store_address'] = $this->config->get('config_address');
		$data['store_telephone'] = $this->config->get('config_telephone');
		$data['store_fax'] = $this->config->get('config_fax');
		$data['store_email'] = $this->config->get('config_email');
			
		$this->load->model('catalog/purchase_order_vendor');

		$data['orders'] = array();
		
		$order_info = $this->model_catalog_purchase_order->getOrder($purchase_order_id);
	
		if ($order_info) {
			$vendor_info = $this->model_catalog_purchase_order_vendor->getVendor($order_info['purchase_order_vendor_id']);
		
			$products = array();
		
			foreach ($order_info['products'] as $result) {
				$products[] = array(
					'name'		=> $result['name'],
					'model'		=> $result['model'],
					'quantity'	=> $result['quantity'],
					'price'		=> $this->currency->format($result['price'], $this->config->get('config_currency_code')),
					'total'		=> $this->currency->format($result['total'], $this->config->get('config_currency_code')),
					'options'	=> $result['options']
				);
			}
			
			$totals = array();
			
			foreach ($order_info['totals'] as $result) {
				$totals[] = array(
					'name'		=> $result['name'],
					'value'		=> $this->currency->format($result['value'], $this->config->get('config_currency_code'))
				);
			}
		
			$data['order_id'] = $order_info['purchase_order_id'];
			$data['title'] = $order_info['order_name'];
			$data['order_name'] = $order_info['order_name'];
			$data['vendor'] = $order_info['vendor'];
			$data['purchase_order_payment'] = $order_info['purchase_order_payment'];
			$data['purchase_order_shipping'] = $order_info['purchase_order_shipping'];
			$data['date_arrival'] = $order_info['date_arrival'];
			$data['comment'] = $order_info['comment'];
			$data['received'] = $order_info['received'];
			$data['date_received'] = $order_info['date_received'];
			$data['order_total'] = $this->currency->format($order_info['total'], $this->config->get('config_currency_code'));
			$data['products'] = $products;
			$data['totals'] = $totals;
			$data['address_1'] = $vendor_info['address_1'];
			$data['address_2'] = $vendor_info['address_2'];
			$data['city'] = $vendor_info['city'];
			$data['postcode'] = $vendor_info['postcode'];
			$data['country'] = $vendor_info['country'];
			$data['zone'] = $vendor_info['zone'];
			$data['email'] = $vendor_info['email'];
			$data['telephone'] = $vendor_info['telephone'];
			$data['fax'] = $vendor_info['fax'];
			$data['date_added'] = date('d M Y', strtotime($order_info['date_added']));
			
			$html = $this->load->view('catalog/purchase_order_mail.tpl', $data);
			
			$subject = sprintf($this->language->get('mail_subject'), $this->config->get('config_name'), $order_info['order_name'], $order_info['purchase_order_id']);
			
			// Text Mail
			$text = $this->language->get('mail_greeting') . "\n\n";
			$text .= $this->language->get('mail_order') . "\n\n";
			
			// Products
			$text .= $this->language->get('entry_product') . "\n";
			
			foreach ($products as $product) {
				$text .= $product['quantity'] . 'x ' . $product['name'] . ' (' . $product['model'] . ') ' . $product['total'] . "\n";

				foreach ($product['options'] as $option) {
					$text .= chr(9) . '-' . $option['name'] . ' ' . $option['value'] . "\n";
				}
			}
						
			$text .= "\n";
			
			$text .= $this->language->get('entry_total') . "\n";
			
			foreach ($totals as $total) {
				$text .= $total['name'] . ': ' . $total['value'] . "\n";
			}			
			
			$text .= "\n";
			
			// Comment
			if ($order_info['comment']) {
				$text .= $this->language->get('entry_comment') . "\n\n";
				$text .= $order_info['comment'] . "\n\n";
			}
			
			if (version_compare(VERSION, '2.0.2.0', '<')) {
					$mail = new Mail($this->config->get('config_mail'));
				} else {
					$mail = new Mail();
					$mail->protocol = $this->config->get('config_mail_protocol');
					$mail->parameter = $this->config->get('config_mail_parameter');
					$mail->smtp_hostname = $this->config->get('config_mail_smtp_host');
					$mail->smtp_username = $this->config->get('config_mail_smtp_username');
					$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
					$mail->smtp_port = $this->config->get('config_mail_smtp_port');
					$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
				}
				
			$mail->setTo($vendor_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($this->config->get('config_name'));
			$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			$mail->setHtml($html);
			$mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
			$mail->send();
		}
  	}
  	
	protected function validateForm() {
    	if (!$this->user->hasPermission('modify', 'catalog/purchase_order')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}
	
		if ((utf8_strlen($this->request->post['order_name']) < 3) || (utf8_strlen($this->request->post['order_name']) > 255)) {
			$this->error['name'] = $this->language->get('error_name');
		}
		
		if (empty($this->request->post['purchase_order_vendor_id'])) {
			$this->error['vendor'] = $this->language->get('error_vendor');
		}
		
		if (empty($this->request->post['purchase_order_payment_id'])) {
			$this->error['payment'] = $this->language->get('error_payment');
		}
		
		if (empty($this->request->post['purchase_order_shipping_id'])) {
			$this->error['shipping'] = $this->language->get('error_shipping');
		}
		
		if (empty($this->request->post['date_arrival'])) {
			$this->error['date_arrival'] = $this->language->get('error_date_arrival');
		}
		
		if (empty($this->request->post['status_id'])) {
			$this->error['status'] = $this->language->get('error_status');
		}
		
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}

  	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/purchase_order')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}
		
		if (!$this->error) { 
	  		return true;
		} else {
	  		return false;
		}
  	}	  
}
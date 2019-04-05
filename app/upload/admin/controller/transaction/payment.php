<?php
class ControllerTransactionPayment extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('transaction/payment');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('transaction/payment');

		$this->getList();
	}

	public function add() {
		$this->load->language('transaction/payment');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('transaction/payment');

		unset($this->session->data['cookie']);

		if ($this->validate()) {
			// API
			$this->load->model('user/api');

			$api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

			if ($api_info) {
				$curl = curl_init();

				// Set SSL if required
				if (substr(HTTPS_CATALOG, 0, 5) == 'https') {
					curl_setopt($curl, CURLOPT_PORT, 443);
				}

				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLINFO_HEADER_OUT, true);
				curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_URL, HTTPS_CATALOG . 'index.php?route=api/login');
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($api_info));

				$json = curl_exec($curl);

				if (!$json) {
					$this->error['warning'] = sprintf($this->language->get('error_curl'), curl_error($curl), curl_errno($curl));
				} else {
					$response = json_decode($json, true);

					if (isset($response['cookie'])) {
						$this->session->data['cookie'] = $response['cookie'];
					}

					curl_close($curl);
				}
			}
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('transaction/payment');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('transaction/payment');

		unset($this->session->data['cookie']);

		if ($this->validate()) {
			// API
			$this->load->model('user/api');

			$api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

			if ($api_info) {
				$curl = curl_init();

				// Set SSL if required
				if (substr(HTTPS_CATALOG, 0, 5) == 'https') {
					curl_setopt($curl, CURLOPT_PORT, 443);
				}

				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLINFO_HEADER_OUT, true);
				curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_URL, HTTPS_CATALOG . 'index.php?route=api/login');
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($api_info));

				$json = curl_exec($curl);

				if (!$json) {
					$this->error['warning'] = sprintf($this->language->get('error_curl'), curl_error($curl), curl_errno($curl));
				} else {
					$response = json_decode($json, true);

					if (isset($response['cookie'])) {
						$this->session->data['cookie'] = $response['cookie'];
					}

					curl_close($curl);
				}
			}
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('transaction/payment');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('transaction/payment');

		unset($this->session->data['cookie']);

		if (isset($this->request->get['payment_id']) && $this->validate()) {
			// API
			$this->load->model('user/api');

			$api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

			if ($api_info) {
				$curl = curl_init();

				// Set SSL if required
				if (substr(HTTPS_CATALOG, 0, 5) == 'https') {
					curl_setopt($curl, CURLOPT_PORT, 443);
				}

				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLINFO_HEADER_OUT, true);
				curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_URL, HTTPS_CATALOG . 'index.php?route=api/login');
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($api_info));

				$json = curl_exec($curl);

				if (!$json) {
					$this->error['warning'] = sprintf($this->language->get('error_curl'), curl_error($curl), curl_errno($curl));
				} else {
					$response = json_decode($json, true);

					if (isset($response['cookie'])) {
						$this->session->data['cookie'] = $response['cookie'];
					}

					curl_close($curl);
				}
			}
		}

		if (isset($this->session->data['cookie'])) {
			$curl = curl_init();

			// Set SSL if required
			if (substr(HTTPS_CATALOG, 0, 5) == 'https') {
				curl_setopt($curl, CURLOPT_PORT, 443);
			}

			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLINFO_HEADER_OUT, true);
			curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_URL, HTTPS_CATALOG . 'index.php?route=api/payment/delete&payment_id=' . $this->request->get['payment_id']);
			curl_setopt($curl, CURLOPT_COOKIE, session_name() . '=' . $this->session->data['cookie'] . ';');

			$json = curl_exec($curl);

			if (!$json) {
				$this->error['warning'] = sprintf($this->language->get('error_curl'), curl_error($curl), curl_errno($curl));
			} else {
				$response = json_decode($json, true);

				curl_close($curl);

				if (isset($response['error'])) {
					$this->error['warning'] = $response['error'];
				}
			}
		}

		if (isset($response['error'])) {
			$this->error['warning'] = $response['error'];
		}

		if (isset($response['success'])) {
			$this->session->data['success'] = $response['success'];

			$url = '';

			if (isset($this->request->get['filter_payment_id'])) {
				$url .= '&filter_payment_id=' . $this->request->get['filter_payment_id'];
			}

			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_payment_status'])) {
				$url .= '&filter_payment_status=' . $this->request->get['filter_payment_status'];
			}

			if (isset($this->request->get['filter_total'])) {
				$url .= '&filter_total=' . $this->request->get['filter_total'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['payment'])) {
				$url .= '&payment=' . $this->request->get['payment'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('transaction/payment', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_payment_id'])) {
			$filter_payment_id = $this->request->get['filter_payment_id'];
		} else {
			$filter_payment_id = null;
		}

		if (isset($this->request->get['filter_customer'])) {
			$filter_customer = $this->request->get['filter_customer'];
		} else {
			$filter_customer = null;
		}

		if (isset($this->request->get['filter_payment_status'])) {
			$filter_payment_status = $this->request->get['filter_payment_status'];
		} else {
			$filter_payment_status = null;
		}

		if (isset($this->request->get['filter_total'])) {
			$filter_total = $this->request->get['filter_total'];
		} else {
			$filter_total = null;
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'o.payment_id';
		}

		if (isset($this->request->get['payment'])) {
			$payment = $this->request->get['payment'];
		} else {
			$payment = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_payment_id'])) {
			$url .= '&filter_payment_id=' . $this->request->get['filter_payment_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_payment_status'])) {
			$url .= '&filter_payment_status=' . $this->request->get['filter_payment_status'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['payment'])) {
			$url .= '&payment=' . $this->request->get['payment'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('transaction/payment', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['invoice'] = $this->url->link('transaction/payment/invoice', 'token=' . $this->session->data['token'], 'SSL');
		$data['shipping'] = $this->url->link('transaction/payment/shipping', 'token=' . $this->session->data['token'], 'SSL');
		$data['add'] = $this->url->link('transaction/payment/add', 'token=' . $this->session->data['token'], 'SSL');

		$data['payments'] = array();

		$filter_data = array(
			'filter_payment_id'      => $filter_payment_id,
			'filter_customer'	   => $filter_customer,
			'filter_payment_status'  => $filter_payment_status,
			'filter_total'         => $filter_total,
			'filter_date_added'    => $filter_date_added,
			'filter_date_modified' => $filter_date_modified,
			'sort'                 => $sort,
			'payment'                => $payment,
			'start'                => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                => $this->config->get('config_limit_admin')
		);

		$payment_total = $this->model_transaction_payment->getTotalPayments($filter_data);

		$results = $this->model_transaction_payment->getPayments($filter_data);

		foreach ($results as $result) {
			$data['payments'][] = array(
				'payment_id'      => $result['payment_id'],
				'customer'      => $result['customer'],
				'status'        => $result['status'],
				'total'         => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'date_added'    => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
				'shipping_code' => $result['shipping_code'],
				'view'          => $this->url->link('transaction/payment/info', 'token=' . $this->session->data['token'] . '&payment_id=' . $result['payment_id'] . $url, 'SSL'),
				'edit'          => $this->url->link('transaction/payment/edit', 'token=' . $this->session->data['token'] . '&payment_id=' . $result['payment_id'] . $url, 'SSL'),
				'delete'        => $this->url->link('transaction/payment/delete', 'token=' . $this->session->data['token'] . '&payment_id=' . $result['payment_id'] . $url, 'SSL')
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_missing'] = $this->language->get('text_missing');

		$data['column_payment_id'] = $this->language->get('column_payment_id');
		$data['column_customer'] = $this->language->get('column_customer');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_total'] = $this->language->get('column_total');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_date_modified'] = $this->language->get('column_date_modified');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_return_id'] = $this->language->get('entry_return_id');
		$data['entry_payment_id'] = $this->language->get('entry_payment_id');
		$data['entry_customer'] = $this->language->get('entry_customer');
		$data['entry_payment_status'] = $this->language->get('entry_payment_status');
		$data['entry_total'] = $this->language->get('entry_total');
		$data['entry_date_added'] = $this->language->get('entry_date_added');
		$data['entry_date_modified'] = $this->language->get('entry_date_modified');

		$data['button_invoice_print'] = $this->language->get('button_invoice_print');
		$data['button_shipping_print'] = $this->language->get('button_shipping_print');
		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');
		$data['button_view'] = $this->language->get('button_view');

		$data['token'] = $this->session->data['token'];

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

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_payment_id'])) {
			$url .= '&filter_payment_id=' . $this->request->get['filter_payment_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_payment_status'])) {
			$url .= '&filter_payment_status=' . $this->request->get['filter_payment_status'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if ($payment == 'ASC') {
			$url .= '&payment=DESC';
		} else {
			$url .= '&payment=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_payment'] = $this->url->link('transaction/payment', 'token=' . $this->session->data['token'] . '&sort=o.payment_id' . $url, 'SSL');
		$data['sort_customer'] = $this->url->link('transaction/payment', 'token=' . $this->session->data['token'] . '&sort=customer' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('transaction/payment', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');
		$data['sort_total'] = $this->url->link('transaction/payment', 'token=' . $this->session->data['token'] . '&sort=o.total' . $url, 'SSL');
		$data['sort_date_added'] = $this->url->link('transaction/payment', 'token=' . $this->session->data['token'] . '&sort=o.date_added' . $url, 'SSL');
		$data['sort_date_modified'] = $this->url->link('transaction/payment', 'token=' . $this->session->data['token'] . '&sort=o.date_modified' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['filter_payment_id'])) {
			$url .= '&filter_payment_id=' . $this->request->get['filter_payment_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_payment_status'])) {
			$url .= '&filter_payment_status=' . $this->request->get['filter_payment_status'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['payment'])) {
			$url .= '&payment=' . $this->request->get['payment'];
		}

		$pagination = new Pagination();
		$pagination->total = $payment_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('transaction/payment', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($payment_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($payment_total - $this->config->get('config_limit_admin'))) ? $payment_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $payment_total, ceil($payment_total / $this->config->get('config_limit_admin')));

		$data['filter_payment_id'] = $filter_payment_id;
		$data['filter_customer'] = $filter_customer;
		$data['filter_payment_status'] = $filter_payment_status;
		$data['filter_total'] = $filter_total;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_date_modified'] = $filter_date_modified;

		$this->load->model('localisation/payment_status');

		$data['payment_statuses'] = $this->model_localisation_payment_status->getPaymentStatuses();

		$data['sort'] = $sort;
		$data['payment'] = $payment;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('transaction/payment_list.tpl', $data));
	}

	public function getForm() {
		$this->load->model('transaction/customer');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['payment_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['text_product'] = $this->language->get('text_product');
		$data['text_voucher'] = $this->language->get('text_voucher');
		$data['text_payment'] = $this->language->get('text_payment');

		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_customer'] = $this->language->get('entry_customer');
		$data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$data['entry_firstname'] = $this->language->get('entry_firstname');
		$data['entry_lastname'] = $this->language->get('entry_lastname');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_telephone'] = $this->language->get('entry_telephone');
		$data['entry_fax'] = $this->language->get('entry_fax');
		$data['entry_comment'] = $this->language->get('entry_comment');
		$data['entry_affiliate'] = $this->language->get('entry_affiliate');
		$data['entry_address'] = $this->language->get('entry_address');
		$data['entry_company'] = $this->language->get('entry_company');
		$data['entry_address_1'] = $this->language->get('entry_address_1');
		$data['entry_address_2'] = $this->language->get('entry_address_2');
		$data['entry_city'] = $this->language->get('entry_city');
		$data['entry_postcode'] = $this->language->get('entry_postcode');
		$data['entry_zone'] = $this->language->get('entry_zone');
		$data['entry_zone_code'] = $this->language->get('entry_zone_code');
		$data['entry_country'] = $this->language->get('entry_country');
		$data['entry_product'] = $this->language->get('entry_product');
		$data['entry_option'] = $this->language->get('entry_option');
		$data['entry_quantity'] = $this->language->get('entry_quantity');
		$data['entry_to_name'] = $this->language->get('entry_to_name');
		$data['entry_to_email'] = $this->language->get('entry_to_email');
		$data['entry_from_name'] = $this->language->get('entry_from_name');
		$data['entry_from_email'] = $this->language->get('entry_from_email');
		$data['entry_theme'] = $this->language->get('entry_theme');
		$data['entry_message'] = $this->language->get('entry_message');
		$data['entry_amount'] = $this->language->get('entry_amount');
		$data['entry_currency'] = $this->language->get('entry_currency');
		$data['entry_shipping_method'] = $this->language->get('entry_shipping_method');
		$data['entry_payment_method'] = $this->language->get('entry_payment_method');
		$data['entry_coupon'] = $this->language->get('entry_coupon');
		$data['entry_voucher'] = $this->language->get('entry_voucher');
		$data['entry_reward'] = $this->language->get('entry_reward');
		$data['entry_payment_status'] = $this->language->get('entry_payment_status');

		$data['column_product'] = $this->language->get('column_product');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_total'] = $this->language->get('column_total');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_continue'] = $this->language->get('button_continue');
		$data['button_back'] = $this->language->get('button_back');
		$data['button_product_add'] = $this->language->get('button_product_add');
		$data['button_voucher_add'] = $this->language->get('button_voucher_add');
		$data['button_apply'] = $this->language->get('button_apply');
		$data['button_upload'] = $this->language->get('button_upload');
		$data['button_remove'] = $this->language->get('button_remove');

		$data['tab_payment'] = $this->language->get('tab_payment');
		$data['tab_customer'] = $this->language->get('tab_customer');
		$data['tab_payment'] = $this->language->get('tab_payment');
		$data['tab_shipping'] = $this->language->get('tab_shipping');
		$data['tab_product'] = $this->language->get('tab_product');
		$data['tab_voucher'] = $this->language->get('tab_voucher');
		$data['tab_total'] = $this->language->get('tab_total');

		$data['token'] = $this->session->data['token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_payment_id'])) {
			$url .= '&filter_payment_id=' . $this->request->get['filter_payment_id'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_payment_status'])) {
			$url .= '&filter_payment_status=' . $this->request->get['filter_payment_status'];
		}

		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['payment'])) {
			$url .= '&payment=' . $this->request->get['payment'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('transaction/payment', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['cancel'] = $this->url->link('transaction/payment', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['payment_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$payment_info = $this->model_transaction_payment->getPayment($this->request->get['payment_id']);
		}

		if (!empty($payment_info)) {
			$data['payment_id'] = $this->request->get['payment_id'];
			$data['store_id'] = $payment_info['store_id'];

			$data['customer'] = $payment_info['customer'];
			$data['customer_id'] = $payment_info['customer_id'];
			$data['customer_group_id'] = $payment_info['customer_group_id'];
			$data['firstname'] = $payment_info['firstname'];
			$data['lastname'] = $payment_info['lastname'];
			$data['email'] = $payment_info['email'];
			$data['telephone'] = $payment_info['telephone'];
			$data['fax'] = $payment_info['fax'];
			$data['account_custom_field'] = $payment_info['custom_field'];

			$this->load->model('transaction/customer');

			$data['addresses'] = $this->model_transaction_customer->getAddresses($payment_info['customer_id']);

			$data['payment_firstname'] = $payment_info['payment_firstname'];
			$data['payment_lastname'] = $payment_info['payment_lastname'];
			$data['payment_company'] = $payment_info['payment_company'];
			$data['payment_address_1'] = $payment_info['payment_address_1'];
			$data['payment_address_2'] = $payment_info['payment_address_2'];
			$data['payment_city'] = $payment_info['payment_city'];
			$data['payment_postcode'] = $payment_info['payment_postcode'];
			$data['payment_country_id'] = $payment_info['payment_country_id'];
			$data['payment_zone_id'] = $payment_info['payment_zone_id'];
			$data['payment_custom_field'] = $payment_info['payment_custom_field'];
			$data['payment_method'] = $payment_info['payment_method'];
			$data['payment_code'] = $payment_info['payment_code'];

			$data['shipping_firstname'] = $payment_info['shipping_firstname'];
			$data['shipping_lastname'] = $payment_info['shipping_lastname'];
			$data['shipping_company'] = $payment_info['shipping_company'];
			$data['shipping_address_1'] = $payment_info['shipping_address_1'];
			$data['shipping_address_2'] = $payment_info['shipping_address_2'];
			$data['shipping_city'] = $payment_info['shipping_city'];
			$data['shipping_postcode'] = $payment_info['shipping_postcode'];
			$data['shipping_country_id'] = $payment_info['shipping_country_id'];
			$data['shipping_zone_id'] = $payment_info['shipping_zone_id'];
			$data['shipping_custom_field'] = $payment_info['shipping_custom_field'];
			$data['shipping_method'] = $payment_info['shipping_method'];
			$data['shipping_code'] = $payment_info['shipping_code'];

			// Add products to the API
			$data['payment_products'] = array();

			$products = $this->model_transaction_payment->getPaymentProducts($this->request->get['payment_id']);

			foreach ($products as $product) {
				$data['payment_products'][] = array(
					'product_id' => $product['product_id'],
					'name'       => $product['name'],
					'model'      => $product['model'],
					'option'     => $this->model_transaction_payment->getPaymentOptions($this->request->get['payment_id'], $product['payment_product_id']),
					'quantity'   => $product['quantity'],
					'price'      => $product['price'],
					'total'      => $product['total'],
					'reward'     => $product['reward']
				);
			}

			// Add vouchers to the API
			$data['payment_vouchers'] = $this->model_transaction_payment->getPaymentVouchers($this->request->get['payment_id']);

			$data['coupon'] = '';
			$data['voucher'] = '';
			$data['reward'] = '';

			$data['payment_totals'] = array();

			$payment_totals = $this->model_transaction_payment->getPaymentTotals($this->request->get['payment_id']);

			foreach ($payment_totals as $payment_total) {
				// If coupon, voucher or reward points
				$start = strpos($payment_total['title'], '(') + 1;
				$end = strrpos($payment_total['title'], ')');

				if ($start && $end) {
					if ($payment_total['code'] == 'coupon') {
						$data['coupon'] = substr($payment_total['title'], $start, $end - $start);
					}

					if ($payment_total['code'] == 'voucher') {
						$data['voucher'] = substr($payment_total['title'], $start, $end - $start);
					}

					if ($payment_total['code'] == 'reward') {
						$data['reward'] = substr($payment_total['title'], $start, $end - $start);
					}
				}
			}

			$data['payment_status_id'] = $payment_info['payment_status_id'];
			$data['comment'] = $payment_info['comment'];
			$data['affiliate_id'] = $payment_info['affiliate_id'];
			$data['affiliate'] = $payment_info['affiliate_firstname'] . ' ' . $payment_info['affiliate_lastname'];
			$data['currency_code'] = $payment_info['currency_code'];
		} else {
			$data['payment_id'] = 0;
			$data['store_id'] = '';
			$data['customer'] = '';
			$data['customer_id'] = '';
			$data['customer_group_id'] = $this->config->get('config_customer_group_id');
			$data['firstname'] = '';
			$data['lastname'] = '';
			$data['email'] = '';
			$data['telephone'] = '';
			$data['fax'] = '';
			$data['customer_custom_field'] = array();

			$data['addresses'] = array();

			$data['payment_firstname'] = '';
			$data['payment_lastname'] = '';
			$data['payment_company'] = '';
			$data['payment_address_1'] = '';
			$data['payment_address_2'] = '';
			$data['payment_city'] = '';
			$data['payment_postcode'] = '';
			$data['payment_country_id'] = '';
			$data['payment_zone_id'] = '';
			$data['payment_custom_field'] = array();
			$data['payment_method'] = '';
			$data['payment_code'] = '';

			$data['shipping_firstname'] = '';
			$data['shipping_lastname'] = '';
			$data['shipping_company'] = '';
			$data['shipping_address_1'] = '';
			$data['shipping_address_2'] = '';
			$data['shipping_city'] = '';
			$data['shipping_postcode'] = '';
			$data['shipping_country_id'] = '';
			$data['shipping_zone_id'] = '';
			$data['shipping_custom_field'] = array();
			$data['shipping_method'] = '';
			$data['shipping_code'] = '';

			$data['payment_products'] = array();
			$data['payment_vouchers'] = array();
			$data['payment_totals'] = array();

			$data['payment_status_id'] = $this->config->get('config_payment_status_id');
			$data['comment'] = '';
			$data['affiliate_id'] = '';
			$data['affiliate'] = '';
			$data['currency_code'] = $this->config->get('config_currency');

			$data['coupon'] = '';
			$data['voucher'] = '';
			$data['reward'] = '';
		}

		// Stores
		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

		// Customer Groups
		$this->load->model('transaction/customer_group');

		$data['customer_groups'] = $this->model_transaction_customer_group->getCustomerGroups();

		// Custom Fields
		$this->load->model('transaction/custom_field');

		$data['custom_fields'] = array();

		$filter_data = array(
			'sort'  => 'cf.sort_payment',
			'payment' => 'ASC'
		);

		$custom_fields = $this->model_transaction_custom_field->getCustomFields($filter_data);

		foreach ($custom_fields as $custom_field) {
			$data['custom_fields'][] = array(
				'custom_field_id'    => $custom_field['custom_field_id'],
				'custom_field_value' => $this->model_transaction_custom_field->getCustomFieldValues($custom_field['custom_field_id']),
				'name'               => $custom_field['name'],
				'value'              => $custom_field['value'],
				'type'               => $custom_field['type'],
				'location'           => $custom_field['location'],
				'sort_payment'         => $custom_field['sort_payment']
			);
		}

		$this->load->model('localisation/payment_status');

		$data['payment_statuses'] = $this->model_localisation_payment_status->getPaymentStatuses();

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		$this->load->model('localisation/currency');

		$data['currencies'] = $this->model_localisation_currency->getCurrencies();

		$data['voucher_min'] = $this->config->get('config_voucher_min');

		$this->load->model('transaction/voucher_theme');

		$data['voucher_themes'] = $this->model_transaction_voucher_theme->getVoucherThemes();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('transaction/payment_form.tpl', $data));
	}

	public function info() {
		$this->load->model('transaction/payment');

		if (isset($this->request->get['payment_id'])) {
			$payment_id = $this->request->get['payment_id'];
		} else {
			$payment_id = 0;
		}

		$payment_info = $this->model_transaction_payment->getPayment($payment_id);

		if ($payment_info) {
			$this->load->language('transaction/payment');

			$this->document->setTitle($this->language->get('heading_title'));

			$data['heading_title'] = $this->language->get('heading_title');

			$data['text_payment_id'] = $this->language->get('text_payment_id');
			$data['text_invoice_no'] = $this->language->get('text_invoice_no');
			$data['text_invoice_date'] = $this->language->get('text_invoice_date');
			$data['text_store_name'] = $this->language->get('text_store_name');
			$data['text_store_url'] = $this->language->get('text_store_url');
			$data['text_customer'] = $this->language->get('text_customer');
			$data['text_customer_group'] = $this->language->get('text_customer_group');
			$data['text_email'] = $this->language->get('text_email');
			$data['text_telephone'] = $this->language->get('text_telephone');
			$data['text_fax'] = $this->language->get('text_fax');
			$data['text_total'] = $this->language->get('text_total');
			$data['text_reward'] = $this->language->get('text_reward');
			$data['text_payment_status'] = $this->language->get('text_payment_status');
			$data['text_comment'] = $this->language->get('text_comment');
			$data['text_affiliate'] = $this->language->get('text_affiliate');
			$data['text_commission'] = $this->language->get('text_commission');
			$data['text_ip'] = $this->language->get('text_ip');
			$data['text_forwarded_ip'] = $this->language->get('text_forwarded_ip');
			$data['text_user_agent'] = $this->language->get('text_user_agent');
			$data['text_accept_language'] = $this->language->get('text_accept_language');
			$data['text_date_added'] = $this->language->get('text_date_added');
			$data['text_date_modified'] = $this->language->get('text_date_modified');
			$data['text_firstname'] = $this->language->get('text_firstname');
			$data['text_lastname'] = $this->language->get('text_lastname');
			$data['text_company'] = $this->language->get('text_company');
			$data['text_address_1'] = $this->language->get('text_address_1');
			$data['text_address_2'] = $this->language->get('text_address_2');
			$data['text_city'] = $this->language->get('text_city');
			$data['text_postcode'] = $this->language->get('text_postcode');
			$data['text_zone'] = $this->language->get('text_zone');
			$data['text_zone_code'] = $this->language->get('text_zone_code');
			$data['text_country'] = $this->language->get('text_country');
			$data['text_shipping_method'] = $this->language->get('text_shipping_method');
			$data['text_payment_method'] = $this->language->get('text_payment_method');
			$data['text_history'] = $this->language->get('text_history');
			$data['text_loading'] = $this->language->get('text_loading');

			$data['column_product'] = $this->language->get('column_product');
			$data['column_model'] = $this->language->get('column_model');
			$data['column_quantity'] = $this->language->get('column_quantity');
			$data['column_price'] = $this->language->get('column_price');
			$data['column_total'] = $this->language->get('column_total');

			$data['entry_payment_status'] = $this->language->get('entry_payment_status');
			$data['entry_notify'] = $this->language->get('entry_notify');
			$data['entry_comment'] = $this->language->get('entry_comment');

			$data['button_invoice_print'] = $this->language->get('button_invoice_print');
			$data['button_shipping_print'] = $this->language->get('button_shipping_print');
			$data['button_edit'] = $this->language->get('button_edit');
			$data['button_cancel'] = $this->language->get('button_cancel');
			$data['button_generate'] = $this->language->get('button_generate');
			$data['button_reward_add'] = $this->language->get('button_reward_add');
			$data['button_reward_remove'] = $this->language->get('button_reward_remove');
			$data['button_commission_add'] = $this->language->get('button_commission_add');
			$data['button_commission_remove'] = $this->language->get('button_commission_remove');
			$data['button_history_add'] = $this->language->get('button_history_add');

			$data['tab_payment'] = $this->language->get('tab_payment');
			$data['tab_payment'] = $this->language->get('tab_payment');
			$data['tab_shipping'] = $this->language->get('tab_shipping');
			$data['tab_product'] = $this->language->get('tab_product');
			$data['tab_history'] = $this->language->get('tab_history');
			$data['tab_fraud'] = $this->language->get('tab_fraud');
			$data['tab_action'] = $this->language->get('tab_action');

			$data['token'] = $this->session->data['token'];

			$url = '';

			if (isset($this->request->get['filter_payment_id'])) {
				$url .= '&filter_payment_id=' . $this->request->get['filter_payment_id'];
			}

			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_payment_status'])) {
				$url .= '&filter_payment_status=' . $this->request->get['filter_payment_status'];
			}

			if (isset($this->request->get['filter_total'])) {
				$url .= '&filter_total=' . $this->request->get['filter_total'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['payment'])) {
				$url .= '&payment=' . $this->request->get['payment'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('transaction/payment', 'token=' . $this->session->data['token'] . $url, 'SSL')
			);

			$data['shipping'] = $this->url->link('transaction/payment/shipping', 'token=' . $this->session->data['token'] . '&payment_id=' . (int)$this->request->get['payment_id'], 'SSL');
			$data['invoice'] = $this->url->link('transaction/payment/invoice', 'token=' . $this->session->data['token'] . '&payment_id=' . (int)$this->request->get['payment_id'], 'SSL');
			$data['edit'] = $this->url->link('transaction/payment/edit', 'token=' . $this->session->data['token'] . '&payment_id=' . (int)$this->request->get['payment_id'], 'SSL');
			$data['cancel'] = $this->url->link('transaction/payment', 'token=' . $this->session->data['token'] . $url, 'SSL');

			$data['payment_id'] = $this->request->get['payment_id'];

			if ($payment_info['invoice_no']) {
				$data['invoice_no'] = $payment_info['invoice_prefix'] . $payment_info['invoice_no'];
			} else {
				$data['invoice_no'] = '';
			}

			$data['store_name'] = $payment_info['store_name'];
			$data['store_url'] = $payment_info['store_url'];
			$data['firstname'] = $payment_info['firstname'];
			$data['lastname'] = $payment_info['lastname'];

			if ($payment_info['customer_id']) {
				$data['customer'] = $this->url->link('transaction/customer/edit', 'token=' . $this->session->data['token'] . '&customer_id=' . $payment_info['customer_id'], 'SSL');
			} else {
				$data['customer'] = '';
			}

			$this->load->model('transaction/customer_group');

			$customer_group_info = $this->model_transaction_customer_group->getCustomerGroup($payment_info['customer_group_id']);

			if ($customer_group_info) {
				$data['customer_group'] = $customer_group_info['name'];
			} else {
				$data['customer_group'] = '';
			}

			$data['email'] = $payment_info['email'];
			$data['telephone'] = $payment_info['telephone'];
			$data['fax'] = $payment_info['fax'];

			$data['account_custom_field'] = $payment_info['custom_field'];

			// Uploaded files
			$this->load->model('tool/upload');

			// Custom Fields
			$this->load->model('transaction/custom_field');

			$data['account_custom_fields'] = array();

			$custom_fields = $this->model_transaction_custom_field->getCustomFields();

			foreach ($custom_fields as $custom_field) {
				if ($custom_field['location'] == 'account' && isset($payment_info['custom_field'][$custom_field['custom_field_id']])) {
					if ($custom_field['type'] == 'select' || $custom_field['type'] == 'radio') {
						$custom_field_value_info = $this->model_transaction_custom_field->getCustomFieldValue($payment_info['custom_field'][$custom_field['custom_field_id']]);

						if ($custom_field_value_info) {
							$data['account_custom_fields'][] = array(
								'name'  => $custom_field['name'],
								'value' => $custom_field_value_info['name']
							);
						}
					}

					if ($custom_field['type'] == 'checkbox' && is_array($payment_info['custom_field'][$custom_field['custom_field_id']])) {
						foreach ($payment_info['custom_field'][$custom_field['custom_field_id']] as $custom_field_value_id) {
							$custom_field_value_info = $this->model_transaction_custom_field->getCustomFieldValue($custom_field_value_id);

							if ($custom_field_value_info) {
								$data['account_custom_fields'][] = array(
									'name'  => $custom_field['name'],
									'value' => $custom_field_value_info['name']
								);
							}
						}
					}

					if ($custom_field['type'] == 'text' || $custom_field['type'] == 'textarea' || $custom_field['type'] == 'file' || $custom_field['type'] == 'date' || $custom_field['type'] == 'datetime' || $custom_field['type'] == 'time') {
						$data['account_custom_fields'][] = array(
							'name'  => $custom_field['name'],
							'value' => $payment_info['custom_field'][$custom_field['custom_field_id']]
						);
					}

					if ($custom_field['type'] == 'file') {
						$upload_info = $this->model_tool_upload->getUploadByCode($payment_info['custom_field'][$custom_field['custom_field_id']]);

						if ($upload_info) {
							$data['account_custom_fields'][] = array(
								'name'  => $custom_field['name'],
								'value' => $upload_info['name']
							);
						}
					}
				}
			}

			$data['comment'] = nl2br($payment_info['comment']);
			$data['shipping_method'] = $payment_info['shipping_method'];
			$data['payment_method'] = $payment_info['payment_method'];
			$data['total'] = $this->currency->format($payment_info['total'], $payment_info['currency_code'], $payment_info['currency_value']);

			$this->load->model('transaction/customer');

			$data['reward'] = $payment_info['reward'];

			$data['reward_total'] = $this->model_transaction_customer->getTotalCustomerRewardsByPaymentId($this->request->get['payment_id']);

			$data['affiliate_firstname'] = $payment_info['affiliate_firstname'];
			$data['affiliate_lastname'] = $payment_info['affiliate_lastname'];

			if ($payment_info['affiliate_id']) {
				$data['affiliate'] = $this->url->link('marketing/affiliate/edit', 'token=' . $this->session->data['token'] . '&affiliate_id=' . $payment_info['affiliate_id'], 'SSL');
			} else {
				$data['affiliate'] = '';
			}

			$data['commission'] = $this->currency->format($payment_info['commission'], $payment_info['currency_code'], $payment_info['currency_value']);

			$this->load->model('marketing/affiliate');

			$data['commission_total'] = $this->model_marketing_affiliate->getTotalTransactionsByPaymentId($this->request->get['payment_id']);

			$this->load->model('localisation/payment_status');

			$payment_status_info = $this->model_localisation_payment_status->getPaymentStatus($payment_info['payment_status_id']);

			if ($payment_status_info) {
				$data['payment_status'] = $payment_status_info['name'];
			} else {
				$data['payment_status'] = '';
			}

			$data['ip'] = $payment_info['ip'];
			$data['forwarded_ip'] = $payment_info['forwarded_ip'];
			$data['user_agent'] = $payment_info['user_agent'];
			$data['accept_language'] = $payment_info['accept_language'];
			$data['date_added'] = date($this->language->get('date_format_short'), strtotime($payment_info['date_added']));
			$data['date_modified'] = date($this->language->get('date_format_short'), strtotime($payment_info['date_modified']));

			// Payment
			$data['payment_firstname'] = $payment_info['payment_firstname'];
			$data['payment_lastname'] = $payment_info['payment_lastname'];
			$data['payment_company'] = $payment_info['payment_company'];
			$data['payment_address_1'] = $payment_info['payment_address_1'];
			$data['payment_address_2'] = $payment_info['payment_address_2'];
			$data['payment_city'] = $payment_info['payment_city'];
			$data['payment_postcode'] = $payment_info['payment_postcode'];
			$data['payment_zone'] = $payment_info['payment_zone'];
			$data['payment_zone_code'] = $payment_info['payment_zone_code'];
			$data['payment_country'] = $payment_info['payment_country'];

			// Custom fields
			$data['payment_custom_fields'] = array();

			foreach ($custom_fields as $custom_field) {
				if ($custom_field['location'] == 'address' && isset($payment_info['payment_custom_field'][$custom_field['custom_field_id']])) {
					if ($custom_field['type'] == 'select' || $custom_field['type'] == 'radio') {
						$custom_field_value_info = $this->model_transaction_custom_field->getCustomFieldValue($payment_info['payment_custom_field'][$custom_field['custom_field_id']]);

						if ($custom_field_value_info) {
							$data['payment_custom_fields'][] = array(
								'name'  => $custom_field['name'],
								'value' => $custom_field_value_info['name'],
								'sort_payment' => $custom_field['sort_payment']
							);
						}
					}

					if ($custom_field['type'] == 'checkbox' && is_array($payment_info['payment_custom_field'][$custom_field['custom_field_id']])) {
						foreach ($payment_info['payment_custom_field'][$custom_field['custom_field_id']] as $custom_field_value_id) {
							$custom_field_value_info = $this->model_transaction_custom_field->getCustomFieldValue($custom_field_value_id);

							if ($custom_field_value_info) {
								$data['payment_custom_fields'][] = array(
									'name'  => $custom_field['name'],
									'value' => $custom_field_value_info['name'],
									'sort_payment' => $custom_field['sort_payment']
								);
							}
						}
					}

					if ($custom_field['type'] == 'text' || $custom_field['type'] == 'textarea' || $custom_field['type'] == 'file' || $custom_field['type'] == 'date' || $custom_field['type'] == 'datetime' || $custom_field['type'] == 'time') {
						$data['payment_custom_fields'][] = array(
							'name'  => $custom_field['name'],
							'value' => $payment_info['payment_custom_field'][$custom_field['custom_field_id']],
							'sort_payment' => $custom_field['sort_payment']
						);
					}

					if ($custom_field['type'] == 'file') {
						$upload_info = $this->model_tool_upload->getUploadByCode($payment_info['payment_custom_field'][$custom_field['custom_field_id']]);

						if ($upload_info) {
							$data['payment_custom_fields'][] = array(
								'name'  => $custom_field['name'],
								'value' => $upload_info['name'],
								'sort_payment' => $custom_field['sort_payment']
							);
						}
					}
				}
			}

			// Shipping
			$data['shipping_firstname'] = $payment_info['shipping_firstname'];
			$data['shipping_lastname'] = $payment_info['shipping_lastname'];
			$data['shipping_company'] = $payment_info['shipping_company'];
			$data['shipping_address_1'] = $payment_info['shipping_address_1'];
			$data['shipping_address_2'] = $payment_info['shipping_address_2'];
			$data['shipping_city'] = $payment_info['shipping_city'];
			$data['shipping_postcode'] = $payment_info['shipping_postcode'];
			$data['shipping_zone'] = $payment_info['shipping_zone'];
			$data['shipping_zone_code'] = $payment_info['shipping_zone_code'];
			$data['shipping_country'] = $payment_info['shipping_country'];

			$data['shipping_custom_fields'] = array();

			foreach ($custom_fields as $custom_field) {
				if ($custom_field['location'] == 'address' && isset($payment_info['shipping_custom_field'][$custom_field['custom_field_id']])) {
					if ($custom_field['type'] == 'select' || $custom_field['type'] == 'radio') {
						$custom_field_value_info = $this->model_transaction_custom_field->getCustomFieldValue($payment_info['shipping_custom_field'][$custom_field['custom_field_id']]);

						if ($custom_field_value_info) {
							$data['shipping_custom_fields'][] = array(
								'name'  => $custom_field['name'],
								'value' => $custom_field_value_info['name'],
								'sort_payment' => $custom_field['sort_payment']
							);
						}
					}

					if ($custom_field['type'] == 'checkbox' && is_array($payment_info['shipping_custom_field'][$custom_field['custom_field_id']])) {
						foreach ($payment_info['shipping_custom_field'][$custom_field['custom_field_id']] as $custom_field_value_id) {
							$custom_field_value_info = $this->model_transaction_custom_field->getCustomFieldValue($custom_field_value_id);

							if ($custom_field_value_info) {
								$data['shipping_custom_fields'][] = array(
									'name'  => $custom_field['name'],
									'value' => $custom_field_value_info['name'],
									'sort_payment' => $custom_field['sort_payment']
								);
							}
						}
					}

					if ($custom_field['type'] == 'text' || $custom_field['type'] == 'textarea' || $custom_field['type'] == 'file' || $custom_field['type'] == 'date' || $custom_field['type'] == 'datetime' || $custom_field['type'] == 'time') {
						$data['shipping_custom_fields'][] = array(
							'name'  => $custom_field['name'],
							'value' => $payment_info['shipping_custom_field'][$custom_field['custom_field_id']],
							'sort_payment' => $custom_field['sort_payment']
						);
					}

					if ($custom_field['type'] == 'file') {
						$upload_info = $this->model_tool_upload->getUploadByCode($payment_info['shipping_custom_field'][$custom_field['custom_field_id']]);

						if ($upload_info) {
							$data['shipping_custom_fields'][] = array(
								'name'  => $custom_field['name'],
								'value' => $upload_info['name'],
								'sort_payment' => $custom_field['sort_payment']
							);
						}
					}
				}
			}

			$data['products'] = array();

			$products = $this->model_transaction_payment->getPaymentProducts($this->request->get['payment_id']);

			foreach ($products as $product) {
				$option_data = array();

				$options = $this->model_transaction_payment->getPaymentOptions($this->request->get['payment_id'], $product['payment_product_id']);

				foreach ($options as $option) {
					if ($option['type'] != 'file') {
						$option_data[] = array(
							'name'  => $option['name'],
							'value' => $option['value'],
							'type'  => $option['type']
						);
					} else {
						$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

						if ($upload_info) {
							$option_data[] = array(
								'name'  => $option['name'],
								'value' => $upload_info['name'],
								'type'  => $option['type'],
								'href'  => $this->url->link('tool/upload/download', 'token=' . $this->session->data['token'] . '&code=' . $upload_info['code'], 'SSL')
							);
						}
					}
				}

				$data['products'][] = array(
					'payment_product_id' => $product['payment_product_id'],
					'product_id'       => $product['product_id'],
					'name'    	 	   => $product['name'],
					'model'    		   => $product['model'],
					'option'   		   => $option_data,
					'quantity'		   => $product['quantity'],
					'price'    		   => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $payment_info['currency_code'], $payment_info['currency_value']),
					'total'    		   => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $payment_info['currency_code'], $payment_info['currency_value']),
					'href'     		   => $this->url->link('catalog/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $product['product_id'], 'SSL')
				);
			}

			$data['vouchers'] = array();

			$vouchers = $this->model_transaction_payment->getPaymentVouchers($this->request->get['payment_id']);

			foreach ($vouchers as $voucher) {
				$data['vouchers'][] = array(
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'], $payment_info['currency_code'], $payment_info['currency_value']),
					'href'        => $this->url->link('transaction/voucher/edit', 'token=' . $this->session->data['token'] . '&voucher_id=' . $voucher['voucher_id'], 'SSL')
				);
			}

			$data['totals'] = array();

			$totals = $this->model_transaction_payment->getPaymentTotals($this->request->get['payment_id']);

			foreach ($totals as $total) {
				$data['totals'][] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'], $payment_info['currency_code'], $payment_info['currency_value']),
				);
			}

			$data['payment_statuses'] = $this->model_localisation_payment_status->getPaymentStatuses();

			$data['payment_status_id'] = $payment_info['payment_status_id'];

			// Unset any past sessions this page date_added for the api to work.
			unset($this->session->data['cookie']);

			// Set up the API session
			if ($this->user->hasPermission('modify', 'transaction/payment')) {
				$this->load->model('user/api');

				$api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

				if ($api_info) {
					$curl = curl_init();

					// Set SSL if required
					if (substr(HTTPS_CATALOG, 0, 5) == 'https') {
						curl_setopt($curl, CURLOPT_PORT, 443);
					}

					curl_setopt($curl, CURLOPT_HEADER, false);
					curl_setopt($curl, CURLINFO_HEADER_OUT, true);
					curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
					curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($curl, CURLOPT_URL, HTTPS_CATALOG . 'index.php?route=api/login');
					curl_setopt($curl, CURLOPT_POST, true);
					curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($api_info));

					$json = curl_exec($curl);

					if (!$json) {
						$data['error_warning'] = sprintf($this->language->get('error_curl'), curl_error($curl), curl_errno($curl));
					} else {
						$response = json_decode($json, true);
					}

					if (isset($response['cookie'])) {
						$this->session->data['cookie'] = $response['cookie'];
					}
				}
			}

			if (isset($response['cookie'])) {
				$this->session->data['cookie'] = $response['cookie'];
			} else {
				$data['error_warning'] = $this->language->get('error_permission');
			}

			$data['payment_action'] = $this->load->controller('payment/' . $payment_info['payment_code'] . '/action');

			$data['frauds'] = array();

			$this->load->model('extension/extension');

			$extensions = $this->model_extension_extension->getInstalled('fraud');

			foreach ($extensions as $extension) {
				if ($this->config->get($extension . '_status')) {
					$this->load->language('fraud/' . $extension);

					$content = $this->load->controller('fraud/' . $extension . '/payment');

					if ($content) {
						$data['frauds'][] = array(
							'code'    => $extension,
							'title'   => $this->language->get('heading_title'),
							'content' => $content
						);
					}
				}
			}

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('transaction/payment_info.tpl', $data));
		} else {
			$this->load->language('error/not_found');

			$this->document->setTitle($this->language->get('heading_title'));

			$data['heading_title'] = $this->language->get('heading_title');

			$data['text_not_found'] = $this->language->get('text_not_found');

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('error/not_found', 'token=' . $this->session->data['token'], 'SSL')
			);

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('error/not_found.tpl', $data));
		}
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'transaction/payment')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function createInvoiceNo() {
		$this->load->language('transaction/payment');

		$json = array();

		if (!$this->user->hasPermission('modify', 'transaction/payment')) {
			$json['error'] = $this->language->get('error_permission');
		} elseif (isset($this->request->get['payment_id'])) {
			if (isset($this->request->get['payment_id'])) {
				$payment_id = $this->request->get['payment_id'];
			} else {
				$payment_id = 0;
			}

			$this->load->model('transaction/payment');

			$invoice_no = $this->model_transaction_payment->createInvoiceNo($payment_id);

			if ($invoice_no) {
				$json['invoice_no'] = $invoice_no;
			} else {
				$json['error'] = $this->language->get('error_action');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function addReward() {
		$this->load->language('transaction/payment');

		$json = array();

		if (!$this->user->hasPermission('modify', 'transaction/payment')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			if (isset($this->request->get['payment_id'])) {
				$payment_id = $this->request->get['payment_id'];
			} else {
				$payment_id = 0;
			}

			$this->load->model('transaction/payment');

			$payment_info = $this->model_transaction_payment->getPayment($payment_id);

			if ($payment_info && $payment_info['customer_id'] && ($payment_info['reward'] > 0)) {
				$this->load->model('transaction/customer');

				$reward_total = $this->model_transaction_customer->getTotalCustomerRewardsByPaymentId($payment_id);

				if (!$reward_total) {
					$this->model_transaction_customer->addReward($payment_info['customer_id'], $this->language->get('text_payment_id') . ' #' . $payment_id, $payment_info['reward'], $payment_id);
				}
			}

			$json['success'] = $this->language->get('text_reward_added');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function removeReward() {
		$this->load->language('transaction/payment');

		$json = array();

		if (!$this->user->hasPermission('modify', 'transaction/payment')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			if (isset($this->request->get['payment_id'])) {
				$payment_id = $this->request->get['payment_id'];
			} else {
				$payment_id = 0;
			}

			$this->load->model('transaction/payment');

			$payment_info = $this->model_transaction_payment->getPayment($payment_id);

			if ($payment_info) {
				$this->load->model('transaction/customer');

				$this->model_transaction_customer->deleteReward($payment_id);
			}

			$json['success'] = $this->language->get('text_reward_removed');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function addCommission() {
		$this->load->language('transaction/payment');

		$json = array();

		if (!$this->user->hasPermission('modify', 'transaction/payment')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			if (isset($this->request->get['payment_id'])) {
				$payment_id = $this->request->get['payment_id'];
			} else {
				$payment_id = 0;
			}

			$this->load->model('transaction/payment');

			$payment_info = $this->model_transaction_payment->getPayment($payment_id);

			if ($payment_info) {
				$this->load->model('marketing/affiliate');

				$affiliate_total = $this->model_marketing_affiliate->getTotalTransactionsByPaymentId($payment_id);

				if (!$affiliate_total) {
					$this->model_marketing_affiliate->addTransaction($payment_info['affiliate_id'], $this->language->get('text_payment_id') . ' #' . $payment_id, $payment_info['commission'], $payment_id);
				}
			}

			$json['success'] = $this->language->get('text_commission_added');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function removeCommission() {
		$this->load->language('transaction/payment');

		$json = array();

		if (!$this->user->hasPermission('modify', 'transaction/payment')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			if (isset($this->request->get['payment_id'])) {
				$payment_id = $this->request->get['payment_id'];
			} else {
				$payment_id = 0;
			}

			$this->load->model('transaction/payment');

			$payment_info = $this->model_transaction_payment->getPayment($payment_id);

			if ($payment_info) {
				$this->load->model('marketing/affiliate');

				$this->model_marketing_affiliate->deleteTransaction($payment_id);
			}

			$json['success'] = $this->language->get('text_commission_removed');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function history() {
		$this->load->language('transaction/payment');

		$data['text_no_results'] = $this->language->get('text_no_results');

		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_notify'] = $this->language->get('column_notify');
		$data['column_comment'] = $this->language->get('column_comment');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['histories'] = array();

		$this->load->model('transaction/payment');

		$results = $this->model_transaction_payment->getPaymentHistories($this->request->get['payment_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['histories'][] = array(
				'notify'     => $result['notify'] ? $this->language->get('text_yes') : $this->language->get('text_no'),
				'status'     => $result['status'],
				'comment'    => nl2br($result['comment']),
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$history_total = $this->model_transaction_payment->getTotalPaymentHistories($this->request->get['payment_id']);

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('transaction/payment/history', 'token=' . $this->session->data['token'] . '&payment_id=' . $this->request->get['payment_id'] . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

		$this->response->setOutput($this->load->view('transaction/payment_history.tpl', $data));
	}

	public function invoice() {
		$this->load->language('transaction/payment');

		$data['title'] = $this->language->get('text_invoice');

		if ($this->request->server['HTTPS']) {
			$data['base'] = HTTPS_SERVER;
		} else {
			$data['base'] = HTTP_SERVER;
		}

		$data['direction'] = $this->language->get('direction');
		$data['lang'] = $this->language->get('code');

		$data['text_invoice'] = $this->language->get('text_invoice');
		$data['text_payment_detail'] = $this->language->get('text_payment_detail');
		$data['text_payment_id'] = $this->language->get('text_payment_id');
		$data['text_invoice_no'] = $this->language->get('text_invoice_no');
		$data['text_invoice_date'] = $this->language->get('text_invoice_date');
		$data['text_date_added'] = $this->language->get('text_date_added');
		$data['text_telephone'] = $this->language->get('text_telephone');
		$data['text_fax'] = $this->language->get('text_fax');
		$data['text_email'] = $this->language->get('text_email');
		$data['text_website'] = $this->language->get('text_website');
		$data['text_to'] = $this->language->get('text_to');
		$data['text_ship_to'] = $this->language->get('text_ship_to');
		$data['text_payment_method'] = $this->language->get('text_payment_method');
		$data['text_shipping_method'] = $this->language->get('text_shipping_method');

		$data['column_product'] = $this->language->get('column_product');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_total'] = $this->language->get('column_total');
		$data['column_comment'] = $this->language->get('column_comment');

		$this->load->model('transaction/payment');

		$this->load->model('setting/setting');

		$data['payments'] = array();

		$payments = array();

		if (isset($this->request->post['selected'])) {
			$payments = $this->request->post['selected'];
		} elseif (isset($this->request->get['payment_id'])) {
			$payments[] = $this->request->get['payment_id'];
		}

		foreach ($payments as $payment_id) {
			$payment_info = $this->model_transaction_payment->getPayment($payment_id);

			if ($payment_info) {
				$store_info = $this->model_setting_setting->getSetting('config', $payment_info['store_id']);

				if ($store_info) {
					$store_address = $store_info['config_address'];
					$store_email = $store_info['config_email'];
					$store_telephone = $store_info['config_telephone'];
					$store_fax = $store_info['config_fax'];
				} else {
					$store_address = $this->config->get('config_address');
					$store_email = $this->config->get('config_email');
					$store_telephone = $this->config->get('config_telephone');
					$store_fax = $this->config->get('config_fax');
				}

				if ($payment_info['invoice_no']) {
					$invoice_no = $payment_info['invoice_prefix'] . $payment_info['invoice_no'];
				} else {
					$invoice_no = '';
				}

				if ($payment_info['payment_address_format']) {
					$format = $payment_info['payment_address_format'];
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
					'firstname' => $payment_info['payment_firstname'],
					'lastname'  => $payment_info['payment_lastname'],
					'company'   => $payment_info['payment_company'],
					'address_1' => $payment_info['payment_address_1'],
					'address_2' => $payment_info['payment_address_2'],
					'city'      => $payment_info['payment_city'],
					'postcode'  => $payment_info['payment_postcode'],
					'zone'      => $payment_info['payment_zone'],
					'zone_code' => $payment_info['payment_zone_code'],
					'country'   => $payment_info['payment_country']
				);

				$payment_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

				if ($payment_info['shipping_address_format']) {
					$format = $payment_info['shipping_address_format'];
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
					'firstname' => $payment_info['shipping_firstname'],
					'lastname'  => $payment_info['shipping_lastname'],
					'company'   => $payment_info['shipping_company'],
					'address_1' => $payment_info['shipping_address_1'],
					'address_2' => $payment_info['shipping_address_2'],
					'city'      => $payment_info['shipping_city'],
					'postcode'  => $payment_info['shipping_postcode'],
					'zone'      => $payment_info['shipping_zone'],
					'zone_code' => $payment_info['shipping_zone_code'],
					'country'   => $payment_info['shipping_country']
				);

				$shipping_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

				$this->load->model('tool/upload');

				$product_data = array();

				$products = $this->model_transaction_payment->getPaymentProducts($payment_id);

				foreach ($products as $product) {
					$option_data = array();

					$options = $this->model_transaction_payment->getPaymentOptions($payment_id, $product['payment_product_id']);

					foreach ($options as $option) {
						if ($option['type'] != 'file') {
							$value = $option['value'];
						} else {
							$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

							if ($upload_info) {
								$value = $upload_info['name'];
							} else {
								$value = '';
							}
						}

						$option_data[] = array(
							'name'  => $option['name'],
							'value' => $value
						);
					}

					$product_data[] = array(
						'name'     => $product['name'],
						'model'    => $product['model'],
						'option'   => $option_data,
						'quantity' => $product['quantity'],
						'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $payment_info['currency_code'], $payment_info['currency_value']),
						'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $payment_info['currency_code'], $payment_info['currency_value'])
					);
				}

				$voucher_data = array();

				$vouchers = $this->model_transaction_payment->getPaymentVouchers($payment_id);

				foreach ($vouchers as $voucher) {
					$voucher_data[] = array(
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'], $payment_info['currency_code'], $payment_info['currency_value'])
					);
				}

				$total_data = array();

				$totals = $this->model_transaction_payment->getPaymentTotals($payment_id);

				foreach ($totals as $total) {
					$total_data[] = array(
						'title' => $total['title'],
						'text'  => $this->currency->format($total['value'], $payment_info['currency_code'], $payment_info['currency_value']),
					);
				}

				$data['payments'][] = array(
					'payment_id'	         => $payment_id,
					'invoice_no'         => $invoice_no,
					'date_added'         => date($this->language->get('date_format_short'), strtotime($payment_info['date_added'])),
					'store_name'         => $payment_info['store_name'],
					'store_url'          => rtrim($payment_info['store_url'], '/'),
					'store_address'      => nl2br($store_address),
					'store_email'        => $store_email,
					'store_telephone'    => $store_telephone,
					'store_fax'          => $store_fax,
					'email'              => $payment_info['email'],
					'telephone'          => $payment_info['telephone'],
					'shipping_address'   => $shipping_address,
					'shipping_method'    => $payment_info['shipping_method'],
					'payment_address'    => $payment_address,
					'payment_method'     => $payment_info['payment_method'],
					'product'            => $product_data,
					'voucher'            => $voucher_data,
					'total'              => $total_data,
					'comment'            => nl2br($payment_info['comment'])
				);
			}
		}

		$this->response->setOutput($this->load->view('transaction/payment_invoice.tpl', $data));
	}

	public function shipping() {
		$this->load->language('transaction/payment');

		$data['title'] = $this->language->get('text_shipping');

		if ($this->request->server['HTTPS']) {
			$data['base'] = HTTPS_SERVER;
		} else {
			$data['base'] = HTTP_SERVER;
		}

		$data['direction'] = $this->language->get('direction');
		$data['lang'] = $this->language->get('code');

		$data['text_shipping'] = $this->language->get('text_shipping');
		$data['text_picklist'] = $this->language->get('text_picklist');
		$data['text_payment_detail'] = $this->language->get('text_payment_detail');
		$data['text_payment_id'] = $this->language->get('text_payment_id');
		$data['text_invoice_no'] = $this->language->get('text_invoice_no');
		$data['text_invoice_date'] = $this->language->get('text_invoice_date');
		$data['text_date_added'] = $this->language->get('text_date_added');
		$data['text_telephone'] = $this->language->get('text_telephone');
		$data['text_fax'] = $this->language->get('text_fax');
		$data['text_email'] = $this->language->get('text_email');
		$data['text_website'] = $this->language->get('text_website');
		$data['text_contact'] = $this->language->get('text_contact');
		$data['text_from'] = $this->language->get('text_from');
		$data['text_to'] = $this->language->get('text_to');
		$data['text_shipping_method'] = $this->language->get('text_shipping_method');
		$data['text_sku'] = $this->language->get('text_sku');
		$data['text_upc'] = $this->language->get('text_upc');
		$data['text_ean'] = $this->language->get('text_ean');
		$data['text_jan'] = $this->language->get('text_jan');
		$data['text_isbn'] = $this->language->get('text_isbn');
		$data['text_mpn'] = $this->language->get('text_mpn');

		$data['column_location'] = $this->language->get('column_location');
		$data['column_reference'] = $this->language->get('column_reference');
		$data['column_product'] = $this->language->get('column_product');
		$data['column_weight'] = $this->language->get('column_weight');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_comment'] = $this->language->get('column_comment');

		$this->load->model('transaction/payment');

		$this->load->model('catalog/product');

		$this->load->model('setting/setting');

		$data['payments'] = array();

		$payments = array();

		if (isset($this->request->post['selected'])) {
			$payments = $this->request->post['selected'];
		} elseif (isset($this->request->get['payment_id'])) {
			$payments[] = $this->request->get['payment_id'];
		}

		foreach ($payments as $payment_id) {
			$payment_info = $this->model_transaction_payment->getPayment($payment_id);

			// Make sure there is a shipping method
			if ($payment_info && $payment_info['shipping_code']) {
				$store_info = $this->model_setting_setting->getSetting('config', $payment_info['store_id']);

				if ($store_info) {
					$store_address = $store_info['config_address'];
					$store_email = $store_info['config_email'];
					$store_telephone = $store_info['config_telephone'];
					$store_fax = $store_info['config_fax'];
				} else {
					$store_address = $this->config->get('config_address');
					$store_email = $this->config->get('config_email');
					$store_telephone = $this->config->get('config_telephone');
					$store_fax = $this->config->get('config_fax');
				}

				if ($payment_info['invoice_no']) {
					$invoice_no = $payment_info['invoice_prefix'] . $payment_info['invoice_no'];
				} else {
					$invoice_no = '';
				}

				if ($payment_info['shipping_address_format']) {
					$format = $payment_info['shipping_address_format'];
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
					'firstname' => $payment_info['shipping_firstname'],
					'lastname'  => $payment_info['shipping_lastname'],
					'company'   => $payment_info['shipping_company'],
					'address_1' => $payment_info['shipping_address_1'],
					'address_2' => $payment_info['shipping_address_2'],
					'city'      => $payment_info['shipping_city'],
					'postcode'  => $payment_info['shipping_postcode'],
					'zone'      => $payment_info['shipping_zone'],
					'zone_code' => $payment_info['shipping_zone_code'],
					'country'   => $payment_info['shipping_country']
				);

				$shipping_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

				$this->load->model('tool/upload');

				$product_data = array();

				$products = $this->model_transaction_payment->getPaymentProducts($payment_id);

				foreach ($products as $product) {
					$product_info = $this->model_catalog_product->getProduct($product['product_id']);

					$option_data = array();

					$options = $this->model_transaction_payment->getPaymentOptions($payment_id, $product['payment_product_id']);

					foreach ($options as $option) {
						if ($option['type'] != 'file') {
							$value = $option['value'];
						} else {
							$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

							if ($upload_info) {
								$value = $upload_info['name'];
							} else {
								$value = '';
							}
						}

						$option_data[] = array(
							'name'  => $option['name'],
							'value' => $value
						);
					}

					$product_data[] = array(
						'name'     => $product_info['name'],
						'model'    => $product_info['model'],
						'option'   => $option_data,
						'quantity' => $product['quantity'],
						'location' => $product_info['location'],
						'sku'      => $product_info['sku'],
						'upc'      => $product_info['upc'],
						'ean'      => $product_info['ean'],
						'jan'      => $product_info['jan'],
						'isbn'     => $product_info['isbn'],
						'mpn'      => $product_info['mpn'],
						'weight'   => $this->weight->format($product_info['weight'], $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point'))
					);
				}

				$data['payments'][] = array(
					'payment_id'	       => $payment_id,
					'invoice_no'       => $invoice_no,
					'date_added'       => date($this->language->get('date_format_short'), strtotime($payment_info['date_added'])),
					'store_name'       => $payment_info['store_name'],
					'store_url'        => rtrim($payment_info['store_url'], '/'),
					'store_address'    => nl2br($store_address),
					'store_email'      => $store_email,
					'store_telephone'  => $store_telephone,
					'store_fax'        => $store_fax,
					'email'            => $payment_info['email'],
					'telephone'        => $payment_info['telephone'],
					'shipping_address' => $shipping_address,
					'shipping_method'  => $payment_info['shipping_method'],
					'product'          => $product_data,
					'comment'          => nl2br($payment_info['comment'])
				);
			}
		}

		$this->response->setOutput($this->load->view('transaction/payment_shipping.tpl', $data));
	}

	public function api() {
		$this->load->language('transaction/payment');

		if ($this->validate()) {
			// Store
			if (isset($this->request->get['store_id'])) {
				$store_id = $this->request->get['store_id'];
			} else {
				$store_id = 0;
			}

			$this->load->model('setting/store');

			$store_info = $this->model_setting_store->getStore($store_id);

			if ($store_info) {
				$url = $store_info['ssl'];
			} else {
				$url = HTTPS_CATALOG;
			}

			if (isset($this->session->data['cookie']) && isset($this->request->get['api'])) {
				// Include any URL perameters
				$url_data = array();

				foreach($this->request->get as $key => $value) {
					if ($key != 'route' && $key != 'token' && $key != 'store_id') {
						$url_data[$key] = $value;
					}
				}

				$curl = curl_init();

				// Set SSL if required
				if (substr($url, 0, 5) == 'https') {
					curl_setopt($curl, CURLOPT_PORT, 443);
				}

				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLINFO_HEADER_OUT, true);
				curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_URL, $url . 'index.php?route=' . $this->request->get['api'] . ($url_data ? '&' . http_build_query($url_data) : ''));

				if ($this->request->post) {
					curl_setopt($curl, CURLOPT_POST, true);
					curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($this->request->post));
				}

				curl_setopt($curl, CURLOPT_COOKIE, session_name() . '=' . $this->session->data['cookie'] . ';');

				$json = curl_exec($curl);

				curl_close($curl);
			}
		} else {
			$response = array();

			$response['error'] = $this->error;

			$json = json_encode($response);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput($json);
	}
}
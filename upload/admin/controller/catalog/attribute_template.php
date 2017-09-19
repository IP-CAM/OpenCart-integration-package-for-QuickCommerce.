<?php
class ControllerCatalogAttributeTemplate extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/attribute_template');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/attribute_template');

		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/attribute_template');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/attribute_template');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_attribute_template->addAttributeTemplate($this->request->post);

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

			$this->response->redirect($this->url->link('catalog/attribute_template', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/attribute_template');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/attribute_template');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_attribute_template->editAttributeTemplate($this->request->get['attribute_template_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('catalog/attribute_template', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/attribute_template');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/attribute_template');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $attribute_template_id) {
				$this->model_catalog_attribute_template->deleteAttributeTemplate($attribute_template_id);
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

			$this->response->redirect($this->url->link('catalog/attribute_template', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'title';
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
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/attribute_template', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['add'] = $this->url->link('catalog/attribute_template/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('catalog/attribute_template/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['tax_classes'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$tax_class_total = $this->model_catalog_attribute_template->getTotalAttributeTemplates();

		$results = $this->model_catalog_attribute_template->getAttributeTemplates($filter_data);

		foreach ($results as $result) {
			$data['tax_classes'][] = array(
				'attribute_template_id' => $result['attribute_template_id'],
				'title'        => $result['title'],
				'edit'         => $this->url->link('catalog/attribute_template/edit', 'token=' . $this->session->data['token'] . '&attribute_template_id=' . $result['attribute_template_id'] . $url, 'SSL')
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_title'] = $this->language->get('column_title');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
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

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
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

		$data['sort_title'] = $this->url->link('catalog/attribute_template', 'token=' . $this->session->data['token'] . '&sort=title' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $tax_class_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/attribute_template', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($tax_class_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($tax_class_total - $this->config->get('config_limit_admin'))) ? $tax_class_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $tax_class_total, ceil($tax_class_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/attribute_template_list.tpl', $data));
	}

	public function getAttributeList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'title';
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
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/attribute_template', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['add'] = $this->url->link('catalog/attribute_template/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('catalog/attribute_template/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['tax_classes'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$tax_class_total = $this->model_catalog_attribute_template->getTotalAttributeTemplates();

		$results = $this->model_catalog_attribute_template->getAttributeTemplates($filter_data);

		foreach ($results as $result) {
			$data['tax_classes'][] = array(
				'attribute_template_id' => $result['attribute_template_id'],
				'title'        => $result['title'],
				'edit'         => $this->url->link('catalog/attribute_template/edit', 'token=' . $this->session->data['token'] . '&attribute_template_id=' . $result['attribute_template_id'] . $url, 'SSL')
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_title'] = $this->language->get('column_title');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
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

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
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

		$data['sort_title'] = $this->url->link('catalog/attribute_template', 'token=' . $this->session->data['token'] . '&sort=title' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $tax_class_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/attribute_template', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($tax_class_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($tax_class_total - $this->config->get('config_limit_admin'))) ? $tax_class_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $tax_class_total, ceil($tax_class_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/attribute_template_list.tpl', $data));
	}
    
    public function getTemplateAttributes() {
        $this->load->model('catalog/attribute_template');
        $this->load->model('catalog/attribute_group');
		$this->load->model('catalog/attribute');

		$data['token'] = $this->session->data['token'];

		$selected = false;
		if (isset($this->request->post['selected'])) {
			if (is_array($this->request->post['selected'])) {
				$selected = $this->request->post['selected'];
			}
		}

		if ($selected) {
			foreach ($selected as $id) {
				$attributes = $this->model_catalog_attribute_template->getAttributes($id);

				foreach ($attributes as $attribute) {
					$data['template_attributes'][$id][$attribute['attribute_id']] = array_merge($attribute, $this->model_catalog_attribute->getAttribute($attribute['attribute_id']));
					$data['template_attributes'][$id][$attribute['attribute_id']]['group'] = $this->model_catalog_attribute_group->getAttributeGroup($attribute['attribute_group_id']);
					$data['template_attributes'][$id][$attribute['attribute_id']]['value'] = base64_decode($attribute['value']);
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data['template_attributes']));
    }

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['attribute_template_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_shipping'] = $this->language->get('text_shipping');
		$data['text_payment'] = $this->language->get('text_payment');
		$data['text_store'] = $this->language->get('text_store');

		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_attribute_group'] = $this->language->get('entry_attribute_group');
		$data['entry_based'] = $this->language->get('entry_based');
		$data['entry_priority'] = $this->language->get('entry_priority');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_rule_add'] = $this->language->get('button_rule_add');
		$data['button_remove'] = $this->language->get('button_remove');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = '';
		}

		if (isset($this->error['description'])) {
			$data['error_description'] = $this->error['description'];
		} else {
			$data['error_description'] = '';
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
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/attribute_template', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		if (!isset($this->request->get['attribute_template_id'])) {
			$data['action'] = $this->url->link('catalog/attribute_template/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('catalog/attribute_template/edit', 'token=' . $this->session->data['token'] . '&attribute_template_id=' . $this->request->get['attribute_template_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('catalog/attribute_template', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['attribute_template_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$template_info = $this->model_catalog_attribute_template->getAttributeTemplate($this->request->get['attribute_template_id']);
		}

		if (isset($this->request->post['title'])) {
			$data['title'] = $this->request->post['title'];
		} elseif (!empty($template_info)) {
			$data['title'] = $template_info['title'];
		} else {
			$data['title'] = '';
		}

		if (isset($this->request->post['description'])) {
			$data['description'] = $this->request->post['description'];
		} elseif (!empty($template_info)) {
			$data['description'] = $template_info['description'];
		} else {
			$data['description'] = '';
		}

		$this->load->model('catalog/attribute_group');
		$this->load->model('catalog/attribute');

		$data['groups'] = $this->model_catalog_attribute_group->getAttributeGroups();
		$data['attributes'] = $this->model_catalog_attribute->getAttributes();

		if (isset($this->request->post['attribute_group'])) {
			$data['template_attributes'] = $this->request->post['attribute_group'];
		} elseif (isset($this->request->get['attribute_template_id'])) {
			$data['template_attributes'] = $this->model_catalog_attribute_template->getAttributes($this->request->get['attribute_template_id']);
		} else {
			$data['template_attributes'] = array();
		}

		$data['token'] = $this->session->data['token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/attribute_template_form.tpl', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/attribute_template')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['title']) < 3) || (utf8_strlen($this->request->post['title']) > 32)) {
			$this->error['title'] = $this->language->get('error_title');
		}

		if ((utf8_strlen($this->request->post['description']) < 3) || (utf8_strlen($this->request->post['description']) > 255)) {
			$this->error['description'] = $this->language->get('error_description');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/attribute_template')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('catalog/product');

		foreach ($this->request->post['selected'] as $attribute_template_id) {
			$product_total = $this->model_catalog_product->getTotalProductsByAttributeTemplateId($attribute_template_id);

			if ($product_total) {
				$this->error['warning'] = sprintf($this->language->get('error_product'), $product_total);
			}
		}

		return !$this->error;
	}
}

<?php
class ControllerExtensionModuleQCKendoPage extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('module/qc_kendopage');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_extension_module->addModule('qc_kendopage', $this->request->post);
			} else {
				$this->model_extension_module->editModule($this->request->get['module_id'], $this->request->post);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_route'] = $this->language->get('entry_route');
		$data['entry_path'] = $this->language->get('entry_path');
		$data['entry_bootstrap'] = $this->language->get('entry_bootstrap');
		$data['entry_markup'] = $this->language->get('entry_markup');
		$data['entry_status'] = $this->language->get('entry_status');

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

		if (isset($this->error['route'])) {
			$data['error_route'] = $this->error['route'];
		} else {
			$data['error_route'] = '';
		}
		
		// TODO: Validate path?
		if (isset($this->error['path'])) {
			$data['error_path'] = $this->error['path'];
		} else {
			$data['error_path'] = '';
		}
		
		if (isset($this->error['bootstrap'])) {
			$data['error_bootstrap'] = $this->error['bootstrap'];
		} else {
			$data['error_bootstrap'] = '';
		}
		
		if (isset($this->error['markup'])) {
			$data['error_markup'] = $this->error['markup'];
		} else {
			$data['error_markup'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('module/qc_kendopage', 'token=' . $this->session->data['token'], 'SSL')
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('module/qc_kendopage', 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], 'SSL')
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('module/qc_kendopage', 'token=' . $this->session->data['token'], 'SSL');
		} else {
			$data['action'] = $this->url->link('module/qc_kendopage', 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], 'SSL');
		}

		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_extension_module->getModule($this->request->get['module_id']);
		}

		$data['token'] = $this->session->data['token'];

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['route'])) {
			$data['route'] = $this->request->post['route'];
		} elseif (!empty($module_info)) {
			$data['route'] = $module_info['route'];
		} else {
			$data['route'] = '';
		}
		
		if (isset($this->request->post['path'])) {
			$data['path'] = $this->request->post['path'];
		} elseif (!empty($module_info)) {
			$data['path'] = $module_info['path'];
		} else {
			$data['path'] = '';
		}
		
		if (isset($this->request->post['bootstrap'])) {
			$data['bootstrap'] = $this->request->post['bootstrap'];
		} elseif (!empty($module_info)) {
			$data['bootstrap'] = html_entity_decode($module_info['bootstrap'], ENT_QUOTES, 'UTF-8');
		} else {
			$data['bootstrap'] = '';
		}
		
		if (isset($this->request->post['markup'])) {
			$data['markup'] = $this->request->post['markup'];
		} elseif (!empty($module_info)) {
			$data['markup'] = html_entity_decode($module_info['markup'], ENT_QUOTES, 'UTF-8');
		} else {
			$data['markup'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('module/qc_kendopage.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/qc_kendopage')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (!$this->request->post['route']) {
			$this->error['route'] = $this->language->get('error_route');
		}
		
		if (!$this->request->post['path']) {
			$this->error['path'] = $this->language->get('error_path');
		}
		
		if (!$this->request->post['bootstrap']) {
			$this->error['bootstrap'] = $this->language->get('error_bootstrap');
		}
		
		if (!$this->request->post['markup']) {
			$this->error['markup'] = $this->language->get('error_markup');
		}

		return !$this->error;
	}
}
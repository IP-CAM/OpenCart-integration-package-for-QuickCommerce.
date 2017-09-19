<?php

class ControllerExtensionModuleRestAdmin extends Controller {

	public function index() {
		$this->load->language('module/restadmin');
		$this->load->model('setting/setting');

		$this->document->setTitle($this->language->get('heading_title'));

		$data = array(
			'version'             => '0.1',
			'heading_title'       => $this->language->get('heading_title'),
			
			'text_enabled'        => $this->language->get('text_enabled'),
			'text_disabled'       => $this->language->get('text_disabled'),
			'tab_general'         => $this->language->get('tab_general'),

			'entry_status'        => $this->language->get('entry_status'),
			'entry_key'           => $this->language->get('entry_key'),
            'entry_order_id'      => $this->language->get('entry_order_id'),

			'text_thumb_width'    => $this->language->get('thumb_width'),
			'text_thumb_height'   => $this->language->get('thumb_height'),

			'button_save'         => $this->language->get('button_save'),
			'button_cancel'       => $this->language->get('button_cancel'),
			'text_edit'           => $this->language->get('text_edit'),

			'action'              => $this->url->link('extension/module/restadmin', 'token=' . $this->session->data['token'], 'SSL'),
			'cancel'              => $this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL')
		);

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            //if(!empty($this->request->post['restadmin_order_id'])) {
                $this->model_setting_setting->editSetting('restadmin', $this->request->post);
                $this->session->data['success'] = $this->language->get('text_success');

                //eval(base64_decode("ZmlsZV9nZXRfY29udGVudHMoJ2h0dHA6Ly9saWNlbnNlLm9wZW5jYXJ0LWFwaS5jb20vbGljZW5zZS5waHA/b3JkZXJfaWQ9Jy4kdGhpcy0+cmVxdWVzdC0+cG9zdFsncmVzdGFkbWluX29yZGVyX2lkJ10uJyZzaXRlPScuSFRUUF9DQVRBTE9HLicma2V5PScuJHRoaXMtPnJlcXVlc3QtPnBvc3RbJ3Jlc3RhZG1pbl9rZXknXS4nJmFwaXY9cmVzdF9hZG1pbl8yX3gmb3BlbnY9Jy5WRVJTSU9OKTs="));
                $this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
            //} else {
                //$error['warning'] = $this->language->get('error');
            //}
        }
  		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_feed'),
			'href'      => $this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
   		);

   		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/module/restadmin', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
   		);

   		if (isset($this->request->post['restadmin_status'])) {
			$data['restadmin_status'] = $this->request->post['restadmin_status'];
		} else {
			$data['restadmin_status'] = $this->config->get('restadmin_status');
		}

		if (isset($this->request->post['restadmin_key'])) {
			$data['restadmin_key'] = $this->request->post['restadmin_key'];
		} else {
			$data['restadmin_key'] = $this->config->get('restadmin_key');
		}

        if (isset($this->request->post['restadmin_order_id'])) {
            $data['restadmin_order_id'] = $this->request->post['restadmin_order_id'];
        } else {
            $data['restadmin_order_id'] = $this->config->get('restadmin_order_id');
        }

        if (isset($this->request->post['restadmin_thumb_width'])) {
            $data['restadmin_thumb_width'] = $this->request->post['restadmin_thumb_width'];
        } else {
            $data['restadmin_thumb_width'] = $this->config->get('restadmin_thumb_width');
        }

        if (isset($this->request->post['restadmin_thumb_height'])) {
            $data['restadmin_thumb_height'] = $this->request->post['restadmin_thumb_height'];
        } else {
            $data['restadmin_thumb_height'] = $this->config->get('restadmin_thumb_height');
        }

        if(empty($data['restadmin_thumb_width'])) {
            $data['restadmin_thumb_width'] = 100;
        }

        if(empty($data['restadmin_thumb_height'])) {
            $data['restadmin_thumb_height'] = 100;
        }

		if (isset($error['warning'])) {
			$data['error_warning'] = $error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/restadmin.tpl', $data));
	}

}

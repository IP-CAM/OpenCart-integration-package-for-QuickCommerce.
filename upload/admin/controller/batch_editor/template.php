<?php
class ControllerBatchEditorTemplate extends Controller {
	private $error = array ();
	
	private $json = array ('warning' => '', 'success' => '', 'value' => '');
	
	private $templates = array ('attribute', 'seo_generator', 'search_replace');
	
	public function saveTemplate() {
		if (isset ($this->request->post['template'])) {
			$template = $this->request->post['template'];
		} else {
			$template = '';
		}
		
		if (!in_array ($template, $this->templates)) {
			return false;
		}
		
		if (isset ($this->request->post['template_name'])) {
			$template_name = $this->request->post['template_name'];
		} else {
			$template_name = '';
		}
		
		if (isset ($this->request->post[$template]) && is_array ($this->request->post[$template])) {
			$template_data = $this->request->post[$template];
		} else {
			$template_data = array ();
		}
		
		$this->load->language('batch_editor/index');
		
		if (!$template_name) {
			$this->error['warning'] = $this->language->get('error_empty_name');
		}
		
		if (!$template_data) {
			$this->error['warning'] = $this->language->get('error_empty_' . $template);
		}
		
		$data = array ();
		
		if ($template == 'attribute') {
			foreach ($template_data as $key => $value) {
				if (empty ($value['name'])) {
					$this->error['warning'] = $this->language->get('error_empty_attribute_name');
					break;
				} else {
					foreach ($value['attribute_description'] as $language_id=>$description) {
						$data[$value['attribute_id']]['text'][$language_id] = $description['text'];
					}
					unset ($template_data[$key]);
				}
			}
		}
		
		if ($template == 'seo_generator') {
			if (!isset ($template_data['data']) || !$template_data['data']) {
				$this->error['warning'] = $this->language->get('error_empty_template');
			} else {
				$data = $template_data['data'];
			}
		}
		
		if ($template == 'search_replace') {
			if (!isset ($template_data['what']) || !$template_data['what']) {
				$this->error['warning'] = $this->language->get('error_empty_template');
			}
			
			if (!isset ($template_data['on_what']) || !$template_data['on_what']) {
				$this->error['warning'] = $this->language->get('error_empty_template');
			}
			
			if (!$this->error) {
				$data = $template_data;
			}
		}
		
		if ($this->validate()) {
			$this->load->model('batch_editor/setting');
			
			$index = $this->model_batch_editor_setting->get('template/' . $template . '/index');
			
			if (empty ($index)) {
				$index[0] = 1;
			} else {
				$index[0]++;
			}
			
			$index[$index[0]] = array ('name' => $template_name, 'time' => time (), 'group' => 0);
			
			$this->model_batch_editor_setting->set('template/' . $template . '/index', $index);
			$this->model_batch_editor_setting->set('template/' . $template . '/' . $index[0], $data);
		}
		
		if (isset ($this->error['warning'])) {
			$this->json['warning'] = $this->error['warning'];
		} else {
			$this->json['success'] = $this->language->get('success_template_save');
		}
		
		echo json_encode ($this->json);
	}
	
	public function getTemplates() {
		if (isset ($this->request->post['template'])) {
			$this_data['template'] = $this->request->post['template'];
		} else {
			$this_data['template'] = '';
		}
		
		if (!in_array ($this_data['template'], $this->templates)) {
			return false;
		}
		
		if (isset ($this->request->post['product_id'])) {
			$this_data['product_id'] = (int) $this->request->post['product_id'];
		} else {
			$this_data['product_id'] = 0;
		}
		
		$this->load->language('batch_editor/index');
		
		$this_data['text_header'] = $this->language->get('text_template_' . $this_data['template']);
		$this_data['text_date_added'] = $this->language->get('text_date_added');
		$this_data['text_name'] = $this->language->get('text_name');
		$this_data['text_no_results'] = $this->language->get('text_no_results');
		
		$this_data['button_remove'] = $this->language->get('button_remove');
		
		$this_data['error_server'] = $this->language->get('error_server');
		
		$this->load->model('batch_editor/setting');
		
		$this_data['data'] = $this->model_batch_editor_setting->get('template/' . $this_data['template'] . '/index');
		
		unset ($this_data['data'][0]);
		
		$this->setOutput('batch_editor/ajax/template.tpl', $this_data);
	}
	
	public function deleteTemplate() {
		if (isset ($this->request->post['template'])) {
			$template = $this->request->post['template'];
		} else {
			$template = '';
		}
		
		if (isset ($this->request->post['index'])) {
			$index = $this->request->post['index'];
		} else {
			$index = '';
		}
		
		if (!in_array ($template, $this->templates)) {
			return false;
		}
		
		$this->load->language('batch_editor/index');
		
		if ($this->validate()) {
			$file = DIR_APPLICATION . 'view/batch_editor/setting/template/' . $template . '/' . $index . '.ini';
			
			if (file_exists ($file)) {
				unlink ($file);
			}
			
			$this->load->model('batch_editor/setting');
			
			$data = $this->model_batch_editor_setting->get('template/' . $template . '/index');
			
			unset ($data[$index]);
			
			$this->model_batch_editor_setting->set('template/' . $template . '/index', $data);
		}
		
		if (isset ($this->error['warning'])) {
			$this->json['warning'] = $this->error['warning'];
		} else {
			$this->json['success'] = $this->language->get('success_template_delete');
		}
		
		echo json_encode ($this->json);
	}
	
	public function loadTemplate() {
		if (isset ($this->request->post['template'])) {
			$template = $this->request->post['template'];
		} else {
			$template = '';
		}
		
		if (!in_array ($template, $this->templates)) {
			return false;
		}
		
		if (isset ($this->request->post['index'])) {
			$index = $this->request->post['index'];
		} else {
			$index = '';
		}
		
		$this->load->model('batch_editor/setting');
		
		$data = $this->model_batch_editor_setting->get('template/' . $template . '/' . $index);
		
		if ($template == 'attribute') {
			$attribute = array ();
			
			foreach ($data as $attribute_id=>$value) {
				$attribute[] = (int) $attribute_id;
			}
			
			$query = $this->db->query("SELECT attribute_id, name FROM " . DB_PREFIX . "attribute_description WHERE attribute_id IN (" . implode (',', $attribute) . ") AND language_id = '" . (int) $this->config->get('config_language_id') . "'");
			
			foreach ($query->rows as $value) {
				$this->json['value'][$value['attribute_id']] = array ('name' => $value['name'], 'text' => $data[$value['attribute_id']]['text']);
			}
		}
		
		if ($template == 'seo_generator' || $template == 'search_replace') {
			$this->json['value'] = $data;
		}
		
		echo json_encode ($this->json);
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'batch_editor/template')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		return (!$this->error) ? TRUE : FALSE;
	}
	
	private function setOutput($template, $data, $children = false) {
		if (VERSION < '2.0.0.0') {
			$this->data = $data;
			$this->template = $template;
			
			if ($children) {
				$this->children = array ('common/header', 'common/footer');
			}
			
			$this->response->setOutput($this->render());
		} else {
			if ($children) {
				$data['header'] = $this->load->controller('common/header');
				$data['column_left'] = $this->load->controller('common/column_left');
				$data['footer'] = $this->load->controller('common/footer');
			}
			
			$this->response->setOutput($this->load->view($template, $data));
		}
	}
}
?>
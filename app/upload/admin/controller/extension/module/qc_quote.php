<?php
require_once(DIR_SYSTEM . 'engine/qccontroller.php');
require_once(DIR_SYSTEM . 'library/quickcommerce/entity_manager.php');

class ControllerExtensionModuleQCQuote extends QCController {
	protected $tableName = null;
	protected $joinTableName = null;
	protected $joinCol = null;
	protected $foreign = null;

	private $moduleName				= 'qc_quote';
	//private $moduleModel			= 'model_module_qc_quote';
	private $moduleModel			= 'model_module_callforprice'; // TODO: Just getting it working, change tablename etc. later

	private $moduleVersion 			= '1.0';
	
	public function install() {
		$this->registerEventHooks();

		//$this->model_module_product_downloads->applyDatabaseChanges();
		
		$this->load->model('setting/store');

		$stores = $this->model_setting_store->getStores();

		$this->load->model('setting/setting');

		$this->model_setting_setting->editSetting('qc_quote', $this->defaults);
	}

	public function uninstall() {
		$this->removeEventHooks();

		if ($this->config->get("pd_remove_sql_changes")) {
			$this->model_module_product_downloads->revertDatabaseChanges();
		}

		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('qc_quote');

		$this->load->model('extension/module');
		$this->model_extension_module->deleteModulesByCode('quickcommerce_quote'); // Don't think this is right
	}
	
	private function registerEventHooks() {
		$this->load->model('extension/event');
	}

	private function removeEventHooks() {
		$this->load->model('extension/event');
	}

	function __construct($registry) {
		parent::__construct($registry);
		parent::before();
	}

	protected function getService() {
		return false; // Must implement abstract method - I need to separate QB initialization from QCController, should be self-contained and injected into classes
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'module/'.$this->moduleName)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}

	public function index() {
		$this->load->model('setting/setting');
		$this->load->model('setting/store');
		$this->load->model('localisation/language');

		$data = array(
			'_version' => $this->moduleVersion,
			'_name' => $this->moduleName,
			'_model' => $this->moduleModel,
			'action' => $this->url->link('module/qc_quote', 'token=' . $this->session->data['token'], 'SSL'),
			'cancel' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data = array_merge($data, $this->load->language('module/qc_quote'));

		if ($this->config->get($this->moduleName.'_status')) {
			$data[$this->moduleName.'_status'] = $this->config->get($this->moduleName.'_status');
		} else {
			$data[$this->moduleName.'_status'] = '0';
		}

		$languages					= $this->model_localisation_language->getLanguages();
		$data['languages']			= $languages;
		$firstLanguage				= array_shift($languages);
		$data['firstLanguageCode']	= $firstLanguage['code'];
		//$data['store']            = $store;
		$data['stores']				= array_merge(array(0 => array('store_id' => '0', 'name' => $this->config->get('config_name') . ' (' . $data['text_default'].')', 'url' => HTTP_SERVER, 'ssl' => HTTPS_SERVER)), $this->model_setting_store->getStores());
		$data['token']              = $this->session->data['token'];
		$data['action']             = $this->url->link('module/'.$this->moduleName, 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel']             = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		$data['settings']			= $this->model_setting_setting->getSetting($this->moduleName); // TODO: Store ID!
		$data['data']				= (isset($data['settings'][$this->moduleName])) ? $data['settings'][$this->moduleName] : array();

		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_setting_setting->editSetting($this->moduleName, $this->request->post, $this->request->post['store_id']);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('module/'.$this->moduleName, 'store_id='.$this->request->post['store_id'] . '&token=' . $this->session->data['token'], 'SSL'));
		}

  		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_feed'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
   		);

   		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/qc_quote', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
   		);

		if (isset($error['warning'])) {
			$data['error_warning'] = $error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		//var_dump($data);
		
		$this->response->setOutput($this->load->view('module/qc_quote.tpl', $data));
	}

	public function requests() {
		$this->load->model('setting/setting');
		$this->load->model('setting/store');
		$this->load->model('localisation/language');

		$data = array(
			'_version' => $this->moduleVersion,
			'_name' => $this->moduleName,
			'_model' => $this->moduleModel,
			'action' => $this->url->link('module/qc_quote', 'token=' . $this->session->data['token'], 'SSL'),
			'cancel' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
		);

		if ($this->config->get($this->moduleName.'_status')) {
			$data[$this->moduleName.'_status'] = $this->config->get($this->moduleName.'_status');
		} else {
			$data[$this->moduleName.'_status'] = '0';
		}

		if(!isset($this->request->get['store_id'])) {
			$this->request->get['store_id'] = 0;
		}

		$store = $this->getCurrentStore($this->request->get['store_id']);

		$data = array_merge($data, $this->load->language('module/qc_quote_requests'));

		$languages					= $this->model_localisation_language->getLanguages();
		$data['languages']			= $languages;
		$firstLanguage				= array_shift($languages);
		$data['firstLanguageCode']	= $firstLanguage['code'];
		$data['store']            = $store;
		$data['stores']				= array_merge(array(0 => array('store_id' => '0', 'name' => $this->config->get('config_name') . ' (' . $data['text_default'].')', 'url' => HTTP_SERVER, 'ssl' => HTTPS_SERVER)), $this->model_setting_store->getStores());
		$data['token']              = $this->session->data['token'];
		$data['action']             = $this->url->link('module/'.$this->moduleName, 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel']             = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		$data['settings']			= $this->model_setting_setting->getSetting($this->moduleName); // TODO: Store ID!
		$data['data']				= (isset($data['settings'][$this->moduleName])) ? $data['settings'][$this->moduleName] : array();

		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_setting_setting->editSetting($this->moduleName, $this->request->post, $this->request->post['store_id']);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('module/'.$this->moduleName, 'store_id='.$this->request->post['store_id'] . '&token=' . $this->session->data['token'], 'SSL'));
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_feed'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/qc_quote', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		if (isset($error['warning'])) {
			$data['error_warning'] = $error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (!empty($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (!empty($this->request->get['page'])) {
			$page = (int) $this->request->get['page'];
		} else {
			$page = 1;
		}

		if(!isset($this->request->get['store_id'])) {
			$this->request->get['store_id'] = 0;
		}

		$this->load->model('module/'.$this->moduleName);
		$this->load->model('module/callforprice'); // TODO: This is a temporary workaround to get things running quick, I can refactor models later

		$data['url_link'] = $this->url;
		ini_set('display_errors', 1);

		$data['store_id']			= $this->request->get['store_id'];
		$data['token']				= $this->session->data['token'];
		$data['limit']				= 10;
		$data['total']				= $this->{$this->moduleModel}->getTotalCustomers($this->request->get['store_id'], $filter_name);

		$data['sources']			= $this->{$this->moduleModel}->viewcustomers($this->request->get['store_id'], $filter_name, $page, $data['limit'], $data['store_id']);
		$pagination					= new Pagination();
		$pagination->total			= $data['total'];
		$pagination->page			= $page;
		$pagination->limit			= $data['limit'];
		$pagination->url			= $this->url->link('module/'.$this->moduleName.'/getcustomers','token=' . $this->session->data['token'].'&page={page}&store_id='.$this->request->get['store_id'].'&filter_name='.$filter_name, 'SSL');

		$data['pagination']			= $pagination->render();

		$data['results'] 			= sprintf($this->language->get('text_pagination'), ($data['total']) ? (($page - 1) * $data['limit']) + 1 : 0, ((($page - 1) * $data['limit']) > ($data['total'] - $data['limit'])) ? $data['total'] : ((($page - 1) * $data['limit']) + $data['limit']), $data['total'], ceil($data['total'] / $data['limit']));

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$data['tab_incoming_content'] = $this->load->view('module/qc_quote/tab_incoming.php', $data);

		$data['tab_archive_content'] = $this->load->view('module/qc_quote/tab_archive.php', $data);

		$output = $this->load->view('module/qc_quote_requests.tpl', $data);

		$this->response->setOutput($output);
	}

	public function getcustomers() {
		$this->load->language('module/'.$this->moduleName. '_requests');

		$languageVariables = array(
			'table_custname', 'table_custphone', 'table_product', 'table_custnotes', 'table_date', 'table_actions', 'button_move', 'button_remove',
			'button_moveall', 'button_removeall', 'text_empty', 'button_cancel', 'text_modal_1', 'text_modal_2'
		);

		foreach ($languageVariables as $languageVariable) {
			$data[$languageVariable]		= $this->language->get($languageVariable);
		}

		if (!empty($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (!empty($this->request->get['page'])) {
			$page = (int) $this->request->get['page'];
		} else {
			$page = 1;
		}

		if(!isset($this->request->get['store_id'])) {
			$this->request->get['store_id'] = 0;
		}

		$store = $this->getCurrentStore($this->request->get['store_id']);

		$this->load->model('module/'.$this->moduleName);
		$this->load->model('module/callforprice');

		$data['url_link'] = $this->url;

		$data['store']				= $store;
		$data['store_id']			= $this->request->get['store_id'];
		$data['token']				= $this->session->data['token'];
		$data['limit']				= 10;
		$data['total']				= $this->{$this->moduleModel}->getTotalCustomers($this->request->get['store_id'], $filter_name);

		$data['sources']			= $this->{$this->moduleModel}->viewcustomers($this->request->get['store_id'], $filter_name, $page, $data['limit'], $data['store_id']);
		$pagination					= new Pagination();
		$pagination->total			= $data['total'];
		$pagination->page			= $page;
		$pagination->limit			= $data['limit'];
		$pagination->url			= $this->url->link('module/'.$this->moduleName.'/getcustomers','token=' . $this->session->data['token'].'&page={page}&store_id='.$this->request->get['store_id'].'&filter_name='.$filter_name, 'SSL');

		$data['pagination']			= $pagination->render();

		$data['results'] 			= sprintf($this->language->get('text_pagination'), ($data['total']) ? (($page - 1) * $data['limit']) + 1 : 0, ((($page - 1) * $data['limit']) > ($data['total'] - $data['limit'])) ? $data['total'] : ((($page - 1) * $data['limit']) + $data['limit']), $data['total'], ceil($data['total'] / $data['limit']));

		$this->response->setOutput($this->load->view('module/'.$this->moduleName.'/incoming.tpl', $data));
	}

	public function getarchive() {
		$this->load->language('module/'.$this->moduleName. '_requests');

		$languageVariables = array(
			'table_custname', 'table_custphone', 'table_product', 'table_custnotes', 'table_date', 'table_actions', 'button_move', 'button_remove',
			'button_removeall', 'table_adminnotes', 'text_empty'
		);

		foreach ($languageVariables as $languageVariable) {
			$data[$languageVariable]		= $this->language->get($languageVariable);
		}

		if (!empty($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (!empty($this->request->get['page'])) {
			$page = (int) $this->request->get['page'];
		} else {
			$page = 1;
		}

		if(!isset($this->request->get['store_id'])) {
			$this->request->get['store_id'] = 0;
		}

		$store = $this->getCurrentStore($this->request->get['store_id']);

		$this->load->model('module/'.$this->moduleName);
		$this->load->model('module/callforprice');

		$data['url_link'] = $this->url;

		$data['store']              = $store;
		$data['store_id']			= $this->request->get['store_id'];
		$data['token']				= $this->session->data['token'];
		$data['limit']				= 10;
		$data['total']				= $this->{$this->moduleModel}->getTotalNotifiedCustomers($this->request->get['store_id'], $filter_name);

		$data['sources']			= $this->{$this->moduleModel}->viewnotifiedcustomers($this->request->get['store_id'], $filter_name, $page, $data['limit'], $data['store_id']);
		$pagination					= new Pagination();
		$pagination->total			= $data['total'];
		$pagination->page			= $page;
		$pagination->limit			= $data['limit'];
		$pagination->url			= $this->url->link('module/'.$this->moduleName.'/getarchive','token=' . $this->session->data['token'].'&page={page}&store_id='.$this->request->get['store_id'].'&filter_name='.$filter_name, 'SSL');

		$data['pagination']			= $pagination->render();

		$data['results'] 			= sprintf($this->language->get('text_pagination'), ($data['total']) ? (($page - 1) * $data['limit']) + 1 : 0, ((($page - 1) * $data['limit']) > ($data['total'] - $data['limit'])) ? $data['total'] : ((($page - 1) * $data['limit']) + $data['limit']), $data['total'], ceil($data['total'] / $data['limit']));

		$this->response->setOutput($this->load->view('module/'.$this->moduleName.'/archive.tpl', $data));
	}

	public function removecustomer() {
		if (isset($_POST['callforprice_id'])) {
			$run_query = $this->db->query("DELETE FROM `" . DB_PREFIX . "callforprice` WHERE `callforprice_id`=".(int)$_POST['callforprice_id']);
			if ($run_query) echo "Success!";
		}
	}

	public function movecustomer() {
		if (isset($_POST['callforprice_id'])) {
			$anotes = (isset($_POST['callforprice_notes']) && !empty($_POST['callforprice_notes'])) ? ($_POST['callforprice_notes']) : '';
			$run_query = $this->db->query("UPDATE `" . DB_PREFIX . "callforprice` SET `customer_notified`='1', `anotes` = '".$anotes."' WHERE `callforprice_id`=".(int)$_POST['callforprice_id']);
			if ($run_query) echo "Success!";
		}
	}

	public function removeallcustomers() {
		if (isset($this->request->post['remove']) && ($this->request->post['remove']==true)) {
			$run_query = $this->db->query("DELETE FROM `" . DB_PREFIX . "callforprice` WHERE `customer_notified`='0' AND `store_id`='".$this->request->get['store_id']."'");
			if ($run_query) echo "Success!";
		}
	}

	public function moveallcustomers() {
		if (isset($this->request->post['move']) && ($this->request->post['move']==true)) {
			$run_query = $this->db->query("UPDATE `" . DB_PREFIX . "callforprice` SET `customer_notified`='1' WHERE `customer_notified`='0' AND `store_id`='".$this->request->get['store_id']."'");
			if ($run_query) echo "Success!";
		}
	}

	public function removeallarchive() {
		if (isset($this->request->post['remove']) && ($this->request->post['remove']==true)) {
			$run_query = $this->db->query("DELETE FROM `" . DB_PREFIX . "callforprice` WHERE `customer_notified`='1' AND `store_id`='".$this->request->get['store_id']."'");
			if ($run_query) echo "Success!";
		}
	}

	private function getCatalogURL() {
		if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
			$storeURL = HTTPS_CATALOG;
		} else {
			$storeURL = HTTP_CATALOG;
		}
		return $storeURL;
	}

	private function getServerURL() {
		if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
			$storeURL = HTTPS_SERVER;
		} else {
			$storeURL = HTTP_SERVER;
		}
		return $storeURL;
	}

	private function getCurrentStore($store_id) {
		if($store_id && $store_id != 0) {
			$store = $this->model_setting_store->getStore($store_id);
		} else {
			$store['store_id'] = 0;
			$store['name'] = $this->config->get('config_name');
			$store['url'] = $this->getCatalogURL();
		}
		return $store;
	}
}
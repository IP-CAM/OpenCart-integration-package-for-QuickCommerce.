<?php
require_once(DIR_SYSTEM . 'engine/qccontroller.php');
require_once(DIR_SYSTEM . 'library/quickcommerce/entity_manager.php');

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Reader\ArrayReader;
use Ddeboer\DataImport\Reader\OneToManyReader;
use Ddeboer\DataImport\Writer\ArrayWriter;
use Ddeboer\DataImport\Writer\CallbackWriter;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Ddeboer\DataImport\ItemConverter\MappingItemConverter;
use Ddeboer\DataImport\ItemConverter\NestedMappingItemConverter;
use Ddeboer\DataImport\ValueConverter\DateTimeValueConverter;

class ControllerExtensionModuleQCAdmin extends QCController {
	protected $tableName = null;
	protected $joinTableName = null;
	protected $joinCol = null;
	protected $foreign = null;
	
	public function install() {
		$this->registerEventHooks();

		$this->model_module_product_downloads->applyDatabaseChanges();
		
		$this->load->model('setting/store');

		$stores = $this->model_setting_store->getStores();

		$this->load->model('setting/setting');

		$this->model_setting_setting->editSetting('qc', $this->defaults);
	}

	public function uninstall() {
		$this->removeEventHooks();

		if ($this->config->get("pd_remove_sql_changes")) {
			$this->model_module_product_downloads->revertDatabaseChanges();
		}

		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('qc');

		$this->load->model('extension/module');
		$this->model_extension_module->deleteModulesByCode('quickcommerce'); // Don't think this is right
	}
	
	private function registerEventHooks() {
		$this->load->model('extension/event');
		$this->model_extension_event->addEvent('qc.event.after_customer_add', 'post.customer.add', 'qc/customer/eventAfterAddCustomer');
		$this->model_extension_event->addEvent('qc.event.after_customer_edit', 'post.customer.edit', 'qc/customer/eventAfterEditCustomer');
		$this->model_extension_event->addEvent('qc.event.after_product_add', 'post.admin.product.add', 'qc/product/eventAfterAddProduct');
		$this->model_extension_event->addEvent('qc.event.after_product_edit', 'post.admin.product.edit', 'qc/product/eventAfterEditProduct');
		$this->model_extension_event->addEvent('qc.event.before_customer_add', 'pre.customer_add', 'qc/customer/eventBeforeAddCustomer');
		$this->model_extension_event->addEvent('qc.event.before_customer_edit', 'pre.customer.edit', 'qc/customer/eventBeforeEditCustomer');
		$this->model_extension_event->addEvent('qc.event.before_product_add', 'pre.admin.product.add', 'qc/product/eventBeforeAddProduct');
		$this->model_extension_event->addEvent('qc.event.before_product_edit', 'pre.admin.product.edit', 'qc/product/eventBeforeEditProduct');
	}

	private function removeEventHooks() {
		$this->load->model('extension/event');
		$this->model_extension_event->deleteEvent('qc.event.after_customer_add');
		$this->model_extension_event->deleteEvent('qc.event.after_customer_edit');
		$this->model_extension_event->deleteEvent('qc.event.after_product_add');
		$this->model_extension_event->deleteEvent('qc.event.after_product_edit');
		$this->model_extension_event->deleteEvent('qc.event.before_customer_add');
		$this->model_extension_event->deleteEvent('qc.event.before_customer_edit');
		$this->model_extension_event->deleteEvent('qc.event.before_product_add');
		$this->model_extension_event->deleteEvent('qc.event.before_product_edit');
	}

	/*private function updateEventHooks() {
		$this->load->model('extension/event');

		$event_hooks = array(
			'pd.order.add'              => array('trigger' => 'post.order.add',                     'action' => 'checkout/download/order_add_hook'),
			'pd.order.edit'             => array('trigger' => 'post.order.edit',                    'action' => 'checkout/download/order_edit_hook'),
			'pd.order.delete'           => array('trigger' => 'pre.order.delete',                   'action' => 'checkout/download/order_delete_hook'),
		);

		foreach ($event_hooks as $code => $hook) {
			$event = $this->model_extension_event->getEvent($code);

			if (!$event || $event['trigger'] != $hook['trigger'] || $event['action'] != $hook['action']) {
				$this->model_extension_event->addEvent($code, $hook['trigger'], $hook['action']);

				if (empty($this->alert['success']['hooks_updated'])) {
					$this->alert['success']['hooks_updated'] = $this->language->get('text_success_hooks_update');
				}
			}
		}

		// Delete old triggers
		$query = $this->db->query("SELECT `code` FROM " . DB_PREFIX . "event WHERE `code` LIKE 'pd.%'");
		$events = array_keys($event_hooks);

		foreach ($query->rows as $row) {
			if (!in_array($row['code'], $events)) {
				$this->model_extension_event->deleteEvent($row['code']);

				if (empty($this->alert['success']['hooks_updated'])) {
					$this->alert['success']['hooks_updated'] = $this->language->get('text_success_hooks_update');
				}
			}
		}
	}*/

	function __construct($registry) {
		parent::__construct($registry);
		parent::before();
	}

	protected function getService() {
		return false; // Must implement abstract method - I need to separate QB initialization from QCController, should be self-contained and injected into classes
	}

	private function getAccounts() {
		$filter_data = null;

		$this->load->model('resource/namelist');
		$rModel = &$this->model_resource_namelist;

		$rService = new NameListAccount($this, 'OcAccount');
		$rService->setEntityManager($this->em);
		$rModel->setResourceType($rService);

		$results = $this->model_resource_namelist->getResources($filter_data); // Get resources currently does not accept any parameters

		return $results;
	}

	public function success() {

	}
	
	public function connect() {
		// Try to handle the OAuth request 
		if ($this->IntuitAnywhere->handle($this->username, $this->tenant)) {
			$this->quickbooks_is_connected = true;
			
		} else {
			// If this happens, something went wrong with the OAuth handshake
			$this->quickbooks_is_connected = false;
			die('Oh no, something bad happened: ' . $this->IntuitAnywhere->errorNumber() . ': ' . $this->IntuitAnywhere->errorMessage());
		}
	}
	
	public function disconnect() {
		$this->IntuitAnywhere->disconnect($this->username, $this->tenant);
		$this->quickbooks_is_connected = false;
	}
	
	public function menu() {
		die($this->IntuitAnywhere->widgetMenu($this->username, $this->tenant));
	}

	public function index() {
		$this->load->language('module/qc_admin');
		$this->load->model('setting/setting');

		$this->document->setTitle($this->language->get('heading_title'));

		$data = array(
			'version'             => '0.1',
			'heading_title'       => $this->language->get('heading_title'),
			
			'text_enabled'        => $this->language->get('text_enabled'),
			'text_disabled'       => $this->language->get('text_disabled'),
			'tab_settings'        => $this->language->get('tab_settings'),
			'tab_connection'      => $this->language->get('tab_connection'),
			'tab_test'            => $this->language->get('tab_test'),

			'entry_status'        => $this->language->get('entry_status'),
			'entry_token'         => $this->language->get('entry_token'),
			'entry_key'           => $this->language->get('entry_key'),
			'entry_secret'        => $this->language->get('entry_secret'),
			'entry_mode'          => $this->language->get('entry_mode'),
			'entry_oauth_url'     => $this->language->get('entry_oauth_url'),
			'entry_success_url'   => $this->language->get('entry_success_url'),
			'entry_menu_url'   	  => $this->language->get('entry_menu_url'),
			'entry_dsn'   	      => $this->language->get('entry_dsn'),
			'entry_enc_key'   	  => $this->language->get('entry_enc_key'),

			'button_save'         => $this->language->get('button_save'),
			'button_cancel'       => $this->language->get('button_cancel'),
			'text_edit'           => $this->language->get('text_edit'),

			'action'              => $this->url->link('module/qc_admin', 'token=' . $this->session->data['token'], 'SSL'),
			'cancel'              => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
		);

		//echo base64_decode("ZmlsZV9nZXRfY29udGVudHMoJ2h0dHA6Ly9saWNlbnNlLm9wZW5jYXJ0LWFwaS5jb20vbGljZW5zZS5waHA/b3JkZXJfaWQ9Jy4kdGhpcy0+cmVxdWVzdC0+cG9zdFsncmVzdGFkbWluX29yZGVyX2lkJ10uJyZzaXRlPScuSFRUUF9DQVRBTE9HLicma2V5PScuJHRoaXMtPnJlcXVlc3QtPnBvc3RbJ3Jlc3RhZG1pbl9rZXknXS4nJmFwaXY9cmVzdF9hZG1pbl8yX3gmb3BlbnY9Jy5WRVJTSU9OKTs=");
		//exit;
		
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            if(!empty($this->request->post['qc_dev_ipp_token']) || !empty($this->request->post['qc_prod_ipp_token'])) {
				//var_dump($this->request->post);
				//exit;
                $this->model_setting_setting->editSetting('qc', $this->request->post);				
                $this->session->data['success'] = $this->language->get('text_success');

                //eval(base64_decode("ZmlsZV9nZXRfY29udGVudHMoJ2h0dHA6Ly9saWNlbnNlLm9wZW5jYXJ0LWFwaS5jb20vbGljZW5zZS5waHA/b3JkZXJfaWQ9Jy4kdGhpcy0+cmVxdWVzdC0+cG9zdFsncmVzdGFkbWluX29yZGVyX2lkJ10uJyZzaXRlPScuSFRUUF9DQVRBTE9HLicma2V5PScuJHRoaXMtPnJlcXVlc3QtPnBvc3RbJ3Jlc3RhZG1pbl9rZXknXS4nJmFwaXY9cmVzdF9hZG1pbl8yX3gmb3BlbnY9Jy5WRVJTSU9OKTs="));
                $this->response->redirect($this->url->link('module/qc_admin', 'token=' . $this->session->data['token'], 'SSL'));
            } else {
                $error['warning'] = $this->language->get('error');
            }
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
			'href'      => $this->url->link('module/qc_admin', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
   		);
		
		$data['token'] = $this->session->data['token'];

   		if (isset($this->request->post['qc_status'])) {
			$data['status'] = $this->request->post['qc_status'];
		} else {
			$data['status'] = $this->config->get('qc_status');
		}
		
		if (isset($this->request->post['qc_mode'])) {
            $data['mode'] = $this->request->post['qc_mode'];
        } else {
            $data['mode'] = $this->config->get('qc_mode');
        }
		
		if (isset($this->request->post['qc_dev_ipp_token'])) {
            $data['dev_ipp_token'] = $this->request->post['qc_dev_ipp_token'];
        } else {
            $data['dev_ipp_token'] = $this->config->get('qc_dev_ipp_token');
        }

		if (isset($this->request->post['qc_dev_ipp_key'])) {
			$data['dev_ipp_key'] = $this->request->post['qc_dev_ipp_key'];
		} else {
			$data['dev_ipp_key'] = $this->config->get('qc_dev_ipp_key');
		}

        if (isset($this->request->post['qc_dev_ipp_secret'])) {
            $data['dev_ipp_secret'] = $this->request->post['qc_dev_ipp_secret'];
        } else {
            $data['dev_ipp_secret'] = $this->config->get('qc_dev_ipp_secret');
        }
		
		if (isset($this->request->post['qc_prod_ipp_token'])) {
            $data['prod_ipp_token'] = $this->request->post['qc_prod_ipp_token'];
        } else {
            $data['prod_ipp_token'] = $this->config->get('qc_prod_ipp_token');
        }

		if (isset($this->request->post['qc_prod_ipp_key'])) {
			$data['prod_ipp_key'] = $this->request->post['qc_prod_ipp_key'];
		} else {
			$data['prod_ipp_key'] = $this->config->get('qc_prod_ipp_key');
		}

        if (isset($this->request->post['qc_prod_ipp_secret'])) {
            $data['prod_ipp_secret'] = $this->request->post['qc_prod_ipp_secret'];
        } else {
            $data['prod_ipp_secret'] = $this->config->get('qc_prod_ipp_secret');
        }
		
		// NEW STYLE URLs
		$data['oauth_url'] = $this->oauth_url;
		$data['success_url'] = $this->success_url;
		$data['menu_url'] = $this->menu_url;
		$data['disconnect_url'] = $this->disconnect_url;
		
		$data['connected'] = $this->quickbooks_is_connected;

		// URLs
		/*if (isset($this->request->post['qc_oauth_url'])) {
            $data['oauth_url'] = $this->request->post['qc_oauth_url'];
        } else {
            $data['oauth_url'] = $this->config->get('qc_oauth_url');
        }
		
		if (isset($this->request->post['qc_success_url'])) {
            $data['success_url'] = $this->request->post['qc_success_url'];
        } else {
            $data['success_url'] = $this->config->get('qc_success_url');
        }
		
		if (isset($this->request->post['qc_menu_url'])) {
            $data['menu_url'] = $this->request->post['qc_menu_url'];
        } else {
            $data['menu_url'] = $this->config->get('qc_menu_url');
        }*/

		// Dev URLs
		/*if (isset($this->request->post['qc_dev_oauth_url'])) {
			$data['dev_oauth_url'] = $this->request->post['qc_dev_oauth_url'];
		} else {
			$data['dev_oauth_url'] = $this->config->get('qc_dev_oauth_url');
		}

		if (isset($this->request->post['qc_dev_success_url'])) {
			$data['dev_success_url'] = $this->request->post['qc_dev_success_url'];
		} else {
			$data['dev_success_url'] = $this->config->get('qc_dev_success_url');
		}

		if (isset($this->request->post['qc_dev_menu_url'])) {
			$data['dev_menu_url'] = $this->request->post['qc_dev_menu_url'];
		} else {
			$data['dev_menu_url'] = $this->config->get('qc_menu_url');
		}*/
		
		if (isset($this->request->post['qc_dsn'])) {
            $data['dsn'] = $this->request->post['qc_dsn'];
        } else {
            $data['dsn'] = $this->config->get('qc_dsn');
        }
		
		if (isset($this->request->post['qc_enc_key'])) {
            $data['enc_key'] = $this->request->post['qc_enc_key'];
        } else {
            $data['enc_key'] = $this->config->get('qc_enc_key');
        }
		
		// Accounts
		if (isset($this->request->post['qc_income_account'])) {
			$data['income_account'] = (int)$this->request->post['qc_income_account'];
		} else {
			$data['income_account'] = (int)$this->config->get('qc_income_account');
		}

		if (isset($this->request->post['qc_account'])) {
			$data['cogs_account'] = (int)$this->request->post['qc_cogs_account'];
		} else {
			$data['cogs_account'] = (int)$this->config->get('qc_cogs_account');
		}

		if (isset($this->request->post['qc_asset_account'])) {
			$data['asset_account'] = (int)$this->request->post['qc_asset_account'];
		} else {
			$data['asset_account'] = (int)$this->config->get('qc_asset_account');
		}

		if (isset($error['warning'])) {
			$data['error_warning'] = $error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['accounts'] = $this->getAccounts();
		
		if (isset($this->request->post['qc_default_product'])) {
			$data['default_product'] = (int)$this->request->post['qc_default_product'];
		} else {
			$data['default_product'] = (int)$this->config->get('qc_default_product');
		}
		
		if (isset($this->request->post['qc_default_service'])) {
			$data['default_service'] = (int)$this->request->post['qc_default_service'];
		} else {
			$data['default_service'] = (int)$this->config->get('qc_default_service');
		}
		
		if (isset($this->request->post['qc_default_shipping'])) {
			$data['default_shipping'] = (int)$this->request->post['qc_default_shipping'];
		} else {
			$data['default_shipping'] = (int)$this->config->get('qc_default_shipping');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('module/qc_admin.tpl', $data));
	}
}
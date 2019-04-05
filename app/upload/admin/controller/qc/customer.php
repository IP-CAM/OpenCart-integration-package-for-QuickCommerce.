<?php
require_once(DIR_SYSTEM . 'engine/qccontroller.php');
require_once(DIR_SYSTEM . 'library/quickcommerce/entity_manager.php');

use Doctrine\Common\Util\Inflector;
use Doctrine\Common\Util\Debug;
use Doctrine\Common\Collections\Criteria;

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Reader\ArrayReader;
use Ddeboer\DataImport\Reader\OneToManyReader;
use Ddeboer\DataImport\Writer\ArrayWriter;
use Ddeboer\DataImport\Writer\CallbackWriter;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Ddeboer\DataImport\ItemConverter\MappingItemConverter;
use Ddeboer\DataImport\ItemConverter\NestedMappingItemConverter;
use Ddeboer\DataImport\ValueConverter\DateTimeValueConverter;

class ControllerQCCustomer extends QCController {
	protected $tableName = 'qcli_customer';
	protected $joinTableName = 'customer';
	protected $joinCol = 'customer_id';
	protected $foreign = 'Customer';
	
	function __construct($registry) {
		parent::__construct($registry);		
		parent::before();
	}

	private function loadMetadata() {
		$this->cMeta = $this->em->getClassMetadata('OcCustomer');
		$this->aMeta = $this->em->getClassMetadata('OcAddress');
	}
    
    protected function init() {
		$this->sService = new \App\Resource\Store($this->em, 'OcStore');
		$this->cService = new \App\Resource\Customer($this->em, 'OcCustomer');
		$this->cgService = new \App\Resource\CustomerGroup($this->em, 'OcCustomerGroup');
		$this->aService = new \App\Resource\Address($this->em, 'OcAddress');
		$this->ccService = new \App\Resource\Country($this->em, 'OcCountry');
		$this->zService = new \App\Resource\Zone($this->em, 'OcZone');
	}
	
	// TODO: I can make a generic one of these, just copying for now...
	protected function getService() {
		$service = new \App\Resource\Customer($this->em, 'OcCustomer');
		return $service;
	}
	
	/** 
	 * Heavy batch operations should be changed to use XMLWriter or
	 * something that doesn't have to load everything into memory?
	 * This should be fine for small or medium-sized stores anyway
	 */
	public function fetch() {
        $this->init();
        
		$this->loadMetadata();
        
		// getMappings is used to map remote entities unlike mapDoctrineEntity and its wrappers which simply convert between OpenCart/OpenCart DB fields and their Doctrine entity equivalents
		$mappings = $this->getMappings($this->foreign); // Get the remote item mappings

		$c = null;
		$data = array();
        
		//$items = $this->getCollection();

		$importItem = function (&$item, &$data) {
			$error = null;
			$id = self::qbId($item->getId());

			if ($item->getActive() == 'false') {
				$error[] = $item->getFullyQualifiedName() . ' is inactive';
			}

			if (empty($item->getPrimaryEmailAddr())) {
				$error[] = $item->getFullyQualifiedName() . ' does not have an email address';
			} else {
				if (trim($item->getPrimaryEmailAddr()->getAddress()) == '') {
					$error[] = $item->getFullyQualifiedName() . 'does not have an email address';
				}
			}

			if (is_array($error) && count($error) > 0) {
				$this->error['validation'][$id] = $error;
			}

			$mappings = $this->getMappings($this->foreign); // They're already loaded, just getting the reference
			$c = array();
			self::importEntity($item, $mappings, $this->cMeta, $c);
			$data[] = $c;
		};

		 $this->iterateCollection($importItem, $data);
		// TODO: Would be better if I didn't have to deal with huge arrays of data... maybe I can process on the fly?

		$defaultGroup = $this->cgService->getEntity(1, false);
		$store = $this->sService->getEntity(1, false);
		$country = $this->ccService->getEntity(38, false);
		$zone = $this->zService->getEntity(602, false);

		$reader = new ArrayReader($data);
		$writer = new CallbackWriter(
			function ($item) use (&$c, &$defaultGroup, &$country, &$zone) {
				try {
					//unset($item['dateAdded']);
					//unset($item['dateModified']);

					// Have to do this manually because I disabled association processing in App\Resource::fillEntity
					if (isset($item['address'])) {
						$address = $item['address'];
						unset($item['address']);
					}

					// Customer should be tested for a unique email address - we don't want duplicates in OpenCart
					// That might not be the case in QuickBooks?
					$feedId = self::qbId($item['_entity']->getId());
					$exists = (int)$this->listItemExists('email', $item['email']); // TODO: Method defaults maybe?
					$isValid = (!isset($this->error['validation'][$feedId])) ? true : false;
                    
                    // It's a brand new customer
					if ($exists == false) {
						if (!$isValid) return;

						$date = new DateTime();
						$c = $this->cService->writeItem($item);
                        // It's a new user, so the password won't exist
                        // TODO: Make this configurable from admin or something
                        
                        $c->setPassword('D3f@ultP@ssw0rd'); // Does not exist in QBO
						$c->setSalt('default'); // Does not exist in QBO
						
                        $c->setCustomField(''); // Does not exist in QBO
						$c->setCustomerGroup($defaultGroup); // Does not exist in QBO

						// TODO: Get config and default customer group
						$c->setIp(''); // Does not exist in QBO
						$c->setStatus(1); // Does not exist in QBO
						$c->setApproved(1); // Does not exist in QBO
						$c->setSafe(1); // Does not exist in QBO
						$c->setToken(''); // Does not exist in QBO
						$c->setDateAdded($date); // Does not exist in QBO
						//$c->setAddress(0); // Does not exist in QBO
						//$c->setDateModified($date); // Does not exist in QBO

						// TODO: Need to paste in OC logic

						$this->cService->updateEntity($c);

						// TODO: I need something to fill in blank spaces for non-nullable string fields in OpenCart
						if (isset($address)) {
							$stripChars = '/([^\w]+)([^\w]+)/';

							$a = $this->aService->writeItem($address);
							$a->setFirstname($c->getFirstname());
							$a->setLastname($c->getLastname());
							$a->setCompany(''); // Where in QBO?
                            $a->setAddress1(trim(preg_replace($stripChars, ' ', $a->getAddress1()))); // Where in QBO?
							$a->setAddress2(trim(preg_replace($stripChars, ' ', $a->getAddress2()))); // Where in QBO?
							$a->setCity(trim(preg_replace($stripChars, ' ', $a->getCity()))); // Where in QBO?
							$a->setCountry($country); // Where in QBO?
							$a->setZone($zone); // Where in QBO?
							$a->setCustomer($c);
							$this->aService->updateEntity($a);
							// If default address assign to customer too
						}

						$this->db->query("UPDATE " .  DB_PREFIX . "customer SET store_id = '0' WHERE store_id IS NULL"); // MySQL-only fix for "0" store id - no idea how to work around this in Doctrine
						$this->db->query("UPDATE " .  DB_PREFIX . "customer SET address_id = '0' WHERE address_id IS NULL"); // MySQL-only fix for "0" store id - no idea how to work around this in Doctrine

						$item['_entity']->setOcId($c->getCustomerId());
						$this->_writeListItem($item['_entity'], null, false);
					} else {
						if (!$isValid) {
							$msg = $this->error['validation'][$feedId];
							return;
						}

						// Update the list item
						$item['_entity']->setOcId($exists);
						$this->_writeListItem($item['_entity'], null, $exists, true); // Final flag indicates 'flush' to delete any old qcli link records
					}

				} catch (Exception $e) {
					throw $e;
				}
			});

		//self::addDateConverters($workflow); // TODO: Where is this method?
		$workflow = new Workflow($reader);
		$workflow->addWriter($writer);
		$workflow->process();

		if (is_array($this->error['validation'])) {
			foreach ($this->error['validation'] as $id => $error) {
				$pre = 'Customer import error: ';
				foreach ($error as $err) {
					$msg = $pre . trim($err);
					$this->error['validation'][$id] = $msg;
				}
			};
		}

		if (!count($this->error) > 0) {
			$this->sendResponse(array());
		} else {
			$this->sendResponse(array('errors' => $this->error));
		}

	}
    
    // Working on it...
	// This is alright for base OpenCart install
	// OC doesn't like nulls... need to set default vals somehow
	public function pull($customer, $remote) {
        $this->init();
        
		$this->loadMetadata();
		// getMappings is used to map remote entities unlike mapDoctrineEntity and its wrappers which simply convert between OpenCart/OpenCart DB fields and their Doctrine entity equivalents
		$mappings = $this->getMappings($this->foreign); // Get the remote item mappings

		$data = array();

		// Import the remote entity
		self::importEntity($remote, $mappings, $this->cMeta, $data);

		try {
			$data = array_merge($this->cService->serializeEntity($customer, false, array(), array(), false), $data);
		} catch (Exception $e) {
			var_dump($e);
		}

		$defaultGroup = $this->cgService->getEntity(1, false);
		$store = $this->sService->getEntity(1, false);

		$countryString = $remote->getBillAddr()->getCountry();
		$zoneString = $remote->getBillAddr()->getCountrySubDivisionCode();

		$country = $this->getCountryByString($countryString);
		$zone = $this->getCountryZoneByString($country, $zoneString);

		$reader = new ArrayReader(array($data)); // Wrap in an array it so we drop it into the writer
		$writer = new CallbackWriter(
			function ($item) use (&$remote, &$c, &$defaultGroup, &$country, &$zone) {
					try {
						// Have to do this manually because I disabled association processing in App\Resource::fillEntity
                        if (isset($item['address'])) {
                            $address = $item['address'];
                            unset($item['address']);
                        }
                        // Customer should be tested for a unique email address - we don't want duplicates in OpenCart
                        // That might not be the case in QuickBooks?
                        //$feedId = self::qbId($item['_entity']->getId());
                        //$exists = (int)$this->listItemExists('email', $item['email']); // TODO: Method defaults maybe?
                        //$isValid = (!isset($this->error['validation'][$feedId])) ? true : false;
                        
						//if (!$exists) {
							$date = new DateTime();
                            $c = $this->cService->writeItem($item);
                            
                            if (empty($c->getPassword())) {
                                $c->setPassword('D3f@ultP@ssw0rd'); // Does not exist in QBO
                                $c->setSalt('default'); // Does not exist in QBO
                            }
                            
                            $c->setCustomField(''); // Does not exist in QBO
                            $c->setCustomerGroup($defaultGroup); // Does not exist in QBO

                            // TODO: Get config and default customer group
                            $c->setIp(''); // Does not exist in QBO
                            $c->setStatus(1); // Does not exist in QBO
                            $c->setApproved(1); // Does not exist in QBO
                            $c->setSafe(1); // Does not exist in QBO
                            $c->setToken(''); // Does not exist in QBO
                            
							if (empty($c->getDateAdded())) {
                                $c->setDateAdded($date);
                            }
                            
                            if (empty($c->getDateModified())) {
                                $c->setDateModified($date);
                            }
                            
							// Try to get the description
							if (!empty($c->getAddress())) {
								$address['addressId'] = $c->getAddress()->getAddressId();
								$a = $this->aService->writeItem($address); // TODO: Multi-language
							} else {
								if (!isset($address)) $address = array();
								$a = $this->aService->writeItem($address);
							}
                            
                            $stripChars = '/([^\w]+)([^\w]+)/';

							$a->setFirstname($c->getFirstname());
							$a->setLastname($c->getLastname());
							$a->setCompany(''); // Where in QBO?
							$a->setAddress1(trim(preg_replace($stripChars, ' ', $a->getAddress1())));
							$a->setAddress2(trim(preg_replace($stripChars, ' ', $a->getAddress2())));
							$a->setCity(trim(preg_replace($stripChars, ' ', $a->getCity())));
							$a->setCompany('');
							$a->setCountry($country);
							$a->setZone($zone);
							$a->setCustomer($c);
                            
							$this->aService->updateEntity($a);

							$this->cService->updateEntity($c);
                            
							//$this->db->query("UPDATE " .  DB_PREFIX . "customer SET store_id = '0' WHERE store_id IS NULL"); // MySQL-only fix for "0" store id - no idea how to work around this in Doctrine
							//$this->db->query("UPDATE " .  DB_PREFIX . "customer SET address_id = '0' WHERE address_id IS NULL"); // MySQL-only fix for "0" store id - no idea how to work around this in Doctrine
							
							$item['_entity']->setOcId($c->getCustomerId());
							//$this->_writeListItem($item['_entity']); // TODO: _updateListItem - in case refs were updated
						//} else {
							// If the customer exists, maybe we can do an update instead, if the QBO record is more current than the OC record
							// Just ignore for now
						//}

					} catch (Exception $e) {
						throw $e;
					}
			});

		$workflow = new Workflow($reader);
		//self::addDateConverters($workflow); // TODO: Where is this method?
		$workflow->addWriter($writer);
		$workflow->process();
	}

	public function sync() {
		$this->__sync();
	}

	public function getSyncStatuses() {
		$this->__getSyncStatuses();
	}
	
	// TODO: This could be moved to Country service?
	protected function getCountryByString($string) {
		try {
			$criteria = Criteria::create()
				->where(Criteria::expr()->eq('name', $string))
				->orWhere(Criteria::expr()->eq('isoCode2', $string))
				->orWhere(Criteria::expr()->eq('isoCode3', $string))
				->setFirstResult(0)
				->setMaxResults(1);

			$c = $this->ccService->find($criteria);

			return $c[0];
		} catch (Exception $e) {
			//echo $e;
		}
	}
    
    // TODO: This could be moved to Country service?
	protected function getZoneByString($string) {
		try {
			$criteria = Criteria::create()
				->where(Criteria::expr()->eq('name', $string))
				->setFirstResult(0)
				->setMaxResults(1);

			$z = $this->zService->find($criteria);

			return $z[0];
		} catch (Exception $e) {
			//echo $e;
		}
	}
    
    // TODO: This could be moved to Country service?
	protected function getCountryZoneByString(OcCountry $country, $string) {
		try {
			$criteria = Criteria::create()
				// TODO: Fix this for international!
				->where(Criteria::expr()->eq('country', $country))
				->andWhere(Criteria::expr()->eq('name', $string))
				->orWhere(Criteria::expr()->eq('code', $string))
				->setFirstResult(0)
				->setMaxResults(1);

			$z = $this->zService->find($criteria);

			return $z[0];
		} catch (Exception $e) {
			//echo $e;
		}
	}
	
	/**
	 * @param $customerId
     */
	public function add($customerId) {
		$this->getMappings('Customer');
		$this->getMappings('Address');
		$mappings = $this->mappings;
		
		$this->load->model('customer/customer');
		$this->load->model('customer/customer_group');
		$data = $this->model_customer_customer->getCustomer($customerId);
		$address = $this->model_customer_customer->getAddress($data['address_id']); // TODO: Some way to automate this? From here on in it is done already...
		$group = $this->model_customer_customer_group->getCustomerGroup($data['customer_group_id']);
		
		$entityService = new QuickBooks_IPP_Service_Customer();
		$entity = new QuickBooks_IPP_Object_Customer();
		$entity->setOcId($customerId);
		$cMeta = $this->em->getClassMetadata('OcCustomer');
		
		$c = ObjectFactory::createEntity($this->em, 'OcCustomer', $data, array('address' => $address, 'customerGroup' => $group));
		
		// Populate entity data
		$this->fillEntity($entity, $mappings['Customer']['fields'], $cMeta, $c);
		$this->fillEntityObjects('Customer', $entity, $mappings, $cMeta, $c);
		$this->fillEntityRefs($entity, $mappings['Customer']['refs'], $c);
		
		// TODO: Extend services with export func.
		// I've isolated the code using static helpers right now
		// so it should be pretty easy to move around later
		$this->export($entityService, $entity, false);
	}
	
	/**
	 * @param int $customerId
	 * @param array $data
     */
	public function edit($customerId = 0, $feedEntity = false) {
		// Create the mappings
		$this->getMappings('Customer');
		$this->getMappings('Address');
		$mappings = $this->mappings;
		
		// Load OpenCart models and fetch any required data
		// TODO: When Doctrine integration is complete this won't be necessary
		$this->load->model('customer/customer');
		$this->load->model('customer/customer_group');
		$data = $this->model_customer_customer->getCustomer($customerId);
		$address = $this->model_customer_customer->getAddress($data['address_id']); // TODO: Some way to automate this? From here on in it is done already...
		$group = $this->model_customer_customer_group->getCustomerGroup($data['customer_group_id']);
		
		// Create the service client
		$entityService = new QuickBooks_IPP_Service_Customer();
		$entity = null; //new QuickBooks_IPP_Object_Customer();
		$feedId = null;

		// Set our $feedId and $entity variables - this process is repeated in most QCController classes
		// This method handles the log
		$this->setRemoteEntityVars($feedId, $entity, $customerId, $feedEntity);

		if ($entity) {
			$entity->setOcId($customerId); // Set the corresponding OpenCart "entity" ID
			
			// Create a blank OpenCart entity using Doctrine
			$cMeta = $this->em->getClassMetadata('OcCustomer'); // Get the Doctrine class metadata
			// Returns an array representation of the OpenCart entity using Doctrine metadata, populated with any provided data
			$c = ObjectFactory::createEntity($this->em, 'OcCustomer', $data, array('address' => $address, 'customerGroup' => $group));

			// Populate the returned entity with OpenCart data using the appropriate mapping
			$this->fillEntity($entity, $mappings['Customer']['fields'], $cMeta, $c);
			$this->fillEntityObjects('Customer', $entity, $mappings, $cMeta, $c); // Fill any objects
			$this->fillEntityRefs($entity, $mappings['Customer']['refs'], $c); // Fill references

			// Fix text to ID for address country and zone
			// TODO: Need to automap this somehow
			$entity->getBillAddr()->setCountry($address['country']);
			$entity->getBillAddr()->setCountrySubDivisionCode($address['zone']);

			$this->export($entityService, $entity, false);
		} else {
			// If the customer was deleted in QBO re-add it
			$this->add($customerId);
		}
	}
    
    protected function buildFilter() {
        if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = null;
		}

		if (isset($this->request->get['filter_customer_group_id'])) {
			$filter_customer_group_id = $this->request->get['filter_customer_group_id'];
		} else {
			$filter_customer_group_id = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['filter_approved'])) {
			$filter_approved = $this->request->get['filter_approved'];
		} else {
			$filter_approved = null;
		}

		if (isset($this->request->get['filter_ip'])) {
			$filter_ip = $this->request->get['filter_ip'];
		} else {
			$filter_ip = null;
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}

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

		$start = ($page - 1) * $this->config->get('config_limit_admin') + 1; // Default

		if (isset($this->request->get['records'])) {
			$records = $this->request->get['records'];
			$start = $records + 1; // Adjust start
		} else {
			$records = 0;
		}
        
        $filter_data = array(
			'filter_name'              => $filter_name,
			'filter_email'             => $filter_email,
			'filter_customer_group_id' => $filter_customer_group_id,
			'filter_status'            => $filter_status,
			'filter_approved'          => $filter_approved,
			'filter_date_added'        => $filter_date_added,
			'filter_ip'                => $filter_ip,
			//'filter_match'			=> $filter_match,
			'sort'					=> $sort,
			'order'					=> $order,
			'records'				=> $records,
			'start'					=> $start,
			'limit'					=> $this->config->get('config_limit_admin')
		);
        
        return $filter_data;
    }
    
    protected function buildQuery() {
        $url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_approved'])) {
			$url .= '&filter_approved=' . $this->request->get['filter_approved'];
		}

		if (isset($this->request->get['filter_ip'])) {
			$url .= '&filter_ip=' . $this->request->get['filter_ip'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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
        
        return $url;
    }
    
    protected function getFieldNames(&$data = array()) {
        $this->load->language('sale/customer'); // Just in case
        
        $data['entry_name'] = $this->language->get('column_name');
        $data['entry_company'] = $this->language->get('entry_company');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_telephone'] = $this->language->get('entry_telephone');
		$data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_approved'] = $this->language->get('entry_approved');
		$data['entry_ip'] = $this->language->get('entry_ip');
		$data['entry_date_added'] = $this->language->get('entry_date_added');
    }
    
    protected function getColumnNames(&$data = array()) {
        $this->load->language('sale/customer'); // Just in case
        
        $data['column_name'] = $this->language->get('column_name');
        $data['column_company'] = $this->language->get('entry_company');
		$data['column_email'] = $this->language->get('column_email');
		$data['column_telephone'] = $this->language->get('entry_telephone');
		$data['column_customer_group'] = $this->language->get('column_customer_group');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_approved'] = $this->language->get('column_approved');
		$data['column_ip'] = $this->language->get('column_ip');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_action'] = $this->language->get('column_action');
    }
    
    public function getRelinkList() {
		$this->init();
		
        $this->loadMetadata();

		$this->load->language('sale/customer');

		$this->load->model('sale/customer');

		$url = $this->buildQuery();
		$filter_data = $this->buildFilter();

		$data['add'] = $this->url->link('sale/customer/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['copy'] = $this->url->link('sale/customer/copy', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('sale/customer/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['customers'] = array();

		$qb_customer_total = $this->getCount($filter_data);
		$qb_results = array(); //$this->getCollection($filter_data['start'], $filter_data['limit']);
		$qb_processed = 0;
		// This is not the same as $importItem in fetch and other methods -- we check to see if the record exists before adding it

		$importItem = function (&$item, &$data, &$exclude) {
			$mappings = $this->getMappings($this->foreign); // They're already loaded, just getting the reference
			$c = array();
			$cMeta = $this->cMeta; // Will result in indirect modification of overloaded property notice if provided
			self::importEntity($item, $mappings, $cMeta, $c);

			$process = false;
			
			if ($process == false) {
				$data[] = $c;
			} else {
				$exclude = true;
			}
		};

		$start = $filter_data['start'];

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$qb_processed = $this->iterateCollection($importItem, $qb_results, $start, $filter_data['limit'], $filter_data, 'Id DESC');

		$reader = new ArrayReader($qb_results);
		$writer = new CallbackWriter(
			function ($item) use (&$importedIds, &$data) {
                try {
					// Customer should be tested for a unique email address - we don't want duplicates in OpenCart
					// That might not be the case in QuickBooks?
					//$exists = $this->listItemExists('qbname', $item['qbname']); // TODO: Method defaults maybe?
					// Check to see if a mapped customer exists? I don't think it's necessary

					$qbid = 0;
					if ($item['_entity'] instanceof QuickBooks_IPP_Object_Customer) {
						$qbid = self::qbId($item['_entity']->getId());
					}

					//if ($exists == false) {
					$data['db2_customers'][] = array(
						'qbid' 		    => $qbid,
						'local_id'      => (isset($result['local_id'])) ? $result['local_id'] : '',
						'local_model'   => '', //(isset($result['local_model'])) ? $result['local_model'] : '',
						'display_name'  => (isset($item['displayName']) && !empty(trim($item['displayName']))) ? trim($item['displayName']) : implode(' ', array($item['firstname'], $item['lastname'])),
						'company_name'  => (isset($item['companyName'])) ? $item['companyName'] : '',
						'email'         => (isset($item['email'])) ? $item['email'] : '',
						'telephone'     => (isset($item['telephone'])) ? $item['telephone'] : '',
						'status'        => '' //($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled')
					);
					/*} else {
                        // Product exists -- we need to update the totals and record count
                        $exclude = true;
                    }*/
				} catch (Exception $e) {
					throw $e;
				}
			}
		);

		$workflow = new Workflow($reader);
		//self::addDateConverters($workflow); // TODO: Where is this method?
		$workflow->addWriter($writer);
		$workflow->process();

		// There's no real way to make this paging 100% accurate without wasting a sh**-ton of resources
		// because we don't know if a record actually exists until it's been fetched.
		// To minimize the margin of error, we simply subtract the number of processed records...
		$qb_customer_total = $qb_customer_total - $qb_processed - $filter_data['records'];
		$qb_pagination = new Pagination();
		$qb_pagination->total = $qb_customer_total;
		$qb_pagination->page = $page;
		$qb_pagination->limit = $this->config->get('config_limit_admin');
		$qb_pagination->url = $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		// db2 instead of qb so we can reuse template
		$data['db2_pagination'] = $qb_pagination->render();

		$data['db2_results'] = sprintf($this->language->get('text_pagination'), ($qb_customer_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($qb_customer_total - $this->config->get('config_limit_admin'))) ? $qb_customer_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $qb_customer_total, ceil($qb_customer_total / $this->config->get('config_limit_admin')));

		$data['db2_records'] = $qb_processed + $filter_data['records'];

		$this->getFieldNames($data);
		$this->getColumnNames($data);

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$data['sort'] = $filter_data['sort'];
		$data['order'] = $filter_data['order'];

		$data['token'] = $this->session->data['token'];

		$this->response->setOutput($this->load->view('sale/customer_relink_list.tpl', $data));
	}

	public function getLocalCustomer() {
		$this->load->language('sale/customer');
		$this->getColumnNames($data);

		$data['customer'] = null;

		if (isset($this->request->get['customer_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$this->load->model('sale/customer');

			$customer_info = $this->model_sale_customer->getCustomer($this->request->get['customer_id']);
            
			if ($customer_info != null) {
				$data['customer'] = array(
					'customer_id'    => $customer_info['customer_id'],
                    'display_name'   => implode(' ', array($customer_info['firstname'], $customer_info['lastname'])),
                    'company_name'   => $customer_info['company_name'],
                    'email'          => $customer_info['email'],
                    'telephone'      => $customer_info['telephone'],
                    //'customer_group' => $customer_info['customer_group'],
                    'status'         => ($customer_info['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
                    'date_added'     => date($this->language->get('date_format_short'), strtotime($customer_info['date_added'])),
                    'ip'             => $customer_info['ip']
				);
			}
		}

		$this->response->setOutput($this->load->view('sale/customer_local_item.tpl', $data));

		//$this->response->addHeader('Content-Type: application/json');
		//$this->response->setOutput(json_encode($json));
	}

	/**
	 * @param int $start
	 * @param int $max
	 * @param string $className
	 * @param null $service
	 * @return mixed
	 */
	public function getOrderedCollection($start = 1, $max = 1000, $filters = array(), $orderBy = 'Metadata.LastUpdatedTime', $className = '', $service = null) {
		$className = (!empty($className)) ? $className : "QuickBooks_IPP_Service_" . $this->foreign;
		$service = (!empty($service)) ? $service : new $className();

		// I think for some reason maybe Keith Palmer's findAll method needs to be updated
		//$items = $service->findAll($this->Context, $this->realm, "SELECT * FROM Item ORDER BY Metadata.LastUpdatedTime", $page, $size);

		$where = array();

		if (isset($filters['filter_name'])) {
			$where[] = "FullyQualifiedName LIKE '%" . $filters['filter_name'] . "%'";
			//$where[] = "GivenName LIKE '%" . $filters['filter_name'] . "%'";
			//$where[] = "FamilyName LIKE '%" . $filters['filter_name'] . "%'";
			//$where[] = "CompanyName LIKE '%" . $filters['filter_name'] . "%'";
			//$where[] = "PrimaryEmailAddr LIKE '%" . $filters['filter_name'] . "%'";
		}

		$where = (count($where) > 0) ? ' WHERE ' . implode(' AND ', $where) : ''; // OR is not supported
		$query = "SELECT * FROM " . $this->foreign . $where . " ORDER BY " . $orderBy . " STARTPOSITION " . (int)$start . " MAXRESULTS " . (int)$max;

		$items = $service->query($this->Context, $this->realm, $query);

		if ($items) {
			return $items;
		}
		else
		{
			// TODO: Log
			//print($service->lastError($this->Context));
		}

		/*foreach ($items as $item) {
			print('Item Id=' . $item->getId() . ' is named: ' . $item->getName() . '<br>');
		}*/

		return $items;
	}

	protected function getCount($filters = array(), $className = '', $service = null) {
		$count = false;

		// Get the count
		$className = (!empty($className)) ? $className : "QuickBooks_IPP_Service_" . $this->foreign;
		$service = (!empty($service)) ? $service : new $className();

		$where = array();

		if (isset($filters['filter_name'])) {
			$where[] = "FullyQualifiedName LIKE '%" . $filters['filter_name'] . "%'";
			//$where[] = "GivenName LIKE '%" . $filters['filter_name'] . "%'";
			//$where[] = "FamilyName LIKE '%" . $filters['filter_name'] . "%'";
			//$where[] = "CompanyName LIKE '%" . $filters['filter_name'] . "%'";
			//$where[] = "PrimaryEmailAddr LIKE '%" . $filters['filter_name'] . "%'";
		}

		$where = (count($where) > 0) ? ' WHERE ' . implode(' AND ', $where) : '';
		$query = "SELECT COUNT(*) FROM " . $this->foreign . $where;

		$result = $service->query($this->Context, $this->realm, $query);

		if ($result) {
			$result = (int)$result;

			if ($result > 0) {
				$count = $result;
			}
		} else {
			// Parse QBO error
		}

		return $count;
	}

	protected function getTotal($className = '', $service = null) {
		$count = false;

		// Get the count
		$className = (!empty($className)) ? $className : "QuickBooks_IPP_Service_" . $this->foreign;
		$service = (!empty($service)) ? $service : new $className();

		$result = $service->query($this->Context, $this->realm, "SELECT COUNT(*) FROM " . $this->foreign);


		if ($result) {
			$result = (int)$result;

			if ($result > 0) {
				$count = $result;
			}
		} else {
			// Parse QBO error
		}

		return $count;
	}
	
	// TODO: I can make a generic one of these, just copying for now...
	
	private static function explodeSubCustomer($item, $fullyQualified = false) {
		if (!$fullyQualified) {
			$item = explode(':', trim($item));
			return array_pop($item);
		}
		
		return $item;
	}
	
	public function test() {
		$obj = ObjectFactory::createObject($this->em, 'MetaData', array());
		//var_dump($obj->getLastUpdatedTime());
	}
	
	/**
	 * Proxy method allows for stronger type hinting
	 */
	protected function export (QuickBooks_IPP_Service_Customer &$service, QuickBooks_IPP_Object_Customer &$item, $asXml = false) {
		$this->_export($service, $item, $asXml);
	}
	
	/**
	 * Event hook triggered before adding a customer
	 */
	public function eventBeforeAddCustomer($customerId) {
		
	}
	
	/**
	 * Event hook triggered after adding a customer
	 */
	public function eventAfterAddCustomer($customerId) {
		if ($this->quickbooks_is_connected) {
			// Post customer to QBO
			$this->add($customerId);
		} else {
			$errorDetail = array(
				'error' => 'QuickBooks is not connected'
			);

			$this->session->data['ipp_error']['warning'] = $errorDetail;
		}
	}
	
	/**
	 * Event hook triggered before editing a customer
	 */
	public function eventBeforeEditCustomer() {
		
	}
	
	/**
	 * Event hook triggered after editing a customer
	 */
	public function eventAfterEditCustomer($customerId) {
		if ($this->quickbooks_is_connected) {
			// Post changes to QBO
			$this->edit($customerId);
		} else {
			$errorDetail = array(
				'error' => 'QuickBooks is not connected'
			);

			$this->session->data['ipp_error']['warning'] = $errorDetail;
		}
	}
	
	/*public function eventOnDeleteCustomer() {
		
	}*/
}
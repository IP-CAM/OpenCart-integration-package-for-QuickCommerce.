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

class ControllerQCVendor extends QCController {
	protected $tableName = 'qcli_vendor';
	protected $joinTableName = 'vendor';
	protected $joinCol = 'vendor_id';
	protected $foreign = 'Vendor';
	
	function __construct($registry) {
		parent::__construct($registry);		
		parent::before();
	}

	private function loadMetadata() {
		$this->cMeta = $this->em->getClassMetadata('OcVendor');
		$this->aMeta = $this->em->getClassMetadata('OcAddress');
	}
	
	// TODO: I can make a generic one of these, just copying for now...
	protected function getService() {
		$service = new \App\Resource\Vendor($this->em, 'OcVendor');
		return $service;
	}
	
	/** 
	 * Heavy batch operations should be changed to use XMLWriter or
	 * something that doesn't have to load everything into memory?
	 * This should be fine for small or medium-sized stores anyway
	 */
	public function fetch() {
		$this->loadMetadata();
		// getMappings is used to map remote entities unlike mapDoctrineEntity and its wrappers which simply convert between OpenCart/OpenCart DB fields and their Doctrine entity equivalents
		$mappings = $this->getMappings($this->foreign); // Get the remote item mappings

		$c = null;
		$data = array();

		$sService = new \App\Resource\Store($this->em, 'OcStore');
		$cService = new \App\Resource\Vendor($this->em, 'OcVendor');
		//$cgService = new \App\Resource\VendorGroup($this->em, 'OcVendorGroup');
		$aService = new \App\Resource\Address($this->em, 'OcAddress');
		$ccService = new \App\Resource\Country($this->em, 'OcCountry');
		$zService = new \App\Resource\Zone($this->em, 'OcZone');

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

		//$defaultGroup = $cgService->getEntity(1, false);
		$defaultGroup = null;
		$store = $sService->getEntity(1, false);
		$country = $ccService->getEntity(38, false);
		$zone = $zService->getEntity(602, false);

		$reader = new ArrayReader($data);
		$writer = new CallbackWriter(
			function ($item) use (&$c, &$cService, &$aService, &$defaultGroup, &$country, &$zone) {
				try {
					//unset($item['dateAdded']);
					//unset($item['dateModified']);

					// Have to do this manually because I disabled association processing in App\Resource::fillEntity
					if (isset($item['address'])) {
						$address = $item['address'];
						unset($item['address']);
					}

					// Vendor should be tested for a unique email address - we don't want duplicates in OpenCart
					// That might not be the case in QuickBooks?
					$feedId = self::qbId($item['_entity']->getId());
					$exists = (int)$this->listItemExists('email', $item['email']); // TODO: Method defaults maybe?
					$isValid = (!isset($this->error['validation'][$feedId])) ? true : false;

					if ($exists == false) {
						if (!$isValid) return;

						$date = new DateTime();
						$c = $cService->writeItem($item);
						$c->setPassword('D3f@ultP@ssw0rd'); // Does not exist in QBO
						$c->setSalt('default'); // Does not exist in QBO
						$c->setCustomField(''); // Does not exist in QBO
						//$c->setVendorGroup($defaultGroup); // Does not exist in QBO

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

						$cService->updateEntity($c);

						// TODO: I need something to fill in blank spaces for non-nullable string fields in OpenCart
						if (isset($address)) {
							$a = $aService->writeItem($address);
							$a->setFirstname($c->getFirstname());
							$a->setLastname($c->getLastname());
							$a->setCompany(''); // Where in QBO?
							$a->setCountry($country); // Where in QBO?
							$a->setZone($zone); // Where in QBO?
							$a->setVendor($c);
							$aService->updateEntity($a);
							// If default address assign to customer too
						}

						$this->db->query("UPDATE " .  DB_PREFIX . "customer SET store_id = '0' WHERE store_id IS NULL"); // MySQL-only fix for "0" store id - no idea how to work around this in Doctrine
						$this->db->query("UPDATE " .  DB_PREFIX . "customer SET address_id = '0' WHERE address_id IS NULL"); // MySQL-only fix for "0" store id - no idea how to work around this in Doctrine

						$item['_entity']->setOcId($c->getVendorId());
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
				$pre = 'Vendor import error: ';
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

	public function sync() {
		$this->__sync();
	}

	public function getSyncStatuses() {
		$this->__getSyncStatuses();
	}
	
	/**
	 * @param $customerId
     */
	public function add($customerId) {
		$this->getMappings('Vendor');
		$this->getMappings('Address');
		$mappings = $this->mappings;
		
		$this->load->model('customer/customer');
		$this->load->model('customer/customer_group');
		$data = $this->model_customer_customer->getVendor($customerId);
		$address = $this->model_customer_customer->getAddress($data['address_id']); // TODO: Some way to automate this? From here on in it is done already...
		$group = $this->model_customer_customer_group->getVendorGroup($data['customer_group_id']);
		
		$entityService = new QuickBooks_IPP_Service_Vendor();
		$entity = new QuickBooks_IPP_Object_Vendor();
		$entity->setOcId($customerId);
		$cMeta = $this->em->getClassMetadata('OcVendor');
		
		$c = ObjectFactory::createEntity($this->em, 'OcVendor', $data, array('address' => $address, 'customerGroup' => $group));
		
		// Populate entity data
		$this->fillEntity($entity, $mappings['Vendor']['fields'], $cMeta, $c);
		$this->fillEntityObjects('Vendor', $entity, $mappings, $cMeta, $c);
		$this->fillEntityRefs($entity, $mappings['Vendor']['refs'], $c);
		
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
		$this->getMappings('Vendor');
		$this->getMappings('Address');
		$mappings = $this->mappings;
		
		// Load OpenCart models and fetch any required data
		// TODO: When Doctrine integration is complete this won't be necessary
		$this->load->model('customer/customer');
		$this->load->model('customer/customer_group');
		$data = $this->model_customer_customer->getVendor($customerId);
		$address = $this->model_customer_customer->getAddress($data['address_id']); // TODO: Some way to automate this? From here on in it is done already...
		$group = $this->model_customer_customer_group->getVendorGroup($data['customer_group_id']);
		
		// Create the service client
		$entityService = new QuickBooks_IPP_Service_Vendor();
		$entity = null; //new QuickBooks_IPP_Object_Vendor();
		$feedId = null;

		// Set our $feedId and $entity variables - this process is repeated in most QCController classes
		// This method handles the log
		$this->setRemoteEntityVars($feedId, $entity, $customerId, $feedEntity);

		if ($entity) {
			$entity->setOcId($customerId); // Set the corresponding OpenCart "entity" ID
			
			// Create a blank OpenCart entity using Doctrine
			$cMeta = $this->em->getClassMetadata('OcVendor'); // Get the Doctrine class metadata
			// Returns an array representation of the OpenCart entity using Doctrine metadata, populated with any provided data
			$c = ObjectFactory::createEntity($this->em, 'OcVendor', $data, array('address' => $address, 'customerGroup' => $group));
			
			// Populate the returned entity with OpenCart data using the appropriate mapping
			$this->fillEntity($entity, $mappings['Vendor']['fields'], $cMeta, $c);
			$this->fillEntityObjects('Vendor', $entity, $mappings, $cMeta, $c); // Fill any objects
			$this->fillEntityRefs($entity, $mappings['Vendor']['refs'], $c); // Fill references

			$this->export($entityService, $entity, false);
		} else {
			// If the customer was deleted in QBO re-add it
			$this->add($customerId);
		}
	}
	
	private static function explodeSubVendor($item, $fullyQualified = false) {
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
	protected function export (QuickBooks_IPP_Service_Vendor &$service, QuickBooks_IPP_Object_Vendor &$item, $asXml = false) {
		$this->_export($service, $item, $asXml);
	}
	
	/**
	 * Event hook triggered before adding a customer
	 */
	public function eventBeforeAddVendor($customerId) {
		
	}
	
	/**
	 * Event hook triggered after adding a customer
	 */
	public function eventAfterAddVendor($customerId) {
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
	public function eventBeforeEditVendor() {
		
	}
	
	/**
	 * Event hook triggered after editing a customer
	 */
	public function eventAfterEditVendor($customerId) {
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
	
	/*public function eventOnDeleteVendor() {
		
	}*/
}
<?php
require_once(DIR_QC . 'vendor/autoload.php');
require_once(DIR_SYSTEM . 'library/quickcommerce/doctrine.php');

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\NamingStrategy;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Tools\EntityGenerator;
use Doctrine\ORM\Tools\DisconnectedClassMetadataFactory;
use Doctrine\Common\Util\Inflector;

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Reader\ArrayReader;
use Ddeboer\DataImport\Reader\OneToManyReader;
use Ddeboer\DataImport\Writer\ArrayWriter;
use Ddeboer\DataImport\Writer\CallbackWriter;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Ddeboer\DataImport\ItemConverter\MappingItemConverter;
use Ddeboer\DataImport\ItemConverter\NestedMappingItemConverter;
use Ddeboer\DataImport\ValueConverter\DateTimeValueConverter;
use Doctrine\Common\Collections\Collection;

/**
 * Class QCController
 */
abstract class QCController extends Controller {
	protected $error = array();
	/**
	 * @var bool
     */
	protected $quickbooks_is_connected = false;
	/**
	 * @var
     */
	protected $Context;
	/**
	 * @var
     */
	protected $IntuitAnywhere;
	/**
	 * @var
     */
	protected $IPP;
	/**
	 * @var
     */
	protected $username;
	/**
	 * @var
     */
	protected $tenant;
	/**
	 * @var
     */
	protected $quickbooks_CompanyInfo;
	/**
	 * @var
     */
	protected $realm;
	/**
	 * @var
     */
	protected $settings;
	/**
	 * @var EntityManager
     */
	public $em;
	/**
	 * @var string
     */
	public $feedMap;
	/**
	 * @var SimpleXMLElement
     */
	public $mapXml;

	/**
	 * @var array
     */
	protected $mappings = array();

	/**
	 * @var string
     */
	protected $tableName = '';

	/**
	 * @var string
	 */
	protected $foreign = '';

	/**
	 * @var string
     */
	protected $foreignType = 'list';
	
	/* Example
	protected $tableName = 'qcli_account';
	protected $joinTableName = 'order';
	protected $joinCol = 'account_id';
	protected $foreign = 'Account';
	protected $foreignType = 'account';*/
	
	public $sandbox;
	
	public $oauth_url;
	public $success_url;
	public $disconnect_url;
	public $menu_url;

	public $token;
	public $oauth_consumer_key;
	public $oauth_consumer_secret;

	
	public function getTableName() {
		return $this->tableName;
	}
	
	public function getJoinColumn() {
		return $this->joinCol;
	}

	// These have also been included via mod in the base controller class
	protected function getPostVar($key, $default = null) {
		return $this->getRequestVar($key, 'post', $default);
	}

	protected function getRequestVar($key, $type = 'get', $default = null) {
		$types = array('get', 'post');
		if (!in_array($type, $types)) {
			throw new Exception('Invalid request type');
		}

		if (isset($this->request->{$type}[$key])) {
			if (!empty($this->request->{$type}[$key])) {
				return $this->request->{$type}[$key];
			}
		}

		return $default;
	}

	/**
	 * Returns the mappings for converting an OpenCart doctrine entity to a QuickBooks (or another remote) entity. 
	 * To convert between OpenCart/OpenCart DB fields and their Doctrine entity equivalents try mapDoctrineEntity. 
	 *
	 * @param $entityName
	 * @param $export Setting this flag to true will flip the mappings
	 * @return mixed
     */
	protected function getMappings($entityName, $export = false) {
		if (!isset($this->mappings[$entityName])) {
			$this->buildMappings($entityName, $export);
		}
		
		return $this->mappings[$entityName];
	}

	/**
	 * Builds mappings used for converting an OpenCart doctrine entity to a QuickBooks entity.
	 *
	 * @param null $entityName
	 * @param bool|false $export
     */
	protected function buildMappings($entityName = null, $export = false) {
		$entityName = ($entityName != null) ? $entityName : $this->foreign;
		EntityMapper::mapEntities($this->em, $entityName, $this->mapXml, $this->mappings, $export);
	}

	/**
	 * @param $registry
	 * @throws Exception
	 * @throws \Doctrine\DBAL\DBALException
	 * @throws \Doctrine\ORM\ORMException
     */
	function __construct($registry) {
		/*if (empty($this->tableName)) // TODO: Interface yo
			throw new Exception('Mapping table name ($tableName) was not specified in the extending controller class');
		if (empty($this->joinTableName)) // TODO: Interface yo
			throw new Exception('Join table name ($joinTableName) was not specified in the extending controller class');
		if (empty($this->joinCol)) // TODO: Interface yo
			throw new Exception('Join column name ($joinCol) was not specified in the extending controller class');*/
		
		parent::__construct($registry);
		
		$di = new DoctrineInitializer($this, $registry);
	}
	
	protected function setIppUrls() {
		$this->oauth_url = htmlspecialchars_decode($this->url->link('module/qc_admin/connect', 'token=' . $this->session->data['token'], 'SSL'));
		$this->success_url = htmlspecialchars_decode($this->url->link('module/qc_admin/success', 'token=' . $this->session->data['token'], 'SSL'));
		$this->disconnect_url = htmlspecialchars_decode($this->url->link('module/qc_admin/disconnect', 'token=' . $this->session->data['token'], 'SSL'));
		$this->menu_url = htmlspecialchars_decode($this->url->link('module/qc_admin/menu', 'token=' . $this->session->data['token'], 'SSL'));
	}
	
	protected function setIppKeys() {
		$this->sandbox = false;
		// Your application token (Intuit will give you this when you register an Intuit Anywhere app)
		if (isset($this->settings['qc_mode'])) {
			$this->sandbox = (bool)$this->settings['qc_mode'];
		}
		
		if ($this->sandbox == false) {
			$this->token = $this->settings['qc_prod_ipp_token'];
			$this->oauth_consumer_key = $this->settings['qc_prod_ipp_key'];
			$this->oauth_consumer_secret = $this->settings['qc_prod_ipp_secret'];
		} else {
			$this->token = $this->settings['qc_dev_ipp_token'];
			$this->oauth_consumer_key = $this->settings['qc_dev_ipp_key'];
			$this->oauth_consumer_secret = $this->settings['qc_dev_ipp_secret'];
		}
	}
	
	// Instantiate our Intuit Anywhere auth handler 
	// 
	// The parameters passed to the constructor are:
	//	$dsn					
	//	$oauth_consumer_key		Intuit will give this to you when you create a new Intuit Anywhere application at AppCenter.Intuit.com
	//	$oauth_consumer_secret	Intuit will give this to you too
	//	$this_url				This is the full URL (e.g. http://path/to/this/file.php) of THIS SCRIPT
	//	$that_url				After the user authenticates, they will be forwarded to this URL
	// 
	private function initIntuitAnywhere($dsn) {
		// You should set this to an encryption key specific to your app
		$encryption_key = (isset($this->settings['qc_enc_key'])) ? $this->settings['qc_enc_key'] : false;
		$this->IntuitAnywhere = new QuickBooks_IPP_IntuitAnywhere($dsn, $encryption_key, $this->oauth_consumer_key, $this->oauth_consumer_secret, $this->oauth_url, $this->success_url);
	}

	/**
	 *
     */
	protected function before() {
		/**
		 * Intuit Partner Platform configuration variables
		 * 
		 * See the scripts that use these variables for more details. 
		 * 
		 * @package QuickBooks
		 * @subpackage Documentation
		 */

		// Turn on some error reporting
		error_reporting(E_ALL);
		ini_set('display_errors', 1);

		// Require the library code
		require_once DIR_QC . 'vendor/consolibyte/quickbooks/QuickBooks.php';
		
		$this->load->model('setting/setting');
		$this->settings = $this->model_setting_setting->getSetting('qc', 0);
		
		// Your OAuth consumer key and secret (Intuit will give you both of these when you register an Intuit app)
		// 
		// IMPORTANT:
		//	To pass your tech review with Intuit, you'll have to AES encrypt these and 
		//	store them somewhere safe. 
		// 
		// The OAuth request/access tokens will be encrypted and stored for you by the 
		//	PHP DevKit IntuitAnywhere classes automatically. 
		$this->setIppKeys();
		$this->setIppUrls();

		// This is a database connection string that will be used to store the OAuth credentials 
		// $dsn = 'pgsql://username:password@hostname/database';
		// $dsn = 'mysql://username:password@hostname/database';

		// TODO: Test DSN, substitute OC config if we can't connect or there is an error
		//$dsn = (isset($this->settings['qc_dsn'])) ? $this->settings['qc_dsn'] : false;
		$dsn = 'mysqli://' . DB_USERNAME . ':' . DB_PASSWORD . '@' . DB_HOSTNAME . '/' . DB_DATABASE;

		// Do not change this unless you really know what you're doing!!!  99% of apps will not require a change to this.
		$this->username = 'DO_NOT_CHANGE_ME';

		// The tenant that user is accessing within your own app
		$this->tenant = 12345;
		
		if ($dsn) {
			try {
				// Initialize the database tables for storing OAuth information
				if (!QuickBooks_Utilities::initialized($dsn))
				{
					// Initialize creates the neccessary database schema for queueing up requests and logging
					QuickBooks_Utilities::initialize($dsn);
				}

				$this->initIntuitAnywhere($dsn);
			} catch (Exception $e) {
				echo 'No DSN?';
			}

			// Are they connected to QuickBooks right now? 
			if ($this->IntuitAnywhere->check($this->username, $this->tenant) and 
				$this->IntuitAnywhere->test($this->username, $this->tenant))
			{
				// Yes, they are 
				$this->quickbooks_is_connected = true;

				// Set up the IPP instance
				$this->IPP = new QuickBooks_IPP($dsn);

				// Get our OAuth credentials from the database
				$creds = $this->IntuitAnywhere->load($this->username, $this->tenant);

				// Tell the framework to load some data from the OAuth store
				$this->IPP->authMode(
					QuickBooks_IPP::AUTHMODE_OAUTH, 
					$this->username, 
					$creds);

				$this->IPP->sandbox($this->sandbox);

				$this->IPP->flavor(QuickBooks_IPP_IDS::FLAVOR_ONLINE); // We only care about QuickBooks Online for now

				// Print the credentials we're using
				//print_r($creds);

				// This is our current realm
				$this->realm = $creds['qb_realm'];

				// Load the OAuth information from the database
				$this->Context = $this->IPP->context();

				// Get some company info
				$CompanyInfoService = new QuickBooks_IPP_Service_CompanyInfo();
				$this->quickbooks_CompanyInfo = $CompanyInfoService->get($this->Context, $this->realm);
			}
			else
			{
				// No, they are not
				$this->quickbooks_is_connected = false;

				$errorDetail = array(
					'error' => 'QuickBooks is not connected'
				);

				$this->session->data['ipp_error']['warning'] = $errorDetail;
			}
		} else {
			$errorDetail = array(
				'error' => 'QuickCommerce DSN is either not set or invalid'
			);

			$this->session->data['ipp_error']['warning'] = $errorDetail;
		}
	}

	protected function importEntity(&$entity, $mappings, &$meta, &$data) {
		if (!isset($entity)) {
			//throw new Exception('Attempting to import an entity that is null or undefined'); // TODO: Or log/display somehow
			return null;
		} else {
			$data['_entity'] = $entity; // Store a reference to the entity for processing later
		}
		
		// TODO: Need to do something like in mapDoctrineEntity so 
		$objects = array();
		$fieldMappings = $mappings['fields'];
		$objectMappings = (isset($mappings['objects'])) ? $mappings['objects'] : null;
		$assocMappings = (isset($mappings['assoc'])) ? $mappings['assoc'] : null;
		$associations = $meta->associationMappings;
		$fields = $meta->fieldMappings;

		// TODO: This is almost like QcController::fillEntity
		$columns = $meta->columnNames;
		foreach ($fieldMappings as $foreign => $local) {
			if (is_array($columns) && array_key_exists($local, $columns)) {
				//$entity->{'set' . $foreign}($data[$columns[$local]]);
				$data[$local] = $entity->{'get' . $foreign}(); // TODO: This isn't going to work if foreign prop isn't uc worded
			} elseif (empty($columns)) {
                echo 'Mapping property ' . $foreign . ' failed. $columns is an empty array';
            }
		}
		
		if ($objectMappings) {
			foreach ($objectMappings as $local => $foreign) {
				$path = explode('->', $foreign); // eg. array('EmailAddress:PrimaryEmailAddr', 'Address')
				$prop = array_shift($path); // Create the entity, returns eg. EmailAddress:PrimaryEmailAddr

				// TODO: Stuff this comment into the method description
				// If the property is a QuickBooks object of a specific type (not an entity) in QuickBooks Online
				// BUT in OpenCart it IS an entity ({qbo object}->{oc entity} mapping) then the type must be specified
				// in the .dcm.xml mapping configuration
				$parts = explode(':', $prop);
				$nodeType = false;

				if (count($parts) > 1 && (version_compare(phpversion(), '7', '<'))) {
					list($nodeName, $nodeType) = $parts;
				} elseif (count($parts) > 1) {
					list($nodeType, $nodeName) = $parts;
				} else {
					// TODO: This is QBO specific... to bind to for say eg. schemaless XML data we'll have to do something else here
					// TODO: This is designed for use with Keith Palmer's QuickBooks SDK
					// I don't have time to do this right now
					//throw new Exception('Binding QBO object to OpenCart entity failed - no object type specified');
				}

				/*$object = (array_key_exists($nodeName, $stack)) ? $stack[$nodeName] : false;
				if (!$object && $nodeType != false) {
					$object = ObjectFactory::createObject($this->em, $nodeType);
				}

				if ($object != false) {*/
					$node = $entity->{'get'. $nodeName}();
					if ($node) {
						$current = array_shift($path);
						$val = $node->{'get'. $current}();
						$isDateTime = strtolower($fields[$local]['type'] == 'datetime');
						$isDate = strtolower($fields[$local]['type'] == 'date');
						// Check for time strings
						if (isset($fields[$local]) && ($isDate || $isDateTime)) {
							if ($isDate) {
								$val = new Date($val);
							} elseif ($isDateTime) {
								$val = new DateTime($val);
							}
						}

						$data[$local] = $val;
					}
				//}
			}
		}
		
		if (isset($assocMappings)) {
			foreach ($assocMappings as $local => $foreign) {
				// TODO: Stuff this comment into the method description
				// If the property is a QuickBooks object of a specific type (not an entity) in QuickBooks Online
				// BUT in OpenCart it IS an entity ({qbo object}->{oc entity} mapping) then the type must be specified
				// in the .dcm.xml mapping configuration
				$parts = explode(':', $foreign);
				$nodeType = false;

				if (count($parts) > 1 && (version_compare(phpversion(), '7', '<'))) {
					list($nodeName, $nodeType) = $parts;
				} elseif (count($parts) > 1) {
					list($nodeType, $nodeName) = $parts;
				} else {
					// TODO: This is QBO specific... to bind to for say eg. schemaless XML data we'll have to do something else here
					// TODO: This is designed for use with Keith Palmer's QuickBooks SDK
					// I don't have time to do this right now
					//throw new Exception('Binding QBO object to OpenCart entity failed - no object type specified');
				}

				if ($nodeType != false) {
					$assocMeta = $this->em->getClassMetadata($associations[$local]['targetEntity']);					
					$nested = $entity->{'get'. $nodeName}();
					
					$child = array();
					$childMappings = $this->getMappings('Address');  // TODO: No hardcoding
					self::importEntity($nested, $childMappings, $assocMeta, $child);
					$data[$local] = $child;
				}
			}
		}
	}
	
	// TODO: This will trigger unhandled error if null (no mapped firlds in a given entity)
	// Argument 1 passed to ControllerQCCustomer::export() must be an instance of QuickBooks_IPP_Service_Customer, null given
	/**
	 * Generic method to set entity field values using provided data mappings. This works with both Doctrine and QBO entities.
	 *
	 * <Customer>
	 *   ...
	 *   <GivenName>Bob</GivenName>
	 *   <PrimaryEmailAddr>
	 *     <Address>user@company.com</Address>
	 *   </PrimaryEmailAddr>
	 *   ...
	 * </Customer>
	 */
	public function fillEntity(&$entity, $fieldMappings, &$meta, $data) {
		$columns = $meta->columnNames;
		foreach ($fieldMappings as $foreign => $local) {
			if (array_key_exists($local, $columns) && array_key_exists($columns[$local], $data)) {
				$entity->{'set' . $foreign}($data[$columns[$local]]);
			}
		}
	}
	
	// TODO: This will trigger unhandled error if null (no mapped firlds in a given entity)
	// Argument 1 passed to ControllerQCCustomer::export() must be an instance of QuickBooks_IPP_Service_Customer, null given
	/**
	 * <Customer>
	 *   ...
	 *   <GivenName>Bob</GivenName>
	 *   <PrimaryEmailAddr>
	 *     <Address>user@company.com</Address>
	 *   </PrimaryEmailAddr>
	 *   ...
	 * </Customer>
	 */
	public function fillEntityRefs(&$entity, $refMappings, $data) {
		$query = "SELECT * FROM " . DB_PREFIX . $this->tableName . " WHERE feed_id = '" . self::qbId($entity->getId()) . "' AND oc_entity_id = '" . $entity->getOcId() . "'";
		$query = $this->db->query($query);
		
		if (!empty($query->rows)) {
			$ref =  $query->rows[0];
			$data = array_merge($ref, $data);
		}
		
		// Assign values
		foreach ($refMappings as $local => $foreign) {
			
			if (array_key_exists($local, $data)) {
				$entity->{'set' . $foreign}($data[$local]);
			}
		}
	}

	/**
	 * Updates extended entity information in the database with values supplied from QuickBooks Online.
	 *
	 * @param $entity
	 * @param $refMappings
	 */
	public function updateEntityRefs(&$entity, $refMappings) {
		if ($entity instanceof QuickBooks_IPP_Object) {
			$query = "UPDATE " . DB_PREFIX . $this->tableName . " SET ";
			
			$values = array();
			// Callback...
			// TODO: Sanitize references - if the property doesn't exist we don't want to set it
			// Maybe return a notic but whatever, we can do that later
			// For now, just unset
			foreach ($refMappings as $local => $foreign) {
				$col = $local;
				// TODO: Is this a real property? method_exists isn't gonna
				$val = $entity->{'get' . $foreign}();


				// Is it a QuickBooks ID? They come back wrapped so we need to check for it
				if (preg_match('/{-(\d+)\}/', $val)) {
					array_push($values, $col . " = '" . self::qbId($val) .  "'");
				} else {
					// String booleans requiring a cast?
					if ($val === 'true' || $val === 'false') {
						$val = (int)filter_var($val, FILTER_VALIDATE_BOOLEAN);
					}
					
					if ($val === null) {
						// TODO: Check if nullable first
						array_push($values, $col . " = NULL");
					} else { 
						array_push($values, $col . " = '" . $val .  "'");
					}
				}
			}
			
			$values = implode(', ', $values);
			$query .= $values;
			
			$query .= " WHERE feed_id = '" . self::qbId($entity->getId()) . "' AND oc_entity_id = '" . $entity->getOcId() . "'";

			$this->db->query($query);
			// TODO: Attach callback via decorate()*/
		}
	}
	
	// Testing an alternative way to build mappings... do not use
	/*protected static function buildObject($parent, $json) {
			$obj = json_decode($json);
			var_dump($obj);
			exit;
			$currentNode = null;
			// Grab the next node name in the xpath or return if empty
			$parts = explode('/', trim($xpath, '/'));
			if (is_array($parts) && count($parts) > 0) {
				$currentNode = array_shift($parts);
			} else {
				return;
			}
			
			if (isset($currentNode)) {
				echo 'currentNode is set';
			}

			// Re-join the remainder of the array as an xpath expression and recurse
			$rest = implode('/', $parts);
			return self::buildObject($parent, $rest);
	}*/
	
	// TODO: Somthing recursive? Could get nuts after a certain number of levels...
	// TODO: Flip array when export flag is set
	/**
	 * Map paths are going to look like $classType:$prop->val
	 * Consider the following QuickBooks customer entity
	 * <Customer>
	 *   ...
	 *   <GivenName>Bob</GivenName>
	 *   <PrimaryEmailAddr>
	 *     <Address>user@company.com</Address>
	 *   </PrimaryEmailAddr>
	 *   ...
	 * </Customer>
	 *                                                     $classType  :$prop           ->val
	 * For instance, to map PrimaryEmailAddress.Address to OpenCart's customer email field we would specify PrimaryEmailAddr:Email->Address as the binding (@attr=foreign)
	 * <field name="email" type="string" column="email" length="96" nullable="false" foreign="PrimaryEmailAddr:Email->Address">
     *   <options>
     *      <option name="fixed"/>
     *   </options>
     * </field>
	 */
	// TODO: This 
	public function fillEntityObjects($type, &$entity, &$mappings, &$meta, $data) {
		//if (!($entity instanceof QuickBooks_IPP_Object));
		// Fail if not a Doctrine entity or a QuickBooks IPP Object
		
		$objects = array();
		$objectMappings = &$mappings[$type]['objects'];
		$assocMappings = &$mappings[$type]['assoc'];
		$columns = $meta->columnNames;
		$associations = $meta->associationMappings;
		
		$stack = array();

		$createAndStackObject = function ($localProperty, $localType, $foreignMapping) use (&$stack, &$data, &$columns) {
			$path = explode('->', $foreignMapping); // eg. array('EmailAddress:PrimaryEmailAddr', 'Address')
			$nodeName = array_shift($path); // Create the entity, returns eg. EmailAddress:PrimaryEmailAddr

			$object = $this->getStackObject($stack, $nodeName);
			if (!$object && $localType != false) {
				$object = ObjectFactory::createObject($this->em, $localType);
			}

			if ($object != false) {
				$current = array_shift($path);
				if (array_key_exists($localProperty, $columns) && array_key_exists($columns[$localProperty], $data)) {
					$object->{'set' . $current}($data[$columns[$localProperty]]);
				}

				$stack[$nodeName] = $object;
			}
		};
		
		foreach ($objectMappings as $localProperty => $objectMapping) {
			if (is_array($objectMapping)) {
				foreach ($objectMapping as $localType => $foreignMapping) {
					$createAndStackObject($localProperty, $localType, $foreignMapping);
				}
			} elseif (is_string($objectMapping)) {
				$parts = explode(':', $objectMapping);
				$localType = $parts[0];
				$foreignMapping = $parts[1];

				$createAndStackObject($localProperty, $localType, $foreignMapping);
			}


		}
		
		// NOTE: Keith Palmer's lib doesn't work quite as expected and as result 
		// I will have to change the way I'm creating these nested objects
		// I can achieve same results using just mappings for now
		
		foreach ($stack as $prop => $obj) {
			$entity->{'set' . $prop}($obj);
		}

		foreach ($assocMappings as $local => $foreign) {
			// TODO: Stuff this comment into the method description
			// If the property is a QuickBooks object of a specific type (not an entity) in QuickBooks Online
			// BUT in OpenCart it IS an entity ({qbo object}->{oc entity} mapping) then the type must be specified
			// in the .dcm.xml mapping configuration
			$parts = explode(':', $foreign);
			$nodeType = false;
			
			//var_dump($parts);

			if (count($parts) > 1 && (version_compare(phpversion(), '7', '<'))) {
				list($nodeName, $nodeType) = $parts;
			} elseif (count($parts) > 1) {
				list($nodeType, $nodeName) = $parts;
			} else {
				// TODO: This is QBO specific... to bind to for say eg. schemaless XML data we'll have to do something else here
				// TODO: This is designed for use with Keith Palmer's QuickBooks SDK
				// I don't have time to do this right now
				//throw new Exception('Binding QBO object to OpenCart entity failed - no object type specified');
			}
			
			$object = false;
			if ($nodeType != false) {
				// NOTE: Keith Palmer's lib doesn't work quite as expected and as result 
				// I will have to change the way I'm creating these nested objects
				// I can achieve same results using just mappings for now
				$object = ObjectFactory::createObject($this->em, $nodeName);
				
				if ($object) { // TODO: This check sucks but it's better than nothing
					$assocMeta = $this->em->getClassMetadata($associations[$local]['targetEntity']); // TODO: Goddamn hardcoding
					$columns = $assocMeta->columnNames;
					
					$this->fillEntity($object, $mappings[$nodeType]['fields'], $assocMeta, $data[$associations[$local]['fieldName']]);
					$entity->{'set' . $nodeName}($object);
				}
			}
		}
	}

	private function getStackObject(&$stack, $nodeName) {
		return (array_key_exists($nodeName, $stack)) ? $stack[$nodeName] : false;
	}

	private function stackObject(&$stack, &$columns, $data, $localProperty, $foreignProperty) {
		$parts = explode(':', $foreignProperty);

		$nodeType = $parts[0];
		$path = explode('->', $parts[1]); // eg. array('EmailAddress:PrimaryEmailAddr', 'Address')
		$nodeName = array_shift($path); // Create the entity, returns eg. EmailAddress:PrimaryEmailAddr

		$object = (array_key_exists($nodeName, $stack)) ? $stack[$nodeName] : false;
		if (!$object && $nodeType != false) {
			$object = ObjectFactory::createObject($this->em, $nodeType);
		}

		if ($object != false) {
			$current = array_shift($path);
			if (array_key_exists($localProperty, $columns) && array_key_exists($columns[$localProperty], $data)) {
				//var_dump($current);
				$object->{'set' . $current}($data[$columns[$localProperty]]);
			}

			$stack[$nodeName] = $object;
		}
	}

	
	
	/**
	 * Converts...
	 */
	public function mapDoctrineEntity(&$mappings, $config = array(), $children = false, $foreign = true) {
		DoctrineEntityMapper::mapDoctrineEntity($this, $mappings, $config, $children, $foreign);
	}

	protected abstract function getService(); // Temporary?

	/**
	 * return QuickBooks_IPP_Object_Item
	 */
	public function get($id, $className = '') {
		$className = (!empty($className)) ? $className : "QuickBooks_IPP_Service_" . $this->foreign;
		$service = (!empty($service)) ? $service : new $className();

		// Get the existing entity
		$entities = $service->query($this->Context, $this->realm, "SELECT * FROM " . $this->foreign . " WHERE Id = '" . $id . "'");
		$entity = ($entities && count($entities) > 0) ? $entities[0] : null;

		return $entity;
	}

	/**
	 * @param int | array $id
	 * @param string $className
	 * @return QuickBooks_IPP_Object_Item (or a collection)
	 */
	public function getMetaData($id, $className = '') {
		$className = (!empty($className)) ? $className : "QuickBooks_IPP_Service_" . $this->foreign;
		$service = (!empty($service)) ? $service : new $className();

		$query = "SELECT MetaData FROM " . $this->foreign;
		if (is_int($id)) {
			$query .= " WHERE Id = '" . $id . "'";
			$query .= " MAXRESULTS 1000"; // TODO: How to set limit?
		} elseif (is_array($id) && count($id) > 0) {
			// TODO: Filter keys - we only want numerics
			$query .= " WHERE Id IN ('" . implode("', '", $id) . "')";
			$query .= " MAXRESULTS 1000"; // TODO: How to set limit?
		}

		// Get the existing entity
		$entities = $service->query($this->Context, $this->realm, $query);
		$entity = ($entities && count($entities) > 0) ? $entities[0] : null;

		return ($entities && count($entities) > 1) ? $entities : $entity;
	}

	/**
	 * @param $feedId
	 * @param $entity
	 * @param $localId
	 * @param $feedEntity QuickBooks_IPP_Object | int The remote entity or its corresponding ID
	 */
	protected function setRemoteEntityVars(&$feedId = null, &$entity, $localId = null, &$feedEntity = false) {
		if (!($localId > 0)) {
			throw new Exception('Something went wrong in setRemoteEntityVars - a required parameter was not provided');
		}

		if ($feedEntity instanceof QuickBooks_IPP_Object) {
			// An initialized feed entity was provided
			$entity = $feedEntity;
		} else {

			if (!is_int($feedEntity)) {
				// A feed entity ID was not provided
				// Get the feed ID using the provided local ID
				$feedId = (int)$this->getFeedId($localId);
			}

			if ($feedId > 0) {
				$entity = $this->get($feedId);
			}
		}
	}

	/**
	 * @param $ocId
	 * @return bool
     */
	protected function getFeedId($ocId, $tableName = null) {
		$tableName = (isset($tableName)) ? $tableName : $this->tableName;
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . $tableName . " WHERE oc_entity_id = '" . $ocId . "'");

		return ($query->num_rows) ? $query->row['feed_id'] : false;
	}

	/**
	 * @param $feedId
	 * @return bool
	 */
	protected function getOcId($feedId, $tableName = null) {
		$tableName = (isset($tableName)) ? $tableName : $this->tableName;
		$query = $this->db->query("SELECT oc_entity_id FROM " . DB_PREFIX . $tableName . " WHERE feed_id = '" . $feedId . "'");

		return ($query->num_rows) ? $query->row['oc_entity_id'] : false;
	}

	/**
	 * @param $ocId
	 * @return bool
	 */
	protected function getByFeedId($feedId, $tableName = null) {
		$tableName = (isset($tableName)) ? $tableName : $this->tableName;
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . $tableName . " WHERE feed_id = '" . $feedId . "'");

		return ($query->num_rows) ? $query->row : false;
	}

	/**
	 * @param $ocId
	 * @return bool
	 */
	protected function getByOpenCartId($ocId, $tableName = null) {
		$tableName = (isset($tableName)) ? $tableName : $this->tableName;
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . $tableName . " WHERE oc_entity_id = '" . $ocId . "'");

		return ($query->num_rows) ? $query->row : false;
	}

	protected function getCount($filters = array(), $className = '', $service = null) {
		$count = false;

		// Get the count
		$className = (!empty($className)) ? $className : "QuickBooks_IPP_Service_" . $this->foreign;
		$service = (!empty($service)) ? $service : new $className();

		$where = array();

		if (isset($filters['filter_model'])) {
			$where[] = "Name LIKE '%" . $filters['filter_model'] . "%'";
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

	/**
	 * Intuit services allow a maximum of 1000 entities per request, so we need a way to grab all the objects
	 * when importing large inventories
	 */
	protected function iterateCollection($callback, &$data = array(), $start = 0, $pageSize = null, $filters = array(), $orderBy = 'Metadata.LastUpdatedTime') {
		$className = (!empty($className)) ? $className : "QuickBooks_IPP_Service_" . $this->foreign;
		$service = (!empty($service)) ? $service : new $className();
		$limit = (isset($filters['limit']) && is_int($filters['limit'])) ? $filters['limit'] : 0;

		// Get/set count vars
		$processed = 0;
		$count = $this->getCount($filters, $className, $service);

		$pageSize = (!is_null($pageSize)) ? $pageSize : 20; // TODO: Configure this in settings so it's adjustable

        $doCallback = function ($items, &$excludeCount) use (&$callback, &$data, &$processed) {
            foreach ($items as $item) {
                $exclude = false;
				if (is_callable($callback)) {
					// TODO: Might need a call_user_func type thing for older PHP?
					$callback($item, $data, $exclude);
				}
                
                if ($exclude) {
                    $excludeCount++;
                }

				$processed++;
			}
        };
        
        $fillPage = function ($excludeCount) use (&$data, &$doCallback, &$fillPage, &$start, &$filters, $orderBy, $className, &$service){
            $start = $start + $excludeCount;
            $pageSize = $excludeCount;
            $excludeCount = 0;
            
            $items = $this->getOrderedCollection($start, $pageSize, $filters, $orderBy, $className, $service);
            
            $doCallback($items, $excludeCount);
            
            if ($excludeCount > 0) {
                $fillPage($excludeCount);
            }
        };
        
		// Ok, say there are 44 items
		$pages = ceil($count / $pageSize);
		for ($pageCount = 1; $pageCount < $pages + 1; $pageCount++) {
			// 44 items = 5 pages of results
			// pg. 1: 1 * 10 = 10 - 10 = 0 + 1 = start on item 1, end on 10
			// pg. 2: 2 * 10 = 20 - 10 = 10 + 1 = start on item 11, end on 20
			// pg. 3: 3 * 10 = 30 - 10 = 20 + 1 = start on item 21, end on 30
			// pg. 4: 4 * 10 = 40 - 10 = 30 + 1 = start on item 31, end on 40
			// pg. 5: 5 * 10 = 50 - 10 = 40 + 1 = start on item 41, end on 44
            $excludeCount = 0;
			$skip = (int)$start + (int)$processed + 1; // Account for 0 index, add 1

			$items = $this->getOrderedCollection($skip, $pageSize, $filters, $orderBy, $className, $service);
            
            $doCallback($items, $excludeCount);

			// Recursively fill any missing slots in the results
            if ($excludeCount > 0) {
                $fillPage($excludeCount);
            }

			// Return $processed either way
			if (count($data) == 0 && $excludeCount == 0) {
				return $processed;
			} elseif (count($data) == $limit) {
				return $processed;
			}
		}
	}

	/**
	 * @param int $start
	 * @param int $max
	 * @param string $className
	 * @param null $service
	 * @return mixed
     */
	public function getCollection($start = 1, $max = 1000, $className = '', $service = null) {
		$className = (!empty($className)) ? $className : "QuickBooks_IPP_Service_" . $this->foreign;
		$service = (!empty($service)) ? $service : new $className();

		// I think for some reason maybe Keith Palmer's findAll method needs to be updated
		//$items = $service->findAll($this->Context, $this->realm, "SELECT * FROM Item ORDER BY Metadata.LastUpdatedTime", $page, $size);
		$items = $service->query($this->Context, $this->realm, "SELECT * FROM " . $this->foreign . " ORDER BY Metadata.LastUpdatedTime STARTPOSITION " . (int)$start . " MAXRESULTS " . (int)$max);

		if ($items) {
			return $items;
		}
		else
		{
			print($service->lastError($this->Context));
		}

		/*foreach ($items as $item) {
			print('Item Id=' . $item->getId() . ' is named: ' . $item->getName() . '<br>');
		}*/

		return $items;
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

		if (isset($filters['filter_model'])) {
			$where[] = "Name LIKE '%" . $filters['filter_model'] . "%'";
		}

		$where = (count($where) > 0) ? ' WHERE ' . implode(' AND ', $where) : '';
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
	
	/**
	 * Using this helper to parse returned QuickBooks Online ID ie: {-95}
	 * I should probably take a look at implementing a callback for this so it can be configured by module users depending on their feed(s)
	 */
	protected static function qbId($string) {
		return abs((int)strtr($string, array('{' => '', '}' => '')));
	}

	public function link() {
		$feedId = (int)$this->getRequestVar('qbid');
		$qbEntity = $this->get($feedId);

		$id = (int)$this->getRequestVar($this->joinCol);
		$qbEntity->setOcId($id);

		$this->_writeListItem($qbEntity, null, false, true); // Set final parameter to true to purge any attached records
	}
	
	protected function doSync($id = null) {
		$service = $this->getService();

		// Make sure there's somethign to sync
		if (!isset($this->request->get[$this->joinCol])) return;

		$id = (is_null($id) || !is_int($id)) ? (int)$this->request->get[$this->joinCol] : $id;
		$feedId = (int)$this->getFeedId($id);
		
		$qbEntity = $this->get($feedId);
		if (!$qbEntity) {
			$this->add($id);
			
			return;
		}
		
		$tRemote = new DateTime($qbEntity->getMetaData()->getLastUpdatedTime()); // TODO: Might have to adjust for locales later
		
		$entity = $service->getEntity($id, false); // Serialize
		$tLocal = $entity->getDateModified();
		
		if ($tRemote > $tLocal) {
			//echo 'remote newer: do something to fetch';
			$this->pull($entity, $qbEntity);
		} elseif ($tLocal > $tRemote) {
			//echo 'local newer: do something push';
			$this->edit($entity, $qbEntity); // This uses less overhead
		} else {
			// Dev ONLY comment out when done
			//$this->pull($entity, $qbEntity);
		}
	}

	// TODO: Add a property called allowSync or something so not all controllers have this exposed
	protected function __sync() {
		if (!isset($this->request->post['selected'])) {
			$this->doSync();
		} else {
			$service = $this->getService();
			foreach ($this->request->post['selected'] as $selected) {
				$id = $selected;
				$feedId = (int)$this->getFeedId($id);

				$qbEntity = $this->get($feedId);
				if (!$qbEntity) {
					$this->add($id);

					return;
				}

				$tRemote = new DateTime($qbEntity->getMetaData()->getLastUpdatedTime()); // TODO: Might have to adjust for locales later
				$entity = $service->getEntity($id, false); // Serialize
				$tLocal = $entity->getDateModified();

				if ($tRemote > $tLocal) {
					//echo 'remote newer: do something to fetch';
					$this->pull($entity, $qbEntity);
				} elseif ($tLocal > $tRemote) {
					//echo 'local newer: do something push';
					$this->edit($entity, $qbEntity); // This uses less overhead
				} else {
					// Dev ONLY comment out when done
					//$this->pull($entity, $qbEntity);
				}
			}
		}
	}

	/**
	 * Adding functionality to autosync - should be able to disable from admin
	 * @param $info
	 */
	protected function __getSyncStatuses(&$info = false) {
		if (!isset($this->request->post['selected'])) {
			//$this->doSync();
		}

		$data = array();
		$ids = array();

		$service = $this->getService();

		// Build an array of remote ids to fetch so we can grab them all in one go
		foreach ($this->request->post['selected'] as $selected) {
			$id = $selected;

			$feedId = (int)$this->getFeedId($id);

			$ids[$id] = ($feedId > 0) ? $feedId : null;
		}

		$remoteIds = array_filter(array_values($ids), function ($value) {
			return (!is_int($value)) ? false : true;
		});

		$meta = array();
		$response = $this->getMetaData($remoteIds);

		if (is_array($response)) {
			foreach ($response as $obj) {
				$objId = $obj->getId();
				$meta[self::qbId($objId)] = $obj;
			}
		} elseif ($response != null) {
			$objId = $response->getId();
			$meta[self::qbId($objId)] = $response;
		}

		foreach ($this->request->post['selected'] as $selected) {
			$id = $selected;

			if (is_null($ids[$id]) || !is_int($ids[$id])) {
				$data[$id] = 'localonly';
			} else {
				if (isset($meta[$ids[$id]])) {
					$tRemote = new DateTime($meta[$ids[$id]]->getMetaData()->getLastUpdatedTime()); // TODO: Might have to adjust for locales later

					$entity = $service->getEntity($id, false); // Serialize
					if (method_exists($entity, 'getDateModified')) {
						$tLocal = $entity->getDateModified(); // Name List Entity
					} else {
						$tEntity = $entity->getTransaction();
						if ($tEntity != null) {
							$tLocal = $tEntity->getDateModified();
						}
					}


					if ($tRemote > $tLocal) {
						$data[$id] = 'remotenewer';
						try {
							// TODO: Add an option to turn off autosync
							$this->doSync($id);

							// If all good....
							$data[$id] = 'ok';
						} catch (Exception $e) {
							// Do nothing, just ignore
							// This is a catch all for issues like accounts not being set etc.
							$data[$id] = 'localnewer';
						}

					} else {
						$data[$id] = 'ok';
					}
				} else {
					$data[$id] = 'unlinked'; // TODO: Discern why
				}
			}

			if (is_array($info)) {
				$data[$id] = array(
					'status' => $data[$id],
					'local' => $tLocal,
					'remote' => $tRemote
				);
			}
		}

		if (!is_array($info)) {
			$this->sendResponse($data);
		} else {
			$info = $data;
		}
	}

	protected function isUnique($table, $col, $value) {
		$unique = true;

		$query = $this->db->query("SELECT count(*) AS count FROM " . DB_PREFIX . $table . " WHERE " . $col . " = '" . $value . "'");
		if (count($query->rows) > 0) {
			$count = (int)$query->row['count'];
			if ($count > 0) $unique = false;
		}

		return $unique;
	}

	protected function like($table, $col, $value) {
		$like = false;
		$sql = "SELECT * FROM " . DB_PREFIX . $table . " WHERE LOWER(" . $col . ") LIKE '" . $value . "'";

		$query = $this->db->query($sql);

		//var_dump($sql);

		if (count($query->rows) > 0) {
			$count = (int)count($query->rows);
			if ($count > 0) $like = $query->rows;
		}

		return $like;
	}

	protected function listItemExists($col, $value) {
		$ocEntityId = false;
		$unique = true;

		$query = $this->db->query("SELECT " . $this->joinCol . " AS oc_entity_id FROM " . DB_PREFIX . $this->joinTableName . " WHERE " . $col . " = '" . $this->db->escape($value) . "'");
		if (count($query->rows) > 0) {
			$count = (int)count($query->rows);
			if ($count > 0) $unique = false;

			$ocEntityId = $query->row['oc_entity_id'];
		}

		if ($unique == false) {
			// TODO: Log this!
			//throw new Exception('listItemExists query failed - ' . $count . ' rows were detected sharing the same oc_entity_id');
		}

		return $ocEntityId;
	}

	protected function mapSelectedArray($key, $selected) {
		$ids = $this->request->post[$key];
		$selected = $this->request->post[$selected];

		if (isset($selected)) {
			if (count($selected) > 0) {
				$selectedIds = array_fill_keys($selected, null);

				foreach ($selectedIds as $id => $val) {
					if (isset($ids) && isset($ids[$id])) {
						if (is_numeric($ids[$id])) {
							$selectedIds[$id] = (int)$ids[$id];
						}
					} else {
						$selectedIds[$id] = $id;
					}
				}
			}
		}

		if (!$selectedIds) {
			return false;
		} else {
			return $selectedIds;
		}
	}
	
	protected function exists($table, $col, $value) {
		$exists = false;
		
		return (!$this->isUnique($table, $col, $value));
	}
	
	/**
	 * This creates a qcli_{tablename} entry in the database
	 * Property name specifies entity field to use for OcId
	 */
	protected function _writeListItem(&$entity, $propertyName = null, $exists = false, $flush = false) {
		//I Could implement this as a callback; this query is only necessary because I can't map on SKU field
		// For most feeds I don't think this will be necessary, as I should be able to map data entirely based on the entity mappings provided
		$feedId = self::qbId($entity->getId());

		// TODO: Don't I already have a standard way of doing this?
		if (is_string($propertyName) && !empty($propertyName)) {
			$ocId = $entity->{'get' . ucwords($propertyName)}();
		} else {
			$ocId = $entity->getOcId();
		}

		if ($feedId > 0 && $ocId > 0) {
			// We need parent ref and ref name to do updates on sub-entities and they don't map to anything in OC or QC right now
			$refName = $entity->getFullyQualifiedName();
			$refId = self::qbId($entity->getParentRef());

			$exists = ($exists != false) ? $exists : $this->exists($this->tableName, 'feed_id', $feedId); // Saves a check if possible

			if ($flush == true) {
				// Either that or a garbage collector might be an even better idea
				$deleteQuery = "DELETE FROM " . DB_PREFIX . $this->tableName . " WHERE oc_entity_id = '" . $ocId . "'";

				if ($exists) {
					$deleteQuery .= " OR feed_id='" . $feedId . "'";
				}

				$this->db->query($deleteQuery);

				$query = "INSERT INTO " . DB_PREFIX . $this->tableName . " SET feed_id = '" . $feedId . "', oc_entity_id = '" . $ocId . "'";
			} else {
				if ($exists) {
					$query = "UPDATE " . DB_PREFIX . $this->tableName . " SET oc_entity_id = '" . $ocId . "' WHERE feed_id = '" . $feedId . "'";
				} else {
					$query = "REPLACE INTO " . DB_PREFIX . $this->tableName . " SET oc_entity_id = '" . $ocId . "', feed_id = '" . $feedId . "'";
				}
			}
			
			$query = $this->db->query($query);

			// Store references
			// We're at least reusing the entity...
			// Mapping generation needs to be moved to _before impl. in the extending controller class
			if (isset($this->mappings[$this->foreign]['refs'])) {
				$this->updateEntityRefs($entity, $this->mappings[$this->foreign]['refs']); // Populate entity data
			}
		} else {
			//print('Invalid mapping for ' . $entity->getName() . ': feedId => ' . $feedId . ', product_id => ' .  $ocId);
			//var_dump($entity);

		}
	}

	/**
	 * @param QuickBooks_IPP_Service $service
	 * @param QuickBooks_IPP_Object $entity
     */
	protected function _add(QuickBooks_IPP_Service &$service, QuickBooks_IPP_Object &$entity) {
		//print('Adding entity: ' . $entity->getName());
		$ocId = $entity->getOcId(); // This is not a QBO field, so set the value here before overwriting the entity reference
		$entity->remove('OcId');

		if ($resp = $service->add($this->Context, $this->realm, $entity))
		{
			// Clean the ID
			$feedId = self::qbId($resp); // Feed ID will come like this: '{-12}' so it has to be parsed

			// We need parent ref and ref name to do updates on sub-entities and they don't map to anything in OC or QC right now
			$entity = $this->get($feedId); // Fetch the entity from QBO

			// Re-set the OpenCart ID - it was cleared with the refresh from QBO
			$entity->setOcId($ocId);
			
			$this->_writeListItem($entity);
			//print('Added entity: ' . $entity->getName());
			//print($entity->getFullyQualifiedName());
		}
		else
		{
			$this->handleIPPError($service, $entity);
		}
	}

	/**
	 * Pushes a locally stored QuickBooks entity to QuickBooks online
	 *
	 * @param QuickBooks_IPP_Service $service
	 * @param QuickBooks_IPP_Object $entity
     */
	protected function _update(QuickBooks_IPP_Service &$service, QuickBooks_IPP_Object &$entity) {
		//print('Updating entity: ' . $entity->getName());
		
		// We need to save a reference to the OpenCart "entity" ID - this property will not exist in the entity returned from the remote service
		$ocId = $entity->getOcId(); // This is not a QBO field, so set the value here before overwriting the entity reference
		$entity->remove('OcId');

		if ($resp = $service->update($this->Context, $this->realm, $entity->getId(), $entity))
		{
			// Clean the ID
			$feedId = self::qbId($entity->getId()); // Feed ID will come like this: '{-12}' so it has to be parsed
			
			// Fetch the entity from QBO
			$entity = $this->get($feedId); 
			
			// Re-set the OpenCart ID - it was cleared with the refresh from QBO
			$entity->setOcId($ocId);
			
			$this->_writeListItem($entity);

			$query = "UPDATE " . DB_PREFIX . $this->joinTableName . " SET date_modified = '" . $entity->getMetaData()->getLastUpdatedTime() . "' WHERE " . $this->joinCol . " = '" . $ocId . "'";
			$this->db->query($query);

			//print('Updated entity: ' . $entity->getName());
			//print($entity->getFullyQualifiedName());
		}
		else
		{
			$this->handleIPPError($service, $entity);
		}
	}

	protected function handleIPPError(QuickBooks_IPP_Service $service, QuickBooks_IPP_Object $entity = null) {
		$errorDetail = array(
			'error' => 'Your record has been updated but there was an error posting it to QuickBooks.',
			//'entity' => $entity->getId(),
			'code' => $service->errorNumber(),
			'message' => $service->errorMessage(),
			'detail' => $service->errorDetail()
		);

		$this->session->data['ipp_error']['warning'] = $errorDetail;

		// OK for ajax
		$this->sendResponse($errorDetail);
	}
	
	/** 
	 * Heavy batch operations should be changed to use XMLWriter or
	 * something that doesn't have to load everything into memory?
	 * This should be fine for small or medium-sized stores anyway
	 * Boom! It's a generic push method
	 */
	public function push() {
		$output = [];
		
		// TODO: Test vs schema to make sure def matches what Doctrine needs
		$converter = null;
		// Build mappings
		$converters = [];
		$mappings = [];
		
		// TODO: Remember to remove status WHERE clause when moving to admin
		//$query = $this->db->query("SELECT oc." . $this->joinCol . ", f.feed_id, f.sync, oc.date_added, oc.date_modified FROM " . DB_PREFIX . $this->joinTableName . " oc LEFT JOIN " . DB_PREFIX . $this->tableName . " f ON (oc." . $this->joinCol . " = f.oc_entity_id)");
		$query = $this->db->query("SELECT oc." . $this->joinCol . ", f.feed_id, f.sync FROM " . DB_PREFIX . $this->joinTableName . " oc LEFT JOIN " . DB_PREFIX . $this->tableName . " f ON (oc." . $this->joinCol . " = f.oc_entity_id)");
		$reader = new ArrayReader($query->rows);
		
		$workflow = new Workflow($reader);
		$output = [];
		
		// Adapter specific
		$this->load->model('rest/restadmin');
		
		$i = 0;
		
		//header('Content-Type: text/xml; charset=utf-8');
		//header('Content-Disposition: attachment; filename="data.xml"');
		//echo '<Collection>';
		$workflow->addWriter(new CallbackWriter(function ($row) use ($mappings, &$i) {			
			if (isset($row['feed_id']) && $row['feed_id'] > 0) {
				if ($i < 5) {
					// Get the product from feed (QBO)
					//var_dump($row);
					$item = $this->get($row['feed_id']);
					
					if ($item) {
						$this->edit($row[$this->joinCol]);
					} else {
						// Throw some kind of error or warning?
						$this->add($row[$this->joinCol]);
					}
				}
				
				// Check to see if it's up-to-date
			} else {
				if ($row['feed_id'] == null || $row['feed_id'] == 0) {
					$this->add($row[$this->joinCol]);
				}
			}
			$i++;
		}));
		
		$workflow->process();
		
		//echo '</Collection>';
	}

	/**
	 * @param QuickBooks_IPP_Service $service
	 * @param QuickBooks_IPP_Object $entity
	 * @param bool|false $asXml
     */
	protected function _export(QuickBooks_IPP_Service &$service, QuickBooks_IPP_Object &$entity, $asXml = false) {
		if ($asXml) {
			echo $entity->asXML();
		} else {
			if (self::qbId($entity->getId()) > 0) {
				$this->_update($service, $entity);
			} else {
				$this->_add($service, $entity);
			}
		}
	}
	
	/**
	 * The _add and _update methods don't set extra entity metadata/fields that may be needed on a per-class basis.
	 * We obviously don't want to have to extend/implement _add and _update methods in inheriting classes, so instead
	 * we provide the option to decorate the object returned by the aforementioned methods. The generated metadata can be
	 * passed to an OpenCart module...
	 *
	 * TODO: Would be cool if params could somehow be specified in mapping XML? I haven't given it any real thought yet
	 */
	public function decorate() {
		//echo "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='" . DB_DATABASE . "' AND `TABLE_NAME`='" . DB_PREFIX . $this->tableName ."'";
		$tables = $this->db->query("SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='" . DB_DATABASE . "' AND `TABLE_NAME`='" . DB_PREFIX . $this->tableName ."'");
		
		/*$query = "INSERT INTO " . DB_PREFIX . $this->tableName . " SET feed_id = '" . $feedId . "', oc_entity_id = '" . $ocId . "'";
		$query .= ($entity->getSubItem() == true) ? ", parent_ref_id = '" . $refId . "', name = '" . $refName . "'" : '';
		$query = $this->db->query($query);*/
	}

	/**
	 * Testing only!
	 * return array[QuickBooks_IPP_Object_Account]
	 */
	public function dump() {
		$className = "QuickBooks_IPP_Service_" . $this->foreign;
		$itemService = new $className();

		$items = $itemService->query($this->Context, $this->realm, "SELECT * FROM " . $this->foreign . " ORDER BY Metadata.LastUpdatedTime");

		var_dump($items);

		return $items;
	}

	/**
	 * Testing only!
	 * return array[QuickBooks_IPP_Object_Account]
	 */
	public function dumpById() {
		$id = $this->request->get['id'];

		$className = "QuickBooks_IPP_Service_" . $this->foreign;
		$itemService = new $className();

		$items = $itemService->query($this->Context, $this->realm, "SELECT * FROM " . $this->foreign . " WHERE Id = '" . $id . "' ORDER BY Metadata.LastUpdatedTime");

		//var_dump($items);

		return $items;
	}
	
	/**
	 * TODO: INSTALLER! At some point in the future I might want to create tables on the fly.
	 * I might as well do this anyway simply because I could use it in the installer.
	 */
	private function createReferenceTable() {
		
	}
	
	
	/**
	 * Deletes a locally stored QuickCommerce entity. This does not delete any corresponding OpenCart entities or db records.
	 */
	public function delete($ocId) {
		$query = "DELETE FROM " . DB_PREFIX . $this->tableName . " WHERE oc_entity_id = '" . $ocId . "'";
		$query = $this->db->query($query);
	}
	
	/**
	 * Dereferences a locally stored QuickCommerce entity.
	 */
	public function dereference($ocId) {
		$query = "DELETE FROM " . DB_PREFIX . $this->tableName . " WHERE oc_entity_id = '" . $ocId . "'";
		//$query = $this->db->query($query);
	}
	
	// Yeah, this isn't great but whatever for now
	/**
	 * @param $class
	 * @return bool
     */
	public static function autoloadEntities($class) {
		$file = DIR_QC . 'vendor/quickcommerce/src/Entity/' . str_replace('\\', '/', strtolower($class)) . '.php';
		
		if (is_file($file)) {
			include_once($file);
			return true;
		}
		
		return false;
	}

	/**
	 * @param $class
	 * @return bool
     */
	public static function autoload($class) {
		$file = DIR_SYSTEM . 'library/quickcommerce/vendor/' . str_replace('\\', '/', strtolower($class)) . '.php';
		
		if (is_file($file)) {
			include_once($file);
			return true;
		}
		
		return false;
	}

	/**
	 *
     */
	public function checkPlugin() {
    }

	/**
	 * @param $output
     */
	public function sendResponse($output) {
		// Clean out any previous junk that was echoed, printed or dumped prior to setting our headers
		// Obviously there shouldn't be anything happening like that, but we can log that stuff
		$buffer = ob_get_contents();
		if (!empty($buffer)) {
			ob_clean();
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($output));
    }

    //update user session
	/**
	 * @param $osc_session
     */
	function updateSession($osc_session) {
        if(session_id() != $osc_session){
            // Close the current session
            session_write_close();
            session_id($osc_session);
            session_start();
            $this->session->data = $_SESSION;
        }
    }

	/**
	 *
     */
	public function returnDeprecated(){
        $json['success'] = false;
        $json['error'] = "This service has been removed for security reasons.Please contact us for more information.";
        //echo(json_encode($json));
        exit;
    }

}

/**
 * Class XML2Array
 */
class XML2Array {

    private static $xml = null;
    private static $encoding = 'UTF-8';

    /**
     * Initialize the root XML node [optional]
     * @param $version
     * @param $encoding
     * @param $format_output
     */
    public static function init($version = '1.0', $encoding = 'UTF-8', $format_output = true) {
        self::$xml = new DOMDocument($version, $encoding);
        self::$xml->formatOutput = $format_output;
        self::$encoding = $encoding;
    }

    /**
     * Convert an XML to Array
     * @param string $node_name - name of the root node to be converted
     * @param array $arr - aray to be converterd
     * @return DOMDocument
     */
    public static function &createArray($input_xml) {
        $xml = self::getXMLRoot();
        if(is_string($input_xml)) {
            $parsed = $xml->loadXML($input_xml);
            if(!$parsed) {
                throw new Exception('[XML2Array] Error parsing the XML string.');
            }
        } else {
            if(get_class($input_xml) != 'DOMDocument') {
                throw new Exception('[XML2Array] The input XML object should be of type: DOMDocument.');
            }
            $xml = self::$xml = $input_xml;
        }
        $array[$xml->documentElement->tagName] = self::convert($xml->documentElement);
        self::$xml = null;    // clear the xml node in the class for 2nd time use.
        return $array;
    }

    /**
     * Convert an Array to XML
     * @param mixed $node - XML as a string or as an object of DOMDocument
     * @return mixed
     */
    private static function &convert($node) {
        $output = array();

        switch ($node->nodeType) {
            case XML_CDATA_SECTION_NODE:
                $output['@cdata'] = trim($node->textContent);
                break;

            case XML_TEXT_NODE:
                $output = trim($node->textContent);
                break;

            case XML_ELEMENT_NODE:

                // for each child node, call the covert function recursively
                for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = self::convert($child);
                    if(isset($child->tagName)) {
                        $t = $child->tagName;

                        // assume more nodes of same kind are coming
                        if(!isset($output[$t])) {
                            $output[$t] = array();
                        }
                        $output[$t][] = $v;
                    } else {
                        //check if it is not an empty text node
                        if($v !== '') {
                            $output = $v;
                        }
                    }
                }

                if(is_array($output)) {
                    // if only one node of its kind, assign it directly instead if array($value);
                    foreach ($output as $t => $v) {
                        if(is_array($v) && count($v)==1) {
                            $output[$t] = $v[0];
                        }
                    }
                    if(empty($output)) {
                        //for empty nodes
                        $output = '';
                    }
                }

                // loop through the attributes and collect them
                if($node->attributes->length) {
                    $a = array();
                    foreach($node->attributes as $attrName => $attrNode) {
                        $a[$attrName] = (string) $attrNode->value;
                    }
                    // if its an leaf node, store the value in @value instead of directly storing it.
                    if(!is_array($output)) {
                        $output = array('@value' => $output);
                    }
                    $output['@attributes'] = $a;
                }
                break;
        }
        return $output;
    }

    /*
     * Get the root XML node, if there isn't one, create it.
     */
    private static function getXMLRoot(){
        if(empty(self::$xml)) {
            self::init();
        }
        return self::$xml;
    }
}
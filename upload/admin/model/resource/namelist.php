<?php
require_once DIR_SYSTEM . 'library/quickcommerce/resource.php';
require_once DIR_SYSTEM . 'library/quickcommerce/resource/namelist.php';
require_once DIR_SYSTEM . 'library/quickcommerce/resource/account.php';

use Doctrine\Common\Collections\Criteria;

// Extend class & make available to OpenCart using standard naming convention 
class ModelResourceInvoice extends Model {}
class ModelResourceSalesReceipt extends Model {}
class ModelResource extends Model {}

// Rewriting/modding the models one at a time is tedious and doesn't really make sense
// We're gonna wrap the data instead, since OC doesn't have real entities anyway
/**
 * This class, which extends the core OpenCart Model class, 
 */
class ModelResourceNameList extends Model {
	public static $service = null;

	/**
	 * @param $registry
     */
	function __construct($registry) {
		parent::__construct($registry);
	}

	/**
	 * Transactions may have different methods, so we may need to employ more than one strategy
	 *
	 * @param Resource $service
     */
	function setResourceType($service) {
		// TODO: Validate to make sure context has been set
		$this->service = $service;
	}

	public function getService() {
		return self::$service;
	}

	/**
	 * Reads the transaction entity using the service set via setResourceType, and decorate it with data from the resource base table (qcli)
	 *
	 * @param $id
	 * @return array
     */
	public function getResource($id) {
		if (!$id) throw new Exception('No resource id was provided');
		
		 // TODO: What if there's more than one? Obviously that shouldn't happen, unless I want it too...
		$te = $this->service->getEntity($id);
		//$te['order_id'] = $te['oc_entity_id'];

		//$tr = (isset($te['resource_id'])) ? $this->row($te['resource_id']) : null;
		$tr = (isset($te['resource_id'])) ? $this->row($te['resource_id']) : null;
		// TODO: If null we need to throw an exception or an error message...
		return (is_array($tr)) ? array_merge($te, $tr) : $te;
	}

	private function row($id) {
		// This is how we did it with transactions
		//$sql = "SELECT * FROM " . DB_PREFIX . "qcli WHERE resource_id ='" . $id . "'";
		//$query = $this->db->query($sql);
		
		// This is a temporary/permanent workaround - unsure...
		// Get the context (QCController)
		$context = $this->service->getContext();
		
		// Get the qcli tablename
		$table = $context->getTableName();
		
		$sql = "SELECT * FROM " . DB_PREFIX . $table . " WHERE oc_entity_id ='" . $id . "'";
		$query = $this->db->query($sql);

		$tr = $query->rows[0];

		return $tr;
	}

	public function getTotalResources() {
		return $this->service->countAll();
	}

	/**
	 * @return mixed
     */
	public function getResources($params = null) {
		// Get the context (QCController)
		$context = $this->service->getContext();
					
		// Get the qcli join column
		$col = $context->getJoinColumn();

		$defaults = array(
			'order'                => 'DESC',
			'start'                => 0,
			'limit'                => $this->config->get('config_limit_admin')
		);

		if (!is_array($params)) {
			$params = $defaults;
		} else {
			$params = array_merge($defaults, $params);
		}

		$collection = $this->service->getCollection(true, true, array(), array(), $params['limit'], $params['start'], $params['order']);
		
		if ($collection != null) {
			foreach ($collection as &$item) {		
				if (isset($item[$col])) {
					// This is how we did it with transactions
					//$item = array_merge($item, $this->row($item['resource_id']));
					
					$item = array_merge($this->row($item[$col]), $item);
				}

				//$item['order_id'] = $item['oc_entity_id'];
			}
			
			return $collection;
		}
	}

	/**
	 * @param $id
	 * @return mixed
     */
	public function convert($id) {
		return $this->service->convert($id);
	}
}
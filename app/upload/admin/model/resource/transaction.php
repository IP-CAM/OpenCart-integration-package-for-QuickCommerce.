<?php
require_once DIR_SYSTEM . 'library/quickcommerce/resource.php';
require_once DIR_SYSTEM . 'library/quickcommerce/transaction/transaction.php';
require_once DIR_SYSTEM . 'library/quickcommerce/transaction/invoice.php';

use Doctrine\Common\Collections\Criteria;

use App\Service\Product;
//use App\Service\Language;
use App\Service\Option;
use App\Service\ProductOption;
use App\Service\ProductOptionValue;

// Extend class & make available to OpenCart using standard naming convention 
class ModelResourcePurchaseOrder extends Model {}
class ModelResourceInvoice extends Model {}
class ModelResourceSalesReceipt extends Model {}
class ModelResource extends Model {}

// Rewriting/modding the models one at a time is tedious and doesn't really make sense
// We're gonna wrap the data instead, since OC doesn't have real entities anyway
/**
 * This class, which extends the core OpenCart Model class, 
 */
class ModelResourceTransaction extends Model {
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
	 * @param ResourceService $service
     */
	function setTransactionType($service) {
        // TODO: Validate to make sure context has been set
		self::$service = $service;
	}

	public function getService() {
		return self::$service;
	}

	/**
	 * Reads the transaction entity using the service set via setTransactionType, and decorate it with data from the transaction base table (qctr)
	 *
	 * @param $id
	 * @return array
     */
	public function getTransaction($id) {
		if (!$id) throw new Exception('No transaction id was provided');
		
		 // TODO: What if there's more than one? Obviously that shouldn't happen, unless I want it too...
		$te = self::$service->getEntity($id);
		$te['order_id'] = $te['oc_entity_id'];

		$tr = $this->row($te['transaction_id']);
		// TODO: Throw errors if these don't exist

		return (is_array($tr)) ? array_merge($te, $tr) : $te;
	}

	private function row($id) {
		$tr = null;

		$sql = "SELECT * FROM " . DB_PREFIX . "qctr WHERE transaction_id ='" . $id . "'";
		$query = $this->db->query($sql);

		if (count($query->rows) > 0) {
			$tr = $query->rows[0];
		}

		return $tr;
	}

	/**
	 * @return mixed
     */
	public function getTransactions($params = null) {
		$defaults = array(
			'order'                => 'ASC',
			'start'                => 0,
			'limit'                => $this->config->get('config_limit_admin')
		);

		if (!is_array($params)) {
			$params = $defaults;
		} else {
			$params = array_merge($defaults, $params);
		}

		$collection = self::$service->search($params, true, true);
		
		if ($collection != null) {
			foreach ($collection as &$item) {
				$txnRow = $this->row($item['transaction_id']);
				$item = ($txnRow != null) ? array_merge($item, $txnRow) : $item;
				$item['order_id'] = $item['oc_entity_id']; // This is kinda invoice specific O.o
			}
			
			return $collection;
		}
	}

	/**
	 * @param $id
	 * @return mixed
     */
	public function convert($id) {
		return self::$service->convert($id);
	}

	/**
	 * Used by OpenCart
	 * @param $order_id
	 */
	public function getLineItems($id = 0) {
		$data = array();

		$te = self::$service->getEntity($id, false); // Set serialize to false so we get the entity
		
		//$em = self::$service->getEntityManager();
		//$repo = $em->getRepository('OcInvoiceLine'); // Not used
		$collection = $te->getLines(); // Returns PersistentCollection

		$criteria = Criteria::create()
			//->where(Criteria::expr()->eq('detailType', 'SalesItemLineDetail'))
			->orderBy(array('name' => 'ASC')) // Cannot order by relationed field or this can crash...
			->setFirstResult(0)
			->setMaxResults(50);

		$collection = $collection->matching($criteria);
		foreach ($collection as $item) {
			$data[] = self::$service->toArray($item);
		}

		return $data;
	}

	/**
	 * Used by OpenCart
	 */
	public function getCustomer($id) {
		// TODO: I need a workaround for this... Doctrine keeps looking for zero-valued ids
		$data = array();

		try {
			$te = self::$service->getEntity($id, false);

			$entity = $te->getCustomer();
			if ($entity != null) {
				$entity->getEmail(); // Do something to trigger load;

				$data = self::$service->toArray($entity);
				$parts = array($data['firstname'], $data['lastname']);
				$data['fullname'] = implode(' ', $parts);
			}
		} catch (Doctrine\ORM\EntityNotFoundException $e) {
			// Ignore errors
		}

		return $data;
	}
	
	/**
	 * Used by OpenCart
	 * This method needs to be moved to its own model
	 */
	public function getInvoiceStatuses() {
		$data = array();

		$em = self::$service->getEntityManager();
		$repo = $em->getRepository('OcInvoiceStatus'); // Not used

		$criteria = Criteria::create()
			//->where(Criteria::expr()->eq('detailType', 'SalesItemLineDetail'))
			->orderBy(array('name' => 'ASC')) // Cannot order by relationed field or this can crash...
			->setFirstResult(0)
			->setMaxResults(50);

		//$collection = $repo->findBy($criteria);
		$collection = $repo->findAll();
		foreach ($collection as $item) {
			$data[] = self::$service->toArray($item);
		}

		return $data;
	}
	
	/**
	 * Used by OpenCart
	 * This method needs to be moved to its own model
	 */
	public function getInvoiceStatus($id) {
		$data = array();

		$te = self::$service->getEntity($id, false); // Set serialize to false so we get the entity
		
		//$em = self::$service->getEntityManager();
		//$repo = $em->getRepository('OcInvoiceLine'); // Not used
		/*$collection = $te->getLines(); // Returns PersistentCollection

		$criteria = Criteria::create()
			//->where(Criteria::expr()->eq('detailType', 'SalesItemLineDetail'))
			->orderBy(array('name' => 'ASC')) // Cannot order by relationed field or this can crash...
			->setFirstResult(0)
			->setMaxResults(50);

		$collection = $collection->matching($criteria);
		foreach ($collection as $item) {
			$data[] = self::$service->toArray($item);
		}*/
		
		$status = $te->getInvoiceStatus();
		if ($status != null) {
			$data = self::$service->toArray($status);
		}

		return $data;
	}

	/**
	 * Used by OpenCart
	 * This method needs to be moved to its own model
	 */
	public function getPurchaseOrderStatuses() {
		$data = array();

		$em = self::$service->getEntityManager();
		$repo = $em->getRepository('OcPurchaseOrderStatus'); // Not used

		$criteria = Criteria::create()
			//->where(Criteria::expr()->eq('detailType', 'SalesItemLineDetail'))
			->orderBy(array('name' => 'ASC')) // Cannot order by relationed field or this can crash...
			->setFirstResult(0)
			->setMaxResults(50);

		//$collection = $repo->findBy($criteria);
		$collection = $repo->findAll();
		foreach ($collection as $item) {
			$data[] = self::$service->toArray($item);
		}

		return $data;
	}

	/**
	 * Used by OpenCart
	 * This method needs to be moved to its own model
	 */
	public function getPurchaseOrderStatus($id) {
		$data = array();

		$te = self::$service->getEntity($id, false); // Set serialize to false so we get the entity

		//$em = self::$service->getEntityManager();
		//$repo = $em->getRepository('OcPurchaseOrderLine'); // Not used
		/*$collection = $te->getLines(); // Returns PersistentCollection

		$criteria = Criteria::create()
			//->where(Criteria::expr()->eq('detailType', 'SalesItemLineDetail'))
			->orderBy(array('name' => 'ASC')) // Cannot order by relationed field or this can crash...
			->setFirstResult(0)
			->setMaxResults(50);

		$collection = $collection->matching($criteria);
		foreach ($collection as $item) {
			$data[] = self::$service->toArray($item);
		}*/

		$status = $te->getPurchaseOrderStatus();
		if ($status != null) {
			$data = self::$service->toArray($status);
		}

		return $data;
	}

	/**
	 * Used by OpenCart
	 * @param $order_id
     */
	public function getTransactionTotals($order_id) {
	}

	/**
	 * Used by OpenCart
	 * @param array $data
     */
	public function getTotalTransactions($data = array()) {
		return self::$service->countAll();
	}

	/**
	 * Used by OpenCart
	 * @param $store_id
     */
	public function getTotalTransactionsByStoreId($store_id) {
	}

	/**
	 * Used by OpenCart
	 * @param $order_status_id
     */
	public function getTotalTransactionsByStatusId($order_status_id) {
	}

	/*public function createInvoiceNo($order_id) {
		$order_info = $this->getOrder($order_id);

		if ($order_info && !$order_info['invoice_no']) {
			$query = $this->db->query("SELECT MAX(invoice_no) AS invoice_no FROM `" . DB_PREFIX . "order` WHERE invoice_prefix = '" . $this->db->escape($order_info['invoice_prefix']) . "'");

			if ($query->row['invoice_no']) {
				$invoice_no = $query->row['invoice_no'] + 1;
			} else {
				$invoice_no = 1;
			}

			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET invoice_no = '" . (int)$invoice_no . "', invoice_prefix = '" . $this->db->escape($order_info['invoice_prefix']) . "' WHERE order_id = '" . (int)$order_id . "'");

			return $order_info['invoice_prefix'] . $invoice_no;
		}
	}*/
}
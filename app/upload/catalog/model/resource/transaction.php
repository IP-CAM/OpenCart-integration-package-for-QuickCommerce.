<?php
require_once DIR_SYSTEM . 'library/quickcommerce/resource.php';
require_once DIR_SYSTEM . 'library/quickcommerce/transaction/transaction.php';
require_once DIR_SYSTEM . 'library/quickcommerce/transaction/invoice.php';

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
class ModelResourceTransaction extends Model {
	public $service = null;

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
	function setTransactionType(ResourceService $service) {
		// TODO: Validate to make sure context has been set
		$this->service = $service;
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
		$tr = $this->row($id);
		$te = $this->service->getEntity($id);
		$te['order_id'] = $te['oc_entity_id'];
		// TODO: Throw errors if these don't exist

		return (is_array($tr)) ? array_merge($te, $tr) : $te;
	}

	private function row($id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "qctr WHERE transaction_id ='" . $id . "'";
		$query = $this->db->query($sql);

		$tr = $query->rows[0];

		return $tr;
	}

	/**
	 * @return mixed
	 */
	public function getTransactions() {
		$collection = $this->service->getCollection();
		foreach ($collection as &$item) {
			$item = array_merge($item, $this->row($item['transaction_id']));
			$item['order_id'] = $item['oc_entity_id'];
		}

		return $collection;
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function convert($id) {
		return $this->service->convert($id);
	}

	/**
	 * Used by OpenCart
	 * @param $order_id
	 */
	public function getLineItems() {
		$data = array();

		$te = $this->service->getEntity(8, false); // Set serialize to false so we get the entity

		$em = $this->service->getEntityManager();
		$repo = $em->getRepository('OcInvoiceLine');
		$collection = $te->getLines(); // Returns PersistentCollection

		$criteria = Criteria::create()
			->where(Criteria::expr()->eq('detailType', 'SalesItemLineDetail'))
			->orderBy(array('name' => 'ASC')) // Cannot order by relationed field or this can crash...
			->setFirstResult(0)
			->setMaxResults(20);

		$collection = $collection->matching($criteria);
		foreach ($collection as $item) {
			$data[] = $this->service->toArray($item);
		}

		return $data;
	}

	/**
	 * Used by OpenCart
	 * @param $order_id
	 */
	public function getCustomer($id) {
		$data = array();

		$te = $this->service->getEntity($id, false);
		$entity = $te->getCustomer();
		$entity->getEmail(); // Do something to trigger load;

		$data = $this->service->toArray($entity);
		$parts = array($data['firstname'], $data['lastname']);
		$data['fullname'] = implode(' ', $parts);

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
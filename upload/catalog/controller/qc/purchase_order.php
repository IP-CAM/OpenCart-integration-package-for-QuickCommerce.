<?php
require_once(DIR_SYSTEM . 'engine/qccontroller.php');

class ControllerQCPurchaseOrder extends QCController {
	protected $config;
	
	function __construct($registry) {
		parent::__construct($registry);
		parent::before();
	}
	
	public function getPurchaseOrders() {
		$PurchaseOrderService = new QuickBooks_IPP_Service_PurchaseOrder();

		$pos = $PurchaseOrderService->query($this->Context, $this->realm, "SELECT * FROM PurchaseOrder");

		//print_r($terms);

		foreach ($pos as $PurchaseOrder)
		{
			//print_r($Term);

			print('PurchaseOrder Id=' . $PurchaseOrder->getId() . ' is named: ' . $PurchaseOrder->getDocNumber() . '<br>');
			print_r($PurchaseOrder);
		}
	}
	
	public function eventBeforeAddPurchaseOrder() {
		echo 'eventBeforeAddPurchaseOrder';
		exit;
	}
	
	public function eventAfterAddPurchaseOrder() {
		$PurchaseOrder_id = 22;
		
		$this->load->model('checkout/PurchaseOrder');
		
		$data = $this->model_checkout_PurchaseOrder->getPurchaseOrder($PurchaseOrder_id);
		var_dump($data);
		
		$InvoiceService = new QuickBooks_IPP_Service_Invoice();
		
		$CustomerService = new QuickBooks_IPP_Service_Customer();
		$customers = $CustomerService->query($this->Context, $this->realm, "SELECT * FROM Customer");
		var_dump($customers);

		$Invoice = new QuickBooks_IPP_Object_Invoice();

		$Invoice->setDocNumber('WEB' . mt_rand(0, 10000));
		$Invoice->setTxnDate('2013-10-11');

		$Line = new QuickBooks_IPP_Object_Line();
		$Line->setDetailType('SalesItemLineDetail');
		$Line->setAmount(12.95 * 2);
		$Line->setDescription('Test description goes here.');

		$SalesItemLineDetail = new QuickBooks_IPP_Object_SalesItemLineDetail();
		$SalesItemLineDetail->setItemRef('8');
		$SalesItemLineDetail->setUnitPrice(12.95);
		$SalesItemLineDetail->setQty(2);

		$Line->addSalesItemLineDetail($SalesItemLineDetail);

		$Invoice->addLine($Line);

		$Invoice->setCustomerRef('67');


		if ($resp = $InvoiceService->add($this->Context, $this->realm, $Invoice))
		{
			print('Our new Invoice ID is: [' . $resp . ']');
		}
		else
		{
			print($InvoiceService->lastError());
		}

		print('<br><br><br><br>');
		print("\n\n\n\n\n\n\n\n");
		print('Request [' . $IPP->lastRequest() . ']');
		print("\n\n\n\n");
		print('Response [' . $IPP->lastResponse() . ']');
		print("\n\n\n\n\n\n\n\n\n");

		exit;
	}
	
	public function eventBeforeEditPurchaseOrder() {
		echo 'eventBeforeEditPurchaseOrder';
		exit;
	}
	
	public function eventAfterEditPurchaseOrder() {
		$PurchaseOrderService = new QuickBooks_IPP_Service_PurchaseOrder();

		// Get the existing invoice first (you need the latest SyncToken value)
		$orders = $PurchaseOrderService->query($this->Context, $this->realm, "SELECT * FROM PurchaseOrder");
		$PurchaseOrder = $orders[0];

		$Line = $PurchaseOrder->getLine(0);
		$Line->setDescription('Update of my description on ' . date('r'));

		print_r($PurchaseOrder);

		$PurchaseOrder->setTxnDate(date('Y-m-d'));  // Update the invoice date to today's date 

		if ($resp = $PurchaseOrderService->update($this->Context, $this->realm, $PurchaseOrder->getId(), $PurchaseOrder))
		{
			print('&nbsp; Updated!<br>');
		}
		else
		{
			print('&nbsp; ' . $PurchaseOrderService->lastError() . '<br>');
		}
		
		exit;
	}
	
	public function eventBeforeDeletePurchaseOrder() {
		
	}
	
	public function eventAfterDeletePurchaseOrder() {
		$InvoiceService = new QuickBooks_IPP_Service_Invoice();

		$the_invoice_to_delete = '{-10}';

		$retr = $InvoiceService->delete($this->Context, $this->realm, $the_invoice_to_delete);
		if ($retr)
		{
			print('The invoice was deleted!');
		}
		else
		{
			print('Could not delete invoice: ' . $InvoiceService->lastError());
		}
	}
	
	public function eventOnAddPurchaseOrderHistory() {
		
	}
}
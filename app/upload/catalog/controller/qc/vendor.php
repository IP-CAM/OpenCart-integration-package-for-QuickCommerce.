<?php
require_once(DIR_SYSTEM . 'engine/qccontroller.php');

class ControllerQCVendor extends QCController {
	function __construct($registry) {
		parent::__construct($registry);
		parent::before();
	}
	
	public function eventBeforeAddVendor() {
		echo 'eventBeforeAddVendor';
		exit;
	}
	
	public function eventAfterAddVendor() {
		$Vendor_id = 22;
		
		$this->load->model('checkout/Vendor');
		
		$data = $this->model_checkout_Vendor->getVendor($Vendor_id);
		var_dump($data);
		
		$InvoiceService = new QuickBooks_IPP_Service_Invoice();
		
		$CustomerService = new QuickBooks_IPP_Service_Customer();
		$customers = $CustomerService->query($this->Context, $this->realm, "SELECT * FROM Customer");
		var_dump($customers);

		$VendorService = new QuickBooks_IPP_Service_Vendor();

		$Vendor = new QuickBooks_IPP_Object_Vendor();
		$Vendor->setTitle('Mr');
		$Vendor->setGivenName('Keith');
		$Vendor->setMiddleName('R');
		$Vendor->setFamilyName('Palmer');
		$Vendor->setDisplayName('Keith R Palmer Jr ' . mt_rand(0, 1000));

		if ($resp = $VendorService->add($Context, $realm, $Vendor))
		{
			print('Our new Vendor ID is: [' . $resp . ']');
		}
		else
		{
			print($VendorService->lastError($Context));
		}

		print('<br><br><br><br>');
		print("\n\n\n\n\n\n\n\n");
		print('Request [' . $IPP->lastRequest() . ']');
		print("\n\n\n\n");
		print('Response [' . $IPP->lastResponse() . ']');
		print("\n\n\n\n\n\n\n\n\n");

		exit;
	}
	
	public function eventBeforeEditVendor() {
		echo 'eventBeforeEditVendor';
		exit;
	}
	
	public function eventAfterEditVendor() {
		$InvoiceService = new QuickBooks_IPP_Service_Invoice();

		// Get the existing invoice first (you need the latest SyncToken value)
		$invoices = $InvoiceService->query($this->Context, $this->realm, "SELECT * FROM Invoice WHERE Id = '34' ");
		$Invoice = $invoices[0];

		$Line = $Invoice->getLine(0);
		$Line->setDescription('Update of my description on ' . date('r'));

		print_r($Invoice);

		$Invoice->setTxnDate(date('Y-m-d'));  // Update the invoice date to today's date 

		if ($resp = $InvoiceService->update($this->Context, $this->realm, $Invoice->getId(), $Invoice))
		{
			print('&nbsp; Updated!<br>');
		}
		else
		{
			print('&nbsp; ' . $InvoiceService->lastError() . '<br>');
		}
		
		exit;
	}
	
	public function eventBeforeDeleteVendor() {
		
	}
	
	public function eventAfterDeleteVendor() {
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
	
	public function eventOnAddVendorHistory() {
		
	}
}
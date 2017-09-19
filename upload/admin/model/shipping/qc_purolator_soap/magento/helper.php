<?php

class Blogshop_Purolator_Helper_Data extends Mage_Core_Helper_Abstract {

	public function getSession() {
		return Mage::getSingleton('customer/session');
	}
	
	public function getShippingModule()
	{
		return Mage::getSingleton('purolatormodule/carrier_shippingmethod');
	}
	
	public function getAddressvalidation()
	{
		return (bool)Mage::getStoreConfig('carriers/purolatormodule/active') == 1 && (bool)Mage::getSingleton('purolatormodule/addressvalidation') == 1;
	}
	
	
	public function isActive() 
	{
		return (bool)Mage::getStoreConfig('carriers/purolatormodule/active');
	}
	
	public function testAddress($_address) 
	{
		if($this->isActive())
		{
			  return $this->getAddressvalidation()->testAddress($_address);
		}
	}
	
	public function append(&$dom, $ns)

    /**
     * Get description corresponding to Purolator error code
     *
     * @param $code
     * @return null|string
     */
    public function getErrorDescription($code)
    {
        $description = NULL;

        switch ($code) {
            case '9001':
                $description = 'Authorization failed -- Service Operation is inactive.';
                break;
            case '9000-1':
                $description = 'Authorization failed -- Daily Threshold limit reached.';
                break;
            case '9000-2':
                $description = 'Authorization failed -- Hourly Threshold limit reached.';
                break;
            case '9000-3':
                $description = 'Authorization failed -- Minute Threshold limit reached.';
                break;
            case '1100000':
                $description = 'One or more errors have occurred.';
                break;
            case '1100100':
                $description = 'Field is missing.';
                break;
            case '1100101':
                $description = 'The Account Number is a mandatory field.';
                break;
            case '1100102':
                $description = 'The Account Name is a mandatory field.';
                break;
            case '1100103':
                $description = 'The Account Number is a mandatory field.';
                break;
            case '1100104':
                $description = 'The City Name is a mandatory field.';
                break;
            case '1100105':
                $description = 'The Close Time is a mandatory field.';
                break;
            case '1100106':
                $description = 'The Contact Name is a mandatory field.';
                break;
            case '1100107':
                $description = 'The Country is a mandatory field.';
                break;
            case '1100108':
                $description = 'The Email Address is a mandatory field.';
                break;
            case '1100109':
                $description = 'The Fax Number is a mandatory field.';
                break;
            case '1100110':
                $description = 'The Package Type is a mandatory field.';
                break;
            case '1100111':
                $description = 'The Telephone Extension is a mandatory field.';
                break;
            case '1100112':
                $description = 'The Telephone Number is a mandatory field.';
                break;
            case '1100113':
                $description = 'The Pickup Location is a mandatory field.';
                break;
            case '1100114':
                $description = 'The Pickup Method is a mandatory field.';
                break;
            case '1100115':
                $description = 'The Postal Code is a mandatory field.';
                break;
            case '1100116':
                $description = 'The Province is a mandatory field.';
                break;
            case '1100117':
                $description = 'The Ready Time is a mandatory field.';
                break;
            case '1100118':
                $description = 'Undefined error.';
                break;
            case '1100119':
                $description = 'The Service Mode is a mandatory field.';
                break;
            case '1100120':
                $description = 'The Share ID is a mandatory field.';
                break;
            case '1100121':
                $description = 'The Share Type is a mandatory field.';
                break;
        }

        return $description;
    }

    /**
     * Get all document types provided by GetDocuments web-service
     *
     * @return array
     */
    public function getDocumentTypes() {
        $documentTypes = array(
            'COSBillOfLading',
            'CustomsInvoice',
            'CustomsInvoiceThermal',
            'DangerousGoodsDeclaration',
            'DomesticBillOfLading',
            'DomesticBillOfLadingThermal',
            'ExpressChequeReceipt',
            'ExpressChequeReceiptThermal',
            'FCC740',
            'FDA2877',
            'InternationalBillOfLading',
            'InternationalBillOfLadingThermal',
            'NAFTA'
        );

        return $documentTypes;
    }

    /**
     * Validate document type
     *
     * @param $type
     * @return bool
     */
    public function isDocumentType($type) {
        if ( in_array($type, $this->getDocumentTypes()) ) {
            return true;
        }
        return false;
    }
}

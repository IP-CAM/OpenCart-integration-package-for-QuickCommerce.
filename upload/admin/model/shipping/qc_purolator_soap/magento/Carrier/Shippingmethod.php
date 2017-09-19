<?php

class Blogshop_Purolator_Model_Carrier_Shippingmethod extends Mage_Shipping_Model_Carrier_Abstract
{
    /**
     * Code of the carrier
     *
     * @var string
     */
    protected $_code = 'purolatormodule';

    /**
     * Core resources
     *
     */
    protected $_cr = false;
    protected $_cw = false;
    protected $_th = false;

    /**
     * Rate request data
     *
     * @var Mage_Shipping_Model_Rate_Request|null
     */
    protected $_request = null;

    /**
     * Raw rate request data
     *
     * @var Varien_Object|null
     */
    protected $_rawRequest = null;

    /**
     * Rate result data
     *
     * @var Mage_Shipping_Model_Rate_Result|null
     */
    protected $_result = null;

    public function isShippingLabelsAvailable()
    {
        return true;
    }

    public function isCityRequired()
    {
        return true;
    }

    public function isZipCodeRequired($countryId = null)
    {
        return true;
    }

    public function getConfig($key)
    {
        return Mage::getStoreConfig('carriers/' . $this->_code . '/' . $key);
    }

    private function getClient($type = Blogshop_Purolator_Model_Soapinterface::ESTIMATINGSERVICE)
    {
        return Mage::getSingleton('purolatormodule/soapinterface')->getClient($type);
    }

    public function isTest()
    {
        return $this->getConfig('testing') == 1;
    }

    public function isDebug()
    {
        return $this->getConfig('debug') == 1;
    }

    public function isStateProvinceRequired()
    {
        return true;
    }

    public function isActive()
    {
        return Mage::helper('purolatormodule')->isActive();
    }

    public function getAddressValidation()
    {
        return Mage::getSingleton('purolatormodule/addressvalidation');
    }

    private function getCw()
    {
        if (!$this->_cw) {
            $this->_cw = Mage::getSingleton('core/resource')->getConnection('core_write');
        }
        return $this->_cw;
    }

    private function getCr()
    {
        if (!$this->_cr) {
            $this->_cr = Mage::getSingleton('core/resource')->getConnection('core_read');
        }
        return $this->_cr;
    }

    private function getTh()
    {
        if (!$this->_th) {
            $this->_th = Mage::getSingleton('core/resource');
        }
        return $this->_th;
    }

    private function getMethodSource()
    {
        return mage::getSingleton('purolatormodule/source_method');
    }

    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        // skip if not enabled
        if (!$this->isActive()) {
            return false;
        }
        $result = Mage::getModel('shipping/rate_result');

        if ($this->getAddressValidation()->getAddressValidationActive()) {
            $address = array();
            $address['City'] = $request->getDestCity();
            $address['RegionCode'] = $request->getDestRegionCode();
            $address['CountryId'] = $request->getDestCountryId();
            $address['Postcode'] = $request->getDestPostcode();
            $tmp = $this->getAddressValidation()->testAddress($address);
            if ($tmp) {
                mage::log(__CLASS__ . "Failed address validation ");
                $error = Mage::getModel('shipping/rate_result_error');
                $error->setCarrier($this->_code);
                $error->setCarrierTitle($this->getConfigData('title'));
                //$error->setErrorMessage($errorTitle);
                $error->setErrorMessage($tmp['description']);
                $result->append($error);
                return false;
            }
        }


        $freeBoxes = 0;
        foreach ($request->getAllItems() as $item) {
            if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isShipSeparately()) {
                foreach ($item->getChildren() as $child) {
                    if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                        $freeBoxes += $item->getQty() * $child->getQty();
                    }
                }
            }
            elseif ($item->getFreeShipping()) {
                $freeBoxes += $item->getQty();
            }
        }

        $this->setFreeBoxes($freeBoxes);
        $weight = $this->getTotalNumOfBoxes($request->getPackageWeight());
        $weight = ceil($weight * 10) / 10;
        $_results = false;

        //	$_items = $this->_getItems($request);
        $_items = array();
        // we need to do something here
        // if its cart / do simple request
        // if its checkout do complex request?
        $_bits = $this->_makeSimpleRequest($request, $_items);

        $_results = $this->_cachedBits($_bits);

        if (!$_results) {
            $_results = $this->_postBits($_bits);
            // NEED ERROR CHECK HERE SO WE DONT CACHE BAD RESPONSE
            $this->_makeCache($_results, $_bits);
            Mage::log("Purolator XML RESPONSE " . print_r($_results, 1));
        }

        $handling = 0;
        if ($this->getConfig('handling') > 0) {
            $handling = $this->getConfig('handling');
        }

        if ($this->getConfig('handling_type') == 'P' && $request->getPackageValue() > 0) {
            $handling = $request->getPackageValue() * $handling;
        }

        //mage::log(__METHOD__ . " results: " . print_r($_results,1));
        $bad = (bool)(!is_array($_results)) || (property_exists($_results, 'ResponseInformation') && (property_exists($_results->ResponseInformation, 'Errors') && property_exists($_results->ResponseInformation, 'Error')));
        if (!count($_results) || is_object($_results) || $_results === false || $bad) {

            if ($this->getConfig('failover_rate') > 0) {
                $method = Mage::getModel('shipping/rate_result_method');
                $method->setCarrier($this->_code);
                $method->setCarrierTitle($this->getConfig('title'));
                $method->setMethod('Regular');
                $method->setMethodTitle($this->getConfig('failover_ratetitle'));
                $method->setCost($this->getConfig('failover_rate'));
                $method->setPrice($this->getConfig('failover_rate'));
                $method->setBadAddress($_results->ResponseInformation->Errors->Error->Description);
                $result->append($method);
            }
            else {
                $error = Mage::getModel('shipping/rate_result_error');
                $error->setCarrier($this->_code);
                $error->setCarrierTitle($this->getConfigData('title'));
                //$error->setErrorMessage($errorTitle);
                $error->setErrorMessage($_results->ResponseInformation->Errors->Error->Description);
                $result->append($error);

            }
            return $result;
        }

        $allowed_methods = explode(',', $this->getConfigData('allowed_methods'));

        foreach ($_results as $prod) {
            //	$prod['service']
            //$prod['rate']
            //$prod['transitTime']
            //$prod['deliveryDate']
            if (!in_array($prod['service'], $allowed_methods)) {
                mage::log(__METHOD__ . " skipping as not in allowed " . $prod['service']);
                continue;
            }

            $_method_title = $this->getMethodSource()->getMethodTitle($prod['service']);
            if (strlen($prod['deliveryDate'])) {
                $newdate = strtotime($prod['deliveryDate']);
                if ($this->getConfig('additionaldays') > 0) {
                    $newdate = strtotime('+' . $this->getConfig('additionaldays') . ' day', $newdate);
                }
                $_method_title .= ' ' . Mage::helper('purolatormodule')->__('Est. Delivery:') . " " . date('Y-m-d', $newdate);
            }
            mage::log(__FUNCTION__ . __LINE__ . " " . $_method_title);
            $_realprice = $_price = $prod['rate'];
            if ((int)$this->getConfig('markupval') > 0) {
                $_price = ((int)$this->getConfig('markupval') * .01) * $prod['rate'];
            }

            if ($request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes()) {
                $_realprice = '0.00';
            }

            $method = Mage::getModel('shipping/rate_result_method');
            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfig('title'));
            $method->setMethod($prod['service']);
            $method->setMethodTitle($_method_title);
            $method->setCost($prod['rate']);
            $method->setPrice($_realprice);
            $result->append($method);
        }
        return $result;
    }

    public function _cachedBits($xml_bits)
    {
        if (!$this->getConfig('usecache')) {
            return false;
        }

        $resp = 0;
        $sql = 'select xmlresponse from  ' . $this->getTh()->getTableName('purolatormodule_cache') .
            ' WHERE md5_request = "' . md5(serialize($xml_bits)) . '" and xmlresponse != "" limit 1';
        $res = $this->getCr()->fetchRow($sql);

        if (isset($res['xmlresponse']) && strlen($res['xmlresponse']) > 10) {
            return unserialize($res['xmlresponse']);
        }
        return $resp;
    }

    public function _makeCache($xml_results, $xml_bits)
    {
        if (!$this->getConfig('usecache')) {
            return false;
        }
        if ((int)rand() % 3) {
            $sql = 'delete from ' . $this->getTh()->getTableName('purolatormodule_cache') . ' where to_days(datestamp) < (to_days(now())-1)';
            $this->getCw()->query($sql);
        }
        if (count($xml_results) == 0) {
            return true;
        }
        // mysql_real_escape_string
        $sql = 'INSERT INTO ' . $this->getTh()->getTableName('purolatormodule_cache') . ' values (NULL, now(),"' .
            md5(serialize($xml_bits)) . '","' . mysql_escape_string(serialize($xml_results)) . '")';
        //echo "<fieldset>  Debug:  " . print_r($query,1) . "</fieldset>";
        $this->getCw()->query($sql);
        return true;
    }

    private function log($v, $force = false)
    {
        if ($this->isTest() || $this->isDebug() || $force) {
            mage::log($v);
        }
    }

    public function getHelper()
    {
        return mage::Helper('purolatormodule');
    }

    private function getEstimateRate($estimate)
    {
        return $estimate->TotalPrice;
        if (0) {
            return $estimate->BasePrice;
        }
    }

    public function _postBits($bits)
    {
        $results = array();

        if ($this->isDebug()) {
            mage::log(__FUNCTION__ . __LINE__ . print_r($bits, 1));
        }
        try {
            $response = $this->getClient()->GetQuickEstimate($bits);
        }
        catch (Mage_Core_Exception $e) {
            mage::log(__METHOD__ . "exception");
            mage::log(__METHOD__ . " " . $e->getMessage());
            return false;
        }
        catch (Exception $e) {
            mage::log(__METHOD__ . "exception");
            mage::log(__METHOD__ . " " . $e->getMessage());
            return false;
        }

        if ($this->isDebug()) {
            mage::log(__FUNCTION__ . __LINE__ . print_r($response, 1));
        }

        if (!property_exists($response, 'ResponseInformation') || !property_exists($response->ResponseInformation, 'Errors')) {
            $this->log("Getting rates from Purolator failed: " . print_r($bits, 1) . " Respionse " . print_r($response, 1), true);
            Mage::throwException($this->getHelper()->__("Getting rates from Purolator failed: " . print_r($bits, 1) . " Respionse " . print_r($response, 1)));
            return false;
        }

        if (property_exists($response->ResponseInformation->Errors, 'Error')) {
            $this->log("Got Error from Purolator : " . print_r($bits, 1), true);
            $this->log("Response error  " . print_r($response, 1), true);
            return $response;
        }
        if ($response && $response->ShipmentEstimates->ShipmentEstimate) {
            $d = $response->ShipmentEstimates->ShipmentEstimate;
            mage::log("here we go " . print_r($d, 1));
            if (is_object($d) && property_exists($d, 'ServiceID')) {
                $d = array($response->ShipmentEstimates->ShipmentEstimate);
            }
            //Loop through each Service returned and display the ID and TotalPrice
            //foreach($response->ShipmentEstimates->ShipmentEstimate as $estimate)
            foreach ($d as $estimate) {
                //echo "$estimate->ServiceID is available for $ $estimate->TotalPrice\n";
                $res['service'] = (string)$estimate->ServiceID;
                $res['rate'] = $this->getEstimateRate($estimate);
                $res['transitTime'] = (string)$estimate->EstimatedTransitDays;
                $res['deliveryDate'] = (string)$estimate->ExpectedDeliveryDate;
                $results[] = $res;
            }
        }
        return $results;
    }

    public function _makeSimpleRequest(Mage_Shipping_Model_Rate_Request $request, $items)
    {
        $smallrequest = new stdClass();

        $smallrequest->BillingAccountNumber = $this->getConfig('billingaccount');
        //Populate the Origin Information
        $smallrequest->SenderPostalCode = Mage::getStoreConfig('shipping/origin/postcode', $this->getStore());
        //Populate the Desination Information
        $smallrequest->ReceiverAddress = new stdClass();
        $smallrequest->TotalWeight = new stdClass();
        $smallrequest->ReceiverAddress->City = 'ukn';
        $smallrequest->ReceiverAddress->City = $request->getDestCity();
        $smallrequest->ReceiverAddress->Province = $request->getDestRegionCode();
        $smallrequest->ReceiverAddress->Country = $request->getDestCountryId();
        $smallrequest->ReceiverAddress->PostalCode = $request->getDestPostcode();
        //Populate the Package Information
        $smallrequest->PackageType = "CustomerPackaging";
        //Populate the Shipment Weight
        //assume weight is in KG for now
        $smallrequest->TotalWeight->Value = 1;
        // we have to tally weight. as products could be stored in lbs / kg etc
        // $weight = $this->getTotalNumOfBoxes($request->getPackageWeight())/2.2;
        // $weight = (ceil($weight*10) / 10);
        //getConvertedWeight
        $weight = $this->getPackageWeightLb($request);
        if ($weight > 1) {
            $smallrequest->TotalWeight->Value = $weight;
        }

        $smallrequest->TotalWeight->WeightUnit = "lb";
        return $smallrequest;
    }

    public function _makeFullRequest(Mage_Shipping_Model_Rate_Request $request, $items) {}

    private function getConvertedWeight($w, $u)
    {
        $weight = $w;
        switch ($u) {
            case Blogshop_Purolator_Model_Source_Weightunits::LB:
                $weight = round($w * 0.4535, 2);
                break;
            case Blogshop_Purolator_Model_Source_Weightunits::GR:
                $weight = round($w * 0.001, 2);
                break;
            case Blogshop_Purolator_Model_Source_Weightunits::OZ:
                $weight = round($w * 0.028349, 2);
                break;
            case Blogshop_Purolator_Model_Source_Weightunits::KG:
            default:
                $weight = $w;
                break;
        }
        return $weight;
    }

    private function getConvertedMeasure($w, $u)
    {
        $unit = $w;
        switch ($u) {
            case Blogshop_Purolator_Model_Source_Dimensionunits::MM:
                $unit = round($w * 0.1, 0);
                break;
            case Blogshop_Purolator_Model_Source_Dimensionunits::FT:
                $unit = round($w * 30.48, 0);
                break;
            case Blogshop_Purolator_Model_Source_Dimensionunits::IN:
                $unit = round($w * 2.54, 0);
                break;
            case Blogshop_Purolator_Model_Source_Dimensionunits::CM:
            default:
                $unit = $w;
                break;
        }
        return $unit;
    }

    private function getPackageWeightLb(Mage_Shipping_Model_Rate_Request $request)
    {
        $weight = 0;
        foreach ($request->getAllItems() as $item) {
            if ($item->getIsVirtual() || $item->getParentItem()) {
                continue;
            }
            $i = 0;
            // Get quantity for each Item and multiply by volume
            $qty = ($item->getQty() * 1);
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            $weight += $this->getConvertedWeight(($qty * $product->getWeight()), $product->getWeightUnits());
            // Skip virtual products
        }
        return $weight;
    }

    public function _getItems(Mage_Shipping_Model_Rate_Request $request)
    {
        $post_string = '';
        // get the items from the shipping cart
        foreach ($request->getAllItems() as $item) {
            if ($product->getIsVirtual() || $item->getParentItem()) { // Wouldn't this be better as a do-while loop?
                continue;
            }
            $i = 0;
            // Get quantity for each Item and multiply by volume
            $qty = ($item->getQty() * 1);
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            // Skip virtual products

            $i++;
        }
        return false;
    }

    public function isTrackingAvailable()
    {
        return true;
    }

    public function getTrackingInfo($tracking)
    {
        $info = array();
        $result = $this->getTracking($tracking);
        if ($result instanceof Mage_Shipping_Model_Tracking_Result) {
            if ($trackings = $result->getAllTrackings()) {
                return $trackings[0];
            }
        }
        elseif (is_string($result) && !empty($result)) {
            return $result;
        }
        return false;
    }

    public function getTracking($trackings)
    {
        if (!is_array($trackings)) {
            $trackings = array($trackings);
        }
        return $this->_getCgiTracking($trackings);
    }

    protected function _getCgiTracking($trackings)
    {
        $result = Mage::getModel('shipping/tracking_result');
        $defaults = $this->getDefaults();
        foreach ($trackings as $tracking) {
            $status = Mage::getModel('shipping/tracking_result_status');
            $status->setCarrier('ups');
            $status->setCarrierTitle($this->getConfigData('title'));
            $status->setTracking($tracking);
            $status->setPopup(1);
            $status->setUrl("https://eshiponline.purolator.com/ShipOnline/Public/Track/TrackingDetails.aspx?pin=$tracking");
            $result->append($status);
        }
        $this->_result = $result;
        return $result;
    }

    public function getMethod()
    {
        return Blogshop_Purolator_Model_Source_Method::toOptionArray();
    }

    function get_string($string, $start, $end)
    {
        $string = " " . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return "";
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    /**
     * Do request to shipment
     *
     * @param Mage_Shipping_Model_Shipment_Request $request
     * @return Varien_Object
     */
    public function requestToShipment(Mage_Shipping_Model_Shipment_Request $request)
    {
        $packages = $request->getPackages();
        if (!is_array($packages) || !$packages) {
            Mage::throwException(Mage::helper('usa')->__('No packages for request'));
        }
        if ($request->getStoreId() != null) {
            $this->setStore($request->getStoreId());
        }
        $data = array();
        foreach ($packages as $packageId => $package) {
            $request->setPackageId($packageId);
            $request->setPackagingType($package['params']['container']);
            $request->setPackageWeight($package['params']['weight']);
            $request->setPackageParams(new Varien_Object($package['params']));
            $request->setPackageItems($package['items']);
            $result = $this->_doShipmentRequest($request);

            if ($result->hasErrors()) {
                //$this->rollBack($data);
                break;
            } else {
                $data[] = array(
                    'tracking_number' => $result->getTrackingNumber(),
                    'label_content' => $result->getShippingLabelContent()
                );
            }
            if (!isset($isFirstRequest)) {
                $request->setMasterTrackingId($result->getTrackingNumber());
                $isFirstRequest = false;
            }
        }

        $response = new Varien_Object(array(
            'info' => $data
        ));

        if ($result->getErrors()) {
            $response->setErrors($result->getErrors());
        }
        return $response;
    }

    /**
     * Do shipment request to carrier web service, add tracking information and process errors in response
     * @param Varien_Object $request
     * @return Varien_Object
     */
    protected function _doShipmentRequest(Varien_Object $request)
    {

        $result = new Varien_Object();
        $client = $this->getClient(Blogshop_Purolator_Model_Soapinterface::SHIPPINGSERVICE);
        $xml = $this->_formShipmentRequest($request);

        if ($this->isDebug()) {
            Mage::log(__METHOD__ . " Printing XML request data: \n" . $xml);
        }

        // Since we're using raw XML for our CreateShipmentRequest, we have to specify XSD_ANYXML as the encoding type
        $xmlVar = new SoapVar($xml, XSD_ANYXML);

        try {
            $response = $client->CreateShipment($xmlVar);

            if ($this->isDebug()) {
                Mage::log(__METHOD__ . " Printing SOAP request data: \n" . $client->__getLastRequest());
                Mage::log(__METHOD__ . " Printing SOAP response data: \n" . print_r($response, 1));
            }
        }
        catch (Mage_Core_Exception $e) {
            Mage::log(__METHOD__ . " exception");
            Mage::log(__METHOD__ . " " . $e->getMessage());

            if (isset($e->InnerException)) {
                Mage::log(__METHOD__ . " inner exception");
                Mage::log(__METHOD__ . " " . $e->InnerException->getMessage());
                Mage::log(__METHOD__ . " trace");
                Mage::log(__METHOD__ . $e->getTrace());
            }

            if ($this->isDebug()) {
                Mage::log(__METHOD__ . " Printing SOAP request data: \n" . $client->__getLastRequest());
                Mage::log(__METHOD__ . " Printing SOAP response data: \n" . print_r($response, 1));
            }

            return false;
        }
        catch (Exception $e) {
            Mage::log(__METHOD__ . " exception");
            Mage::log(__METHOD__ . " " . $e->getMessage());

            if (isset($e->InnerException)) {
                Mage::log(__METHOD__ . " inner exception");
                Mage::log(__METHOD__ . " " . $e->InnerException->getMessage());
                Mage::log(__METHOD__ . " trace");
                Mage::log(__METHOD__ . $e->getTrace());
            }

            if ($this->isDebug()) {
                Mage::log(__METHOD__ . " Printing SOAP request data: \n" . $client->__getLastRequest());
                Mage::log(__METHOD__ . " Printing SOAP response data: \n" . print_r($response, 1));
            }

            return false;
        }

        // Debugging logs
        if ($this->isDebug()) {
            Mage::log(__CLASS__ . " " . __FILE__ . " request: \n" . print_r($request, 1));
            Mage::log(__CLASS__ . " " . __FILE__ . " request: \n" . print_r($response, 1));
        }

        $responseError = property_exists($response->ResponseInformation->Errors, 'Error');

        if (!$responseError) {
            // Add tracking number to result
            $result->setTrackingNumber($response->ShipmentPIN->Value);

            // Obtain print shipping labels and append to result
            $this->_doShipmentLabelRequest($result, $response, $request);

            $debugData = array('request' => $client->__getLastRequest(), 'result' => $client->__getLastResponse());
            $this->_debug($debugData);
        } else {
            $debugData = array(
                'request' => $client->__getLastRequest(),
                'result' => array(
                    'error' => '',
                    'code' => '',
                    'xml' => $client->__getLastResponse()
                )
            );

            if ($responseError) {
                foreach ($response->ResponseInformation->Errors as $error) {
                    $debugData['result']['code'] .= $error->Code . '; ';
                    $debugData['result']['error'] .= $error->Description . '; ';
                }
            }

            $this->_debug($debugData);
            $result->setErrors($debugData['result']['error']);
        }
        $result->setGatewayResponse($client->__getLastResponse());

        return $result;
    }

    /**
     * Accepts response from _doShipmentRequest and requests
     *
     * @param Varien_Object $result
     * @param stdClass $response
     */
    protected function _doShipmentLabelRequest(Varien_Object $result, stdClass $response, Varien_Object $request)
    {
        // Get order
        $order = $request->getOrderShipment()->getOrder();
        $shipping_address = $order->getShippingAddress();

        // Define namespace and prefix
        $namespace = Blogshop_Purolator_Model_Soapinterface::SERVICENAMESPACE;
        $ns = Blogshop_Purolator_Model_Soapinterface::SERVICEPREFIX; // Required!

        // Create GetDocumentsRequest container, so we can easily
        // strip the namespace declaration attached to the root element
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;
        $doc = $dom->createElementNS($namespace, $ns . ':GetDocumentsRequestContainer');

        // Create GetDocumentsRequest
        $getDocsRequest = $dom->createElementNS($namespace, $ns . ':GetDocumentsRequest');
        $doc->appendChild($getDocsRequest);

        // Todo: Add support for the following documents
        // Todo: GetDocuments -> return CustomsInvoice (This is folded and included)
        /*COSBillOfLading
        DangerousGoodsDeclaration
        ExpressChequeReceipt
        ExpressChequeReceiptThermal*/

        $documentCriterium = $getDocsRequest->appendChild($dom->createElementNS($namespace, $ns . ':DocumentCriterium'));
        $documentCriteria = $documentCriterium->appendChild($dom->createElementNS($namespace, $ns . ':DocumentCriteria'));

        $pin = $documentCriteria->appendChild($dom->createElementNS($namespace, $ns . ':PIN'));
        $pin->appendChild($dom->createElementNS($namespace, $ns . ':Value', $response->ShipmentPIN->Value));

        $documentTypes = $documentCriteria->appendChild($dom->createElementNS($namespace, $ns . ':DocumentTypes'));

        if ($shipping_address->getCountryId() == $this->getConfig('sender_country')) {
            if ($this->getConfig('printertype') == 'Regular') {
                $documentTypes->appendChild($dom->createElementNS($namespace, $ns . ':DocumentType', 'DomesticBillOfLading'));
            } elseif ($this->getConfig('printertype') == 'Thermal') {
                $documentTypes->appendChild($dom->createElementNS($namespace, $ns . ':DocumentType', 'DomesticBillOfLadingThermal'));
            }
        } elseif ($shipping_address->getCountryId() !== $this->getConfig('sender_country')) {
            if ($this->getConfig('printertype') == 'Regular') {
                $documentTypes->appendChild($dom->createElementNS($namespace, $ns . ':DocumentType', 'InternationalBillOfLading'));
            } elseif ($this->getConfig('printertype') == 'Thermal') {
                $documentTypes->appendChild($dom->createElementNS($namespace, $ns . ':DocumentType', 'InternationalBillOfLadingThermal'));
            }
            if ($this->getConfig('customs_indicator') == 1) {
                if ($this->getConfig('printertype') == 'Regular') {
                    $documentTypes->appendChild($dom->createElementNS($namespace, $ns . ':DocumentType', 'CustomsInvoice'));
                } elseif ($this->getConfig('printertype') == 'Thermal') {
                    $documentTypes->appendChild($dom->createElementNS($namespace, $ns . ':DocumentType', 'CustomsInvoiceThermal'));
                }
            }
            if (strlen($this->getConfig('nafta_indicator')) > 0) $documentTypes->appendChild($dom->createElementNS($namespace, $ns . ':DocumentType', 'NAFTA'));
            if (strlen($this->getConfig('fda_indicator')) > 0) $documentTypes->appendChild($dom->createElementNS($namespace, $ns . ':DocumentType', 'FDA2877'));
            if ($this->getConfig('fcc_indicator') == 1) $documentTypes->appendChild($dom->createElementNS($namespace, $ns . ':DocumentType', 'FCC740'));
        }

        $xml = $dom->saveXML($getDocsRequest, LIBXML_NOEMPTYTAG);

        // Initialize SOAP client
        $client = $this->getClient(Blogshop_Purolator_Model_Soapinterface::DOCUMENTSSERVICE);

        // Since we're using raw XML for our CreateShipmentRequest, we have to specify XSD_ANYXML as the encoding type
        $xmlVar = new SoapVar($xml, XSD_ANYXML);

        try {
            $response = $client->GetDocuments($xmlVar);
        }
        catch (Mage_Core_Exception $e) {
            mage::log(__METHOD__ . " exception");
            mage::log(__METHOD__ . " " . $e->getMessage());
            return false;
        }
        catch (Exception $e) {
            mage::log(__METHOD__ . " exception");
            mage::log(__METHOD__ . " " . $e->getMessage());
            return false;
        }

        $responseError = property_exists($response->ResponseInformation->Errors, 'Error');

        $documentURL = $response->Documents->Document->DocumentDetails->DocumentDetail->URL;
        $document = file_get_contents($documentURL);

        if (!$responseError) {
            $result->setShippingLabelContent($document);

            // Tracking number should be set in _doShipmentRequest
            //$result->setTrackingNumber($response->Documents->Document->PIN->Value);

            $debugData = array('request' => $client->__getLastRequest(), 'result' => $client->__getLastResponse());
            $this->_debug($debugData);
        } else {
            $debugData = array(
                'request' => $client->__getLastRequest(),
                'result' => array(
                    'error' => '',
                    'code' => '',
                    'xml' => $client->__getLastResponse()
                )
            );

            if ($responseError) {
                foreach ($response->ResponseInformation->Errors as $error) {
                    $debugData['result']['code'] .= $error->Code . '; ';
                    $debugData['result']['error'] .= $error->Description . '; ';
                }
            }

            $this->_debug($debugData);
            $result->setErrors($debugData['result']['error']);
        }
        $result->setGatewayResponse($client->__getLastResponse());

        return $result;
    }

    /**
     * Prepare shipment request.
     * Validate and correct request information
     *
     * @param Varien_Object $request
     *
     */
    protected function _prepareShipmentRequest(Varien_Object $request)
    {
    }

    /**
     * @param Varien_Object $request / defacto Mage_Shipping_Model_Shipment_Request
     * @return XML
     */
    protected function _formShipmentRequest(Varien_Object $request)
    {
        // IDE reference only
        //$request = new Mage_Shipping_Model_Shipment_Request();

        // Get order
        $order = $request->getOrderShipment()->getOrder();

        //$itemsCollection = $order->getItemsCollection()->getItems();
        //foreach ($itemsCollection as $item) Mage::log(__METHOD__ . " " . print_r($item->getProduct(), 1));

        // Get shipping method
        $shippingMethod = $request->getShippingMethod();
        Mage::log(__METHOD__ . " Send shipment using " . $shippingMethod);

        if ($request->getReferenceData()) {
            $referenceData = $request->getReferenceData() . $request->getPackageId();
        } else {
            $referenceData = 'Order #'
                . $order->getIncrementId()
                . ' P'
                . $request->getPackageId();
        }
		
		
		// This is platform specific stuff
		// But the vars are pretty much needed by any shipping method
        $packageParams = $request->getPackageParams();
        $customsValue = $packageParams->getCustomsValue();
        $packageHeight = $packageParams->getHeight();
        $packageWidth = $packageParams->getWidth();
        $packageLength = $packageParams->getLength();
        $packageWeight = ($packageParams->getWeight() > (float) 1) ? $packageParams->getWeight() : (float) '1.0000';
        $weightUnits = $packageParams->getWeightUnits() == Zend_Measure_Weight::POUND ? 'lb' : 'kg';
        $dimensionsUnits = $packageParams->getDimensionUnits() == Zend_Measure_Length::INCH ? 'in' : 'cm';
        $unitPrice = 0;
        $itemsQty = 0;
        $itemsDesc = array();
        $countriesOfManufacture = array();
        $productIds = array();
        $packageItems = $request->getPackageItems();
        foreach ($packageItems as $shipmentItem) {
            $item = new Varien_Object();
            $item->setData($shipmentItem);

            $unitPrice += $item->getPrice();
            $itemsQty += $item->getQty();

            $itemsDesc[] = $item->getName();
            $productIds[] = $item->getProductId();
        }

        // Get countries of manufacture
        $productCollection = Mage::getResourceModel('catalog/product_collection')
            ->addStoreFilter($request->getStoreId())
            ->addFieldToFilter('entity_id', array('in' => $productIds))
            ->addAttributeToSelect('country_of_manufacture');
        foreach ($productCollection as $product) {
            $countriesOfManufacture[] = $product->getCountryOfManufacture();
        }

        //Mage::log(__METHOD__ . " Package setup complete");

        $store_address = Mage::getStoreConfig('general/store_information/address');
        $billing_address = $order->getBillingAddress();
        $shipping_address = $order->getShippingAddress();
		
		// formShipmentRequest(untyped)
        // Define namespace and prefix
        //return formShipmentRequest
    }


    /**
     * For multi-package shipments
     * Delete requested shipments if the current shipment request failed
     *
     * @param array $data
     * @return bool
     */
    public function rollBack($data)
    {
        $requestData = $this->_getAuthDetails();
        $requestData['DeletionControl'] = 'DELETE_ONE_PACKAGE';
        foreach ($data as &$item) {
            $requestData['TrackingId'] = $item['tracking_number'];
            $client = $this->getClient(Blogshop_Purolator_Model_Soapinterface::SHIPPINGSERVICE);
            $client->VoidShipment($requestData);
        }
        return true;
    }

    /**
     * Do request to RMA shipment
     *
     * @param $request
     * @return array
     */
    public function returnOfShipment($request)
    {
        Mage::log(__METHOD__ . "Deleting shipment...");
        $request->setIsReturn(true);
        $packages = $request->getPackages();
        if (!is_array($packages) || !$packages) {
            Mage::throwException(Mage::helper('usa')->__('No packages for request'));
        }
        if ($request->getStoreId() != null) {
            $this->setStore($request->getStoreId());
        }
        $data = array();
        foreach ($packages as $packageId => $package) {
            $request->setPackageId($packageId);
            $request->setPackagingType($package['params']['container']);
            $request->setPackageWeight($package['params']['weight']);
            $request->setPackageParams(new Varien_Object($package['params']));
            $request->setPackageItems($package['items']);;


            try {
                $response = $client->VoidShipment($request);
            }
            catch (Mage_Core_Exception $e) {
                mage::log(__METHOD__ . " exception");
                mage::log(__METHOD__ . " " . $e->getMessage());
                return false;
            }
            catch (Exception $e) {
                mage::log(__METHOD__ . " exception");
                mage::log(__METHOD__ . " " . $e->getMessage());
                return false;
            }

            // Debugging logs
            if ($this->isDebug()) {
                Mage::log(__CLASS__ . " " . __FILE__ . " request: " . print_r($request, 1));
                Mage::log(__CLASS__ . " " . __FILE__ . " request: " . print_r($response, 1));
            }

            /*if ($result->hasErrors()) {
                $this->rollBack($data);
                break;
            } else {
                $data[] = array(
                    'tracking_number' => $result->getTrackingNumber(),
                    'label_content' => $result->getShippingLabelContent()
                );
            }
            if (!isset($isFirstRequest)) {
                $request->setMasterTrackingId($result->getTrackingNumber());
                $isFirstRequest = false;
            }*/
        }

        $response = new Varien_Object(array(
            'info' => $data
        ));
        if ($result->getErrors()) {
            $response->setErrors($result->getErrors());
        }
        return $response;
    }
}
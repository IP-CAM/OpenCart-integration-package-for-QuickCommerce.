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

        if ($this->isTest()) {
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

        if ($this->isTest()) {
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
        $requestClient = $this->_formShipmentRequest($request);

        if ($this->isDebug()) {
            Mage::log(__METHOD__ . " Printing XML request data: \n" . var_dump($requestClient));
        }

        try {
            $response = $client->CreateShipment($requestClient);

            if ($this->isDebug()) {
                Mage::log(__METHOD__ . " Printing SOAP request data: \n" . $client->__getLastRequest());
                Mage::log(__METHOD__ . " Printing SOAP response data: \n" . print_r($response, 1));
            }

            exit;
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
            $this->_doShipmentLabelRequest($result, $response);

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
    protected function _doShipmentLabelRequest(Varien_Object $result, stdClass $response)
    {
        // Populate GetDocuments request
        $request = new stdClass();
        $request->DocumentCriterium = new ArrayObject();

        $documentCriteria = new stdClass();
        $documentCriteria->PIN->Value = $response->ShipmentPIN->Value;
        $documentCriteria->DocumentTypes->DocumentType = "DomesticBillOfLading";

        $request->DocumentCriterium->DocumentCriteria = $documentCriteria;

        // Initialize SOAP client
        $client = $this->getClient(Blogshop_Purolator_Model_Soapinterface::DOCUMENTSSERVICE);

        try {
            $response = $client->GetDocuments($request);
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

        $packageParams = $request->getPackageParams();
        $customsValue = $packageParams->getCustomsValue();
        $packageHeight = $packageParams->getHeight();
        $packageWidth = $packageParams->getWidth();
        $packageLength = $request->getLength();
        $packageWeight = $request->getPackageWeight();
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

        // Define namespace and prefix
        $namespace = Blogshop_Purolator_Model_Soapinterface::SERVICENAMESPACE;
        $ns = Blogshop_Purolator_Model_Soapinterface::SERVICEPREFIX; // Required!

        // Create CreateShipmentRequest container, so we can easily
        // strip the namespace declaration attached to the root element
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;
        $doc = $dom->createElementNS($namespace, $ns . ':CreateShipmentRequestContainer');

        // Create CreateShipmentRequest
        $shipmentRequest = $dom->createElementNS($namespace, $ns . ':CreateShipmentRequest');
        $doc->appendChild($shipmentRequest);

        // Create Shipment elements
        $shipmentData = $shipmentRequest->appendChild($dom->createElementNS($namespace, $ns . ':Shipment'));

        $senderData = $dom->createElementNS($namespace, $ns . ':SenderInformation');
        $receiverData = $dom->createElementNS($namespace, $ns . ':ReceiverInformation');
        $shipmentDateData = $dom->createElementNS($namespace, $ns . ':ShipmentDate');
        $packageData = $dom->createElementNS($namespace, $ns . ':PackageInformation');
        if ($shipping_address->getCountryId() !== $this->getConfig('sender_country')) {
            $intlData = $dom->createElementNS($namespace, $ns . ':InternationalInformation');
        }
        $returnData = $dom->createElementNS($namespace, $ns . ':ReturnShipmentInformation');
        $paymentData = $dom->createElementNS($namespace, $ns . ':PaymentInformation');
        $pickupData = $dom->createElementNS($namespace, $ns . ':PickupInformation');
        $notificationData = $dom->createElementNS($namespace, $ns . ':NotificationInformation');
        $trackingData = $dom->createElementNS($namespace, $ns . ':TrackingReferenceInformation');
        $otherData = $dom->createElementNS($namespace, $ns . ':OtherInformation');

        // Add Shipment elements to CreateShipmentRequestContainer
        $shipmentData->appendChild($senderData);
        $shipmentData->appendChild($receiverData);
        $shipmentData->appendChild($shipmentDateData);
        $shipmentData->appendChild($packageData);
        if ($shipping_address->getCountryId() !== $this->getConfig('sender_country')) {
            $shipmentData->appendChild($intlData);
        }
        //$shipmentData->appendChild($returnData);
        $shipmentData->appendChild($paymentData);
        $shipmentData->appendChild($pickupData);
        $shipmentData->appendChild($notificationData);
        $shipmentData->appendChild($trackingData);
        //$shipmentData->appendChild($otherData);

        // Create & populate SenderInformation
        $senderRegion = Mage::getModel('directory/region')->load($this->getConfig('sender_province'));

        $address = $senderData->appendChild($dom->createElementNS($namespace, $ns . ':Address'));

        $address->appendChild($dom->createElementNS($namespace, $ns . ':Name', $this->getConfig('sender_name')));
        $address->appendChild($dom->createElementNS($namespace, $ns . ':Company', $this->getConfig('sender_company')));
        //$address->appendChild( $dom->createElementNS( $namespace, $ns . ':Department', ) );
        $address->appendChild($dom->createElementNS($namespace, $ns . ':StreetNumber', $this->getConfig('sender_streetnumber')));
        //$address->appendChild( $dom->createElementNS( $namespace, $ns . ':StreetSuffix', ) );
        $address->appendChild($dom->createElementNS($namespace, $ns . ':StreetName', $this->getConfig('sender_streetname')));
        //$address->appendChild( $dom->createElementNS( $namespace, $ns . ':StreetType', ) );
        //$address->appendChild( $dom->createElementNS( $namespace, $ns . ':StreetDirection', ) );
        //$address->appendChild( $dom->createElementNS( $namespace, $ns . ':Suite', ) );
        //$address->appendChild( $dom->createElementNS( $namespace, $ns . ':Floor', ) );
        //$address->appendChild( $dom->createElementNS( $namespace, $ns . ':StreetAddress2', ) );
        //$address->appendChild( $dom->createElementNS( $namespace, $ns . ':StreetAddress3', ) );
        $address->appendChild($dom->createElementNS($namespace, $ns . ':City', $this->getConfig('sender_city')));
        $address->appendChild($dom->createElementNS($namespace, $ns . ':Province', $senderRegion->getCode()));
        $address->appendChild($dom->createElementNS($namespace, $ns . ':Country', $this->getConfig('sender_country')));
        $address->appendChild($dom->createElementNS($namespace, $ns . ':PostalCode', $this->getConfig('sender_postalcode')));

        // Create & populate SenderInformation phone/fax
        // Set phone information
        $country_code = NULL;
        $area_code = NULL;
        $phone = NULL;
        $extension = NULL;

        if ($this->getConfig('sender_phone')) {
            $parts = explode('x', preg_replace('/\s\s+/', ' ', preg_replace('/[^0-9x\-\s]+/', '', $this->getConfig('sender_phone'))));
            if (count($parts) > 1) $extension = array_pop($parts);

            $parts = explode(' ', $parts[0]);
            if (count($parts) > 1 && count($parts) < 4) {
                list($country_code, $area_code, $phone) = $parts;
            } else {
                // Phone number format is incorrect -- try to parse anyway

                // Try to parse 1-555-555-5555 (North American) format
                if (preg_match('/^([1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$', $parts[0])) {
                    $parts = explode('-', $parts[0], 4);

                    if (count($parts) == 4) {
                        list($country_code, $area_code, $phone1, $phone2) = $parts;
                        $phone = $phone1 + $phone2;
                    } elseif (count($parts) == 3) {
                        list($country_code, $area_code, $phone) = $parts;
                    }
                }
            }
        }

        $phone_number = $address->appendChild($dom->createElementNS($namespace, $ns . ':PhoneNumber'));
        $phone_number->appendChild($dom->createElementNS($namespace, $ns . ':CountryCode', $country_code));
        $phone_number->appendChild($dom->createElementNS($namespace, $ns . ':AreaCode', $area_code));
        $phone_number->appendChild($dom->createElementNS($namespace, $ns . ':Phone', str_replace('-', '', $phone)));
        $phone_number->appendChild($dom->createElementNS($namespace, $ns . ':Extension', $extension));

        // Set fax information
        if ($this->getConfig('sender_fax')) {
            $country_code = NULL;
            $area_code = NULL;
            $phone = NULL;
            $extension = NULL;

            $parts = explode('x', preg_replace('/\s\s+/', ' ', preg_replace('/[^0-9x\-\s]+/', '', $this->getConfig('sender_fax'))));
            if (count($parts) > 1) $extension = array_pop($parts);

            $parts = explode(' ', $parts[0]);
            if (count($parts) > 1 && count($parts) < 4) {
                list($country_code, $area_code, $phone) = $parts;
            } else {
                // Phone number format is incorrect -- try to parse anyway

                // Try to parse 1-555-555-5555 (North American) format
                if (preg_match('/^([1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$', $parts[0])) {
                    $parts = explode('-', $parts[0], 4);

                    if (count($parts) == 4) {
                        list($country_code, $area_code, $phone1, $phone2) = $parts;
                        $phone = $phone1 + $phone2;
                    } elseif (count($parts) == 3) {
                        list($country_code, $area_code, $phone) = $parts;
                    }
                }
            }

            $fax_number = $address->appendChild($dom->createElementNS($namespace, $ns . ':FaxNumber'));
            $fax_number->appendChild($dom->createElementNS($namespace, $ns . ':CountryCode', $country_code));
            $fax_number->appendChild($dom->createElementNS($namespace, $ns . ':AreaCode', $area_code));
            $fax_number->appendChild($dom->createElementNS($namespace, $ns . ':Phone', str_replace('-', '', $phone)));
            $fax_number->appendChild($dom->createElementNS($namespace, $ns . ':Extension', $extension));
        }

        if (strlen($this->getConfig('tax_number')) > 0) {
            $senderData->appendChild($dom->createElementNS($namespace, $ns . ':TaxNumber', $this->getConfig('tax_number')));
        }

        if ($this->isDebug()) {
            //Mage::log(__METHOD__ . " Printing SenderInformation node: " . $dom->saveXML($senderData));
            //Mage::log(__METHOD__ . " SenderInformation OK");
        }

        // Unset address variable for later use.
        unset($address);

        // Create & populate ReceiverInformation
        $address = $receiverData->appendChild($dom->createElementNS($namespace, $ns . ':Address'));

        $address->appendChild($dom->createElementNS($namespace, $ns . ':Name', ucwords($shipping_address->firstname . ' ' . $shipping_address->lastname)));
        $address->appendChild($dom->createElementNS($namespace, $ns . ':Company', $shipping_address->company));
        //$address->appendChild( $dom->createElementNS( $namespace, $ns . ':Department', ) );
        $address->appendChild($dom->createElementNS($namespace, $ns . ':StreetNumber', NULL));
        //$address->appendChild( $dom->createElementNS( $namespace, $ns . ':StreetSuffix', ) );
        $address->appendChild($dom->createElementNS($namespace, $ns . ':StreetName', $shipping_address->street));
        //$address->appendChild( $dom->createElementNS( $namespace, $ns . ':StreetType', ) );
        //$address->appendChild( $dom->createElementNS( $namespace, $ns . ':StreetDirection', ) );
        //$address->appendChild( $dom->createElementNS( $namespace, $ns . ':Suite', ) );
        //$address->appendChild( $dom->createElementNS( $namespace, $ns . ':Floor', ) );
        //$address->appendChild( $dom->createElementNS( $namespace, $ns . ':StreetAddress2', ) );
        //$address->appendChild( $dom->createElementNS( $namespace, $ns . ':StreetAddress3', ) );
        $address->appendChild($dom->createElementNS($namespace, $ns . ':City', $shipping_address->city));
        if ($shipping_address->getCountryId() == $this->getConfig('sender_country')) {
            $address->appendChild($dom->createElementNS($namespace, $ns . ':Province', $shipping_address->getRegionCode()));
        }
        $address->appendChild($dom->createElementNS($namespace, $ns . ':Country', $shipping_address->country_id));
        $address->appendChild($dom->createElementNS($namespace, $ns . ':PostalCode', $shipping_address->postcode));

        // Create & populate ReceiverInformation phone/fax
        // Set phone information
        $country_code = NULL;
        $area_code = NULL;
        $phone = NULL;
        $extension = NULL;

        if (isset($shipping_address->telephone) && $shipping_address->telephone) {
            $parts = explode('x', preg_replace('/\s\s+/', ' ', preg_replace('/[^0-9x\-\s]+/', '', $shipping_address->telephone)));
            if (count($parts) > 1) $extension = array_pop($parts);

            $parts = explode(' ', $parts[0]);
            if (count($parts) > 1 && count($parts) < 4) {
                list($country_code, $area_code, $phone) = $parts;
            } else {
                // Phone number format is incorrect -- try to parse anyway

                // Try to parse 1-555-555-5555 (North American) format
                if (preg_match('/^([1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$', $parts[0])) {
                    $parts = explode('-', $parts[0], 4);

                    if (count($parts) == 4) {
                        list($country_code, $area_code, $phone1, $phone2) = $parts;
                        $phone = $phone1 + $phone2;
                    } elseif (count($parts) == 3) {
                        list($country_code, $area_code, $phone) = $parts;
                    }
                }
            }
        }

        $phone_number = $address->appendChild($dom->createElementNS($namespace, $ns . ':PhoneNumber'));
        $phone_number->appendChild($dom->createElementNS($namespace, $ns . ':CountryCode', $country_code));
        $phone_number->appendChild($dom->createElementNS($namespace, $ns . ':AreaCode', $area_code));
        $phone_number->appendChild($dom->createElementNS($namespace, $ns . ':Phone', str_replace('-', '', $phone)));
        $phone_number->appendChild($dom->createElementNS($namespace, $ns . ':Extension', $extension));

        // Set fax information
        if (isset($shipping_address->fax) && $shipping_address->fax) {
            $country_code = NULL;
            $area_code = NULL;
            $phone = NULL;
            $extension = NULL;

            $parts = explode('x', preg_replace('/\s\s+/', ' ', preg_replace('/[^0-9x\-\s]+/', '', $shipping_address->fax)));
            if (count($parts) > 1) $extension = array_pop($parts);

            $parts = explode(' ', $parts[0]);
            if (count($parts) > 1 && count($parts) < 4) {
                list($country_code, $area_code, $phone) = $parts;
            } else {
                // Phone number format is incorrect -- try to parse anyway

                // Try to parse 1-555-555-5555 (North American) format
                if (preg_match('/^([1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$', $parts[0])) {
                    $parts = explode('-', $parts[0], 4);

                    if (count($parts) == 4) {
                        list($country_code, $area_code, $phone1, $phone2) = $parts;
                        $phone = $phone1 + $phone2;
                    } elseif (count($parts) == 3) {
                        list($country_code, $area_code, $phone) = $parts;
                    }
                }
            }

            $fax_number = $address->appendChild($dom->createElementNS($namespace, $ns . ':FaxNumber'));
            $fax_number->appendChild($dom->createElementNS($namespace, $ns . ':CountryCode', $country_code));
            $fax_number->appendChild($dom->createElementNS($namespace, $ns . ':AreaCode', $area_code));
            $fax_number->appendChild($dom->createElementNS($namespace, $ns . ':Phone', str_replace('-', '', $phone)));
            $fax_number->appendChild($dom->createElementNS($namespace, $ns . ':Extension', $extension));
        }

        $receiverData->appendChild($dom->createElementNS($namespace, $ns . ':TaxNumber', '123456'));
        // END - Finished populating ReceiverInformation

        if ($this->isDebug()) {
            //Mage::log(__METHOD__ . " Printing ReceiverInformation node: " . $dom->saveXML($receiverData));
            //Mage::log(__METHOD__ . " ReceiverInformation OK");
        }

        // Populate ShipmentDate
        $shipmentDateData->appendChild($dom->createTextNode(date("Y-m-d"))); // Required. Current date (Format: YYYY-MM-DD). Up to 10 days in advance may be specified

        if ($this->isDebug()) {
            //Mage::log(__METHOD__ . " Printing ShipmentDate node: " . $dom->saveXML($shipmentDateData));
            Mage::log(__METHOD__ . " ShipmentDate OK");

            // Populate Shipment
            Mage::log(__METHOD__ . " Populating shipment package...");
        }
        $packageData->appendChild($dom->createElementNS($namespace, $ns . ':ServiceID', $shippingMethod)); // Required. Default - PurolatorExpress
        $packageData->appendChild($dom->createElementNS($namespace, $ns . ':Description', $this->getConfig('package_description'))); // Required.

        $totalWeight = $packageData->appendChild($dom->createElementNS($namespace, $ns . ':TotalWeight'));
        $totalWeight->appendChild($dom->createElementNS($namespace, $ns . ':Value', $packageWeight)); // Required.
        $totalWeight->appendChild($dom->createElementNS($namespace, $ns . ':WeightUnit', $weightUnits));

        $packageData->appendChild($dom->createElementNS($namespace, $ns . ':TotalPieces', $itemsQty));

        if ($this->isDebug()) {
            Mage::log(__METHOD__ . " Shipment package params set");
            //Mage::log(__METHOD__ . " Items in shipment package:" . print_r($packageItems, 1));
        }

        $pieces = $packageData->appendChild($dom->createElementNS($namespace, $ns . ':PiecesInformation'));

        foreach ($packageItems as $shipmentItem) {
            $item = new Varien_Object();
            $item->setData($shipmentItem);

            if ($this->isDebug()) {
                Mage::log(__METHOD__ . " Adding " . $item->getName() . " to PiecesInformation...");
            }

            $piece = $pieces->appendChild($dom->createElementNS($namespace, $ns . ':Piece'));

            $weight = $piece->appendChild($dom->createElementNS($namespace, $ns . ':Weight'));
            $weight->appendChild($dom->createElementNS($namespace, $ns . ':Value', $item->getWeight())); // Required
            $weight->appendChild($dom->createElementNS($namespace, $ns . ':WeightUnit', $weightUnits)); // Required

            $length = $piece->appendChild($dom->createElementNS($namespace, $ns . ':Length'));
            $length->appendChild($dom->createElementNS($namespace, $ns . ':Value', '50'));
            $length->appendChild($dom->createElementNS($namespace, $ns . ':DimensionUnit', $dimensionsUnits));

            $width = $piece->appendChild($dom->createElementNS($namespace, $ns . ':Width'));
            $width->appendChild($dom->createElementNS($namespace, $ns . ':Value', '50'));
            $width->appendChild($dom->createElementNS($namespace, $ns . ':DimensionUnit', $dimensionsUnits));

            $height = $piece->appendChild($dom->createElementNS($namespace, $ns . ':Height'));
            $height->appendChild($dom->createElementNS($namespace, $ns . ':Value', '50'));
            $height->appendChild($dom->createElementNS($namespace, $ns . ':DimensionUnit', $dimensionsUnits));

            if ($this->isDebug()) {
                Mage::log(__METHOD__ . " Added " . $item->getName() . " to PiecesInformation");
            }
        }

        if ($this->isDebug()) {
            Mage::log(__METHOD__ . " Pieces successfully added to PackageInformation");
        }

        // Options
        $optionsData = $packageData->appendChild($dom->createElementNS($namespace, $ns . ':OptionsInformation'));
        $options = $optionsData->appendChild($dom->createElementNS($namespace, $ns . ':Options'));

        // Todo: Add signature processing
        $originSignatureNotRequired = 'true';
        $residentialDelivery = 'false';

        if ($residentialDelivery === 'true') {
            $originSignatureNotRequired = 'false';
        } elseif ($residentialDelivery === 'false') {
            $originSignatureNotRequired = 'true';
        }

        $data = $options->appendChild($dom->createElementNS($namespace, $ns . ':OptionIDValuePair'));
        $data->appendChild($dom->createElementNS($namespace, $ns . ':ID', 'OriginSignatureNotRequired')); // OriginSignatureNotRequired
        $data->appendChild($dom->createElementNS($namespace, $ns . ':Value', $originSignatureNotRequired));

        $data = $options->appendChild($dom->createElementNS($namespace, $ns . ':OptionIDValuePair'));
        $data->appendChild($dom->createElementNS($namespace, $ns . ':ID', 'ResidentialSignatureDomestic')); // ResidentialSignatureDomestic
        $data->appendChild($dom->createElementNS($namespace, $ns . ':Value', $residentialDelivery));

        if ($this->isDebug()) {
            //Mage::log(__METHOD__ . " Printing PackageInformation node: " . $dom->saveXML($packageData));
            Mage::log(__METHOD__ . " PackageInformation OK");
        }

        // InternationalInformation
        if ($shipping_address->getCountryId() !== $this->getConfig('sender_country')) {
            if ($this->isDebug()) {
                Mage::log(__METHOD__ . " Populating InternationalInformation...");
            }

            $documents_only = ($this->getConfig('documents_only') == '1') ? 'true' : 'false';

            $intlData->appendChild($dom->createElementNS($namespace, $ns . ':DocumentsOnlyIndicator', $documents_only));

            $i = 0;
            $contentDetails = $intlData->appendChild($dom->createElementNS($namespace, $ns . ':ContentDetails'));
            foreach ($packageItems as $shipmentItem) {
                $item = new Varien_Object();
                $item->setData($shipmentItem);

                if ($this->isDebug()) {
                    Mage::log(__METHOD__ . " Adding " . $item->getName() . " to ContentDetails...");
                }

                $contentDetail = $contentDetails->appendChild($dom->createElementNS($namespace, $ns . ':ContentDetail'));
                $contentDetail->appendChild($dom->createElementNS($namespace, $ns . ':Description', $item->getName()));
                if ($this->getConfig('harmonized_code') && strlen($this->getConfig('harmonized_code')) > 0) {
                    $contentDetail->appendChild($dom->createElementNS($namespace, $ns . ':HarmonizedCode', $this->getConfig('harmonized_code')));
                }
                if (count($countriesOfManufacture) > 0) {
                    $contentDetail->appendChild($dom->createElementNS($namespace, $ns . ':CountryOfManufacture', $countriesOfManufacture[$i]));
                } elseif ($this->getConfig('manufacturer_origin') && strlen($this->getConfig('manufacturer_origin')) > 0) {
                    $contentDetail->appendChild($dom->createElementNS($namespace, $ns . ':CountryOfManufacture', $this->getConfig('manufacturer_origin')));
                } else {
                    $contentDetail->appendChild($dom->createElementNS($namespace, $ns . ':CountryOfManufacture', NULL));
                }
                $contentDetail->appendChild($dom->createElementNS($namespace, $ns . ':ProductCode', $item->getProductId()));
                $contentDetail->appendChild($dom->createElementNS($namespace, $ns . ':UnitValue', $item->getPrice()));
                $contentDetail->appendChild($dom->createElementNS($namespace, $ns . ':Quantity', $item->getQty()));
                $contentDetail->appendChild($dom->createElementNS($namespace, $ns . ':NAFTADocumentsIndicator', $this->getConfig('nafta_indicator')));
                if ($this->getConfig('textile_indicator') == 1) {
                    $contentDetail->appendChild($dom->createElementNS($namespace, $ns . ':TextileIndicator', $this->getConfig('textile_indicator')));
                    $contentDetail->appendChild($dom->createElementNS($namespace, $ns . ':TextileManufacturer', $this->getConfig('textile_manufacturer')));
                }
                $contentDetail->appendChild($dom->createElementNS($namespace, $ns . ':FDADocumentsIndicator', $this->getConfig('fda_indicator')));
                $contentDetail->appendChild($dom->createElementNS($namespace, $ns . ':FCCDocumentsIndicator', $this->getConfig('fcc_indicator')));
                $contentDetail->appendChild($dom->createElementNS($namespace, $ns . ':SenderIsProducerIndicator', $this->getConfig('senderproducer_indicator')));

                if ($this->isDebug()) {
                    Mage::log(__METHOD__ . " Added " . $item->getName() . " to ContentDetails");
                }
                unset($contentDetail);
                $i++;
            }
            unset($i);

            if ($this->isDebug()) {
                //Mage::log(__METHOD__ . " Printing ContentDetails node: " . $dom->saveXML($contentDetails));
                Mage::log(__METHOD__ . " ContentDetails OK");
            }

            $buyer = $receiverData->cloneNode(true);
            $buyer->appendChild($dom->createElementNS($namespace, $ns . ':TaxNumber', NULL));

            $intlData->appendChild($buyer);

            $intlData->appendChild($dom->createElementNS($namespace, $ns . ':PreferredCustomsBroker', NULL));

            $duty = $intlData->appendChild($dom->createElementNS($namespace, $ns . ':DutyInformation'));
            $duty->appendChild($dom->createElementNS($namespace, $ns . ':BillDutiesToParty', $this->getConfig('billduties')));
            $duty->appendChild($dom->createElementNS($namespace, $ns . ':BusinessRelationship', $this->getConfig('business_relationship')));
            $duty->appendChild($dom->createElementNS($namespace, $ns . ':Currency', $this->getConfig('duty_currency')));

            $intlData->appendChild($dom->createElementNS($namespace, $ns . ':ImportExportType', $this->getConfig('importexport_type')));
            $intlData->appendChild($dom->createElementNS($namespace, $ns . ':CustomsInvoiceDocumentIndicator', $this->getConfig('customs_indicator')));

            if ($this->isDebug()) {
                //Mage::log(__METHOD__ . " Printing InternationalInformation node: " . $dom->saveXML($intlData));
                Mage::log(__METHOD__ . " InternationalInformation OK");
            }
        }

        // Populate ReturnShipmentInformation

        // Populate PaymentInformation
        $paymentData->appendChild($dom->createElementNS($namespace, $ns . ':PaymentType', 'Sender')); // Sender/Receiver/ThirdParty/CreditCard
        $paymentData->appendChild($dom->createElementNS($namespace, $ns . ':RegisteredAccountNumber', $this->getConfig('registeredaccount')));
        $paymentData->appendChild($dom->createElementNS($namespace, $ns . ':BillingAccountNumber', $this->getConfig('billingaccount')));

        /*$ccData = new SimpleXMLElement('<' . $ns . ':CreditCardInformation></' . $ns . ':CreditCardInformation>');
        $ccData->addChild('CreditCardInformation->Type', NULL); // Visa/Mastercard/AmericanExpress
        $ccData->addChild('CreditCardInformation->Number', NULL);
        $ccData->addChild('CreditCardInformation->Name', NULL);
        $ccData->addChild('CreditCardInformation->ExpiryMonth', NULL);
        $ccData->addChild('CreditCardInformation->CVV', NULL);*/

        // Populate PickupInfomation
        $pickupData->appendChild($dom->createElementNS($namespace, $ns . ':PickupType', $this->getConfig('pickuptype'))); // DropOff/PreScheduled

        // Populate NotificationInformation
        if ($this->getConfig('confirmationemail') == 'Sender'):
            $notificationData->appendChild($dom->createElementNS($namespace, $ns . ':ConfirmationEmailAddress', $shipping_address->email));
            $notificationData->appendChild($dom->createElementNS($namespace, $ns . ':AdvancedShippingNotificationEmailAddress1', $this->getConfig('confirmationemail')));
        else:
            $notificationData->appendChild($dom->createElementNS($namespace, $ns . ':ConfirmationEmailAddress', $this->getConfig('confirmationemail')));
            $notificationData->appendChild($dom->createElementNS($namespace, $ns . ':AdvancedShippingNotificationEmailAddress1', $shipping_address->email));
        endif;

        if ($this->isDebug()) {
            //Mage::log(__METHOD__ . " Printing NotificationInformation node: " . $dom->saveXML($notificationData));
            Mage::log(__METHOD__ . " NotificationInformation OK");
        }

        // Populate TrackingReferenceInformation
        $trackingData->appendChild($dom->createElementNS($namespace, $ns . ':Reference1', $referenceData));
        //$trackingData->appendChild( $dom->createElementNS( $namespace, $ns . ':Reference2', "Items: " . implode(', ', $itemsDesc) ) );
        //$trackingData->appendChild( $dom->createElementNS( $namespace, $ns . ':Reference3', "Product IDs: " . implode(', ', $productIds) ) );

        // Populate OtherInformation
        //$otherData;

        // Define the Shipment Document Type
        $printerType = $dom->createElementNS($namespace, $ns . ':PrinterType', $this->getConfig('printertype'));
        $shipmentRequest->appendChild($printerType);

        //$xpath = new DOMXPath($dom);
        $xml = $dom->saveXML($shipmentRequest, LIBXML_NOEMPTYTAG);
        //$xml = $dom->saveXML($shipmentData, LIBXML_NOEMPTYTAG) . "\n" . $dom->saveXML($printerType);


        if ($this->isDebug()) {
            //Mage::log(__METHOD__ . " " . $xml); // Print request in _doShipmentRequest
        }

        return $this->parseXMLtoObject($xml);
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
            $request->setPackageItems($package['items']);

            $result = new Varien_Object();
            $client = $this->getClient(Blogshop_Purolator_Model_Soapinterface::SHIPPINGSERVICE);

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

            if ($result->hasErrors()) {
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

    function parseXMLtoObject($xml) {
        $obj = new stdClass();

        $xml = explode("\n",$xml);

        $main_n = '';

        foreach ($xml as $x) {
            $first_n = false;
            $close_n = false;
            if ($x != '') {
                $start_val = (strpos($x,">")+1);
                $end_val = strrpos($x,"<") - $start_val;
                $start_n = (strpos($x,"<")+1);
                $end_n = strpos($x,">") - $start_n;
                $n = strtolower(substr($x,$start_n,$end_n));
                if (substr_count($x,"<") == 1) {
                    if (!empty($main_n) && !stristr($n,"/")) {
                        $submain_n = $n;
                        $first_n = true;
                    } else {
                        $main_n = $n;
                        $submain_n = '';
                        $first_n = true;
                    }
                }
                if (!empty($submain_n) && stristr($submain_n,"/")) {
                    $submain_n = '';
                    $first_n = false;
                    $close_n = true;
                }
                if (!empty($main_n) && stristr($main_n,"/")) {
                    $main_n = '';
                    $submain_n = '';
                    $first_n = false;
                    $close_n = true;
                }
                $value = substr($x,$start_val,$end_val);
                if (!$close_n) {
                    if (empty($main_n)) {
                        $obj->$n = $value;
                    } else {
                        if ($first_n) {
                            if (empty($submain_n)) {
                                $obj->$main_n = new stdClass();
                            } else {
                                $obj->$main_n->$submain_n = new stdClass();
                            }
                        } else {
                            if (!empty($value)) {
                                if (empty($submain_n)) {
                                    $obj->$main_n->$n = $value;
                                } else {
                                    $obj->$main_n->$submain_n->$n = $value;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $obj;
    }
}
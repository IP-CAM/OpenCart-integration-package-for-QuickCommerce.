class QCShippingSoapInterface
{
    // namespace
    const SERVICENAMESPACE = 'http://purolator.com/pws/datatypes/v1';
    const SERVICEPREFIX = 'ns1';

    // name of WSDL
    const AVALIABILITYSERVICE = 'ServiceAvailabilityService';
    const DOCUMENTSSERVICE = 'ShippingDocumentsService';
    const ESTIMATINGSERVICE = 'EstimatingService';
    const SHIPPINGSERVICE = 'ShippingService';
    const TRACKINGSERVICE = 'TrackingService';
    protected $_code = 'purolatormodule';
    protected $_cr = false;
    protected $_cw = false;
    protected $_th = false;
    protected $_clients = array();
    // actual path after dev / production host path
    protected $_locations = array(
        self::AVALIABILITYSERVICE => '/ServiceAvailability/ServiceAvailabilityService.asmx',
        self::DOCUMENTSSERVICE => '/ShippingDocuments/ShippingDocumentsService.asmx',
        self::ESTIMATINGSERVICE => '/Estimating/EstimatingService.asmx',
        self::SHIPPINGSERVICE => '/Shipping/ShippingService.asmx',
        self::TRACKINGSERVICE => '/Tracking/TrackingService.asmx'
    );
    protected $_versions = array(
        self::AVALIABILITYSERVICE => '1.2',
        self::DOCUMENTSSERVICE => '1.1',
        self::ESTIMATINGSERVICE => '1.3',
        self::SHIPPINGSERVICE => '1.4',
        self::TRACKINGSERVICE => '1.1',
        //self::PICKUPSERVICE => '1.1'
    );

    public function getHelper()
    {
        return mage::Helper('purolatormodule');
    }

    public function getShippingModule()
    {
        return Mage::getSingleton('purolatormodule/carrier_shippingmethod');
    }

    public function isActive()
    {
        return $this->getShippingModule()->isActive();
    }

    private function validType($type)
    {
        return (bool)array_key_exists($type, $this->_locations);
    }

    public function getClient($type)
    {
        /*if (!$this->validType($type)) {
            Mage::throwException($this->getHelper()->__('Invalid Soap class type.'));
        }*/

        if (!isset($this->_clients[$type])) {
            $this->_clients[$type] = $this->createPWSSOAPClient($type);
        }
        return $this->_clients[$type];
    }

    private function isTest()
    {
        return $this->getShippingModule()->isTest();
    }

    private function isDebug()
    {
        return $this->getShippingModule()->isDebug();
    }

    private function getAddressValidationActive()
    {
        return $this->getShippingModule()->getSetting('addressvalidation');
    }

    private function getKey()
    {
        return $this->getShippingModule()->getSetting('accesskey');
    }

    private function getPass()
    {
        return $this->getShippingModule()->getSetting('accesspassword');
    }

    private function getWsdlPath($type)
    {
        $path_parts = pathinfo(__FILE__);
        $pp = explode(DS, $path_parts['dirname']);
        array_pop($pp);

        $path = 'wsdl' . DS . ($this->isTest() ? 'Development' : 'Production');
        return implode(DS, $pp) . DS . $path . DS . $type . '.wsdl';
    }

    private function log($v, $force = false)
    {
        /*if ($this->isTest() || $this->isDebug() || $force) {
            mage::log($v);
        }*/
    }

    private function getLocation($type)
    {
        //$base = ($this->isTest()) ? 'https://devwebservices.purolator.com/EWS/V1/' : 'https://webservices.purolator.com/EWS/V1/';
		$base = 'https://webservices.purolator.com/EWS/V1/';
        return $base . $this->_locations[$type];
    }

    private function getServiceVersion($type)
    {
        return $this->_versions[$type];
    }

    private function createPWSSOAPClient($type)
    {
        /** Purpose : Creates a SOAP Client in Non-WSDL mode with the appropriate authentication and
         *           header information
         **/
        //Set the parameters for the Non-WSDL mode SOAP communication with your Development/Production credentials
        /*$this->_clients[$type] = new SoapClient($this->getWsdlPath($type),
            array(
                'trace' => $this->isDebug(),
                'location' => $this->getLocation($type),
                'uri' => "http://purolator.com/pws/datatypes/v1",
                'login' => $this->getKey(),
                'password' => $this->getPass()
            )
        );*/

        $this->_clients[$type] = new QCShippingSoapClient($this->getWsdlPath($type),
            array(
                'trace' => $this->isDebug(),
                'location' => $this->getLocation($type),
                'uri' => "http://purolator.com/pws/datatypes/v1",
                'login' => $this->getKey(),
                'password' => $this->getPass(),
                'encoding' => 'utf8',
            )
        );
        //Define the SOAP Envelope Headers
        $headers[] = new SoapHeader('http://purolator.com/pws/datatypes/v1',
            'RequestContext',
            array(
                'Version' => $this->getServiceVersion($type),
                'Language' => 'en',
                'GroupID' => 'xxx',
                'RequestReference' => $type . ' Request'
            )
        );
		
		var_dump($headers);

        //Apply the SOAP Header to your client
        $this->_clients[$type]->__setSoapHeaders($headers);

        return $this->_clients[$type];
    }
}
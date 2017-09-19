<?php
class QCShippingSoapClient extends SoapClient
{

    function __construct($wsdl, $options = null)
    {
        //Mage::log(__METHOD__ . " " . print_r($options, 1));
        parent::__construct($wsdl, $options);
    }
}
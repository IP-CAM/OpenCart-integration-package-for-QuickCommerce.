<?php
abstract class BaseModel extends Model {
	function __isset($key) {
		return $this->registry->has($key);
	}
}
class AddressModel extends BaseModel {}
class ProductModel extends BaseModel {}

class QuickCommerceModelShippingPurolator extends Model {
	/* This SOAP stuff doesn't belong here -- move it later everybody's always in a fuckin' hurry */
	protected $serviceNamespace = QCShippingSoapInterface::SERVICENAMESPACE;
    protected $servicePrefix = QCShippingSoapInterface::SERVICEPREFIX;
	
	private function getWsdlPath($type)
    {
		// TODO: Base path for libs must be implementable - this is fuckin' weak
        $ds = DIRECTORY_SEPARATOR;
		$path_parts = pathinfo(__FILE__);
        $pp = explode($ds, $path_parts['dirname']);
        array_pop($pp);
        $path = 'shipping/qc_purolator_soap/wsdl' . $ds . ($this->isTest() ? 'Development' : 'Production');
        $path = 'shipping/qc_purolator_soap/wsdl' . $ds . 'Development';
        return implode($ds, $pp) . $ds . $path . $ds . $type . '.wsdl';
    }

    private function getLocation($type)
    {
        //$base = ($this->isTest()) ? 'https://devwebservices.purolator.com/EWS/V1/' : 'https://webservices.purolator.com/EWS/V1/';
		$base = 'https://devwebservices.purolator.com/EWS/V1/';
        return $base . $this->_locations[$type];
    }

    private function getServiceVersion($type)
    {
        return $this->_versions[$type];
    }
	
	public function getClient($type = QCShippingSoapInterface::ESTIMATINGSERVICE)
    {
        /*if (!$this->validType($type)) {
            Mage::throwException($this->getHelper()->__('Invalid Soap class type.'));
        }*/

        if (!isset($this->_clients[$type])) {
            $this->_clients[$type] = $this->createPWSSOAPClient($type);
        }
        return $this->_clients[$type];
    }

    protected function createPWSSOAPClient($type)
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
                'trace' => false,//$this->isDebug(),
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

        //Apply the SOAP Header to your client
        $this->_clients[$type]->__setSoapHeaders($headers);

        return $this->_clients[$type];
    }
	// END SOAP
	
	// TODO: This should be abstract or implementable via interface
	function prefix() {
		$classname = str_replace('vq2-catalog_model_shipping_', '', basename(__FILE__, '.php'));
		return $classname . '_';
	}
	
	function setItems($items) {
		// TODO: Mapping shouldn't be fuckin' done here yo
		$products = array();
		foreach ($items as $item) {
			$product = new ProductModel(new Registry());
			$product->product_id = $item['product_id'];
			$product->name = $item['model'];
			$product->length = $item['length'] = 50; // TODO: Min
			$product->width = $item['width'] = 50;
			$product->length = $item['height'] = 50;
			$product->weight = $item['weight'];
			$product->quantity = $item['quantity'];
			$product->price = $item['price'];
			
			$products[] = $product;
		}
		$items = $products;
		$this->items = $items;
		
		$this->calcTotals();
		$this->calcVolumetricDim();
	}
	
	function mapSettings($config = false) {
		//var_dump($config);
		
		$config  = ($config != false) ? $config : $this->config;
		$params = array(
				'username'			=> trim($config->get($this->prefix() . 'mid')),
				'password'			=> trim($config->get($this->prefix() . 'key')),
				'billing_acct'		=> trim($config->get($this->prefix() . 'billing_acct')),
				'reg_acct'			=> trim($config->get($this->prefix() . 'reg_acct')),
				'services'			=> explode(',', $config->get($this->prefix() . 'service')),
				'quote_type' 		=> trim($config->get($this->prefix() . 'quote_type')),
				'pickup'			=> trim($config->get($this->prefix() . 'pickup')),
				'payment'			=> trim($config->get($this->prefix() . 'payment')),
				'tax_class_id'		=> trim($config->get($this->prefix() . 'tax_class_id')),
				'geo_zone_id'		=> trim($config->get($this->prefix() . 'geo_zone_id')),
				'sort_order'		=> trim($config->get($this->prefix() . 'sort_order')),
				'display_weight'	=> trim($config->get($this->prefix() . 'display_weight')),
				'display_dims'		=> trim($config->get($this->prefix() . 'display_dims')),
				'display_date'		=> trim($config->get($this->prefix() . 'display_date')),
				'display_errors'	=> trim($config->get($this->prefix() . 'display_errors')),
				'shipping_calc'		=> trim($config->get($this->prefix() . 'shipping_calc')),
				'weight' 			=> '', //$shipping_weight,
				'length' 			=> ($config->get($this->prefix() . 'length')) ? (float)trim($config->get($this->prefix() . 'length')) : 1, // centimeters
				'width' 			=> ($config->get($this->prefix() . 'width')) ? (float)trim($config->get($this->prefix() . 'width')) : 1, // centimeters
				'height' 			=> ($config->get($this->prefix() . 'height')) ? (float)trim($config->get($this->prefix() . 'height')) : 1, // centimeters
				'adjust'			=> trim($config->get($this->prefix() . 'adjust')),
				'signature'			=> trim($config->get($this->prefix() . 'signature')),
				'insurance'			=> trim($config->get($this->prefix() . 'insurance')),
				'lettermail'		=> trim($config->get($this->prefix() . 'lettermail')),
				'lettermail_us'		=> trim($config->get($this->prefix() . 'lettermail_us')),
				'lettermail_int'	=> trim($config->get($this->prefix() . 'lettermail_int')),
				'docs_only'			=> trim($config->get($this->prefix() . 'docs_only')),
				'imp_exp'			=> trim($config->get($this->prefix() . 'imp_exp')),
				'producer'			=> trim($config->get($this->prefix() . 'producer')),
				'bill_duty'			=> trim($config->get($this->prefix() . 'bill_duty')),
				'duty_currency'		=> trim($config->get($this->prefix() . 'duty_currency')),
				'duty_relationship'	=> trim($config->get($this->prefix() . 'duty_relationship')),
				'customs'			=> trim($config->get($this->prefix() . 'customs')),
				'nafta'				=> trim($config->get($this->prefix() . 'nafta')),
				'fda'				=> trim($config->get($this->prefix() . 'fda')),
				'fcc'				=> trim($config->get($this->prefix() . 'fcc')),
				'textile'			=> trim($config->get($this->prefix() . 'textile')),
				'textile_manf'		=> trim($config->get($this->prefix() . 'textile_manf')),
				'name'				=> trim($config->get($this->prefix() . 'name')),
				'company'			=> trim($config->get($this->prefix() . 'company')),
				'street_no'			=> trim($config->get($this->prefix() . 'street_no')),
				'street_name'		=> trim($config->get($this->prefix() . 'street_name')),
				'city'				=> trim($config->get($this->prefix() . 'city')),
				'province'			=> trim($config->get($this->prefix() . 'province')),
				'postcode'			=> trim($config->get($this->prefix() . 'postcode')),
				'phone'				=> trim($config->get($this->prefix() . 'phone')),
				'fax'				=> trim($config->get($this->prefix() . 'fax')),
				//''				=> trim($config->get($this->prefix() . '')),
				//'contract-id'		=> trim($config->get($this->prefix() . 'contract_id')),
				'destination' 		=> '',//$dest,
				'code' 				=> '',//$code,
				'signature' 		=> $config->get($this->prefix() . 'signature'),
				'insurance' 		=> $config->get($this->prefix() . 'insurance'),
				//'amount' 			=> $this->cart->getSubTotal()
			);
			
		$this->settings = $params;
		
		//var_dump($params);
	}
	
	function buildPackageItem(&$dom, &$targetNode, $productModel) {
		//Mage::log(print_r($product,1));

		//if ($this->isDebug()) {
			//Mage::log(__METHOD__ . " Adding " . $item->getName() . " to PiecesInformation...");
		//}

		$piece = $targetNode->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Piece'));
		
		$weight = $piece->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Weight'));
		$this->buildWeight($dom, $weight, $productModel->weight, 'kg'); // TODO: Fix units
		$this->buildDimensions($dom, $piece, $productModel->width, $productModel->height, $productModel->length, 'cm' /*$dimensionsUnits*/);

		//if ($this->isDebug()) {
			//Mage::log(__METHOD__ . " Added " . $item->getName() . " to PiecesInformation");
		//}
	}
	
	function buildPackageInfo(&$dom, &$targetNode, $productModel) {
		/*if ($this->isDebug()) {
			//Mage::log(__METHOD__ . " Adding " . $item->getName() . " to ContentDetails...");
		}*/

		$contentDetail = $targetNode->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':ContentDetail'));
		$contentDetail->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Description', $productModel->name));
		if ($this->getSetting('harmonized_code') && strlen($this->getSetting('harmonized_code')) > 0) {
			$contentDetail->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':HarmonizedCode', $this->getSetting('harmonized_code')));
		} else {
			$contentDetail->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':HarmonizedCode', NULL));
		}
		
		// FIXME
		/*
		if (count($countriesOfManufacture) > 0 && strlen($countriesOfManufacture[$i] > 0)) {
			$contentDetail->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':CountryOfManufacture', $countriesOfManufacture[$i]));
		} elseif ($this->getSetting('manufacturer_origin')) {
			$contentDetail->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':CountryOfManufacture', $this->getSetting('manufacturer_origin')));
		} else {
			$contentDetail->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':CountryOfManufacture', NULL));
		}
		*/

		$contentDetail->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':ProductCode', $productModel->product_id));
		$contentDetail->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':UnitValue', $productModel->price));
		$contentDetail->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Quantity', $productModel->quantity));
		$contentDetail->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':NAFTADocumentsIndicator', $this->getSetting('nafta')));

		if ($this->getSetting('textile') == 1) {
			$contentDetail->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':TextileIndicator', $this->getSetting('textile')));
			$contentDetail->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':TextileManufacturer', $this->getSetting('textile_manuf')));
		}

		$contentDetail->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':FDADocumentsIndicator', $this->getSetting('fda')));
		$contentDetail->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':FCCDocumentsIndicator', $this->getSetting('fcc')));
		$contentDetail->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':SenderIsProducerIndicator', $this->getSetting('producer')));

		//if ($this->isDebug()) {
			//Mage::log(__METHOD__ . " Added " . $item->getName() . " to ContentDetails");
		//}
		//unset($contentDetail);
	}
	
	function buildBuyer() {
		$buyer = $intlData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':BuyerInformation'));
		$buyer->appendChild($receiverData->getElementsByTagName('Address')->item(0)->cloneNode(true));
		$buyer->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':TaxNumber', NULL));
	}
	
	function buildDuty() {
		$duty = $intlData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':DutyInformation'));
		$duty->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':BillDutiesToParty', $this->getSetting('bill_duty')));
		$duty->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':BusinessRelationship', $this->getSetting('duty_relationship')));
		$duty->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Currency', $this->getSetting('duty_currency')));
	}
	
	function buildPayment() {
		$paymentData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':PaymentType', 'payment')); // Sender/Receiver/ThirdParty/CreditCard
        $paymentData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':RegisteredAccountNumber', $this->getSetting('reg_acct')));
        $paymentData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':BillingAccountNumber', $this->getSetting('billing_acct')));

        // If credit card...
		$this->buildCreditCard();
	}
	
	function buildCreditCard() {
		/*$ccData = new SimpleXMLElement('<' . $this->servicePrefix . ':CreditCardInformation></' . $this->servicePrefix . ':CreditCardInformation>');
        $ccData->addChild('CreditCardInformation->Type', NULL); // Visa/Mastercard/AmericanExpress
        $ccData->addChild('CreditCardInformation->Number', NULL);
        $ccData->addChild('CreditCardInformation->Name', NULL);
        $ccData->addChild('CreditCardInformation->ExpiryMonth', NULL);
        $ccData->addChild('CreditCardInformation->CVV', NULL);*/
	}
	
	function buildAddress(&$dom, &$targetNode, $addressModel = false, $type) {
		$addressModel = ($addressModel != false) ? $addressModel : $this->getAddress($type);
		//var_dump($addressModel);
		$address = $targetNode->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Address'));
		//$address = $senderData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Address'));
		
        $address->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Name', ''));
        $address->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Company', $addressModel->company));
        //$address->appendChild( $dom->createElementNS( $this->serviceNamespace, $this->servicePrefix . ':Department', ) );
        $address->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':StreetNumber', NULL));
        //$address->appendChild( $dom->createElementNS( $this->serviceNamespace, $this->servicePrefix . ':StreetSuffix', ) );
        $address->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':StreetName', $addressModel->street_name));
        //$address->appendChild( $dom->createElementNS( $this->serviceNamespace, $this->servicePrefix . ':StreetType', ) );
        //$address->appendChild( $dom->createElementNS( $this->serviceNamespace, $this->servicePrefix . ':StreetDirection', ) );
        //$address->appendChild( $dom->createElementNS( $this->serviceNamespace, $this->servicePrefix . ':Suite', ) );
        //$address->appendChild( $dom->createElementNS( $this->serviceNamespace, $this->servicePrefix . ':Floor', ) );
        //$address->appendChild( $dom->createElementNS( $this->serviceNamespace, $this->servicePrefix . ':StreetAddress2', ) );
        //$address->appendChild( $dom->createElementNS( $this->serviceNamespace, $this->servicePrefix . ':StreetAddress3', ) );
        $address->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':City', $addressModel->city));
        // FIXME
		//if ($addressModel->getCountryId() == $this->getSetting('country')) {
            $address->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Province', $addressModel->province));
        //} else {
            //$address->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Province', NULL));
        //}
        $address->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Country', $addressModel->country));
        $address->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':PostalCode', preg_replace('/\s+/', '', $addressModel->postcode)));
	}
	
	function buildDimensions(&$dom, &$targetNode, $w, $h, $l, $units) {
		$length = $targetNode->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Length'));
        $length->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Value', $l));
        $length->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':DimensionUnit', $units));

        $width = $targetNode->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Width'));
        $width->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Value', $w));
        $width->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':DimensionUnit', $units));

        $height = $targetNode->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Height'));
        $height->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Value', $h));
        $height->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':DimensionUnit', $units));
	}
	
	// Labelled piece in case we want to be able to create boxes/packages of multiple items		
	function buildWeight(&$dom, &$targetNode, $w, $units) {
        $targetNode->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Value', $w)); // Required
        $targetNode->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':WeightUnit', $units)); // Required
	}
	
	function setPhone(&$dom, &$targetNode, $number) {
		// Set phone information
        $country_code = NULL;
        $area_code = NULL;
        $phone = NULL;
        $extension = NULL;
		
		$parts = explode('x', preg_replace('/\s\s+/', ' ', preg_replace('/[^0-9x\-\s]+/', '', $number)));
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
		
		//$fax_number = $address->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':FaxNumber'));
		$phone_number = $targetNode->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':PhoneNumber'));
        $phone_number->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':CountryCode', $country_code));
        $phone_number->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':AreaCode', $area_code));
        $phone_number->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Phone', str_replace('-', '', $phone)));
        $phone_number->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Extension', $extension));
	}
	
	function buildTracking() {
		//$trackingData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Reference1', $referenceData));
		//$trackingData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Reference1', $referenceData));
        //$trackingData->appendChild( $dom->createElementNS( $this->serviceNamespace, $this->servicePrefix . ':Reference2', "Items: " . implode(', ', $itemsDesc) ) );
        //$trackingData->appendChild( $dom->createElementNS( $this->serviceNamespace, $this->servicePrefix . ':Reference3', "Product IDs: " . implode(', ', $productIds) ) );
	}
	
	function buildNotification() {
		/*if ($this->getSetting('confirmationemail') == 'Sender') {
            $notificationData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':ConfirmationEmailAddress', $shipping_address->email));
            $notificationData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':AdvancedShippingNotificationEmailAddress1', $this->getSetting('confirmationemail')));
        } else {
            $notificationData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':ConfirmationEmailAddress', $this->getSetting('confirmationemail')));
            $notificationData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':AdvancedShippingNotificationEmailAddress1', $shipping_address->email));
        }*/

        //if ($this->isDebug()) {
            //Mage::log(__METHOD__ . " Printing NotificationInformation node: " . $dom->saveXML($notificationData));
            //Mage::log(__METHOD__ . " NotificationInformation OK");
        //}
	}
	
	function formatShipment() {
		// TEST_APP_ROUTE
		$settings = $this->mapSettings($this->config);
		//var_dump($settings);
		
        // Create CreateShipmentRequest container, so we can easily
        // strip the namespace declaration attached to the root element
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;
        $doc = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':CreateShipmentRequestContainer');

        // Create CreateShipmentRequest
        $shipmentRequest = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':CreateShipmentRequest');
        $doc->appendChild($shipmentRequest);

        // Create Shipment elements
        $shipmentData = $shipmentRequest->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Shipment'));

        $senderData = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':SenderInformation');
        $receiverData = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':ReceiverInformation');
        $shipmentDateData = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':ShipmentDate');
        $packageData = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':PackageInformation');
		// FIXME
        //if ($shipping_address->getCountryId() !== $this->getSetting('country')) {
            $intlData = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':InternationalInformation');
        //}
        $returnData = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':ReturnShipmentInformation');
        $paymentData = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':PaymentInformation');
        $pickupData = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':PickupInformation');
        $notificationData = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':NotificationInformation');
        $trackingData = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':TrackingReferenceInformation');
        $otherData = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':OtherInformation');

        // Add Shipment elements to CreateShipmentRequestContainer
        $shipmentData->appendChild($senderData);
        $shipmentData->appendChild($receiverData);
        $shipmentData->appendChild($shipmentDateData);
        $shipmentData->appendChild($packageData);
		// FIXME
        //if ($shipping_address->getCountryId() !== $this->getSetting('country')) {
            $shipmentData->appendChild($intlData);
        //}
        //$shipmentData->appendChild($returnData);
        $shipmentData->appendChild($paymentData);
        $shipmentData->appendChild($pickupData);
        $shipmentData->appendChild($notificationData);
        $shipmentData->appendChild($trackingData);
        //$shipmentData->appendChild($otherData);

        // Create & populate SenderInformation
        //$senderRegion = Mage::getModel('directory/region')->load($this->getSetting('province'));

        // Create & populate SenderInformation phone/fax
		
        // set first in case settings are needed for logic
		$this->setSender();
		
		// format
		$this->buildAddress($dom, $senderData, false, 'sender');
		
		
		// TODO: setPhone and buildPhone separation plz...
        if ($this->getSetting('phone')) $this->setPhone($dom, $senderData, $this->getSetting('phone'));
        if ($this->getSetting('fax')) $this->setPhone($dom, $senderData, $this->getSetting('fax'));

        if (strlen($this->getSetting('tax_number')) > 0) {
            $senderData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':TaxNumber', $this->getSetting('tax_number')));
        }

        //if ($this->isDebug()) {
            //Mage::log(__METHOD__ . " Printing SenderInformation node: " . $dom->saveXML($senderData));
            //Mage::log(__METHOD__ . " SenderInformation OK");
        //}
		
		// set first in case settings are needed for logic
		$this->setAddress($this->shipping_address);
		
		// format
		$this->buildAddress($dom, $receiverData, false, 'receiver');

        // Create & populate ReceiverInformation phone/fax
        // FIXME
		//if (isset($shipping_address->telephone) && $shipping_address->telephone) $this->setPhone($shipping_address->telephone);
        //if (isset($shipping_address->fax) && $shipping_address->fax) $this->setPhone($shipping_address->fax);

        //$receiverData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':TaxNumber', '123456'));
        // END - Finished populating ReceiverInformation

        //if ($this->isDebug()) {
            //Mage::log(__METHOD__ . " Printing ReceiverInformation node: " . $dom->saveXML($receiverData));
            //Mage::log(__METHOD__ . " ReceiverInformation OK");
        //}

        // Populate ShipmentDate
        $shipmentDateData->appendChild($dom->createTextNode(date("Y-m-d"))); // Required. Current date (Format: YYYY-MM-DD). Up to 10 days in advance may be specified

        //if ($this->isDebug()) {
            //Mage::log(__METHOD__ . " Printing ShipmentDate node: " . $dom->saveXML($shipmentDateData));
            //Mage::log(__METHOD__ . " ShipmentDate OK");

            // Populate Shipment
            //Mage::log(__METHOD__ . " Populating shipment package...");
        //}
		
        $packageData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':ServiceID', '' /*$shippingMethod*/)); // Required. Default - PurolatorExpress
        $packageData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Description', $this->getSetting('package_description'))); // Required

        $totalWeight = $packageData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':TotalWeight'));
        $this->buildWeight($dom, $totalWeight, $this->totalWeight, 'kg');

        //$packageData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':TotalPieces', $itemsQty));
        $packageData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':TotalPieces', $this->totalPieces));

        //if ($this->isDebug()) {
            //Mage::log(__METHOD__ . " Shipment package params set");
            //Mage::log(__METHOD__ . " Items in shipment package:" . print_r($packageItems, 1));
        //}

        $pieces = $packageData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':PiecesInformation'));

        foreach ($this->items as $item) {
            $this->buildPackageItem($dom, $pieces, $item);
        }

        // 1 piece per package, as current Magento package implementation is not consistent with Purolator's API
        $piece = $pieces->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Piece'));

        // buildWeight
		// buildDimensions

        //if ($this->isDebug()) {
            //Mage::log(__METHOD__ . " Pieces successfully added to PackageInformation");
        //}

        // Options
        $optionsData = $packageData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':OptionsInformation'));
        $options = $optionsData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Options'));

        // Todo: Add signature processing
        $residentialDelivery = 'false';

        // FIXME
		if (true /*$shipping_address->getCountryId() == $this->getSetting('country')*/) {
            // Domestic
            $originSignatureNotRequired = 'true';
        } else {
            // International
            $originSignatureNotRequired = 'false';
        }

        if ($residentialDelivery == 'true') $originSignatureNotRequired = 'false';

        $data = $options->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':OptionIDValuePair'));
        $data->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':ID', 'OriginSignatureNotRequired')); // OriginSignatureNotRequired
        $data->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Value', $originSignatureNotRequired));

        // FIXME
		//if ($shipping_address->getCountryId() == $this->getSetting('country')) {
            $data = $options->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':OptionIDValuePair'));
            $data->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':ID', 'ResidentialSignatureDomestic')); // ResidentialSignatureDomestic
            $data->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Value', $residentialDelivery));
        //}
		
		

        //if ($this->isDebug()) {
            //Mage::log(__METHOD__ . " Printing PackageInformation node: " . $dom->saveXML($packageData));
            //Mage::log(__METHOD__ . " PackageInformation OK");
        //}

        // InternationalInformation
        //if ($shipping_address->getCountryId() !== $this->getSetting('country')) {
            //if ($this->isDebug()) {
                //Mage::log(__METHOD__ . " Populating InternationalInformation...");
            //}

            $documents_only = ($this->getSetting('documents_only') == '1') ? 'true' : 'false';

            $intlData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':DocumentsOnlyIndicator', $documents_only));

            $i = 0;
            $contentDetails = $intlData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':ContentDetails'));
			
			

            // Check country codes
            //if ($this->isDebug() && count($countriesOfManufacture) > 0) {
                //Mage::log("Country array exists:" . print_r($countriesOfManufacture, 1));
            //}

            foreach ($this->items as $item) {
                $this->buildPackageInfo($dom, $contentDetails, $item);
            }

            //if ($this->isDebug()) {
                //Mage::log(__METHOD__ . " Printing ContentDetails node: " . $dom->saveXML($contentDetails));
                //Mage::log(__METHOD__ . " ContentDetails OK");
            //}

            //$this->buildBuyer();

            $intlData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':PreferredCustomsBroker', NULL));

            //$this->buildDuty();

            $intlData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':ImportExportType', $this->getSetting('imp_exp')));
            $intlData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':CustomsInvoiceDocumentIndicator', $this->getSetting('customs')));

            //if ($this->isDebug()) {
                //Mage::log(__METHOD__ . " Printing InternationalInformation node: " . $dom->saveXML($intlData));
                //Mage::log(__METHOD__ . " InternationalInformation OK");
            //}
        //}

        // Populate ReturnShipmentInformation

        // Populate PaymentInformation
        //$this->buildPayment();

        // Populate PickupInfomation
        $pickupData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':PickupType', $this->getSetting('pickup'))); // DropOff/PreScheduled

        // Populate NotificationInformation
        $this->buildNotification();

        // Populate TrackingReferenceInformation
        $this->buildTracking();

        // Populate OtherInformation
        //$otherData;

        // Define the Shipment Document Type
        $printerType = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':PrinterType', $this->getSetting('printertype'));
        $shipmentRequest->appendChild($printerType);
		
		/*var_dump($doc);
		echo htmlspecialchars($dom->saveXML($doc, LIBXML_NOEMPTYTAG));
		exit;*/

        //$xpath = new DOMXPath($dom);
        $xml = $dom->saveXML($shipmentRequest, LIBXML_NOEMPTYTAG);


        //if ($this->isDebug()) {
            //Mage::log(__METHOD__ . " " . $xml); // Print request in _doShipmentRequest
            //exit;
        //}

        return $xml;
	}
	
	function formatQuoteObj() {
		//Populate the Origin Information
		$request->Shipment->SenderInformation->Address->Name = "Aaron Summer";
		$request->Shipment->SenderInformation->Address->StreetNumber = "1234";
		$request->Shipment->SenderInformation->Address->StreetName = "Main Street";
		$request->Shipment->SenderInformation->Address->City = "Mississauga";
		$request->Shipment->SenderInformation->Address->Province = "ON";
		$request->Shipment->SenderInformation->Address->Country = "CA";
		$request->Shipment->SenderInformation->Address->PostalCode = "L4W5M8";    
		$request->Shipment->SenderInformation->Address->PhoneNumber->CountryCode = "1";
		$request->Shipment->SenderInformation->Address->PhoneNumber->AreaCode = "905";
		$request->Shipment->SenderInformation->Address->PhoneNumber->Phone = "5555555";
		//Populate the Desination Information
		$request->Shipment->ReceiverInformation->Address->Name = "Aaron Summer";
		$request->Shipment->ReceiverInformation->Address->StreetNumber = "2245";
		$request->Shipment->ReceiverInformation->Address->StreetName = "Douglas Road";
		$request->Shipment->ReceiverInformation->Address->City = "Burnaby";
		$request->Shipment->ReceiverInformation->Address->Province = "BC";
		$request->Shipment->ReceiverInformation->Address->Country = "CA";
		$request->Shipment->ReceiverInformation->Address->PostalCode = "V5C1A1";    
		$request->Shipment->ReceiverInformation->Address->PhoneNumber->CountryCode = "1";
		$request->Shipment->ReceiverInformation->Address->PhoneNumber->AreaCode = "604";
		$request->Shipment->ReceiverInformation->Address->PhoneNumber->Phone = "2982181";

		//Future Dated Shipments - YYYY-MM-DD format
		$request->Shipment->ShipmentDate = date("Y-m-d");

		//Populate the Package Information
		$request->Shipment->PackageInformation->TotalWeight->Value = "10";
		$request->Shipment->PackageInformation->TotalWeight->WeightUnit = "lb";
		$request->Shipment->PackageInformation->TotalPieces = "1";
		$request->Shipment->PackageInformation->ServiceID = "PurolatorExpress";

		//Define OptionsInformation
		//ResidentialSignatureDomestic
		//$request->Shipment->PackageInformation->OptionsInformation->Options->OptionIDValuePair->ID = "ResidentialSignatureDomestic";
		//$request->Shipment->PackageInformation->OptionsInformation->Options->OptionIDValuePair->Value = "true";

		//ResidentialSignatureIntl
		//$request->Shipment->PackageInformation->OptionsInformation->Options->OptionIDValuePair->ID = "ResidentialSignatureIntl";
		//$request->Shipment->PackageInformation->OptionsInformation->Options->OptionIDValuePair->Value = "true";

		//OriginSignatureNotRequired
		$request->Shipment->PackageInformation->OptionsInformation->Options->OptionIDValuePair->ID = "OriginSignatureNotRequired";
		$request->Shipment->PackageInformation->OptionsInformation->Options->OptionIDValuePair->Value = "true";

		//Populate the Payment Information
		$request->Shipment->PaymentInformation->PaymentType = "Sender";
		$request->Shipment->PaymentInformation->BillingAccountNumber = $this->getSetting('billing_acct');
		$request->Shipment->PaymentInformation->RegisteredAccountNumber = $this->getSetting('reg_acct');
		//Populate the Pickup Information
		$request->Shipment->PickupInformation->PickupType = "DropOff";
		$request->ShowAlternativeServicesIndicator = "true";

		//Define OptionsInformation
		$request->OptionsInformation->Options->OptionIDValuePair->ID = "residentialsignaturedomestic";
		$request->OptionsInformation->Options->OptionIDValuePair->Value = "true";
		//Shipment Reference
		$request->Shipment->TrackingReferenceInformation->Reference1 = "Reference For Shipment";
		//Define the Shipment Document Type
		$request->PrinterType = "Thermal";

		//Define OptionsInformation
		$request->OptionsInformation->Options->OptionIDValuePair->ID = "residentialsignaturedomestic";
		$request->OptionsInformation->Options->OptionIDValuePair->Value = "true";
		
		return $request;
	}
	
	// Fuck this actually sends a shipment
	// TODO: Interface method
	function formatQuote($request = false) {
		// TEST_APP_ROUTE
		$settings = $this->mapSettings($this->config);
		//var_dump($settings);
		
        // Create CreateestimateRequest container, so we can easily
        // strip the namespace declaration attached to the root element
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;
        $doc = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':GetFullEstimateRequestContainer');

        // Create CreateestimateRequest
        $estimateRequest = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':GetFullEstimateRequest');
        $doc->appendChild($estimateRequest);
		
		$alternativeServices = $estimateRequest->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':ShowAlternativeServicesIndicator', true));

        // Create Shipment elements
        $shipmentData = $estimateRequest->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Shipment'));
        $senderData = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':SenderInformation');
        $receiverData = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':ReceiverInformation');
        $shipmentDateData = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':ShipmentDate');
        $packageData = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':PackageInformation');
		// FIXME
        //if ($shipping_address->getCountryId() !== $this->getSetting('country')) {
            $intlData = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':InternationalInformation');
        //}
        $returnData = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':ReturnShipmentInformation');
        $paymentData = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':PaymentInformation');
        $pickupData = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':PickupInformation');
        $notificationData = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':NotificationInformation');
        $trackingData = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':TrackingReferenceInformation');
        $otherData = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':OtherInformation');

        // Add Shipment elements to CreateestimateRequestContainer
        $shipmentData->appendChild($senderData);
        $shipmentData->appendChild($receiverData);
        $shipmentData->appendChild($shipmentDateData);
        $shipmentData->appendChild($packageData);
		// FIXME
        //if ($shipping_address->getCountryId() !== $this->getSetting('country')) {
            $shipmentData->appendChild($intlData);
        //}
        //$shipmentData->appendChild($returnData);
        $shipmentData->appendChild($paymentData);
        $shipmentData->appendChild($pickupData);
        $shipmentData->appendChild($notificationData);
        $shipmentData->appendChild($trackingData);
        //$shipmentData->appendChild($otherData);

        // Create & populate SenderInformation
        //$senderRegion = Mage::getModel('directory/region')->load($this->getSetting('province'));

        // Create & populate SenderInformation phone/fax
		
        // set first in case settings are needed for logic
		$this->setSender();
		
		// format
		$this->buildAddress($dom, $senderData, false, 'sender');
		
		
		// TODO: setPhone and buildPhone separation plz...
        if ($this->getSetting('phone')) $this->setPhone($dom, $senderData, $this->getSetting('phone'));
        if ($this->getSetting('fax')) $this->setPhone($dom, $senderData, $this->getSetting('fax'));

        if (strlen($this->getSetting('tax_number')) > 0) {
            $senderData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':TaxNumber', $this->getSetting('tax_number')));
        }

        //if ($this->isDebug()) {
            //Mage::log(__METHOD__ . " Printing SenderInformation node: " . $dom->saveXML($senderData));
            //Mage::log(__METHOD__ . " SenderInformation OK");
        //}
		
		// set first in case settings are needed for logic
		$this->setAddress($this->shipping_address);
		
		// format
		$this->buildAddress($dom, $receiverData, false, 'receiver');

        // Create & populate ReceiverInformation phone/fax
        // FIXME
		//if (isset($shipping_address->telephone) && $shipping_address->telephone) $this->setPhone($shipping_address->telephone);
        //if (isset($shipping_address->fax) && $shipping_address->fax) $this->setPhone($shipping_address->fax);

        //$receiverData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':TaxNumber', '123456'));
        // END - Finished populating ReceiverInformation

        //if ($this->isDebug()) {
            //Mage::log(__METHOD__ . " Printing ReceiverInformation node: " . $dom->saveXML($receiverData));
            //Mage::log(__METHOD__ . " ReceiverInformation OK");
        //}

        // Populate ShipmentDate
        $shipmentDateData->appendChild($dom->createTextNode(date("Y-m-d"))); // Required. Current date (Format: YYYY-MM-DD). Up to 10 days in advance may be specified

        //if ($this->isDebug()) {
            //Mage::log(__METHOD__ . " Printing ShipmentDate node: " . $dom->saveXML($shipmentDateData));
            //Mage::log(__METHOD__ . " ShipmentDate OK");

            // Populate Shipment
            //Mage::log(__METHOD__ . " Populating shipment package...");
        //}
		
        $packageData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':ServiceID', '' /*$shippingMethod*/)); // Required. Default - PurolatorExpress
        $packageData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Description', $this->getSetting('package_description'))); // Required

        $totalWeight = $packageData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':TotalWeight'));
        $this->buildWeight($dom, $totalWeight, $this->totalWeight, 'kg');

        //$packageData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':TotalPieces', $itemsQty));
        $packageData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':TotalPieces', $this->totalPieces));

        //if ($this->isDebug()) {
            //Mage::log(__METHOD__ . " Shipment package params set");
            //Mage::log(__METHOD__ . " Items in shipment package:" . print_r($packageItems, 1));
        //}

        $pieces = $packageData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':PiecesInformation'));

        foreach ($this->items as $item) {
            $this->buildPackageItem($dom, $pieces, $item);
        }

        // 1 piece per package, as current Magento package implementation is not consistent with Purolator's API
        $piece = $pieces->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Piece'));

        // buildWeight
		// buildDimensions

        //if ($this->isDebug()) {
            //Mage::log(__METHOD__ . " Pieces successfully added to PackageInformation");
        //}

        // Options
        $optionsData = $packageData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':OptionsInformation'));
        $options = $optionsData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Options'));

        // Todo: Add signature processing
        $residentialDelivery = 'false';

        // FIXME
		if (true /*$shipping_address->getCountryId() == $this->getSetting('country')*/) {
            // Domestic
            $originSignatureNotRequired = 'true';
        } else {
            // International
            $originSignatureNotRequired = 'false';
        }

        if ($residentialDelivery == 'true') $originSignatureNotRequired = 'false';

        $data = $options->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':OptionIDValuePair'));
        $data->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':ID', 'OriginSignatureNotRequired')); // OriginSignatureNotRequired
        $data->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Value', $originSignatureNotRequired));

        // FIXME
		//if ($shipping_address->getCountryId() == $this->getSetting('country')) {
            $data = $options->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':OptionIDValuePair'));
            $data->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':ID', 'ResidentialSignatureDomestic')); // ResidentialSignatureDomestic
            $data->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Value', $residentialDelivery));
        //}
		
		

        //if ($this->isDebug()) {
            //Mage::log(__METHOD__ . " Printing PackageInformation node: " . $dom->saveXML($packageData));
            //Mage::log(__METHOD__ . " PackageInformation OK");
        //}

        // InternationalInformation
        //if ($shipping_address->getCountryId() !== $this->getSetting('country')) {
            //if ($this->isDebug()) {
                //Mage::log(__METHOD__ . " Populating InternationalInformation...");
            //}

            $documents_only = ($this->getSetting('documents_only') == '1') ? 'true' : 'false';

            $intlData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':DocumentsOnlyIndicator', $documents_only));

            $i = 0;
            $contentDetails = $intlData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':ContentDetails'));
			
			

            // Check country codes
            //if ($this->isDebug() && count($countriesOfManufacture) > 0) {
                //Mage::log("Country array exists:" . print_r($countriesOfManufacture, 1));
            //}

            foreach ($this->items as $item) {
                $this->buildPackageInfo($dom, $contentDetails, $item);
            }

            //if ($this->isDebug()) {
                //Mage::log(__METHOD__ . " Printing ContentDetails node: " . $dom->saveXML($contentDetails));
                //Mage::log(__METHOD__ . " ContentDetails OK");
            //}

            //$this->buildBuyer();

            $intlData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':PreferredCustomsBroker', NULL));

            //$this->buildDuty();

            $intlData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':ImportExportType', $this->getSetting('imp_exp')));
            $intlData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':CustomsInvoiceDocumentIndicator', $this->getSetting('customs')));

            //if ($this->isDebug()) {
                //Mage::log(__METHOD__ . " Printing InternationalInformation node: " . $dom->saveXML($intlData));
                //Mage::log(__METHOD__ . " InternationalInformation OK");
            //}
        //}

        // Populate ReturnShipmentInformation

        // Populate PaymentInformation
        //$this->buildPayment();

        // Populate PickupInfomation
        $pickupData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':PickupType', $this->getSetting('pickup'))); // DropOff/PreScheduled

        // Populate NotificationInformation
        $this->buildNotification();

        // Populate TrackingReferenceInformation
        $this->buildTracking();

        // Populate OtherInformation
        //$otherData;

        // Define the Shipment Document Type
        $printerType = $dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':PrinterType', $this->getSetting('printertype'));
        $estimateRequest->appendChild($printerType);
		
		/*var_dump($doc);
		echo htmlspecialchars($dom->saveXML($doc, LIBXML_NOEMPTYTAG));
		exit;*/

        //$xpath = new DOMXPath($dom);
        $xml = $dom->saveXML($estimateRequest, LIBXML_NOEMPTYTAG);


        //if ($this->isDebug()) {
            //Mage::log(__METHOD__ . " " . $xml); // Print request in _doestimateRequest
            //exit;
        //}

        return $xml;
	}
}
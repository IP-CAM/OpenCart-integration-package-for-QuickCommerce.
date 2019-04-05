<?php
abstract class BaseModel extends Model {
	function __isset($key) {
		return $this->registry->has($key);
	}
}
class AddressModel extends BaseModel {}
class ProductModel extends BaseModel {}

class QuickCommerceModelShipping extends Model {
	protected $settings = false; // test only
	protected $billing_address = null;
	protected $shipping_address = null;
	protected $phone = null;
	protected $fax = null;
	
	protected $totalWeight = 0;
	protected $totalPieces = 0;
	
	protected $length = 0;
	protected $width = 0;
	protected $height = 0;
	
	protected $items = array();
	
	protected $_clients = array();
	
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

    private function log($v, $force = false)
    {
        /*if ($this->isTest() || $this->isDebug() || $force) {
            mage::log($v);
        }*/
    }

    function getLocation($type) {}

    function getServiceVersion($type) {}
	
	protected function getKey()
    {
        return $this->getSetting('username');
    }

    protected function getPass()
    {
        return $this->getSetting('password');
    }
	
	private function isTest()
    {
        return $this->getSetting('test');
    }
	
	public function getClient() {}
	
	// TODO: This should be abstract or implementable via interface
	function prefix() {}
	
	function getSetting($key) {
		if (array_key_exists($key, $this->settings)) {
			return $this->settings[$key];
		}
		
		return null;
	}
	
	function calcDimensions() {
		$config  = ($config != false) ? $config : $this->config;
		
		if (trim($config->get($this->prefix() . 'shipping_calc') == 'volumetric')) {
			// Volume Packaging Estimated Dimensions
			$volume = 0;
			$this->calcVolumetricDim();
		} elseif (trim($config->get($this->prefix() . 'shipping_calc') == 'linear')) {
			$this->calcLinearDim();
		}

		// Fallback
		$this->calcDim();
	}
	
	function getItems() {
		return $this->items;
	}
	
	function calcTotals() {
		$totalWeight = 0;
		$totalPieces = 0;
		
		foreach ($this->items as $item) {
			$totalWeight += $item->weight;
			$i = 0;
			for ($i = 1; $i <= $item->quantity; $i++) {
				$totalPieces++;
			}
		}
		
		$this->totalWeight = $totalWeight;
		$this->totalPieces = $totalPieces;
	}
	
	function calcVolumetricDim() {		
		// Volume Packaging Estimated Dimensions
		$volume = 0;
		foreach ($this->items as $item) {
			$i = 0;
			for ($i = 1; $i <= $item->quantity; $i++) {
				$l = (float)$item->length ? $item->length : 1;
				$w = (float)$item->width ? $item->width : 1;
				$h = (float)$item->height ? $item->height : 1;
				$volume += ($l * $w * $h);
			}
		}
		if ($volume) {
			$this->length = $this->width = $this->height = round(pow($volume, 1/3), 0);
		}
	}
	
	function calcLinearDim() {
		// Linear Packaging Estimated Dimensions
		// Assume height is always the shortest way to stack items and sum all heights together
		// But length and width will be the MAX length or Width of all products
		$l = $w = $h = 0;
		$max_l = $max_w = 0;
		foreach ($this->items as $item) {
			
			// Set height to the shortest side
			$dims = array($item->length, $item->width, $item->height);
			sort($dims);
			$item->height = $dims[0];
			$item->width  = $dims[1];
			$item->length = $dims[2];
			
			$i = 0;
			for ($i = 1; $i <= $item->quantity; $i++) {
				$l = (float)$item->length ? $item->length : 1; // Get Max length
				$max_l = max($l, $max_l);
				$w = (float)$item->width ? $item->width  : 1; // Get Max Width
				$max_w = max($w, $max_w);
				$h += (float)$item->height ? $item->height : 1; // Sum Heights
			}
		}
		$this->length = $max_l;
		$this->width  = $max_w;
		$this->height = $h;
	}
	
	function calcDim() {		
		$length = '';
		$width  = '';
		$height = '';
	}
	
	function mapSettings($config = false) {}}
	
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
	
	function setBuyer() {
		$buyer = $intlData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':BuyerInformation'));
		$buyer->appendChild($receiverData->getElementsByTagName('Address')->item(0)->cloneNode(true));
		$buyer->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':TaxNumber', NULL));
	}
	
	function setDuty() {
		$duty = $intlData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':DutyInformation'));
		$duty->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':BillDutiesToParty', $this->getSetting('bill_duty')));
		$duty->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':BusinessRelationship', $this->getSetting('duty_relationship')));
		$duty->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':Currency', $this->getSetting('duty_currency')));
	}
	
	function setPayment() {
		$paymentData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':PaymentType', 'payment')); // Sender/Receiver/ThirdParty/CreditCard
        $paymentData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':RegisteredAccountNumber', $this->getSetting('reg_acct')));
        $paymentData->appendChild($dom->createElementNS($this->serviceNamespace, $this->servicePrefix . ':BillingAccountNumber', $this->getSetting('billing_acct')));

        // If credit card...
		$this->setCreditCard();
	}
	
	function setCreditCard() {
		/*$ccData = new SimpleXMLElement('<' . $this->servicePrefix . ':CreditCardInformation></' . $this->servicePrefix . ':CreditCardInformation>');
        $ccData->addChild('CreditCardInformation->Type', NULL); // Visa/Mastercard/AmericanExpress
        $ccData->addChild('CreditCardInformation->Number', NULL);
        $ccData->addChild('CreditCardInformation->Name', NULL);
        $ccData->addChild('CreditCardInformation->ExpiryMonth', NULL);
        $ccData->addChild('CreditCardInformation->CVV', NULL);*/
	}
	
	function setSender() {		
		// Transform
		$address = new AddressModel(new Registry());
		$address->type = 'sender';
		$address->firstname = $this->getSetting('name');
		$address->company = $this->getSetting('company');
		$address->street_no = $this->getSetting('street_no');
		$address->street_name = $this->getSetting('street_name');
		$address->city = $this->getSetting('city');
		$address->province = $this->getSetting('province');
		$address->postcode = $this->getSetting('postcode');
		$address->country = 'Canada';
		
		$this->setAddress($address);
		
		// Just for reference
		/*'name'				=> trim($config->get($this->prefix() . 'name')),
		'company'			=> trim($config->get($this->prefix() . 'company')),
		'street_no'			=> trim($config->get($this->prefix() . 'street_no')),
		'street_name'		=> trim($config->get($this->prefix() . 'street_name')),
		'city'				=> trim($config->get($this->prefix() . 'city')),
		'province'			=> trim($config->get($this->prefix() . 'province')),
		'postcode'			=> trim($config->get($this->prefix() . 'postcode')),
		'phone'				=> trim($config->get($this->prefix() . 'phone')),
		'fax'				=> trim($config->get($this->prefix() . 'fax'))*/
	}
	
	function setAddress($model, $type = false) {
		// TODO: This should be somewhere else and call parent method?
		if (is_array($model)) {
			// Transform
			$address = new AddressModel(new Registry());
			$address->type = $type;
			$address->firstname = $model['firstname'];
			$address->lastname = $model['lastname'];
			$address->company = $model['company'];
			$address->street_no = '';
			$address->street_name = $model['address_1'];
			$address->city = $model['city'];
			$address->province = $model['zone'];
			$address->postcode = $model['postcode'];
			$address->country = $model['iso_code_3'];
			
			$model = $address;
		}
		
		if ($model && !empty($model->type)) {
			if ($model->type == 'sender') {
				$this->sender_address = $model;
			}
			elseif ($model->type == 'receiver') {
				$this->receiver_address = $model;
			}
			elseif ($model->type == 'billing') {
				$this->billing_address = $model;
			}
			elseif ($model->type == 'shipping') {
				$this->shipping_address = $model;
			}
		}
	}
	
	function getAddress($type) {
		$model = null;
		if ($type == 'sender') {
			$model = $this->sender_address;
		}
		elseif ($type == 'receiver') {
			$model = $this->receiver_address;
		}
		elseif ($type == 'shipping') {
			$model = $this->shipping_address;
		}
		elseif ($type == 'billing') {
			$model = $this->billing_address;
		}
		
		return $model;
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
}
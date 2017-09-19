<?php
/**
 * @author Qphoria@gmail.com
 * @web http://www.opencartguru.com/
 *
 * @usage
 *		$params = array(
 *			'username'			=> 'fe61047db2ed681c', // API username
 *			'password'			=> '571273b849e66d29aee19f', // API password
 *			'customer-number'	=> '0009054215', // Customer Number from My profile page
 *			'contract-id'		=> '1234', // Needed for commercial rates
 *			'quote-type' 		=> 'commercial', // 'commercial' means negotiated rates, 'counter' means standard everyday rates
 *			'weight' 			=> '5', // kilograms
 *			'length' 			=> '5', // centimeters
 *			'width' 			=> '5', // centimeters
 *			'height' 			=> '5', // centimeters
 *			'origin-postal-code'=> 'H2B1A0', // Originating Canada post code
 *			'destination' 		=> 'domestic', // 'domestic', 'united-states', or 'international'
 *			'code' 				=> 'K1K4T3', // postal-code, zip-code, or country-code based on the destination param.
 *		);
 *
 *		$cpwsrest = New cpwsrest();
 *		$cpwsrest->getRates($params);
 */

class cpwsrest {

	private $_maxLogSize			= 512000;
	private $_options 				= array();
	private $_prodUrl 				= 'https://soa-gw.canadapost.ca/rs/ship/price';
	private $_testUrl 				= 'https://ct.soa-gw.canadapost.ca/rs/ship/price';
	private $_log;
	public	$test 					= false;

	public function __construct($logpath = '') {
		if ($logpath && is_dir($logpath) && is_writable($logpath)) { $this->_log = $logpath .  basename(__FILE__, '.class.php') . '_debug.txt'; }
	}

	/**
	 * addOption()
	 *
	 * @param string $option SO, COV, COD, PA18, PA19, HFP, DNS, LAD. COV requires value to be set
	 * @return null
	 * @description Adds options to the request. Limit of 20 set by CP.
	 */
	public function addOption($option, $value = '') {
		if (count($this->_options) < 20) {
			$this->_options[$option] = $value;
		}
	}

	/**
	 * getRates()
	 *
	 * @param array $params
	 * @return rates as array('service' => array('rate', 'delivery', etc)
	 * @description Gets rates based on entered data
	 */
	public function getRates($params) {

		$params['length'] 			= !empty($params['length']) ? $params['length'] : 1;
		$params['width'] 			= !empty($params['width'])  ? $params['width']  : 1;
		$params['height'] 			= !empty($params['height']) ? $params['height'] : 1;
		$params['code']		 		= str_replace(array('-',' ','_'), '', $params['code']);
		$params['origin-postal-code'] = str_replace(array('-',' ','_'), '', $params['origin-postal-code']);
		$params['weight'] 			= round($params['weight'], 2);

		if ($this->test) {
			$url = $this->_testUrl;
		} else {
			$url = $this->_prodUrl;
		}

		$xml = $this->buildXML($params);

		$this->writeLog("SENT: " . $xml);

		$result = $this->curl_post($url, $xml, $params['username'], $params['password']);

		$this->writeLog("RCVD: " . print_r($result,1), true);

		if (!empty($result['error'])) {
			return $this->_parseResult($result['error']);
		} else {
			return $this->_parseResult($result['data']);
		}
	}

	public function buildXML($params) {

		$params['code'] = strtoupper($params['code']);

		$xml  = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<mailing-scenario xmlns="http://www.canadapost.ca/ws/ship/rate">';

		if (!empty($params['quote-type'])) {
			$xml .= '	<quote-type>'.$params['quote-type'].'</quote-type>';
		}

		if (empty($params['quote-type']) || (!empty($params['quote-type']) && $params['quote-type'] == 'commercial')) {
			// Only pass customer-number and contract-id if quote-type = 'commercial' or omitted
			if (!empty($params['customer-number'])) {
				$xml .= '	<customer-number>'.$params['customer-number'].'</customer-number>';
			}

			if (!empty($params['contract-id'])) {
				$xml .= '	<contract-id>'.$params['contract-id'].'</contract-id>';
			}
		}

		// Options
		if ($params['signature'] || $params['insurance']) {
			$xml .= '	<options>';
			if ($params['signature']) {
				$xml .= '	    <option>';
				$xml .= '	        <option-code>SO</option-code>';
				$xml .= '	    </option>';
			}

			if ($params['insurance']) {
				$xml .= '	    <option>';
				$xml .= '	        <option-code>COV</option-code>';
				$xml .= '	        <option-amount>'.$params['amount'].'</option-amount>';
				$xml .= '	    </option>';
			}
			$xml .= '	</options>';
		}
		//


		$xml .= '	<parcel-characteristics>';
		$xml .= '		<weight>'.$params['weight'].'</weight>';
		$xml .= '		<dimensions>';
		$xml .= '			<length>'.$params['length'].'</length>';
		$xml .= '			<width>'.$params['width'].'</width>';
		$xml .= '			<height>'.$params['height'].'</height>';
		$xml .= '		</dimensions>';
		$xml .= '	</parcel-characteristics>';
		$xml .= '	<origin-postal-code>'.$params['origin-postal-code'].'</origin-postal-code>';
		$xml .= '	<destination>';

		if ($params['destination'] == 'domestic') {
			$xml .= '		<domestic>';
			$xml .= '			<postal-code>'.$params['code'].'</postal-code>';
			$xml .= '		</domestic>';
		} elseif ($params['destination'] == 'united-states') {
			$xml .= '		<united-states>';
			$xml .= '			<zip-code>'.substr($params['code'], 0, 5).'</zip-code>';
			$xml .= '		</united-states>';
		} else { //int'l
			$xml .= '		<international>';
			$xml .= '			<country-code>'.$params['code'].'</country-code>';
			$xml .= '		</international>';
		}

		$xml .= '	</destination>';
		$xml .= '</mailing-scenario>';

		return $xml;

	}

	private function _parseResult($response) {
		if (!$response) {
			$error = ('Connection found, but empty response received from gateway');
			$this->writeLog($error);
			return array('error' => $error);
		}
		// Parse Response
		
		// Check if response is valid XML. Return error if not.
		if (strpos($response, 'xml') === false) {
			$error  = 'Invalid response received from Canada Post Gateway. Please try again later.';
			return array('error' => $error);
		}
		
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXml($response);

		$messages = $dom->getElementsByTagName('messages')->item(0);

		if (!is_null($messages)) {
			$message = $messages->getElementsByTagName('message')->item(0);
			$error  = $message->getElementsByTagName('code')->item(0)->nodeValue;
			$error .= " :: " . $message->getElementsByTagName('description')->item(0)->nodeValue;
			$error = str_replace(array('cvc-simple-type 1: element', '{http://www.canadapost.ca/ws/ship/rate}'), '', $error);
			return array('error' => $error);
		} else {

			$pricequotes = $dom->getElementsByTagName('price-quotes')->item(0);

			$pricequoteArr = $pricequotes->getElementsByTagName('price-quote');

			$svcArray = array();
			foreach ($pricequoteArr as $pricequote) {
				$serviceCode = $pricequote->getElementsByTagName('service-code')->item(0)->nodeValue;
				$pricedetails = $pricequote->getElementsByTagName('price-details')->item(0);
				$servicestandard = $pricequote->getElementsByTagName('service-standard')->item(0);

				// Build Array.
				$svcArray[$serviceCode]['name'] 		= $pricequote->getElementsByTagName('service-name')->item(0)->nodeValue;
				$svcArray[$serviceCode]['rate'] 		= $pricedetails->getElementsByTagName('due')->item(0)->nodeValue;
				$options						 		= $pricedetails->getElementsByTagName('options')->item(0);

				if (!is_null($options)) {
					$optionArr						 		= $options->getElementsByTagName('option');
					foreach ($optionArr as $k => $option) {
						$svcArray[$serviceCode]['option'][$k] = $option->getElementsByTagName('option-price')->item(0)->nodeValue;
					}
				}

				// Sometimes some elements aren't there so default to blank and check for each
				$svcArray[$serviceCode]['deliveryDate'] = '';
				$svcArray[$serviceCode]['transitTime'] 	= '';
				$svcArray[$serviceCode]['nextDayAM']	= '';

				if (!is_null($servicestandard->getElementsByTagName('expected-delivery-date')->item(0))) {
					$svcArray[$serviceCode]['deliveryDate'] = $servicestandard->getElementsByTagName('expected-delivery-date')->item(0)->nodeValue;
				}

				if (!is_null($servicestandard->getElementsByTagName('expected-transit-time')->item(0))) {
					$svcArray[$serviceCode]['transitTime'] 	= $servicestandard->getElementsByTagName('expected-transit-time')->item(0)->nodeValue;
				}

				if (!is_null($servicestandard->getElementsByTagName('am-delivery')->item(0))) {
					$svcArray[$serviceCode]['nextDayAM']	= $servicestandard->getElementsByTagName('am-delivery')->item(0)->nodeValue;
				}
			}
			return $svcArray;
		}
	}

	private function curl_post($url, $data, $username, $password) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_PORT, 443);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/vnd.cpc.ship.rate+xml', 'Accept: application/vnd.cpc.ship.rate+xml'));

		if (curl_error($ch)) {
			$response['error'] = curl_error($ch) . '(' . curl_errno($ch) . ')';
		} else {
			$response['data'] = curl_exec($ch);
		}

		curl_close($ch);
		return $response;
	}

	private function curl_get ($url, $data, $user, $pass) {
		$ch = curl_init($url . $data);
		curl_setopt($ch, CURLOPT_PORT, 443);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);

		$response = array();

		if (curl_error($ch)) {
			$response['error'] = curl_error($ch) . '(' . curl_errno($ch) . ')';
		} else {
			$response['data'] = curl_exec($ch);
		}

		curl_close($ch);

		return $response;
	}

	private function writeLog($msg, $append = false) {
		if ($this->_log) {
			if (file_exists($this->_log) && (filesize($this->_log) > $this->_maxLogSize)) { @unlink($this->_log); }
			$msg = (str_repeat('-', 70) . "\r\n" . $msg . "\r\n" . str_repeat('-', 70) . "\r\n");
			if ($append) {
				file_put_contents($this->_log, $msg, FILE_APPEND);
			} else {
				file_put_contents($this->_log, $msg);
			}
		}
	}
}
?>
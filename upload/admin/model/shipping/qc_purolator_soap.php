<?php
// TODO: This should be loaded in via autoloader
// TODO: Put this stuff in some sort of test object
define('TEST_APP_ROUTE', '../../../');
define('ENGINE', TEST_APP_ROUTE .  'system/engine/');

require ENGINE . 'registry.php';
require ENGINE . 'model.php';

class ModelShippingQCPurolatorSoap extends ModelQCShipping {
	public function getQuote($address) {
		$classname = str_replace('vq2-catalog_model_shipping_', '', basename(__FILE__, '.php'));
        $this->load->language('shipping/' . $classname);
		
		//var_dump($this->config);


	    if ($this->config->get($classname . '_status')) {

			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get($classname . '_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

			if (!$this->config->get($classname . '_geo_zone_id')) {
				$status = TRUE;
			} elseif ($query->num_rows) {
				$status = TRUE;
			} else {
				$status = FALSE;
			}
        } else {
            $status = FALSE;
        }

		$method_data = array();

		if ($status) {

			$this->load->language('shipping/' . $classname);

			// canada_post does not allow zero weight. All weights in kg. lengths in cm
			$shipping_weight = ($this->cart->getWeight() == '0') ? '0.1' : $this->cart->getWeight();
			$locale_unit = 'kg';
			$weight_unit = 'KGS';

			if (method_exists($this->document, 'addBreadcrumb')) { //1.4.x
				$from = $this->config->get('config_weight_class');
				$to = $locale_unit;
			} else { //v15x
				$from = $this->config->get('config_weight_class_id');
				$locale_weight_class_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "weight_class wc LEFT JOIN " . DB_PREFIX . "weight_class_description wcd ON (wc.weight_class_id = wcd.weight_class_id) WHERE wcd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND wcd.unit = '" . $locale_unit . "'");
				$to = $locale_weight_class_query->row['weight_class_id'];
			}

			if (file_exists(DIR_SYSTEM . 'library/length.php')) { // v1.4.4 and newer
				$shipping_weight = str_replace(',','',$this->weight->convert($shipping_weight, $from, $to));
			} else { // v1.4.0 and older
				$results = $this->db->query("select weight_class_id from " . DB_PREFIX . "weight_class where unit = '" . $locale_unit . "'");
				$shipping_weight = str_replace(',','',$this->weight->convert($shipping_weight, $this->config->get('config_weight_class_id'), $results->row['weight_class_id']));
			}
			$shipping_weight = round($shipping_weight, 2);
			$shipping_weight = ($shipping_weight == '0') ? '0.1' : $shipping_weight;
			
			$rates = array();

			$this->load->model('localisation/country');
			$country_info = $this->model_localisation_country->getCountry($this->config->get('config_country_id'));

			if ($address['iso_code_2'] == 'CA') {
				$dest = 'domestic';
				$code = $address['postcode'];
			} elseif ($address['iso_code_2'] == 'US') {
				$dest = 'united-states';
				$code = $address['postcode'];
			} else {
				$dest = 'international';
				$code = $address['iso_code_2'];
			}

			// Dimensions and pkg stuff

			/*$params = array(
				'username'			=> trim($this->config->get($classname . '_mid')),
				'password'			=> trim($this->config->get($classname . '_key')),
				'customer-number'	=> trim($this->config->get($classname . '_customer_number')),
				'contract-id'		=> trim($this->config->get($classname . '_contract_id')),
				'quote-type' 		=> trim($this->config->get($classname . '_quote_type')),
				'weight' 			=> $shipping_weight,
				'length' 			=> $length, // centimeters
				'width' 			=> $width, // centimeters
				'height' 			=> $height, // centimeters
				'origin-postal-code'=> trim($this->config->get($classname . '_postcode')),
				'destination' 		=> $dest,
				'code' 				=> $code,
				'signature' 		=> $this->config->get($classname . '_signature'),
				'insurance' 		=> $this->config->get($classname . '_insurance'),
				'amount' 			=> $this->cart->getSubTotal()
			);*/

			//require_once(DIR_SYSTEM . '../catalog/model/shipping/canada_post_ws_rest.class.php');
			/*if ($this->config->get($classname . '_debug')) {
				$cpwsrest = New cpwsrest(DIR_LOGS);
			} else {
				$cpwsrest = New cpwsrest();
			}*/
			
			$settings = $this->mapSettings($this->config);
			
			return $this->formatShipment();
			
			//$cpwsrest->test = $this->config->get($classname . '_test') ? true : false;
 			//$rates = $cpwsrest->getRates($params);


			// Error scenarios
			if (empty($rates)) {return $this->retError($this->language->get('error_no_rates')); }
			if (!is_array($rates) && $rates != null) { return $this->retError($rates); }
			if (isset($rates['error'])) { return $this->retError($rates['error']); }



			// Add table based Lettermail Rates
			// Use Canada Letter mail if less than 500g (0.5kg), otherwise get the premium rates from the canadapost server.
			if ((float)$lmheight < 2 && $shipping_weight < .5 && $this->config->get($classname . '_lettermail') && $address['iso_code_2'] == 'CA') {
			//if ($shipping_weight < .5 && $this->config->get($classname . '_lettermail') && $address['iso_code_2'] == 'CA') {

				//Lettermail rates if destination country is Canada
				$weight = $shipping_weight * 1000;
				$tmprates = explode(',', $this->config->get($classname . '_lettermail'));

				foreach ($tmprates as $tmprate) {
					$data = explode(':', $tmprate);

					if ($data[0] >= $weight) {
						if (isset($data[1])) {
							$rates[] = array(
								'name' 				=> $data[0] . " " . $this->language->get('text_grams'),
								'rate' 				=> $data[1],
								'deliveryDate' 		=> '',
								'nextDayAM' 		=> '',
								' sitTime' 		=> '',
							);
						}
						break;
					}
				}
			} elseif ((float)$lmheight < 2 && $shipping_weight < .5 && $this->config->get($classname . '_lettermail') && $address['iso_code_2'] == 'US') {
			//} elseif ($shipping_weight < .5 && $this->config->get($classname . '_lettermail_us') && $address['iso_code_2'] == 'US') {

				//Lettermail rates if destination country is US
				$weight = $shipping_weight * 1000;
				$tmprates = explode(',', $this->config->get($classname . '_lettermail_us'));

				foreach ($tmprates as $tmprate) {
					$data = explode(':', $tmprate);

					if ($data[0] >= $weight) {
						if (isset($data[1])) {
							$rates[] = array(
								'name' 				=> $data[0] . " " . $this->language->get('text_grams'),
								'rate' 				=> $data[1],
								'deliveryDate' 		=> '',
								'nextDayAM' 		=> '',
								'transitTime' 		=> '',
							);
						}
						break;
					}
				}
			} elseif ((float)$lmheight < 2 && $shipping_weight < .5 && $this->config->get($classname . '_lettermail') && ($address['iso_code_2'] != 'CA' && $address['iso_code_2'] != 'US')) {
			//} elseif ($shipping_weight < .5 && $this->config->get($classname . '_lettermail_int') && ($address['iso_code_2'] != 'CA' && $address['iso_code_2'] != 'US')) {

				//Lettermail rates if destination country is Non-US International
				$weight = $shipping_weight * 1000;
				$tmprates = explode(',', $this->config->get($classname . '_lettermail_int'));

				foreach ($tmprates as $tmprate) {
					$data = explode(':', $tmprate);

					if ($data[0] >= $weight) {
						if (isset($data[1])) {
							$rates[] = array(
								'name' 				=> $data[0] . " " . $this->language->get('text_grams'),
								'rate' 				=> $data[1],
								'deliveryDate' 		=> '',
								'nextDayAM' 		=> '',
								'transitTime' 		=> '',
							);
						}
						break;
					}
				}
			}

 			// Error scenarios
			if (empty($rates)) {$this->retError($this->language->get('error_no_rates')); }
			if (!is_array($rates) && $rates != null) { return $this->retError($rates); }
			if (isset($rates['error'])) { return $this->retError($rates['error']); }

			$allowedSvcs = $this->getParam('services');

			$quote_data = array();
	        $i = 0;
			foreach ($rates as $id => $rate) {
				if ($id != '' && !in_array($id, $allowedSvcs)) { continue; }
				//if ($id != 0 && !in_array($id, $allowedSvcs)) { continue; }

				// Rate Adjust
				if (strpos($this->config->get($classname . '_adjust'), '%') !== false) {
					$rate['rate'] = $rate['rate'] + ($rate['rate'] * (float)str_replace('%', '', $this->config->get($classname . '_adjust'))/100);
				} elseif ((float)$this->config->get($classname . '_adjust')) {
					$rate['rate'] = $rate['rate'] + (float)$this->config->get($classname . '_adjust');
				}

				// Service Name Language Override
				if ($this->language->get('text_' . $id) != ('text_' . $id)) {
					$rate_title = $this->language->get('text_' . $id);
				} else {
					$rate_title = $rate['name'];
				}

				// Display Date
				if ($this->config->get($classname . '_display_date') && !empty($rate['deliveryDate'])) {
					// Cutoff Time. Add day to shipping
					if (is_numeric($this->config->get($classname . '_cutoff'))) {
						$offset = 0;
						if ((int)date('H') > (int)$this->config->get($classname . '_cutoff') || date('H') == '00') {
							$offset = 86400;
							if (date('D', (strtotime($rate['deliveryDate']) + $offset)) == 'Sun') { // double it for sunday
								$offset *= 2;
							}
						}
						$rate['deliveryDate'] = date('Y-m-d', (strtotime($rate['deliveryDate']) + $offset));
					}
					$strDate = date('m,d,Y', strtotime($rate['deliveryDate']));
					if ($strDate != "01,01,1970" && $strDate != "12,31,1969") {
						$exDate = explode(",", $strDate);
						if (checkdate($exDate[0], $exDate[1], $exDate[2])) {
							$rate_title .= ' (' . $this->language->get('text_delivery') . date($this->language->get('date_format_short'), strtotime($rate['deliveryDate'])) . ')';
						}
					}
				}

				$quote_data[$classname . '_'.$i] = array(
					'id'    		=> $classname . '.' . $classname . '_'. $i, //v14x
					'code'  		=> $classname . '.' . $classname . '_'. $i, //v15x
					'title' 		=> $rate_title,
					'cost'  		=> $rate['rate'],
					'tax_class_id' 	=> $this->config->get($classname . '_tax_class_id'),
					'text'  		=> $this->currency->format($this->tax->calculate($rate['rate'], $this->config->get($classname . '_tax_class_id'), $this->config->get('config_tax')))
				);
				$i++;
			}


			$title = ($this->config->get($classname . '_title_' . $this->config->get('config_language_id')) ? $this->config->get($classname . '_title_' . $this->config->get('config_language_id')) : ucwords(str_replace(array('-','_','.'), " ", $classname)));
			if ($this->config->get($classname . '_display_weight')) {
				$title .= ' (' . $shipping_weight . $locale_unit . ')';
			}
			if ($this->config->get($classname . '_display_dims')) {
				$title .= ' (' . $text_dims . ')';
			}

			uasort($quote_data, array($this, 'compare'));

			$method_data = array(
				'id'           => $classname, //v14x
				'code'         => $classname, //15x
		        'title'        => $title,
		        'quote'        => $quote_data,
		        'sort_order'   => $this->config->get($classname . '_sort_order'),
		        'tax_class_id' => $this->config->get($classname . '_tax_class_id'),
		        'error'        => false
		    );
		}

		return $method_data;
	}


	private function retError($error="unknown error") {
		$classname = basename(__FILE__, '.php');
		if (strpos($error, 'Destination Postal Code/State Name/ Country is illegal.') !== false && $this->language->get('error_post_code')) {
			$error=$this->language->get('error_post_code');
		}

		if (!$this->config->get($classname . '_display_errors')) {
			return array();
		}

		$title = ($this->config->get($classname . '_title_' . $this->config->get('config_language_id')) ? $this->config->get($classname . '_title_' . $this->config->get('config_language_id')) : ucwords(str_replace(array('-','_','.'), " ", $classname)));
		
    	$quote_data = array();

      	$quote_data['' . $classname] = array(
        	'id'           => $classname . '.' . $classname, //v14x
			'code'         => $classname . '.' . $classname, //v15x
        	'title'        => $title,
			'cost'         => NULL,
         	'tax_class_id' => NULL,
			'text'         => 'ERROR: ' . $error
      	);

    	$method_data = array(
		  'id'           => $classname, //v14x
		  'code'         => $classname, //v15x
		  'title'        => $title,
		  'quote'        => $quote_data,
		  'sort_order'   => $this->config->get($classname . '_sort_order'),
		  'tax_class_id' => $this->config->get($classname . '_tax_class_id'),
		  'error'        => $error
		);
		return $method_data;
	}

	private function compare($a, $b) {
		return $a['cost'] > $b['cost'];
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


/*$registry = new Registry();
$test = new ModelShippingQCPurolatorSoap($registry);

// Test list methods
//var_dump($test);
//var_dump(get_class_methods($test));

$config = new Registry();
$config->username = 'abc123';
$config->password = 'abc123';
$config->billing_acct = '123456';
$config->reg_acct = '654321';
$config->quote_type = 'test';
$config->pickup = '';
$config->payment = '';
$config->tax_class_id = '';
$config->geo_zone_id = '';
$config->sort_order = '';
$config->display_weight = '';
$config->display_dims = '';
$config->display_date = '';
$config->display_errors = '';
$config->shipping_calc = 'volumetric';
$config->weight = 50;
$config->length = 100;
$config->width = 100;
$config->height = 100;
$config->adjust = '';
$config->signature = '';
$config->insurance = '';
$config->lettermail = '';
$config->lettermail_us = '';
$config->lettermail_int = '';
$config->docs_only = '';
$config->imp_exp = '';
$config->producer = '';
$config->bill_duty = '';
$config->duty_currency = '';
$config->duty_relationship = '';
$config->customs = '';
$config->nafta = '';
$config->fda = '';
$config->fcc = '';
$config->textile = '';
$config->textile_manf = '';
$config->name = '';
$config->company = '';
$config->street_no = '';
$config->street_name = '';
$config->city = '';
$config->province = '';
$config->postcode = '';
$config->phone = '';
$config->fax = '';
$config->destination = '';
$config->code = '';
$config->signature = '';
$config->insurance = '';
$config->amount = '';

//var_dump(get_class_methods($config));

$settings = $test->mapSettings($config);

class AddressModel extends Model {}
$senderAddress = new AddressModel(new Registry());
$senderAddress->type = 'sender';
$senderAddress->firstname = 'Lucas';
$senderAddress->lastname = 'Lopatka';
$senderAddress->company = 'Test';
$senderAddress->street = '120 Abbottsfield Rd';
$senderAddress->city = 'Edmonton';
$senderAddress->postcode = 'T5W4S9';

//var_dump($senderAddress);

$test->setAddress($senderAddress);

$billingAddress = new AddressModel(new Registry());
$billingAddress->type = 'billing';
$billingAddress->firstname = 'Lucas';
$billingAddress->lastname = 'Lopatka';
$billingAddress->company = 'Test';
$billingAddress->street = '120 Abbottsfield Rd';
$billingAddress->city = 'Edmonton';
$billingAddress->postcode = 'T5W4S9';
		
//var_dump($billingAddress);

$test->setAddress($billingAddress);

$shippingAddress = new AddressModel(new Registry());
$shippingAddress->type = 'shipping';
$shippingAddress->firstname = 'Lucas';
$shippingAddress->lastname = 'Lopatka';
$shippingAddress->company = 'Test';
$shippingAddress->street = '120 Abbottsfield Rd';
$shippingAddress->city = 'Edmonton';
$shippingAddress->postcode = 'T5W4S9';

//var_dump($shippingAddress);

$test->setAddress($shippingAddress);

// Item
class ProductModel extends Model {}
$productA = new ProductModel(new Registry());
$productA->width = 50;
$productA->height = 50;
$productA->length = 50;
$productA->weight = 45;
$productA->name = 'Test Product A';
$productA->quantity = 2;

$productB = new ProductModel(new Registry());
$productB->width = 50;
$productB->height = 80;
$productB->length = 40;
$productB->weight = 60;
$productB->name = 'Test Product B';
$productB->quantity = 1;

$items = [$productA, $productB];
$test->setItems($items);

echo $test->formatShipment();*/
<?php
//-----------------------------------------
// Author: Qphoria@gmail.com
// Web: http://www.OpenCartGuru.com/
//-----------------------------------------
class ModelShippingCanadaPostWSREST extends Model {

	public function getQuote($address) {
		$classname = str_replace('vq2-catalog_model_shipping_', '', basename(__FILE__, '.php'));
        $this->load->language('shipping/' . $classname);


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
			
			// If total cart weight is greater than 30kg, then that is too large for CP so return empty array.
			if ((float)$shipping_weight > 30) {
				//return array();
				return $this->retError('Parcel too heavy for Canada Post.');
			}
			
			
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

			$lmheight = 0;
			$length = 0;
			$width  = 0;
			$height = 0;

			if ($this->config->get($classname . '_shipping_calc') == 'volumetric') {
				// Volume Packaging Estimated Dimensions
				$volume = 0;
				foreach ($this->cart->getProducts() as $product) {
					$i=0;
					for($i=1; $i<=$product['quantity']; $i++) {
						$l = (float)$product['length'] ? $product['length'] : 1;
						$w = (float)$product['width']  ? $product['width']  : 1;
						$h = (float)$product['height'] ? $product['height'] : 1;
						$volume += ($l*$w*$h);
					}
				}
				if ($volume) {
					$length = $width = $height = round(pow($volume, 1/3), 0);
					$lmheight = $height;
				}
			} elseif ($this->config->get($classname . '_shipping_calc') == 'linear') {
				// Linear Packaging Estimated Dimensions
				// Assume height is always the shortest way to stack items and sum all heights together
				// But length and width will be the MAX length or Width of all products
				$l = $w = $h = 0;
				$max_l = $max_w = 0;
				foreach ($this->cart->getProducts() as $product) {
					
					// Set height to the shortest side
					$dims = array($product['length'], $product['width'], $product['height']);
					sort($dims);
					$product['height'] = $dims[0];
					$product['width']  = $dims[1];
					$product['length'] = $dims[2];
					
					$i=0;
					for($i=1; $i<=$product['quantity']; $i++) {
						$l = (float)$product['length'] ? $product['length'] : 1; // Get Max length
						$max_l = max($l, $max_l);
						$w = (float)$product['width']  ? $product['width']  : 1; // Get Max Width
						$max_w = max($w, $max_w);
						$h += (float)$product['height'] ? $product['height'] : 1; // Sum Heights
					}
				}
				$length = $max_l;
				$width  = $max_w;
				$height = $h;
				$lmheight = $height;
			}

			// Fallback
			$length = ((float)$length) ? $length : ((float)$this->config->get($classname . '_length') ? (float)$this->config->get($classname . '_length') : 1);
			$width  = ((float)$width)  ? $width  : ((float)$this->config->get($classname . '_width')  ? (float)$this->config->get($classname . '_width')  : 1);
			$height = ((float)$height) ? $height : ((float)$this->config->get($classname . '_height') ? (float)$this->config->get($classname . '_height') : 1);

			$locale_dim_unit = 'cm';
			$dim_unit = 'CM';

			// Convert dimensions
			if (method_exists($this->document, 'addBreadcrumb')) { //1.4.x
				$from = $this->config->get('config_length_class');
				$to = $locale_dim_unit;
			} else { //v15x
				$from = $this->config->get('config_length_class_id');
				$locale_length_class_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "length_class lc LEFT JOIN " . DB_PREFIX . "length_class_description lcd ON (lc.length_class_id = lcd.length_class_id) WHERE lcd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND lcd.unit = '" . $locale_dim_unit . "'");
				$to = $locale_length_class_query->row['length_class_id'];
			}

			if (file_exists(DIR_SYSTEM . 'library/length.php')) { // v1.4.4 and newer
				$length = str_replace(',','',$this->length->convert($length, $from, $to));
				$width = str_replace(',','',$this->length->convert($width, $from, $to));
				$height = str_replace(',','',$this->length->convert($height, $from, $to));
			} else { // v1.4.0 and older
				$results = $this->db->query("select length_class_id from " . DB_PREFIX . "length_class where unit = '" . $locale_dim_unit . "'");
				$length = str_replace(',','',$this->length->convert($length, $this->config->get('config_length_class_id'), $results->row['length_class_id']));
				$width = str_replace(',','',$this->length->convert($width, $this->config->get('config_length_class_id'), $results->row['length_class_id']));
				$height = str_replace(',','',$this->length->convert($height, $this->config->get('config_length_class_id'), $results->row['length_class_id']));
			}
			$length = round($length,0);
			$width = round($width,0);
			$height = round($height,0);

			$text_dims = $length .'x'. $width . 'x' . $height . 'cm';
			$lmheight  = ((float)$lmheight)  ? $lmheight  : ((float)$this->config->get($classname . '_height')  ? (float)$this->config->get($classname . '_height')  : 1);
			//

			$params = array(
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
			);

			require_once(DIR_SYSTEM . '../catalog/model/shipping/canada_post_ws_rest.class.php');
			if ($this->config->get($classname . '_debug')) {
				$cpwsrest = New cpwsrest(DIR_LOGS);
			} else {
				$cpwsrest = New cpwsrest();
			}
			$cpwsrest->test = $this->config->get($classname . '_test') ? true : false;
 			$rates = $cpwsrest->getRates($params);


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

			$allowedSvcs = explode(",", $this->config->get($classname . '_service'));

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
}
?>
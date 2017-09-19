<?php

/**
 * Session handlers
 */
class ModelOcSessionCustomer {
	public static function getCustomer($context) {
		$data = array();
		
		// Customer Details
		$data['customer_id'] = $context->session->data['customer']['customer_id'];
		$data['customer_group_id'] = $context->session->data['customer']['customer_group_id'];
		$data['firstname'] = $context->session->data['customer']['firstname'];
		$data['lastname'] = $context->session->data['customer']['lastname'];
		$data['email'] = $context->session->data['customer']['email'];
		$data['telephone'] = $context->session->data['customer']['telephone'];
		$data['fax'] = $context->session->data['customer']['fax'];
		$data['custom_field'] = $context->session->data['customer']['custom_field'];
		
		return $data;
	}

	// TODO: Allow for substitution of keys
	public static function setCustomer($context, &$data) {
		$keys = array(
			'customer_id',
			'customer_group_id',
			'firstname',
			'lastname',
			'bill_email',
			'bill_telephone',
			'bill_fax'
		);

		foreach ($keys as $key) {
			if (!isset($data[$key])) {
				$data[$key] = '';
			}
		}

		// Customer Group
		if (isset($data['customer_group_id']) && is_array($context->config->get('config_customer_group_display')) && in_array($data['customer_group_id'], $context->config->get('config_customer_group_display'))) {
			$customer_group_id = $data['customer_group_id'];
		} else {
			$customer_group_id = $context->config->get('config_customer_group_id');
		}

		$context->session->data['customer'] = array(
			'customer_id' => $data['customer_id'],
			'customer_group_id' => $customer_group_id,
			'firstname' => $data['firstname'],
			'lastname' => $data['lastname'],
			'email' => $data['bill_email'],
			'telephone' => $data['bill_telephone'],
			'fax' => $data['bill_fax'],
			'custom_field' => isset($data['custom_field']) ? $data['custom_field'] : array()
		);
	}
}

class ModelOcSessionPayment {
	public static function getPayment($context) {
		$data = array();
		
		$defaults['payment_firstname'] = '';
		$defaults['payment_lastname'] = '';
		$defaults['payment_company'] = '';
		$defaults['payment_address_1'] = '';
		$defaults['payment_address_2'] = '';
		$defaults['payment_city'] = '';
		$defaults['payment_postcode'] = '';
		$defaults['payment_zone'] = '';
		$defaults['payment_zone_id'] = '';
		$defaults['payment_country'] = '';
		$defaults['payment_country_id'] = '';
		$defaults['payment_address_format'] = '';
		$defaults['payment_custom_field'] = array();
		$defaults['payment_method'] = '';
		$defaults['payment_code'] = '';
		
		if (isset($context->session->data['payment_address'])) {
			// Payment Details
			$data['payment_firstname'] = $context->session->data['payment_address']['firstname'];
			$data['payment_lastname'] = $context->session->data['payment_address']['lastname'];
			$data['payment_company'] = $context->session->data['payment_address']['company'];
			$data['payment_address_1'] = $context->session->data['payment_address']['address_1'];
			$data['payment_address_2'] = $context->session->data['payment_address']['address_2'];
			$data['payment_city'] = $context->session->data['payment_address']['city'];
			$data['payment_postcode'] = $context->session->data['payment_address']['postcode'];
			$data['payment_zone'] = $context->session->data['payment_address']['zone'];
			$data['payment_zone_id'] = (int)$context->session->data['payment_address']['zone_id'];
			$data['payment_country'] = $context->session->data['payment_address']['country'];
			$data['payment_country_id'] = (int)$context->session->data['payment_address']['country_id'];
			$data['payment_address_format'] = $context->session->data['payment_address']['address_format'];
			$data['payment_custom_field'] = $context->session->data['payment_address']['custom_field'];
		}
		
		// TODO: Might wanna move this into the block above
		if (isset($context->session->data['payment_method']['title'])) {
			$data['payment_method'] = $context->session->data['payment_method']['title'];
		} else {
			$data['payment_method'] = '';
		}

		if (isset($context->session->data['payment_method']['code'])) {
			$data['payment_code'] = $context->session->data['payment_method']['code'];
		} else {
			$data['payment_code'] = '';
		}
		
		$data = array_merge($defaults, $data);
		
		return $data;
	}
}

class ModelOcSessionShipping {
	public static function getShipping($context) {
		$data = array();
		
		$defaults['shipping_firstname'] = '';
		$defaults['shipping_lastname'] = '';
		$defaults['shipping_company'] = '';
		$defaults['shipping_address_1'] = '';
		$defaults['shipping_address_2'] = '';
		$defaults['shipping_city'] = '';
		$defaults['shipping_postcode'] = '';
		$defaults['shipping_zone'] = '';
		$defaults['shipping_zone_id'] = null;
		$defaults['shipping_country'] = '';
		$defaults['shipping_country_id'] = null;
		$defaults['shipping_address_format'] = '';
		$defaults['shipping_custom_field'] = array();
		$defaults['shipping_method'] = '';
		$defaults['shipping_code'] = '';
		
		if (isset($context->session->data['shipping_address'])) {
			$data['shipping_firstname'] = $context->session->data['shipping_address']['firstname'];
			$data['shipping_lastname'] = $context->session->data['shipping_address']['lastname'];
			$data['shipping_company'] = $context->session->data['shipping_address']['company'];
			$data['shipping_address_1'] = $context->session->data['shipping_address']['address_1'];
			$data['shipping_address_2'] = $context->session->data['shipping_address']['address_2'];
			$data['shipping_city'] = $context->session->data['shipping_address']['city'];
			$data['shipping_postcode'] = $context->session->data['shipping_address']['postcode'];
			$data['shipping_zone'] = $context->session->data['shipping_address']['zone'];
			$data['shipping_zone_id'] = (int)$context->session->data['shipping_address']['zone_id'];
			$data['shipping_country'] = $context->session->data['shipping_address']['country'];
			$data['shipping_country_id'] = (int)$context->session->data['shipping_address']['country_id'];
			$data['shipping_address_format'] = $context->session->data['shipping_address']['address_format'];
			$data['shipping_custom_field'] = $context->session->data['shipping_address']['custom_field'];
		}

		if (isset($context->session->data['shipping_method']['title'])) {
			$data['shipping_method'] = $context->session->data['shipping_method']['title'];
		} else {
			$data['shipping_method'] = '';
		}

		if (isset($context->session->data['shipping_method']['code'])) {
			$data['shipping_code'] = $context->session->data['shipping_method']['code'];
		} else {
			$data['shipping_code'] = '';
		}
		
		$data = array_merge($defaults, $data);
		
		return $data;
	}
	
	public static function clearShipping($context) {
		unset($context->session->data['shipping_address']);
		unset($context->session->data['shipping_method']);
		unset($context->session->data['shipping_methods']);
	}
}

class ModelOcSessionVoucher {
	public static function getVouchers($context) {
		$data = array();
		
		// Gift Voucher
		$data['vouchers'] = array();

		if (!empty($context->session->data['vouchers'])) {
			foreach ($context->session->data['vouchers'] as $voucher) {
				$data['vouchers'][] = array(
					'description'      => $voucher['description'],
					'code'             => substr(md5(mt_rand()), 0, 10),
					'to_name'          => $voucher['to_name'],
					'to_email'         => $voucher['to_email'],
					'from_name'        => $voucher['from_name'],
					'from_email'       => $voucher['from_email'],
					'voucher_theme_id' => $voucher['voucher_theme_id'],
					'message'          => $voucher['message'],
					'amount'           => $voucher['amount']
				);
			}
		}
		
		return $data;
	}
}
<?php
class ModelSaleSale extends Model {
	public function getSale($sale_id) {
		$sale_query = $this->db->query("SELECT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = o.customer_id) AS customer FROM `" . DB_PREFIX . "sale` o WHERE o.sale_id = '" . (int)$sale_id . "'");

		if ($sale_query->num_rows) {
			$reward = 0;

			$sale_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sale_product WHERE sale_id = '" . (int)$sale_id . "'");

			foreach ($sale_product_query->rows as $product) {
				$reward += $product['reward'];
			}

			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$sale_query->row['payment_country_id'] . "'");

			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$sale_query->row['payment_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}

			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$sale_query->row['shipping_country_id'] . "'");

			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$sale_query->row['shipping_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}

			if ($sale_query->row['affiliate_id']) {
				$affiliate_id = $sale_query->row['affiliate_id'];
			} else {
				$affiliate_id = 0;
			}

			$this->load->model('marketing/affiliate');

			$affiliate_info = $this->model_marketing_affiliate->getAffiliate($affiliate_id);

			if ($affiliate_info) {
				$affiliate_firstname = $affiliate_info['firstname'];
				$affiliate_lastname = $affiliate_info['lastname'];
			} else {
				$affiliate_firstname = '';
				$affiliate_lastname = '';
			}

			$this->load->model('localisation/language');

			$language_info = $this->model_localisation_language->getLanguage($sale_query->row['language_id']);

			if ($language_info) {
				$language_code = $language_info['code'];
				$language_directory = $language_info['directory'];
			} else {
				$language_code = '';
				$language_directory = '';
			}

			return array(
				'sale_id'                => $sale_query->row['sale_id'],
				'invoice_no'              => $sale_query->row['invoice_no'],
				'invoice_prefix'          => $sale_query->row['invoice_prefix'],
				'store_id'                => $sale_query->row['store_id'],
				'store_name'              => $sale_query->row['store_name'],
				'store_url'               => $sale_query->row['store_url'],
				'customer_id'             => $sale_query->row['customer_id'],
				'customer'                => $sale_query->row['customer'],
				'customer_group_id'       => $sale_query->row['customer_group_id'],
				'firstname'               => $sale_query->row['firstname'],
				'lastname'                => $sale_query->row['lastname'],
				'email'                   => $sale_query->row['email'],
				'telephone'               => $sale_query->row['telephone'],
				'fax'                     => $sale_query->row['fax'],
				'custom_field'            => unserialize($sale_query->row['custom_field']),
				'payment_firstname'       => $sale_query->row['payment_firstname'],
				'payment_lastname'        => $sale_query->row['payment_lastname'],
				'payment_company'         => $sale_query->row['payment_company'],
				'payment_address_1'       => $sale_query->row['payment_address_1'],
				'payment_address_2'       => $sale_query->row['payment_address_2'],
				'payment_postcode'        => $sale_query->row['payment_postcode'],
				'payment_city'            => $sale_query->row['payment_city'],
				'payment_zone_id'         => $sale_query->row['payment_zone_id'],
				'payment_zone'            => $sale_query->row['payment_zone'],
				'payment_zone_code'       => $payment_zone_code,
				'payment_country_id'      => $sale_query->row['payment_country_id'],
				'payment_country'         => $sale_query->row['payment_country'],
				'payment_iso_code_2'      => $payment_iso_code_2,
				'payment_iso_code_3'      => $payment_iso_code_3,
				'payment_address_format'  => $sale_query->row['payment_address_format'],
				'payment_custom_field'    => unserialize($sale_query->row['payment_custom_field']),
				'payment_method'          => $sale_query->row['payment_method'],
				'payment_code'            => $sale_query->row['payment_code'],
				'shipping_firstname'      => $sale_query->row['shipping_firstname'],
				'shipping_lastname'       => $sale_query->row['shipping_lastname'],
				'shipping_company'        => $sale_query->row['shipping_company'],
				'shipping_address_1'      => $sale_query->row['shipping_address_1'],
				'shipping_address_2'      => $sale_query->row['shipping_address_2'],
				'shipping_postcode'       => $sale_query->row['shipping_postcode'],
				'shipping_city'           => $sale_query->row['shipping_city'],
				'shipping_zone_id'        => $sale_query->row['shipping_zone_id'],
				'shipping_zone'           => $sale_query->row['shipping_zone'],
				'shipping_zone_code'      => $shipping_zone_code,
				'shipping_country_id'     => $sale_query->row['shipping_country_id'],
				'shipping_country'        => $sale_query->row['shipping_country'],
				'shipping_iso_code_2'     => $shipping_iso_code_2,
				'shipping_iso_code_3'     => $shipping_iso_code_3,
				'shipping_address_format' => $sale_query->row['shipping_address_format'],
				'shipping_custom_field'   => unserialize($sale_query->row['shipping_custom_field']),
				'shipping_method'         => $sale_query->row['shipping_method'],
				'shipping_code'           => $sale_query->row['shipping_code'],
				'comment'                 => $sale_query->row['comment'],
				'total'                   => $sale_query->row['total'],
				'reward'                  => $reward,
				'sale_status_id'         => $sale_query->row['sale_status_id'],
				'affiliate_id'            => $sale_query->row['affiliate_id'],
				'affiliate_firstname'     => $affiliate_firstname,
				'affiliate_lastname'      => $affiliate_lastname,
				'commission'              => $sale_query->row['commission'],
				'language_id'             => $sale_query->row['language_id'],
				'language_code'           => $language_code,
				'language_directory'      => $language_directory,
				'currency_id'             => $sale_query->row['currency_id'],
				'currency_code'           => $sale_query->row['currency_code'],
				'currency_value'          => $sale_query->row['currency_value'],
				'ip'                      => $sale_query->row['ip'],
				'forwarded_ip'            => $sale_query->row['forwarded_ip'],
				'user_agent'              => $sale_query->row['user_agent'],
				'accept_language'         => $sale_query->row['accept_language'],
				'date_added'              => $sale_query->row['date_added'],
				'date_modified'           => $sale_query->row['date_modified']
			);
		} else {
			return;
		}
	}

	public function getSales($data = array()) {
		$sql = "SELECT o.sale_id, CONCAT(o.firstname, ' ', o.lastname) AS customer, (SELECT os.name FROM " . DB_PREFIX . "sale_status os WHERE os.sale_status_id = o.sale_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status, o.shipping_code, o.total, o.currency_code, o.currency_value, o.date_added, o.date_modified FROM `" . DB_PREFIX . "sale` o";

		if (isset($data['filter_sale_status'])) {
			$implode = array();

			$sale_statuses = explode(',', $data['filter_sale_status']);

			foreach ($sale_statuses as $sale_status_id) {
				$implode[] = "o.sale_status_id = '" . (int)$sale_status_id . "'";
			}

			if ($implode) {
				$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
			} else {

			}
		} else {
			$sql .= " WHERE o.sale_status_id > '0'";
		}

		if (!empty($data['filter_sale_id'])) {
			$sql .= " AND o.sale_id = '" . (int)$data['filter_sale_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$sql .= " AND DATE(o.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if (!empty($data['filter_total'])) {
			$sql .= " AND o.total = '" . (float)$data['filter_total'] . "'";
		}

		$sort_data = array(
			'o.sale_id',
			'customer',
			'status',
			'o.date_added',
			'o.date_modified',
			'o.total'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY o.sale_id";
		}

		if (isset($data['sale']) && ($data['sale'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getSaleProducts($sale_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sale_product WHERE sale_id = '" . (int)$sale_id . "'");

		return $query->rows;
	}

	public function getSaleOption($sale_id, $sale_option_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sale_option WHERE sale_id = '" . (int)$sale_id . "' AND sale_option_id = '" . (int)$sale_option_id . "'");

		return $query->row;
	}

	public function getSaleOptions($sale_id, $sale_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sale_option WHERE sale_id = '" . (int)$sale_id . "' AND sale_product_id = '" . (int)$sale_product_id . "'");

		return $query->rows;
	}

	public function getSaleVouchers($sale_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sale_voucher WHERE sale_id = '" . (int)$sale_id . "'");

		return $query->rows;
	}

	public function getSaleVoucherByVoucherId($voucher_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "sale_voucher` WHERE voucher_id = '" . (int)$voucher_id . "'");

		return $query->row;
	}

	public function getSaleTotals($sale_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sale_total WHERE sale_id = '" . (int)$sale_id . "' ORDER BY sort_sale");

		return $query->rows;
	}

	public function getTotalSales($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "sale`";

		if (!empty($data['filter_sale_status'])) {
			$implode = array();

			$sale_statuses = explode(',', $data['filter_sale_status']);

			foreach ($sale_statuses as $sale_status_id) {
				$implode[] = "sale_status_id = '" . (int)$sale_status_id . "'";
			}

			if ($implode) {
				$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
			}
		} else {
			$sql .= " WHERE sale_status_id > '0'";
		}

		if (!empty($data['filter_sale_id'])) {
			$sql .= " AND sale_id = '" . (int)$data['filter_sale_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$sql .= " AND CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$sql .= " AND DATE(date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if (!empty($data['filter_total'])) {
			$sql .= " AND total = '" . (float)$data['filter_total'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalSalesByStoreId($store_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "sale` WHERE store_id = '" . (int)$store_id . "'");

		return $query->row['total'];
	}

	public function getTotalSalesBySaleStatusId($sale_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "sale` WHERE sale_status_id = '" . (int)$sale_status_id . "' AND sale_status_id > '0'");

		return $query->row['total'];
	}

	public function getTotalSalesByProcessingStatus() {
		$implode = array();

		$sale_statuses = $this->config->get('config_processing_status');

		foreach ($sale_statuses as $sale_status_id) {
			$implode[] = "sale_status_id = '" . (int)$sale_status_id . "'";
		}

		if ($implode) {
			$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "sale` WHERE " . implode(" OR ", $implode));

			return $query->row['total'];
		} else {
			return 0;
		}
	}

	public function getTotalSalesByCompleteStatus() {
		$implode = array();

		$sale_statuses = $this->config->get('config_complete_status');

		foreach ($sale_statuses as $sale_status_id) {
			$implode[] = "sale_status_id = '" . (int)$sale_status_id . "'";
		}

		if ($implode) {
			$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "sale` WHERE " . implode(" OR ", $implode) . "");

			return $query->row['total'];
		} else {
			return 0;
		}
	}

	public function getTotalSalesByLanguageId($language_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "sale` WHERE language_id = '" . (int)$language_id . "' AND sale_status_id > '0'");

		return $query->row['total'];
	}

	public function getTotalSalesByCurrencyId($currency_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "sale` WHERE currency_id = '" . (int)$currency_id . "' AND sale_status_id > '0'");

		return $query->row['total'];
	}

	public function createInvoiceNo($sale_id) {
		$sale_info = $this->getSale($sale_id);

		if ($sale_info && !$sale_info['invoice_no']) {
			$query = $this->db->query("SELECT MAX(invoice_no) AS invoice_no FROM `" . DB_PREFIX . "sale` WHERE invoice_prefix = '" . $this->db->escape($sale_info['invoice_prefix']) . "'");

			if ($query->row['invoice_no']) {
				$invoice_no = $query->row['invoice_no'] + 1;
			} else {
				$invoice_no = 1;
			}

			$this->db->query("UPDATE `" . DB_PREFIX . "sale` SET invoice_no = '" . (int)$invoice_no . "', invoice_prefix = '" . $this->db->escape($sale_info['invoice_prefix']) . "' WHERE sale_id = '" . (int)$sale_id . "'");

			return $sale_info['invoice_prefix'] . $invoice_no;
		}
	}

	public function getSaleHistories($sale_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT oh.date_added, os.name AS status, oh.comment, oh.notify FROM " . DB_PREFIX . "sale_history oh LEFT JOIN " . DB_PREFIX . "sale_status os ON oh.sale_status_id = os.sale_status_id WHERE oh.sale_id = '" . (int)$sale_id . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oh.date_added ASC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalSaleHistories($sale_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "sale_history WHERE sale_id = '" . (int)$sale_id . "'");

		return $query->row['total'];
	}

	public function getTotalSaleHistoriesBySaleStatusId($sale_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "sale_history WHERE sale_status_id = '" . (int)$sale_status_id . "'");

		return $query->row['total'];
	}

	public function getEmailsByProductsSaleed($products, $start, $end) {
		$implode = array();

		foreach ($products as $product_id) {
			$implode[] = "op.product_id = '" . (int)$product_id . "'";
		}

		$query = $this->db->query("SELECT DISTINCT email FROM `" . DB_PREFIX . "sale` o LEFT JOIN " . DB_PREFIX . "sale_product op ON (o.sale_id = op.sale_id) WHERE (" . implode(" OR ", $implode) . ") AND o.sale_status_id <> '0' LIMIT " . (int)$start . "," . (int)$end);

		return $query->rows;
	}

	public function getTotalEmailsByProductsSaleed($products) {
		$implode = array();

		foreach ($products as $product_id) {
			$implode[] = "op.product_id = '" . (int)$product_id . "'";
		}

		$query = $this->db->query("SELECT DISTINCT email FROM `" . DB_PREFIX . "sale` o LEFT JOIN " . DB_PREFIX . "sale_product op ON (o.sale_id = op.sale_id) WHERE (" . implode(" OR ", $implode) . ") AND o.sale_status_id <> '0'");

		return $query->row['total'];
	}
}
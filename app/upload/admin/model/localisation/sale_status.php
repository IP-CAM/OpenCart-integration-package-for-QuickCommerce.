<?php
class ModelLocalisationSaleStatus extends Model {
	public function addSaleStatus($data) {
		foreach ($data['sale_status'] as $language_id => $value) {
			if (isset($sale_status_id)) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "sale_status SET sale_status_id = '" . (int)$sale_status_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			} else {
				$this->db->query("INSERT INTO " . DB_PREFIX . "sale_status SET language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");

				$sale_status_id = $this->db->getLastId();
			}
		}

		$this->cache->delete('sale_status');
	}

	public function editSaleStatus($sale_status_id, $data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "sale_status WHERE sale_status_id = '" . (int)$sale_status_id . "'");

		foreach ($data['sale_status'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "sale_status SET sale_status_id = '" . (int)$sale_status_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		$this->cache->delete('sale_status');
	}

	public function deleteSaleStatus($sale_status_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "sale_status WHERE sale_status_id = '" . (int)$sale_status_id . "'");

		$this->cache->delete('sale_status');
	}

	public function getSaleStatus($sale_status_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sale_status WHERE sale_status_id = '" . (int)$sale_status_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getSaleStatuses($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "sale_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";

			$sql .= " ORDER BY name";

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
		} else {
			$sale_status_data = $this->cache->get('sale_status.' . (int)$this->config->get('config_language_id'));

			if (!$sale_status_data) {
				$query = $this->db->query("SELECT sale_status_id, name FROM " . DB_PREFIX . "sale_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");

				$sale_status_data = $query->rows;

				$this->cache->set('sale_status.' . (int)$this->config->get('config_language_id'), $sale_status_data);
			}

			return $sale_status_data;
		}
	}

	public function getSaleStatusDescriptions($sale_status_id) {
		$sale_status_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sale_status WHERE sale_status_id = '" . (int)$sale_status_id . "'");

		foreach ($query->rows as $result) {
			$sale_status_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $sale_status_data;
	}

	public function getTotalSaleStatuses() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "sale_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row['total'];
	}
}
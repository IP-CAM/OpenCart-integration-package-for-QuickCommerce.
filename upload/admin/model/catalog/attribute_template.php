<?php
class ModelCatalogAttributeTemplate extends Model {
	public function addAttributeTemplate($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_template SET title = '" . $this->db->escape($data['title']) . "', description = '" . $this->db->escape($data['description']) . "', date_added = NOW()");

		$id = $this->db->getLastId();

		if (isset($data['template_attribute'])) {
			foreach ($data['template_attribute'] as $attribute) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_template SET attribute_template_id = '" . (int)$id . "', attribute_group_id = '" . (int)$attribute['attribute_group_id'] . "', attribute_id = '" . (int)$attribute['attribute_id'] . "', value = '" . base64_encode($attribute['value']) . "'");
			}
		}

		$this->cache->delete('attribute_template');
	}

	public function editAttributeTemplate($id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "attribute_template SET title = '" . $this->db->escape($data['title']) . "', description = '" . $this->db->escape($data['description']) . "', date_modified = NOW() WHERE attribute_template_id = '" . (int)$id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "attribute_template_attribute WHERE attribute_template_id = '" . (int)$id . "'");

		if (isset($data['template_attribute'])) {
			foreach ($data['template_attribute'] as $attribute) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_template_attribute SET attribute_template_id = '" . (int)$id . "', attribute_group_id = '" . (int)$attribute['attribute_group_id'] . "', attribute_id = '" . (int)$attribute['attribute_id'] . "', value = '" . base64_encode($attribute['value']) . "'");
			}
		}

		$this->cache->delete('attribute_template');
	}

	public function deleteAttributeTemplate($id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "attribute_template WHERE attribute_template_id = '" . (int)$id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "attribute_template_attribute WHERE attribute_template_id = '" . (int)$id . "'");

		$this->cache->delete('attribute_template');
	}

	public function getAttributeTemplate($id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "attribute_template WHERE attribute_template_id = '" . (int)$id . "'");

		return $query->row;
	}

	public function getAttributeTemplates($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "attribute_template";

			$sql .= " ORDER BY title";

			if (isset($data['order']) && ($data['order'] == 'DESC')) {
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
			$data = $this->cache->get('attribute_template');

			if (!$data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "attribute_template");

				$data = $query->rows;

				$this->cache->set('attribute_template', $data);
			}

			return $data;
		}
	}

	public function getTotalAttributeTemplates() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "attribute_template");

		return $query->row['total'];
	}

	public function getAttributes($id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "attribute_template_attribute WHERE attribute_template_id = '" . (int)$id . "'");

		return $query->rows;
	}

	public function getTotalTaxRulesByTaxRateId($id) {
		$query = $this->db->query("SELECT COUNT(DISTINCT attribute_template_id) AS total FROM " . DB_PREFIX . "attribute_template_attribute WHERE attribute_group_id = '" . (int)$id . "'");

		return $query->row['total'];
	}
}
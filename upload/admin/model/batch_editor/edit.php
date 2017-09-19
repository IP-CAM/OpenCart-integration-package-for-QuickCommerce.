<?php
class ModelBatchEditorEdit extends Model {
	private $values = array ();
	private $images = array ();
	
	public function Link($data) {
		$this->load->model('batch_editor/setting');
		
		$fields = $this->model_batch_editor_setting->getTableField($data['table']);
		
		if ($fields) {
			$auto_increment = false;
			$main_category = false;
			$primary_key = array ();
			$duplicate = array ();
			$field_set = array ();
			$type = 'standart';
			
			foreach ($fields as $field => $setting) {
				$field_set[] = $field;
				
				if ($field != 'product_id') {
					$duplicate[] = $field . ' = VALUES(' . $field . ')';
				}
				
				if ($setting['key'] == 'PRI') {
					$primary_key[] = $field;
				}
				
				if ($setting['extra'] == 'auto_increment') {
					$auto_increment = true;
				}
			}
			
			if (count ($primary_key) == 2 && in_array ('language_id', $primary_key)) {
				$type = 'language';
			}
			
			if ($data['action'] == 'upd') {
				$this->db->query("DELETE FROM `" . DB_PREFIX . $data['table'] . "` WHERE `product_id` IN (" . implode (',', $data['product_id']) . ")");
			}
			
			if ($data['action'] == 'upd' || $data['action'] == 'add') {
				if ($type == 'standart') {
					if (isset ($this->request->post['price_action'])) {
						$action = $this->request->post['price_action'];
					} else {
						$action = array ();
					}
					
					foreach ($data['product_id'] as $product_id) {
						$product_price = false;
						
						if ($action) {
							$query = $this->db->query("SELECT `price` FROM `" . DB_PREFIX . "product` WHERE `product_id` = '" . (int) $product_id . "'");
							
							if (isset ($query->row['price'])) {
								$product_price = $query->row['price'];
							}
						}
						
						foreach ($data['data'] as $key => $array) {
							$value_set = array ();
							$value_select = array ();
							
							foreach ($fields as $field => $setting) {
								if (isset ($array[$field])) {
									if (isset ($action[$key]) && $field == 'price') {
										if ($action[$key] == 'plus_number') {
											$value = $product_price + (float) $array[$field];
										} else if ($action[$key] == 'minus_number') {
											$value = $product_price - (float) $array[$field];
										} else if ($action[$key] == 'plus_percent') {
											$value = $product_price * (100 + (float) $array[$field]) * 0.01;
										} else if ($action[$key] == 'minus_percent') {
											$value = $product_price * (100 - (float) $array[$field]) * 0.01;
										} else {
											$value = (float) $array[$field];
										}
									} else {
										$value = $array[$field];
									}
								} else {
									if ($field == 'product_id') {
										$value = (int) $product_id;
									} else {
										$value = '';
									}
								}
								
								$value_set[] = $this->validateFieldType($value, $setting['type']);
								
								if ($setting['extra'] != 'auto_increment') {
									$value_select[] = $field . " = " . $this->validateFieldType($value, $setting['type']);
								}
							}
							
							if ($data['action'] == 'add' && ($auto_increment || $data['table'] == 'product_shipping')) {
								$query = $this->db->query("SELECT `product_id` FROM `" . DB_PREFIX . $data['table'] . "` WHERE " . implode (' AND ', $value_select));
								
								if ($query->num_rows) {
									continue;
								}
							}
							
							$values = "(" . implode (', ', $value_set) . ")";
							
							// Временный костыль
							if ($data['table'] == 'product_shipping' && in_array ($values, $this->values)) {
								continue;
							}
							// Временный костыль
							
							$this->values[] = $values;
							
							if (isset ($array['main_category']) && $array['main_category']) {
								$main_category = true;
							}
						}
					}
				}
				
				if ($type == 'language') {
					$this->load->model('batch_editor/list');
					
					$languages = $this->model_batch_editor_list->getLanguages();
					
					foreach ($data['product_id'] as $product_id) {
						foreach ($languages as $language) {
							$value_set = array ();
							
							foreach ($fields as $field => $setting) {
								if (isset ($data['data'][$language['language_id']][$field])) {
									$value = $data['data'][$language['language_id']][$field];
								} else {
									if ($field == 'product_id') {
										$value = (int) $product_id;
									} else if ($field == 'language_id') {
										$value = $language['language_id'];
									} else {
										$value = '';
									}
								}
								
								$value_set[] = $this->validateFieldType($value, $setting['type']);
							}
							
							$this->values[] = '(' . implode (', ', $value_set) . ')';
						}
					}
				}
				
				if ($this->values) {
					if ($main_category) {
						$this->db->query("UPDATE `" . DB_PREFIX . "product_to_category` SET `main_category` = '0' WHERE `product_id` IN (" . implode (',', $data['product_id']) . ")");
					}
					
					$sql = "INSERT INTO `" . DB_PREFIX . $data['table'] . "` (" . implode (',', $field_set) . ") VALUES " . implode (',', $this->values);
					
					if (count ($primary_key) > 1 && $duplicate) {
						$sql .= " ON DUPLICATE KEY UPDATE " . implode (',', $duplicate);
					}
					
					$this->db->query($sql);
				}
			}
			
			if ($data['action'] == 'del') {
				if (isset ($this->request->post['price_action'])) {
					$price_action = $this->request->post['price_action'];
				} else {
					$price_action = array ();
				}
				
				if ($price_action) {
					$product_price = $this->getProductPrice($data['product_id']);
				}
				
				foreach ($data['product_id'] as $product_id) {
					foreach ($data['data'] as $key => $array) {
						$value_remove = array ();
						
						foreach ($fields as $field => $setting) {
							if ($setting['extra'] != 'auto_increment') {
								$value = '';
								
								if (isset ($array[$field])) {
									if (isset ($action[$key]) && isset ($product_price[$product_id]) && $field == 'price') {
										$value = $this->getActionPrice($action[$key], $product_price[$product_id], $array[$field]);
									} else {
										$value = $array[$field];
									}
								} else {
									if ($field == 'product_id') {
										$value = (int) $product_id;
									}
								}
								
								if ($value || $value == '0') {
									$value_remove[] = $field . " = " . $this->validateFieldType($value, $setting['type']);
								}
							}
						}
						
						if ($value_remove) {
							$this->values[] = "(" . implode (' AND ', $value_remove) . ")";
						}
					}
				}
				
				if ($this->values) {
					$this->db->query("DELETE FROM `" . DB_PREFIX . $data['table'] . "` WHERE " . implode (' OR ', $this->values));
				}
			}
			
			$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `date_modified` = NOW() WHERE `product_id` IN (" . implode (', ', $data['product_id']) . ")");
		}
	}
	
	public function Product($data = array ()) {
		$this->load->model('batch_editor/setting');
		
		$setting = $this->model_batch_editor_setting->get('table');
		
		if (isset ($setting[$data['field']])) {
			$setting = $setting[$data['field']];
		} else {
			return false;
		}
		
		$edit = true;
		$quote = "'";
		
		if (isset ($this->request->post['language_id'])) {
			$language_id = (int) $this->request->post['language_id'];
		} else {
			$language_id = (int) $this->config->get('config_language_id');
		}
		
		if (is_array ($data['product_id'])) {
			foreach ($data['product_id'] as $key=>$product_id) {
				$data['product_id'][$key] = (int) $product_id;
			}
		} else {
			$data['product_id'] = (int) $data['product_id'];
		}
		
		if ($data['field'] == 'tag' && VERSION < '1.5.4') {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "product_tag` WHERE `product_id` IN (" . implode (', ', $data['product_id']) . ") AND `language_id` = '" . $language_id . "'");
			$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `date_modified` = NOW() WHERE `product_id` IN (" . implode (', ', $data['product_id']) . ")");
			
			$data['value'] = explode (',', $data['value']);
			
			$tags = array ();
			
			foreach ($data['value'] as $tag) {
				if (trim ($tag)) {
					$tags[] = $tag;
				}
			}
			
			foreach ($data['product_id'] as $product_id) {
				foreach ($tags as $tag) {
					$this->values[] = '("' . $product_id . '", "' . $language_id . '", "' . $this->db->escape(trim ($tag)) . '")';
				}
			}
			
			if ($this->values) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_tag` (product_id, language_id, tag) VALUES " . implode (', ', $this->values));
			}
			
			$value = html_entity_decode (implode (',', $tags), ENT_QUOTES, 'UTF-8');
		} else if ($data['field'] == 'url_alias') {
			foreach ($data['product_id'] as $product_id) {
				$this->db->query("DELETE FROM `" . DB_PREFIX . "url_alias` WHERE `query` = 'product_id=" . (int) $product_id . "'");
				
				if ($data['value']) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "url_alias` SET `keyword` = '" . $this->db->escape($data['value']) . "', `query` = 'product_id=" . (int) $product_id . "'");
				}
				
				$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `date_modified`=NOW() WHERE `product_id` = " . (int) $product_id);
			}
			
			$value = html_entity_decode ($data['value'], ENT_QUOTES, 'UTF-8');
		} else {
			$product_id = implode (', ', $data['product_id']);
			
			$type = $setting['type'];
			
			if ($type == 'varchar' || $type == 'char' || $type == 'text') {
				$data['value'] = $this->db->escape($data['value']);
				
				if ($data['field'] == 'name' && !$data['value']) {
					$edit = FALSE;
				}
				
				if ($data['field'] == 'model' && !$data['value']) {
					$edit = FALSE;
				}
				
				if ($data['field'] == 'image') {
					$this->load->model('tool/image');
					$this->load->model('batch_editor/setting');
					
					$option = $this->model_batch_editor_setting->get('option');
					
					$value = $this->model_tool_image->resize($data['value'], $option['image']['width'], $option['image']['height']);
				} else {
					$value = html_entity_decode ($data['value'], ENT_QUOTES, 'UTF-8');
				}
			} else if ($type == 'int' || $type == 'tinyint') {
				$data['value'] = (int) $data['value'];
			} else if ($type == 'decimal') {
				$data['value'] = number_format ((float) $data['value'], $setting['size_2'], '.', FALSE);
			} else if ($type == 'date') {
				//if (!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $data['value'])) {
					//$data['value'] = '0000-00-00';
				//}
			} else if ($type == 'datetime') {
				//if (!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{0,2}:[0-9]{0,2}:[0-9]{0,2}$/", $data['value'])) {
					//$data['value'] = '0000-00-00 00:00:00';
				//}
			}
			
			if (isset ($this->request->post['calculate']) && isset ($setting['calc'])) {
				$data['action'] = (string) $this->request->post['calculate'];
				
				$calculate = array (
					'equal_number'    => $data['value'],
					'plus_number'     => '(' . $data['field'] . ' + ' . $data['value'] . ')',
					'minus_number'    => '(' . $data['field'] . ' - ' . $data['value'] . ')',
					'multiply_number' => '(' . $data['field'] . ' * ' . $data['value'] . ')',
					'divide_number'   => '(' . $data['field'] . ' / ' . $data['value'] . ')',
					'plus_percent'    => '(' . $data['field'] . ' * ' . (100 + $data['value']) * 0.01 . ')',
					'minus_percent'   => '(' . $data['field'] . ' * ' . (100 - $data['value']) * 0.01 . ')'
				);
				
				if (isset ($calculate[$data['action']])) {
					$data['value'] = $calculate[$data['action']];
					$quote = '';
				}
			}
			
			if ($edit) {
				if ($setting['table'] == 'p') {
					$this->db->query("UPDATE `" . DB_PREFIX . "product` SET " . $data['field'] . " = " . $quote . $data['value'] . $quote . " WHERE `product_id` IN (" . $product_id . ")");
				} else if ($setting['table'] == 'pd') {
					$this->db->query("UPDATE `" . DB_PREFIX . "product_description` SET " . $data['field'] . " = " . $quote . $data['value'] . $quote . " WHERE `product_id` IN (" . $product_id . ") AND language_id = '" . $language_id . "'");
				}
				
				$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `date_modified`=NOW() WHERE `product_id` IN (" . $product_id . ")");
			}
		}
		
		if (isset ($value)) {
			return $value;
		} else {
			return $data['value'];
		}
	}
	
	public function Description($data) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_description` WHERE `product_id` IN (" . implode (', ', $data['product_id']) . ")");
		
		$field_set = array ('product_id', 'language_id');
		$field_value = array ();
		$tags = array ();
		
		foreach ($data['table'] as $field=>$value) {
			$field_set[] = $field;
		}
		
		foreach ($data['product_id'] as $product_id) {
			foreach ($data['data'] as $language_id=>$fields) {
				$field_value[] = (int) $product_id;
				$field_value[] = (int) $language_id;
				
				foreach ($data['table'] as $field=>$value) {
					if (isset ($fields[$field])) {
						$field_value[] = '"' . $this->db->escape($fields[$field]) . '"';
					}
				}
				
				if (VERSION < '1.5.4') {
					$tags[$language_id] = $fields['tag'];
				}
				
				$this->values[] = '(' . implode (', ', $field_value) . ')';
				$field_value = array ();
			}
		}
		
		if ($this->values) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "product_description` (" . implode (', ', $field_set) . ") VALUES " . implode (', ', $this->values));
		}
		
		if ($tags) {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "product_tag` WHERE `product_id` IN (" . implode (', ', $data['product_id']) . ")");
			
			$field_set = array ('product_id', 'language_id', 'tag');
			$field_value = array ();
			$this->values = array ();
			
			foreach ($data['product_id'] as $product_id) {
				foreach ($tags as $language_id=>$tag) {
					$tag = explode (',', $tag);
					
					foreach ($tag as $value) {
						$field_value[] = (int) $product_id;
						$field_value[] = (int) $language_id;
						$field_value[] = '"' . $this->db->escape(trim ($value)) . '"';
						
						$this->values[] = '(' . implode (', ', $field_value) . ')';
						$field_value = array ();
					}
				}
			}
			
			if ($this->values) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_tag` (" . implode (', ', $field_set) . ") VALUES " . implode (', ', $this->values));
			}
		}
		
		$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `date_modified` = NOW() WHERE `product_id` IN (" . implode (', ', $data['product_id']) . ")");
	}
	
	public function Category($data) {
		if ($data['action'] == 'upd') {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "product_to_category` WHERE `product_id` IN (" . implode (', ', $data['product_id']) . ")");
		}
		
		if ($data['action'] == 'del') {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "product_to_category` WHERE `product_id` IN (" . implode (', ', $data['product_id']) . ") AND `category_id` IN (" . implode (', ', $data['data']) . ")");
		}
		
		if ($data['action'] == 'add' || $data['action'] == 'upd') {
			foreach ($data['product_id'] as $product_id) {
				foreach ($data['data'] as $key=>$category_id) {
					if ($category_id) {
						$this->db->query("INSERT INTO `" . DB_PREFIX . "product_to_category` SET `product_id` = '" . (int) $product_id . "', `category_id` = '" . (int) $category_id . "' ON DUPLICATE KEY UPDATE `product_id` = VALUES(`product_id`), `category_id` = VALUES(`category_id`)");
					}
				}
			}
			
			if (isset ($data['data']['main_category'])) {
				$this->db->query("UPDATE `" . DB_PREFIX . "product_to_category` SET `main_category` = 0 WHERE `product_id` IN (" . implode (', ', $data['product_id']) . ")");
				
				if ($data['data']['main_category']) {
					$this->db->query("UPDATE `" . DB_PREFIX . "product_to_category` SET `main_category` = 1 WHERE `product_id` IN (" . implode (', ', $data['product_id']) . ") AND `category_id` = " . (int) $data['data']['main_category']);
				}
			}
		}
		
		$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `date_modified` = NOW() WHERE `product_id` IN (" . implode (', ', $data['product_id']) . ")");
	}
	
	public function Attribute($data) {
		if ($data['action'] == 'upd') {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "product_attribute` WHERE `product_id` IN (" . implode (', ', $data['product_id']) . ")");
		}
		
		foreach ($data['product_id'] as $product_id) {
			foreach ($data['data'] as $attribute) {
				if ($attribute['attribute_id'] > 0) {
					foreach ($attribute['attribute_description'] as $language_id => $description) {
						if ($data['action'] == 'del') {
							$this->db->query("DELETE FROM `" . DB_PREFIX . "product_attribute` WHERE `product_id` = '" . (int) $product_id . "' AND `attribute_id` = '" . (int) $attribute['attribute_id'] . "'");
						}
						
						if ($data['action'] == 'add' || $data['action'] == 'upd') {
							$this->db->query("INSERT INTO `" . DB_PREFIX . "product_attribute` SET `product_id` = '" . (int) $product_id . "', `attribute_id` = '" . (int) $attribute['attribute_id'] . "', `language_id` = '" . (int) $language_id . "', `text` = '" . $this->db->escape($description['text']) . "' ON DUPLICATE KEY UPDATE `text` = VALUES(`text`)");
						}
					}
				}
			}
		}
		
		////////////////////////////////////////////////////////////////////
		if ($this->config->get('mfilter_plus_version')) {
			$this->load->library('mfilter_plus');
			
			foreach ($data['product_id'] as $product_id) {
				Mfilter_Plus::getInstance($this)->updateProduct($product_id);
			}
		}
		////////////////////////////////////////////////////////////////////
		
		$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `date_modified` = NOW() WHERE `product_id` IN (" . implode (', ', $data['product_id']) . ")");
	}
	
	public function Option($data) {
		$this->load->model('batch_editor/setting');
		
		$option_type = $this->model_batch_editor_setting->get('option', 'option_type');
		
		if ($data['action'] == 'upd') {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "product_option` WHERE `product_id` IN (" . implode (', ', $data['product_id']) . ")");
			$this->db->query("DELETE FROM `" . DB_PREFIX . "product_option_value` WHERE `product_id` IN (" . implode(', ', $data['product_id']) . ")");
		}
		
		foreach ($data['product_id'] as $product_id) {
			foreach ($data['data'] as $option) {
				if ($data['action'] == 'del') {
					$this->db->query("DELETE `FROM " . DB_PREFIX . "product_option` WHERE product_id = '" . (int) $product_id . "' AND option_id = '" . (int) $option['option_id'] . "'");
					$this->db->query("DELETE `FROM " . DB_PREFIX . "product_option_value` WHERE product_id = '" . (int) $product_id . "' AND option_id = '" . (int) $option['option_id'] . "'");
				} else {
					if (in_array ($option['type'], $option_type)) {
						$this->db->query("INSERT INTO `" . DB_PREFIX . "product_option` SET `product_option_id` = '" . (int) $option['product_option_id'] . "', `product_id` = '" . (int) $product_id . "', `option_id` = '" . (int) $option['option_id'] . "', `required` = '" . (int) $option['required'] . "'");
						
						$product_option_id = $this->db->getLastId();
						
						if (isset ($option['product_option_value'])) {
							foreach ($option['product_option_value'] as $option_value) {
								$sql = "INSERT INTO `" . DB_PREFIX . "product_option_value` SET `product_option_value_id` = '" . (int) $option_value['product_option_value_id'] . "', `product_option_id` = '" . (int) $product_option_id . "', `product_id` = '" . (int) $product_id . "', `option_id` = '" . (int) $option['option_id'] . "', `option_value_id` = '" . $this->db->escape($option_value['option_value_id']) . "', `quantity` = '" . (int) $option_value['quantity'] . "', `subtract` = '" . (int) $option_value['subtract'] . "', `price` = '" . (float) $option_value['price'] . "', `price_prefix` = '" . $this->db->escape($option_value['price_prefix']) . "', `points` = '" . (int) $option_value['points'] . "', `points_prefix` = '" . $this->db->escape($option_value['points_prefix']) . "', `weight` = '" . (float) $option_value['weight'] . "', `weight_prefix` = '" . $this->db->escape($option_value['weight_prefix']) . "'";
								
								if (isset ($option_value['base_price'])) {
									$sql .= ", `base_price` = '" . (float) $option_value['base_price'] . "'";
								}
								
								if (isset ($option_value['quantity_foo_rashod'])) {
									$sql .= ", `quantity_foo_rashod` = '" . (float) $option_value['quantity_foo_rashod'] . "'";
								}
								
								$this->db->query($sql);
							}
						}
					} else { 
						$this->db->query("INSERT INTO `" . DB_PREFIX . "product_option` SET `product_option_id` = '" . (int) $option['product_option_id'] . "', `product_id` = '" . (int) $product_id . "', `option_id` = '" . (int) $option['option_id'] . "', " . ((VERSION < '2.0.0.0') ? '`option_value`' : '`value`') . " = '" . $this->db->escape($option['option_value']) . "', `required` = '" . (int) $option['required'] . "'");
					}
				}
			}
		}
		
		$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `date_modified` = NOW() WHERE `product_id` IN (" . implode (", ", $data['product_id']) . ")");
	}
	
	public function Related($data) {
		$this->load->model('batch_editor/setting');
		$option = $this->model_batch_editor_setting->get('option');
		
		if ($data['action'] == 'upd') {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id IN (" . implode (', ', $data['product_id']) . ")");
		}
		
		if ($data['action'] == 'del') {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id IN (" . implode (', ', $data['product_id']) . ") AND related_id IN (" . implode (', ', $data['data']) . ")");
			
			if ($option['related']['del'] == 2) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id IN (" . implode (', ', $data['data']) . ") AND related_id IN (" . implode (', ', $data['product_id']) . ")");
			}
		}
		
		if ($data['action'] == 'add' || $data['action'] == 'upd') {
			foreach ($data['product_id'] as $product_id) {
				foreach ($data['data'] as $related_id) {
					if ($product_id != $related_id) {
						$this->values[] = "(" . (int) $product_id . ", " . (int) $related_id . ")";
						
						if ($option['related']['add'] == 2) {
							$this->values[] = "(" . (int) $related_id . ", " . (int) $product_id . ")";
						}
					}
				}
			}
			
			if ($this->values) {
				$this->db->query('INSERT INTO ' . DB_PREFIX . 'product_related (product_id, related_id) VALUES ' . implode (', ', $this->values) . ' ON DUPLICATE KEY UPDATE product_id = VALUES(product_id), related_id = VALUES(related_id)');
			}
		}
		
		$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `date_modified` = NOW() WHERE `product_id` IN (" . implode (', ', $data['product_id']) . ")");
		
		if ($option['related']['add'] == 2 && $data['data']) {
			$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `date_modified` = NOW() WHERE `product_id` IN (" . implode (', ', $data['data']) . ")");
		}
	}
	
	public function Store($data) {
		if ($data['action'] == 'upd') {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id IN (" . implode (', ', $data['product_id']) . ")");
		}
		
		if ($data['action'] == 'del') {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id IN (" . implode (', ', $data['product_id']) . ") AND store_id IN (" . implode (', ', $data['data']) . ")");
		}
		
		if ($data['action'] == 'add' || $data['action'] == 'upd') {
			foreach ($data['product_id'] as $product_id) {
				foreach ($data['data'] as $store_id) {
					$this->values[] = "(" . (int) $product_id . ", " . (int) $store_id . ")";
				}
			}
			
			if ($this->values) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store (product_id, store_id) VALUES " . implode (', ', $this->values) . " ON DUPLICATE KEY UPDATE product_id = VALUES(product_id), store_id = VALUES(store_id)");
			}
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id IN (" . implode (', ', $data['product_id']) . ")");
	}
	
	public function Download($data) {
		if ($data['action'] == 'upd') {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id IN (" . implode (', ', $data['product_id']) . ")");
		}
		
		if ($data['action'] == 'del') {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id IN (" . implode (', ', $data['product_id']) . ") AND download_id IN (" . implode (', ', $data['data']) . ")");
		}
		
		if ($data['action'] == 'add' || $data['action'] == 'upd') {
			foreach ($data['product_id'] as $product_id) {
				foreach ($data['data'] as $download_id) {
					$this->values[] = "(" . (int) $product_id . ", " . (int) $download_id . ")";
				}
			}
		}
		
		if ($this->values) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download (product_id, download_id) VALUES " . implode (', ', $this->values) . " ON DUPLICATE KEY UPDATE product_id = VALUES(product_id), download_id = VALUES(download_id)");
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id IN (" . implode (', ', $data['product_id']) . ")");
	}
	
	public function Image($data) {
		if ($data['action'] == 'upd') {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id IN (" . implode (', ', $data['product_id']) . ")");
		}
		
		if ($data['action'] == 'del') {
			foreach ($data['data'] as $image) {
				$this->images[] = $this->db->escape($image['image']);
			}
			
			if ($this->images) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id IN (" . implode (', ', $data['product_id']) . ") AND image IN ('" . implode ("','", $this->images) . "')");
			}
		}
		
		if ($data['action'] == 'add' || $data['action'] == 'upd') {
			foreach ($data['product_id'] as $product_id) {
				foreach ($data['data'] as $image) {
					$this->values[] = "('" . (int) $product_id . "', '" . $this->db->escape($image['image']) . "', '" . (int) $image['sort_order'] . "')";
				}
			}
		}
		
		if ($this->values) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_image (product_id, image, sort_order) VALUES " . implode (', ', $this->values) . " ON DUPLICATE KEY UPDATE product_id = VALUES(product_id), image = VALUES(image), sort_order = VALUES(sort_order)");
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id IN (" . implode (', ', $data['product_id']) . ")");
	}
	
	public function Reward($data) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_reward` WHERE `product_id` IN (" . implode (', ', $data['product_id']) . ")");
		
		foreach ($data['product_id'] as $product_id) {
			foreach ($data['data'] as $customer_group_id=>$reward) {
				$this->values[] = "(" . (int) $product_id . ", " . (int) $customer_group_id . ", " . (int) $reward['points'] . ")";
			}
		}
		
		if ($this->values) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "product_reward` (`product_id`, `customer_group_id`, `points`) VALUES " . implode (', ', $this->values));
		}
		
		$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `date_modified` = NOW() WHERE `product_id` IN (" . implode (', ', $data['product_id']) . ")");
	}
	
	public function Layout($data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id IN (" . implode (', ', $data['product_id']) . ")");
		
		foreach ($data['product_id'] as $product_id) {
			foreach ($data['data'] as $store_id=>$layout) {
				if ($layout['layout_id']) {
					$this->values[] = "(" . (int) $product_id . ", " . (int) $store_id . ", " . (int) $layout['layout_id'] . ")";
				}
			}
		}
		
		if ($this->values) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout (product_id, store_id, layout_id) VALUES " . implode (', ', $this->values));
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id IN (" . implode (', ', $data['product_id']) . ")");
	}
	
	public function Filter($data) {
		if ($data['action'] == 'upd') {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id IN (" . implode (', ', $data['product_id']) . ")");
		}
		
		if ($data['action'] == 'del') {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id IN (" . implode (', ', $data['product_id']) . ") AND filter_id IN (" . implode (', ', $data['data']) . ")");
		}
		
		if ($data['action'] == 'add' || $data['action'] == 'upd') {
			foreach ($data['product_id'] as $product_id) {
				foreach ($data['data'] as $filter_id) {
					$this->values[] = "(" . (int) $product_id . ", " . (int) $filter_id . ")";
				}
			}
			
			if ($this->values) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter (product_id, filter_id) VALUES " . implode (', ', $this->values) . " ON DUPLICATE KEY UPDATE product_id = VALUES(product_id), filter_id = VALUES(filter_id)");
			}
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id IN (" . implode (', ', $data['product_id']) . ")");
	}
	
	public function Recurring($data) {
		if ($data['action'] == 'upd') {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "product_recurring` WHERE `product_id` IN ('" . implode ("','", $data['product_id']) . "')");
		}
		
		if ($data['action'] == 'del') {
			$reccuring_id = array ();
			$customer_group_id = array ();
			
			foreach ($data['data'] as $value) {
				$recurring_id[] = $value['recurring_id'];
				$customer_group_id[] = $value['customer_group_id'];
			}
			
			$this->db->query("DELETE FROM `" . DB_PREFIX . "product_recurring` WHERE `product_id` IN ('" . implode ("','", $data['product_id']) . "') AND `recurring_id` IN ('" . implode ("','", $recurring_id) . "') AND `customer_group_id` IN ('" . implode ("','", $customer_group_id) . "')");
		}
		
		if ($data['action'] == 'add' || $data['action'] == 'upd') {
			foreach ($data['product_id'] as $product_id) {
				foreach ($data['data'] as $value) {
					$this->values[] = "('" . (int) $product_id . "', '" . (int) $value['recurring_id'] . "', '" . (int) $value['customer_group_id'] . "')";
				}
			}
			
			if ($this->values) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` (`product_id`, `recurring_id`, `customer_group_id`) VALUES " . implode (', ', $this->values) . " ON DUPLICATE KEY UPDATE `product_id` = VALUES(product_id), `recurring_id` = VALUES(recurring_id), `customer_group_id` = VALUES(customer_group_id)");
			}
		}
		
		$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `date_modified` = NOW() WHERE `product_id` IN ('" . implode ("','", $data['product_id']) . "')");
	}
	
	public function ocFilter($data) {
		$product_category = array ();
		
		$query = $this->db->query("SELECT product_id, category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id IN (" . implode (',', $data['product_id']) . ")");
		
		foreach ($query->rows as $value) {
			$product_category[$value['product_id']][] = (int) $value['category_id'];
		}
		
		$data['option_id'] = array ();
		
		foreach ($data['data'] as $option_id => $values) {
			$data['option_id'][] = (int) $option_id;
		}
		
		if ($data['option_id']) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_option_value_to_product WHERE product_id IN (" . implode (',', $data['product_id']) . ") AND option_id IN (" . implode (',', $data['option_id']) . ")");
			$this->db->query("DELETE FROM " . DB_PREFIX . "ocfilter_option_value_to_product_description WHERE product_id IN (" . implode (',', $data['product_id']) . ") AND option_id IN (" . implode (',', $data['option_id']) . ")");
		}
		
		$option_category = array ();
		
		if ($data['option_id']) {
			$query = $this->db->query("SELECT option_id, category_id FROM " . DB_PREFIX . "ocfilter_option_to_category WHERE option_id IN (" . implode (',', $data['option_id']) . ")");
			
			foreach ($query->rows as $value) {
				$option_category[$value['option_id']][] = $value['category_id'];
			}
		}
		
		$this->values = array ('product_id' => array (), 'option_value' => array (), 'description' => array ());
		
		foreach ($data['product_id'] as $product_id) {
			foreach ($data['data'] as $option_id => $values) {
				$edit = FALSE;
				
				if (isset ($product_category[$product_id])) {
					foreach ($product_category[$product_id] as $category_id) {
						if (isset ($option_category[$option_id]) && in_array ($category_id, $option_category[$option_id])) {
							$edit = TRUE;
							break;
						}
					}
				}
				
				if (!$edit) {
					continue;
				}
				
				foreach ($values['values'] as $value_id => $value) {
					$this->values['product_id'][$product_id] = $product_id;
					
					if (!isset ($value['selected'])) {
						continue;
					}
					
					$slide_value_min = $slide_value_max = 0;
					
					if (isset ($value['slide_value_min'])) {
						$slide_value_min = (float) $value['slide_value_min'];
					}
					
					if (isset ($value['slide_value_max'])) {
						$slide_value_max = (float) $value['slide_value_max'];
					}
					
					$this->values['option_value'][] = "(" . (int) $product_id . ", " . (int) $option_id . ", " . (int) $value_id . ", " . $slide_value_min . ", " . $slide_value_max . ")";
					
					foreach ($value['description'] as $language_id => $description) {
						if (!trim ($description['description'])) {
							continue;
						}
						
						$this->values['description'][] = "(" . (int) $product_id . ", " . (int) $option_id . ", " . (int) $value_id . ", " . (int) $language_id . ", '" . $this->db->escape($description['description']) . "')";
					}
				}
			}
		}
		
		if ($this->values['option_value']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_option_value_to_product (product_id, option_id, value_id, slide_value_min, slide_value_max) VALUES " . implode (',', $this->values['option_value']));
		}
		
		if ($this->values['description']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "ocfilter_option_value_to_product_description (product_id, option_id, value_id, language_id, description) VALUES " . implode (',', $this->values['description']));
		}
		
		if ($this->values['product_id']) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id IN (" . implode (',', $this->values['product_id']) . ")");
		}
	}
	
	public function copyProductData($data) {
		$fields_array = $this->model_batch_editor_setting->getTableField($data['table']);
		
		$fields = array ();
		
		foreach ($fields_array as $field => $setting) {
			if ($setting['extra'] == 'auto_increment') {
				continue;
			}
			
			$fields[$field] = $field;
		}
		
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . $data['table'] . "` WHERE `product_id` = '" . $data['copy_product_id'] . "'");
		
		foreach ($data['product_id'] as $product_id) {
			$product_id = (int) $product_id;
			$sql_set = array ();
			
			$this->db->query("DELETE FROM `" . DB_PREFIX . $data['table'] . "` WHERE `product_id` = '" . $product_id . "'");
			
			foreach ($query->rows as $key => $array) {
				$sql_temp = array ();
				
				foreach ($array as $field => $value) {
					if (!isset ($fields[$field])) {
						continue;
					}
					
					if ($field == 'product_id') {
						$sql_temp[] = "'" . $product_id . "'";
					} else {
						$sql_temp[] = "'" . $value . "'";
					}
				}
				
				if ($sql_temp) {
					$sql_set[] = "(" . implode (',', $sql_temp) . ")";
				}
			}
			
			if ($fields && $sql_set) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . $data['table'] . "` (" . implode (',', $fields) . ") VALUES " . implode (',', $sql_set));
				
				$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `date_modified` = NOW() WHERE `product_id` = '" . $product_id . "'");
			}
		}
	}
	
	public function copyProductOption($data) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option` WHERE `product_id` = '" . $data['copy_product_id'] . "'");
		
		$data_option = $query->rows;
		
		foreach ($data_option as $key => $array) {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option_value` WHERE `product_id` = '" . $data['copy_product_id'] . "' AND `product_option_id` = '" . $array['product_option_id'] . "'");
			
			$data_option[$key]['product_option_value'] = $query->rows;
		}
		
		foreach ($data['product_id'] as $product_id) {
			$product_id = (int) $product_id;
			
			$this->db->query("DELETE FROM `" . DB_PREFIX . "product_option` WHERE `product_id` = '" . $product_id . "'");
			$this->db->query("DELETE FROM `" . DB_PREFIX . "product_option_value` WHERE `product_id` = '" . $product_id . "'");
			
			foreach ($data_option as $array) {
				$sql_set = array ();
				
				foreach ($array as $field => $value) {
					if ($field != 'product_option_id' && $field != 'product_option_value') {
						if ($field == 'product_id') {
							$sql_set[] = "`product_id` = '" . $product_id . "'";
						} else {
							$sql_set[] = "`" . $field . "` = '" . $value . "'";
						}
					}
				}
				
				if ($sql_set) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "product_option` SET " . implode (',', $sql_set));
					
					$product_option_id = $this->db->getLastId();
					
					foreach ($array['product_option_value'] as $key_1 => $array_1) {
						$sql_set = array ();
						
						foreach ($array_1 as $field => $value) {
							if ($field != 'product_option_value_id') {
								if ($field == 'product_option_id') {
									$sql_set[] = "`product_option_id` = '" . $product_option_id . "'";
								} else if ($field == 'product_id') {
									$sql_set[] = "`product_id` = '" . $product_id . "'";
								} else {
									$sql_set[] = "`" . $field . "` = '" . $value . "'";
								}
							}
						}
						
						if ($sql_set) {
							$this->db->query("INSERT INTO `" . DB_PREFIX . "product_option_value` SET " . implode (',', $sql_set));
						}
					}
				}
			}
			
			$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `date_modified` = NOW() WHERE `product_id` = '" . $product_id . "'");
		}
	}
	
	private function validateFieldType($value, $type) {
		if ($type == 'int' || $type == 'tinyint') {
			$value = (int) $value;
		} else if ($type == 'decimal' || $type == 'float') {
			$value = (float) $value;
		} else if ($type == 'char' || $type == 'varchar' || $type == 'text') {
			$value = "'" . $this->db->escape($value) . "'";
		} else {
			$value = "'" . $value . "'";
		}
		
		return $value;
	}
	
	private function getProductPrice($product_id) {
		$product_price = array ();
		
		$query = $this->db->query("SELECT product_id, price FROM " . DB_PREFIX . "product WHERE product_id IN (" . implode (',', $product_id) . ")");
		
		foreach ($query->rows as $value) {
			$product_price[$value['product_id']] = $value['price'];
		}
		
		return $product_price;
	}
	
	private function getActionPrice($action, $price, $value) {
		if ($action == 'minus_number') {
			$value = $price - (float) $value;
		}
		
		if ($action == 'minus_percent') {
			$value = $price * (100 - (float) $value) * 0.01;
		}
		
		return (float) $value;
	}
}
?>
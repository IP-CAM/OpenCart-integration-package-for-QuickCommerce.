<?php
class ModelBatchEditorTool extends Model {
	private $values = array ();
	private $path = '';
	
	public function RoundingNumbers($data) {
		if (isset ($data['rounding'])) {
			$data['rounding'] = (int) $data['rounding'];
		} else {
			$data['rounding'] = 0;
		}
		
		if (isset ($data['rule'])) {
			$data['rule'] = (int) $data['rule'];
		} else {
			$data['rule'] = 1;
		}
		
		$number = 1;
		$counter = abs ($data['rounding']);
		
		for ($i = 0; $i < $counter; $i++) {
			if ($data['rounding'] < 0) {
				$number = $number * 10;
			}
			
			if ($data['rounding'] > 0) {
				$number = $number / 10;
			}
		}
		
		$sql_array = array ();
		
		foreach ($data['apply_to'] as $table => $array) {
			foreach ($array as $field) {
				if ($data['rule'] == 2) {
					$sql_array[$table][] = "`" . $field . "` = (IF((TRUNCATE(`" . $field . "`, " . $data['rounding'] . ")) = 0, `" . $field . "`, IF((`" . $field . "` - TRUNCATE(`" . $field . "`, " . $data['rounding'] . ")) > 0, (TRUNCATE(`" . $field . "`, " . $data['rounding'] . ") + " . $number . "), TRUNCATE(`" . $field . "`, " . $data['rounding'] . "))))";
				} else {
					$sql_array[$table][] = "`" . $field . "` = (IF((ROUND(`" . $field . "`, " . $data['rounding'] . ")) = 0, `" . $field . "`, ROUND(`" . $field . "`, " . $data['rounding'] . ")))";
				}
			}
		}
		
		if ($sql_array) {
			foreach ($sql_array as $table => $set) {
				$this->db->query("UPDATE `" . DB_PREFIX . $table . "` SET " . implode (',', $set) . " WHERE `product_id` IN ('" . implode ("','", $data['product_id']) . "')");
			}
			
			$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `date_modified` = NOW() WHERE `product_id` IN ('" . implode ("','", $data['product_id']) . "')");
		}
	}
	
	public function YandexTranslate($data) {
		$option = $this->model_batch_editor_setting->get('option');
		
		if (isset ($option['yandex_translate_key_api'])) {
			$key = $option['yandex_translate_key_api'];
		} else {
			return array ('attention' => $this->language->get('attention_yandex_translate_401'));
		}
		
		$url = 'https://translate.yandex.net/api/v1.5/tr.json/translate';
		
		$lang = $data['from'] . '-' . $data['to'];
		
		$this->load->model('batch_editor/list');
		
		$languages = $this->model_batch_editor_list->getLanguages();
		
		$language_id = (int) $languages[$data['from']]['language_id'];
		
		if ($data['apply_to'] == 'tag' && VERSION < '1.5.4') {
			$sql = "SELECT `product_id`, GROUP_CONCAT(`tag`) AS `tag` FROM `" . DB_PREFIX . "product_tag` WHERE `product_id` IN ('" . implode ("','", $data['product_id']) . "') AND `language_id` = '" . $language_id . "'";
		} else {
			$sql = "SELECT `product_id`, `" . $data['apply_to'] . "` FROM `" . DB_PREFIX . "product_description` WHERE `product_id` IN ('" . implode ("','", $data['product_id']) . "') AND `language_id` = '" . $language_id . "'";
		}
		
		$query = $this->db->query($sql);
		
		$language_id = (int) $languages[$data['to']]['language_id'];
		
		foreach ($query->rows as $array) {
			$product_id = (int) $array['product_id'];
			
			foreach ($array as $field => $text) {
				if ($field == 'product_id' || !$text) {
					continue;
				}
				
				if ($data['apply_to'] == 'tag' && VERSION < '1.5.4') {
					$sql = "SELECT GROUP_CONCAT(`tag`) AS `tag` FROM `" . DB_PREFIX . "product_tag` WHERE `product_id` = '" . $product_id . "' AND `language_id` = '" . $language_id . "'";
				} else {
					$sql = "SELECT `" . $data['apply_to'] . "` FROM `" . DB_PREFIX . "product_description` WHERE `product_id` = '" . $product_id . "' AND `language_id` = '" . $language_id . "'";
				}
				
				$validate = $this->db->query($sql);
				
				if (!isset ($data['rewrite']) && isset ($validate->row[$data['apply_to']]) && $validate->row[$data['apply_to']]) {
					continue;
				}
				
				$text = urlencode ($text);
				$curl = curl_init ();
				
				curl_setopt ($curl, CURLOPT_URL, $url);
				curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt ($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, 10);
				curl_setopt ($curl, CURLOPT_POST, true);
				curl_setopt ($curl, CURLOPT_POSTFIELDS, 'key=' . $key . '&text=' . $text . '&lang=' . $lang . '&format=html');
				$json = curl_exec ($curl);
				
				curl_close ($curl);
				
				if ($json) {
					$yandex_translate = json_decode ($json);
					
					if (isset ($yandex_translate->code)) {
						if ($yandex_translate->code == '200') {
							if (isset ($yandex_translate->text[0]) && $yandex_translate->text[0]) {
								$text = $this->db->escape(str_replace ('"', '&quot;', $yandex_translate->text[0]));
								
								if ($data['apply_to'] == 'tag' && VERSION < '1.5.4') {
									$this->db->query("DELETE FROM `" . DB_PREFIX . "product_tag` WHERE `product_id` = '" . $product_id . "' AND `language_id` = '" . $language_id . "'");
									
									$tags = explode (',', $text);
									
									foreach ($tags as $tag) {
										if (trim ($tag)) {
											$this->db->query("INSERT INTO `" . DB_PREFIX . "product_tag` (`product_id`, `language_id`, `tag`) VALUES ('" . $product_id . "', '" . $language_id . "', '" . $text . "')");
										}
									}
									
								} else {
									if (isset ($validate->row[$data['apply_to']])) {
										$this->db->query("UPDATE `" . DB_PREFIX . "product_description` SET `" . $field . "` = '" . $text . "' WHERE `product_id` = '" . $product_id . "' AND `language_id` = '" . $language_id . "' ");
									} else {
										$this->db->query("INSERT INTO `" . DB_PREFIX . "product_description` (`product_id`, `language_id`, `" . $field . "`) VALUES ('" . $product_id . "', '" . $language_id . "', '" . $text . "')");
									}
								}
							}
						} else if ($yandex_translate->code == '422') {
							
						} else {
							return array ('attention' => $this->language->get('attention_yandex_translate_' . $yandex_translate->code));
						}
					}
				}
			}
			
			$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `date_modified` = NOW() WHERE `product_id` = '" . $product_id . "'");
		}
	}
	
	public function SeoGenerator($data) {
		$this->load->model('batch_editor/setting');
		
		$table = $this->model_batch_editor_setting->get('table');
		$list = $this->model_batch_editor_setting->get('list');
		
		if (isset ($data['translit'])) {
			$this->load->model('batch_editor/function');
			
			$translit = true;
		} else {
			$translit = false;
		}
		
		if (isset ($data['synonymizer'])) {
			$synonymizer = true;
		} else {
			$synonymizer = false;
		}
		
		$pd = false;
		
		if (!isset ($data['apply_to']['p'])) {
			$data['apply_to']['p'] = array ();
		}
		
		if (!isset ($data['apply_to']['pd'])) {
			$data['apply_to']['pd'] = array ();
		}
		
		foreach ($data['product_id'] as $key => $product_id) {
			$product_id = (int) $product_id;
			
			foreach ($data['language_id'] as $language_id) {
				$language_id = (int) $language_id;
				
				$concat = array ();
				$left_join = array ();
				
				foreach ($data['data'] as $index => $array) {
					foreach ($array as $type => $value) {
						if ($type == 'text') {
							if ($value == '{space}') {
								$concat[] = '" "';
							} else if ($value || $value == '0') {
								if ($synonymizer) {
									$value = explode ('|', $value);
									
									if (count ($value)) {
										shuffle ($value);
										$value = array_pop ($value);
									} else {
										$value = implode ('', $value);
									}
								}
								
								$concat[] = '"' . $this->db->escape($value) . '"';
							}
						} else if ($type == 'data') {
							if ($value == '{attribute}') {
								if (isset ($data['data'][$index]['attribute_id'])) {
									$attribute_id = (int) $data['data'][$index]['attribute_id'];
									
									if ($attribute_id) {
										if (isset ($data['data'][$index]['separator_attribute_value']) && $data['data'][$index]['separator_attribute_value']) {
											$separator_attribute_value = $this->db->escape($data['data'][$index]['separator_attribute_value']);
										} else {
											$separator_attribute_value = ':';
										}
										
										$concat[] = "IFNULL((SELECT CONCAT(REPLACE(ad.name, '\"', '&quot;'), '" . $separator_attribute_value . "', REPLACE(pa.text, '\"', '&quot;')) FROM " . DB_PREFIX . "attribute_description ad LEFT JOIN " . DB_PREFIX . "product_attribute pa ON (pa.attribute_id = ad.attribute_id) WHERE ad.language_id = '" . $language_id . "' AND pa.language_id = '" . $language_id . "' AND pa.product_id = '" . $product_id . "' AND ad.attribute_id = '" . $attribute_id . "'), ' ')";
									}
								}
							} else if ($value == '{attributes_all}') {
								if (isset ($data['data'][$index]['separator_attribute_value']) && $data['data'][$index]['separator_attribute_value']) {
									$separator_attribute_value = $this->db->escape($data['data'][$index]['separator_attribute_value']);
								} else {
									$separator_attribute_value = ':';
								}
								
								if (isset ($data['data'][$index]['separator_attribute']) && $data['data'][$index]['separator_attribute']) {
									$separator_attribute = $this->db->escape($data['data'][$index]['separator_attribute']);
								} else {
									$separator_attribute = ', ';
								}
								
								$concat[] = "IFNULL((SELECT GROUP_CONCAT(REPLACE(ad.name, '\"', '&quot;'), '" . $separator_attribute_value . "', REPLACE(pa.text, '\"', '&quot;') SEPARATOR '" . $separator_attribute . "') FROM " . DB_PREFIX . "attribute_description ad LEFT JOIN " . DB_PREFIX . "product_attribute pa ON (pa.attribute_id = ad.attribute_id) WHERE ad.language_id = '" . $language_id . "' AND pa.language_id = '" . $language_id . "' AND pa.product_id = '" . $product_id . "'), ' ')";
							} else if ($value == 'url_alias') {
								$concat[] = "IFNULL((SELECT REPLACE(keyword, '\"', '&quot;') FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . $product_id . "' LIMIT 1), ' ')";
							} else if ($value == 'tag' && VERSION < '1.5.4') {
								$concat[] = "IFNULL((SELECT GROUP_CONCAT(REPLACE(tag, '\"', '&quot;') SEPARATOR ',') FROM " . DB_PREFIX . "product_tag WHERE product_id = '" . $product_id . "' AND language_id = '" . $language_id . "'), ' ')";
							} else if (preg_match ('/^price_[A-Z]*/', $value)) {
								$currency_code = str_replace ('price_', '', $value);
								
								$query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product WHERE product_id = '" . $product_id . "'");
								
								if (isset ($query->row['price'])) {
									$price = $this->currency->format($query->row['price'], $currency_code);
								} else {
									$price = $this->currency->format(0, $currency_code);
								}
								
								$concat[] = '"' . $price . '"';
							} else if (isset ($list[$value])) {
								$concat[] = "IFNULL(REPLACE(" . $list[$value]['name'] . "." . $list[$value]['field'] . ", '\"', '&quot;'), ' ')";
								
								$left_join[$value] = $value;
							} else {
								if (isset ($table[$value])) {
									$concat[] = "IFNULL(REPLACE(" . $table[$value]['table'] . "." . $value . ", '\"', '&quot;'), ' ')";
									
									if ($table[$value]['table'] == 'pd') {
										$pd = true;
									}
								}
							}
						}
					}
				}
				
				if ($concat) {
					$sql = "SELECT CONCAT(" . implode (',', $concat) . ") AS text FROM " . DB_PREFIX . "product p ";
					
					if ($pd) {
						$sql .= "LEFT JOIN " . DB_PREFIX . "product_description pd ON (pd.product_id = p.product_id AND pd.language_id = '" . $language_id . "') ";
					}
					
					foreach ($left_join as $field) {
						$sql .= "LEFT JOIN " . DB_PREFIX . $list[$field]['table'] . " " . $list[$field]['name'] . " ON (" . $list[$field]['name'] . "." . $field . " = p." . $field . " ";
						
						if (isset ($list[$field]['lang'])) {
							$sql .= "AND " . $list[$field]['name'] . ".language_id = '" . $language_id . "'";
						}
						
						$sql .= ") ";
					}
					
					$sql .= "WHERE p.product_id = '" . $product_id . "' ";
					
					$query = $this->db->query($sql);
				}
				
				if (isset ($query->row['text'])) {
					$text = $query->row['text'];
				} else {
					$text = '';
				}
				
				if ($text) {
					if ($translit) {
						$text = $this->model_batch_editor_function->translit($text, '-');
					}
					
					$set = array ();
					
					foreach ($data['apply_to']['p'] as $apply_to) {
						if ($apply_to == 'url_alias') {
							$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . $product_id . "'");
							$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . $product_id . "', keyword = '" . trim ($text) . "'");
						} else {
							$set[] = $apply_to . ' = "' . trim ($text) . '"';
						}
					}
					
					if ($set) {
						$this->db->query("UPDATE " . DB_PREFIX . "product SET " . implode (',', $set) . " WHERE product_id = '" . $product_id . "'");
					}
					
					$tags = false;
					$set = array ();
					
					foreach ($data['apply_to']['pd'] as $apply_to) {
						if ($apply_to == 'tag' && VERSION < '1.5.4') {
							$tags = trim ($text);
						} else {
							$set[] = $apply_to . ' = "' . trim ($text) . '"';
						}
					}
					
					if ($set) {
						$this->db->query("UPDATE " . DB_PREFIX . "product_description SET " . implode (',', $set) . " WHERE product_id = '" . $product_id . "' AND language_id = '" . $language_id . "'");
					}
					
					if ($tags) {
						$this->values = array ();
						$tags = explode (',', $tags);
						
						foreach ($tags as $tag) {
							$tag = trim ($tag);
							
							if ($tag) {
								$this->values[] = "('" . $product_id . "','" . $language_id . "','" . $this->db->escape($tag) . "')";
							}
						}
						
						if ($this->values) {
							$this->db->query("DELETE FROM " . DB_PREFIX . "product_tag WHERE product_id = '" . $product_id . "' AND language_id = '" . $language_id . "'");
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_tag (product_id, language_id, tag) VALUES " . implode (',', $this->values));
						}
					}
				} else {
					unset ($data['product_id'][$key]);
				}
			}
		}
		
		if ($data['product_id']) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id IN (" . implode (',', $data['product_id']) . ")");
		}
	}
	
	public function SearchReplace($data) {
		if (!isset ($data['apply_to']['p'])) {
			$data['apply_to']['p'] = array ();
		}
		
		if (!isset ($data['apply_to']['pd'])) {
			$data['apply_to']['pd'] = array ();
		}
		
		$this->load->model('batch_editor/setting');
		
		$product = $this->model_batch_editor_setting->table('product');
		unset ($product['product_id']);
		
		$product_description = $this->model_batch_editor_setting->table('product_description');
		unset ($product_description['product_id']);
		unset ($product_description['language_id']);
		
		$list = $this->model_batch_editor_setting->get('list');
		
		$sql_replace = '';
		
		$sql_set = array ();
		
		$sql_table[] = "`" . DB_PREFIX . "product` p, `" . DB_PREFIX . "product_description` pd";
		
		$sql_and[] = "p.product_id = pd.product_id";
		$sql_and[] = "p.product_id IN ('" . implode ("','", $data['product_id']) . "')";
		$sql_and[] = "pd.product_id IN ('" . implode ("','", $data['product_id']) . "')";
		$sql_and[] = "pd.language_id = '" . (int) $data['language_id'] . "'";
		
		foreach ($data['type'] as $key => $type) {
			if (!isset ($data['what'][$key]) || !isset ($data['on_what'][$key])) {
				continue;
			}
			
			if ($type == 'text') {
				if ($sql_replace) {
					$sql_replace = "REPLACE(" . $sql_replace . ", '" . $this->db->escape($data['what'][$key]) . "', '" . $this->db->escape($data['on_what'][$key]) . "')";
				} else {
					$sql_replace = "REPLACE({FIELD}, '" . $this->db->escape($data['what'][$key]) . "', '" . $this->db->escape($data['on_what'][$key]) . "')";
				}
			} else {
				$on_what = "";
				$field = $data['on_what'][$key];
				
				if (isset ($product[$field])) {
					if (isset ($list[$field])) {
						$on_what = "REPLACE(" . $list[$field]['name'] . "." . $list[$field]['field'] . ", '\"', '&quot;')";
						
						$sql_table[$list[$field]['name']] = DB_PREFIX . $list[$field]['table'] . " " . $list[$field]['name'];
						
						$sql_and[$list[$field]['name']] = "p." . $field . " = " . $list[$field]['name'] . "." . $field;
						
						if (isset ($list[$field]['lang'])) {
							$sql_and[$list[$field]['name']] .= " AND " . $list[$field]['name'] . ".language_id = '" . (int) $data['language_id'] . "'";
						}
					} else if ($product[$field]['type'] == 'tinyint') {
						if (preg_match ('/status/', $field)) {
							$text_0 = $this->db->escape($this->language->get('text_disabled'));
							$text_1 = $this->db->escape($this->language->get('text_enabled'));
						} else {
							$text_0 = $this->db->escape($this->language->get('text_no'));
							$text_1 = $this->db->escape($this->language->get('text_yes'));
						}
						
						$on_what = "(IF(p." . $field . ", '" . $text_1 . "', '" . $text_0 . "'))";
					} else {
						$on_what = "REPLACE(p." . $field . ", '\"', '&quot;')";
					}
				}
				
				if (isset ($product_description[$field])) {
					$on_what = "pd." . $field;
				}
				
				if ($field == 'url_alias') {
					$on_what = "(SELECT ua.keyword FROM `" . DB_PREFIX . "url_alias` ua WHERE ua.query = CONCAT('product_id=', p.product_id))";
				}
				
				if ($field == 'tag' && VERSION < '1.5.4') {
					$on_what = "(SELECT CONCAT_WS(',', pt.tag) FROM `" . DB_PREFIX . "product_tag` pt WHERE pt.product_id = p.product_id AND pt.language_id = '" . (int) $data['language_id'] . "')";
				}
				
				if (!$on_what) {
					continue;
				}
				
				if ($sql_replace) {
					$sql_replace = "REPLACE(" . $sql_replace . ", '" . $this->db->escape($data['what'][$key]) . "', " . $on_what . ")";
				} else {
					$sql_replace = "REPLACE({FIELD}, '" . $this->db->escape($data['what'][$key]) . "', " . $on_what . ")";
				}
			}
		}
		
		foreach ($data['apply_to']['p'] as $field) {
			if (isset ($product[$field])) {
				$sql_set[] = "p." . $field . "=" . str_replace ('{FIELD}', 'p.' . $field, $sql_replace);
			}
			
			if ($field == 'url_alias') {
				$sql_set[] = "ua.keyword=" . str_replace ('{FIELD}', 'ua.keyword', $sql_replace);
				
				$sql_table['ua'] = "`" . DB_PREFIX . "url_alias` ua";
				
				$sql_and['ua'] = "ua.query = CONCAT('product_id=', p.product_id)";
			}
		}
		
		foreach ($data['apply_to']['pd'] as $field) {
			if (isset ($product_description[$field])) {
				$sql_set[] = "pd." . $field . "=" . str_replace ('{FIELD}', 'pd.' . $field, $sql_replace);
			}
			
			if ($field == 'tag' && VERSION < '1.5.4') {
				$sql_set[] = "pt." . $field . "=" . str_replace ('{FIELD}', 'pt.' . $field, $sql_replace);
				
				$sql_table['pt'] = "`" . DB_PREFIX . "product_tag` pt";
				$sql_and['pt'] = "p.product_id = pt.product_id AND pt.product_id IN ('" . implode ("','", $data['product_id']) . "') AND pt.language_id = " . (int) $data['language_id'];
			}
			
			if ($field == 'attribute') {
				$sql_set[] = "pa.text=" . str_replace ('{FIELD}', 'pa.text', $sql_replace);
				
				$sql_table['pa'] = "`" . DB_PREFIX . "product_attribute` pa";
				$sql_and['pa'] = "p.product_id = pa.product_id AND pa.product_id IN ('" . implode ("','", $data['product_id']) . "') AND pa.language_id = " . (int) $data['language_id'];
			}
		}
		
		if ($sql_set) {
			$this->db->query("UPDATE " . implode (',', $sql_table) . " SET " . implode (',', $sql_set) . " WHERE " . implode (' AND ', $sql_and));
			
			$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `date_modified` = NOW() WHERE `product_id` IN ('" . implode ("','", $data['product_id']) . "')");
		}
	}
	
	public function OptionPrice($data) {
		if (!is_array ($data['data'])) {
			return false;
		}
		
		$fields = array ('option_id', 'option_value_id', 'subtract', 'quantity_min', 'quantity_max', 'price_prefix', 'price_min', 'price_max', 'points_prefix', 'points_min', 'points_max', 'weight_prefix', 'weight_min', 'weight_max');
		
		foreach ($data['data'] as $value) {
			if (isset ($value['value'])) {
				$value['value'] = (float) $value['value'];
			} else {
				$value['value'] = 0;
			}
			
			$action = array (
				'equal_number'    => $value['value'],
				'plus_number'     => "(price + " . $value['value'] . ")",
				'minus_number'    => "(price - " . $value['value'] . ")",
				'multiply_number' => "(price * " . $value['value'] . ")",
				'divide_number'   => "(price / " . $value['value'] . ")",
				'plus_percent'    => "(price * " . (100 + $value['value']) * 0.01 . ")",
				'minus_percent'   => "(price * " . (100 - $value['value']) * 0.01 . ")"
			);
			
			if (isset ($value['action']) && isset ($action[$value['action']])) {
				$value['action'] = $action[$value['action']];
			} else {
				$value['action'] = $action['equal_number'];
			}
			
			$sql_and = array ();
			
			foreach ($fields as $field) {
				if (isset ($value[$field]) && ($value[$field] || $value[$field] == '0')) {
					$value[$field] = $this->db->escape($value[$field]);
					
					if (preg_match ('/_min$/', $field)) {
						$sql_and[] = preg_replace ('/_min$/', '', $field) . " > '" . $value[$field] . "'";
					} else if (preg_match ('/_max$/', $field)) {
						$sql_and[] = preg_replace ('/_max$/', '', $field) . " < '" . $value[$field] . "'";
					} else {
						$sql_and[] = $field . " = '" . $value[$field] . "'";
					}
				}
			}
			
			$sql = "UPDATE " . DB_PREFIX . "product_option_value SET price = " . $value['action'] . " WHERE product_id IN ('" . implode ("','", $data['product_id']) . "')";
			
			if ($sql_and) {
				$sql .= " AND " . implode (' AND ', $sql_and);
			}
			
			$this->db->query($sql);
			
			$this->db->query("UPDATE " . DB_PREFIX . "product SET date_modified = NOW() WHERE product_id IN ('" . implode ("','", $data['product_id']) . "')");
		}
	}
	
	public function ImageGoogle($data) {
		$this->load->model('batch_editor/function');
		
		$images = array ();
		$image_type = array ('jpg', 'jpeg', 'png', 'gif', 'bmp');
		
		$data['keyword'] = $this->model_batch_editor_function->translit($data['keyword']);
		
		if (!$data['keyword']) {
			$data['keyword'] = 'image_google';
		}
		
		$directory = DIR_IMAGE . $data['directory']['main'];
		unset ($data['directory']['main']);
		
		foreach ($data['directory'] as $folder) {
			$folder = $this->model_batch_editor_function->translit($folder);
			
			if ($folder) {
				$directory .= $folder . '/';
				
				if (!is_dir ($directory)) {
					mkdir ($directory);
				}
			}
		}
		
		foreach ($data['data'] as $key => $url) {
			if (!$url) {
				continue;
			}
			
			$info = pathinfo ($url);
			
			if (!isset ($info['extension'])) {
				continue;
			}
			
			if (!in_array (strtolower ($info['extension']), $image_type)) {
				continue;
			}
			
			$this->validateImagePath($directory, $data['keyword'], $info['extension']);
			
			$ch = curl_init ($url);
			$fp = fopen ($this->path, 'wb');
			curl_setopt ($ch, CURLOPT_FILE, $fp);
			curl_setopt ($ch, CURLOPT_HEADER, 0);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.1.5) Gecko/20091102 Firefox/3.5.5');
			curl_exec ($ch);
			curl_close ($ch);
			fclose ($fp);
			
			if (file_exists ($this->path)) {
				if (filesize ($this->path) < 500) {
					unlink ($this->path);
				} else {
					if (strtolower ($key) == 'main') {
						$images['main'] = str_replace (DIR_IMAGE, '', $this->path);
					} else {
						$images[] = array ('image' => str_replace (DIR_IMAGE, '', $this->path), 'sort_order' => 0);
					}
				}
			}
		}
		
		if ($images) {
			$this->load->model('batch_editor/edit');
			
			if (isset ($images['main'])) {
				$data_set = array ('product_id' => $data['product_id'], 'field' => 'image', 'value' => $images['main']);
				
				unset ($images['main']);
				
				$this->model_batch_editor_edit->Product($data_set);
			}
			
			$data_set = array ('product_id' => $data['product_id'], 'action' => $data['action'], 'data' => $images);
			
			$this->model_batch_editor_edit->Image($data_set);
		}
	}
	
	public function ImageGoogleAuto($data) {
		$this->load->model('batch_editor/setting');
		$this->load->model('batch_editor/function');
		
		$image_type = array ('jpg', 'jpeg', 'png', 'gif', 'bmp');
		
		if (isset ($data['number_images'])) {
			$data['number_images'] = (int) $data['number_images'];
		} else {
			$data['number_images'] = 4;
		}
		
		if ($data['number_images'] > 12) {
			$data['number_images'] = 12;
		}
		
		if ($data['number_images'] < 1) {
			$data['number_images'] = 4;
		}
		
		$pages = ceil ($data['number_images'] / 4);
		
		if (isset ($data['main_image'])) {
			$data['main_image'] = abs ((int) $data['main_image']);
		} else {
			$data['main_image'] = 0;
		}
		
		if ($data['main_image'] > $data['number_images']) {
			$data['main_image'] = $data['number_images'];
		}
		
		$url = 'https://ajax.googleapis.com/ajax/services/search/images?v=1.0&userip=' . $_SERVER['REMOTE_ADDR'];
		
		if (isset ($data['url']) && is_array ($data['url'])) {
			foreach ($data['url'] as $variable => $value) {
				if ($value) {
					$url .= '&' . $variable . '=' . $value;
				}
			}
		}
		
		$count = 0;
		$start_4 = 0;
		
		$main_category = $this->model_batch_editor_setting->getTableField('product_to_category', 'main_category');
		
		foreach ($data['product_id'] as $product_id) {
			$folder_array = array ();
			
			$query = $this->db->query("SELECT `image` FROM `" . DB_PREFIX . "product_image` WHERE `product_id` = '" . (int) $product_id . "' LIMIT 1");
			
			if ($query->num_rows) {
				$folder_array = explode ('/', $query->row['image']);
				
				array_shift ($folder_array);
				array_pop ($folder_array);
			} else {
				$query = $this->db->query("SELECT `image` FROM `" . DB_PREFIX . "product` WHERE `product_id` = '" . (int) $product_id . "' LIMIT 1");
				
				if ($query->num_rows) {
					$folder_array = explode ('/', $query->row['image']);
					
					array_shift ($folder_array);
					array_pop ($folder_array);
				}
			}
			
			if (!$folder_array && $main_category) {
				$query = $this->db->query("SELECT `category_id` FROM `" . DB_PREFIX . "product_to_category` WHERE `product_id` = '" . (int) $product_id . "' AND `main_category` = '1' LIMIT 1");
				
				if ($query->num_rows) {
					$folder_array = $this->getCategoriesArray($query->row['category_id']);
				}
			}
			
			if (!$folder_array) {
				$query = $this->db->query("SELECT `category_id` FROM `" . DB_PREFIX . "product_to_category` WHERE `product_id` = '" . (int) $product_id . "' LIMIT 1");
				
				if ($query->num_rows) {
					$folder_array = $this->getCategoriesArray($query->row['category_id']);
				}
			}
			
			if (!$folder_array) {
				if (isset ($data['directory']) && is_array ($data['directory'])) {
					$folder_array = $data['directory'];
				}
			}
			
			if (VERSION < '2.0.0.0') {
				$directory = DIR_IMAGE . 'data/';
			} else {
				$directory = DIR_IMAGE . 'catalog/';
			}
			
			foreach ($folder_array as $folder) {
				if (trim ($folder)) {
					$folder = $this->model_batch_editor_function->translit($folder);
					
					if ($folder) {
						$directory .= $folder . "/";
						
						if (!is_dir ($directory)) {
							mkdir ($directory);
						}
					}
				}
			}
			
			$setting = $this->model_batch_editor_setting->get('tool/image_google');
			
			$keyword = $this->model_batch_editor_setting->getKeywordFromField($product_id, $setting['keyword']);
			
			if (!$keyword) {
				continue;
			}
			
			$image_name = $this->model_batch_editor_function->translit($keyword);
			
			if (!$image_name) {
				$image_name = 'image_google_auto';
			}
			
			for ($start = 0; $start < $pages; $start++) {
				$url_request = $url . '&q=' . urlencode ($keyword) . '&start=' . $start_4;
				
				$start_4 += 4;
				
				$ch = curl_init ();
				curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 20);
				curl_setopt ($ch, CURLOPT_URL, $url_request);
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt ($ch, CURLOPT_REFERER, HTTP_CATALOG);
				$result = curl_exec ($ch);
				curl_close ($ch);
				
				$json = json_decode ($result);
				
				if (!isset ($json->responseData->results) || !is_array ($json->responseData->results)) {
					continue;
				}
				
				foreach ($json->responseData->results as $value) {
					if ($count >= $data['number_images']) {
						break;
					}
					
					$image_url = $value->unescapedUrl;
					
					$info = pathinfo ($image_url);
					
					if (!isset ($info['extension'])) {
						continue;
					}
					
					if (!in_array (strtolower ($info['extension']), $image_type)) {
						continue;
					}
					
					$this->validateImagePath($directory, $image_name, $info['extension']);
					
					$ch = curl_init ($image_url);
					$fp = fopen ($this->path, 'wb');
					curl_setopt ($ch, CURLOPT_FILE, $fp);
					curl_setopt ($ch, CURLOPT_HEADER, 0);
					curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 10);
					curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.1.5) Gecko/20091102 Firefox/3.5.5');
					curl_exec ($ch);
					curl_close ($ch);
					fclose ($fp);
					
					if (file_exists ($this->path)) {
						if (filesize ($this->path) < 500) {
							unlink ($this->path);
						} else {
							$count++;
							
							$image = $this->db->escape(str_replace (DIR_IMAGE, '', $this->path));
							
							if ($data['main_image'] && $data['main_image'] == $count) {
								$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `image` = '" . $image . "', `date_modified` = NOW() WHERE `product_id` = '" . $product_id . "'");
							} else {
								$this->db->query("INSERT INTO `" . DB_PREFIX . "product_image` (`product_id`, `image`, `sort_order`) VALUES ('" . $product_id . "', '" . $image . "', '0') ON DUPLICATE KEY UPDATE `product_id` = VALUES(`product_id`), `image` = VALUES(`image`), `sort_order` = VALUES(`sort_order`)");
							
								$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `date_modified` = NOW() WHERE `product_id` = '" . $product_id . "'");
							}
						}
					}
				}
			}
		}
	}
	
	public function LostImage($data) {
		$this->load->language('batch_editor/tool');
		
		$lost_image = array ();
		
		if (isset ($data['delete'])) {
			$delete = true;
		} else {
			$delete = false;
		}
		
		$query = $this->db->query("SELECT p.product_id, p.image, pd.name FROM `" . DB_PREFIX . "product` p LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id AND pd.language_id = '" . $this->config->get('config_language_id') . "') WHERE p.product_id IN ('" . implode ("','", $data['product_id']) . "')");
		
		foreach ($query->rows as $array) {
			if ($array['image'] && !file_exists (DIR_IMAGE . $array['image'])) {
				$lost_image[] = array ('name' => $array['name'], 'type' => $this->language->get('text_main'), 'image' => $array['image']);
				
				if ($delete) {
					$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `image` = '', `date_modified` = NOW() WHERE product_id = '" . $array['product_id'] . "'");
				}
			}
		}
		
		$query = $this->db->query("SELECT pi.product_id, pi.image, pd.name FROM `" . DB_PREFIX . "product_image` pi LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (pi.product_id = pd.product_id AND pd.language_id = '" . $this->config->get('config_language_id') . "') WHERE pi.product_id IN ('" . implode ("','", $data['product_id']) . "')");
		
		foreach ($query->rows as $array) {
			if (!file_exists (DIR_IMAGE . $array['image'])) {
				$lost_image[] = array ('name' => $array['name'], 'type' => $this->language->get('text_additional'), 'image' => $array['image']);
				
				if ($delete) {
					$this->db->query("DELETE FROM `" . DB_PREFIX . "product_image` WHERE product_id = '" . $array['product_id'] . "' AND `image` = '" . $this->db->escape($array['image']) . "'");
					$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `date_modified` = NOW() WHERE product_id = '" . $array['product_id'] . "'");
				}
			}
		}
		
		return array ('value' => $lost_image);
	}
	
	public function ImageManager($data) {
		$this->load->model('batch_editor/function');
		
		$image_type = array ('jpg', 'jpeg', 'png', 'gif', 'bmp');
		
		$directory = DIR_IMAGE . $data['directory']['main'];
		unset ($data['directory']['main']);
		
		foreach ($data['directory'] as $folder) {
			$folder = $this->model_batch_editor_function->translit($folder);
			
			if ($folder) {
				$directory .= $folder . '/';
				
				if (!is_dir ($directory)) {
					mkdir ($directory);
				}
			}
		}
		
		if (isset ($data['image_name'])) {
			$data['image_name'] = (int) $data['image_name'];
		} else {
			$data['image_name'] = 0;
		}
		
		if (isset ($data['image']) && is_array ($data['image'])) {
			foreach ($data['product_id'] as $product_id) {
				if ($data['image_name']) {
					$query = $this->db->query("SELECT `name` FROM `" . DB_PREFIX . "product_description` WHERE `product_id` = '" . (int) $product_id . "' AND `language_id` = '" . (int) $this->config->get('config_language_id') . "'");
					
					if ($query->num_rows) {
						$image_name = $this->model_batch_editor_function->translit($query->row['name']);
					} else {
						$image_name = '';
					}
				}
				
				foreach ($data['image'] as $key => $image) {
					if (!isset ($image['data']) || !isset ($image['name'])) {
						continue;
					}
					
					$info = pathinfo ($image['name']);
					
					if (!isset ($info['extension'])) {
						continue;
					}
					
					$info['extension'] = $this->model_batch_editor_function->translit(strtolower ($info['extension']));
					
					if ($data['image_name'] == 0) {
						$image_name = $this->model_batch_editor_function->translit(preg_replace ('/\.' . $info['extension'] . '$/', '' , $image['name']));
					}
					
					if (!$image_name) {
						$image_name = 'image_manager';
					}
					
					if (!in_array ($info['extension'], $image_type)) {
						continue;
					}
					
					$data_temp = explode (',', $image['data']);
					
					if (!isset ($data_temp[1])) {
						continue;
					}
					
					if (isset ($image['sort_order'])) {
						$sort_order = (int) $image['sort_order'];
					} else {
						$sort_order = 0;
					}
					
					$this->validateImagePath($directory, $image_name, $info['extension']);
					
					$data_temp = str_replace (' ', '+', $data_temp[1]);
					$data_temp = base64_decode ($data_temp);
					
					if (file_put_contents ($this->path, $data_temp)) {
						$image_name = str_replace (DIR_IMAGE, '', $this->path);
						
						if (is_integer ($key)) {
							$this->db->query("INSERT INTO `" . DB_PREFIX . "product_image` (`product_id`, `image`, `sort_order`) VALUES ('" . (int) $product_id . "', '" . $image_name . "', '" . $sort_order . "')");
							
							$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `date_modified` = NOW() WHERE `product_id` = '" . (int) $product_id . "'");
						} else {
							$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `image` = '" . $image_name . "', `date_modified` = NOW() WHERE `product_id` = '" . (int) $product_id . "'");
						}
					}
				}
			}
		}
	}
	
	private function validateImagePath($folder, $name, $extension) {
		$path = $folder . $name . '_' . rand (100, 999) . '.' . $extension;
		
		if (file_exists ($path)) {
			$this->validateImagePath($folder, $name, $extension);
		} else {
			$this->path = $path;
		}
	}
	
	private function getCategoriesArray($category_id) {
		static $array = array ();
		
		$query = $this->db->query("SELECT c.category_id, c.parent_id, cd.name FROM `" . DB_PREFIX . "category` c LEFT JOIN `" . DB_PREFIX . "category_description` cd ON (c.category_id = cd.category_id) WHERE c.category_id = '" . (int) $category_id . "' AND cd.language_id = '" . (int) $this->config->get("config_language_id") . "'");
		
		if ($query->num_rows) {
			array_unshift ($array, $query->row['name']);
			
			if ($query->row['parent_id']) {
				$this->getCategoriesArray($query->row['parent_id']);
			}
		}
		
		return $array;
	}
}
?>
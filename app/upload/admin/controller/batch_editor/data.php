<?php
class ControllerBatchEditorData extends Controller {
	private $json = array ();
	
	public function getProductPrice() {
		$data = array ();
		
		if (isset ($this->request->post['selected']) && is_array ($this->request->post['selected'])) {
			$selected = $this->request->post['selected'];
		} else {
			$selected = array ();
		}
		
		if (isset ($this->request->post['price']) && $this->request->post['price'] == 'product_discount') {
			$table = 'product_discount';
		} else {
			$table = 'product_special';
		}
		
		if ($selected) {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . $table . "` WHERE `product_id` IN ('" . implode ("','", $selected) . "') ORDER BY `priority`, `price` DESC");
			
			foreach ($query->rows as $array) {
				if (($array['date_start'] == '0000-00-00' || date ('Y-m-d') >= $array['date_start']) && ($array['date_end'] == '0000-00-00' || date ('Y-m-d') <= $array['date_end'])) {
					$data[$array['product_id']] = $array['price'];
				}
			}
			
			foreach ($selected as $product_id) {
				if (!isset ($data[$product_id])) {
					$data[$product_id] = '';
				}
			}
		}
		
		echo json_encode ($data);
	}
	
	public function getProductDateModified() {
		$data = array ();
		$products = array ();
		
		if (isset ($this->request->post['products']) && is_array ($this->request->post['products'])) {
			$products = $this->request->post['products'];
		}
		
		if ($products) {
			$query = $this->db->query("SELECT product_id, date_modified FROM " . DB_PREFIX . "product WHERE product_id IN (" . implode (', ', $products) . ")");
			
			foreach($query->rows as $value) {
				$data[$value['product_id']] = $value['date_modified'];
			}
		}
		
		echo json_encode ($data);
	}
	
	public function getProductCount() {
		$this->load->model('batch_editor/setting');
		
		$_link_ = array ();
		
		$link_data = $this->model_batch_editor_setting->get('link');
		
		foreach ($link_data as $link => $value) {
			if (isset ($value['enable']['product'])) {
				$_link_[$link]['table'] = $value['table'];
			}
		}
		
		$link_data = $this->model_batch_editor_setting->getAdditionalLink();
		
		foreach ($link_data as $link => $value) {
			if (isset ($value['enable']['product'])) {
				$_link_[$link]['table'] = $value['table'];
			}
		}
		
		$data = array ();
		
		if (isset ($this->request->post['selected']) && is_array ($this->request->post['selected'])) {
			$selected = $this->request->post['selected'];
		} else {
			$selected = array ();
		}
		
		if (isset ($this->request->post['links']) && is_array ($this->request->post['links'])) {
			$links = $this->request->post['links'];
		} else {
			$links = array ();
		}
		
		foreach ($links as $key => $link) {
			if (!isset ($_link_[$link])) {
				unset ($links[$key]);
			}
		}
		
		if (!$links) {
			foreach ($_link_ as $link => $value) {
				$links[] = $link;
			}
		}
		
		foreach ($selected as $product_id) {
			foreach ($links as $link) {
				$sql = "";
				
				if ($link == 'attribute') {
					$sql = "AND language_id = '" . (int) $this->config->get('config_language_id') . "'";
				}
				
				$query = $this->db->query("SELECT COUNT(product_id) AS `count` FROM `" . DB_PREFIX . $_link_[$link]['table'] . "` WHERE `product_id` = '" . (int) $product_id . "' " . $sql);
				
				$data[$product_id][$link] = $query->row['count'];
			}
		}
		
		echo json_encode ($data);
	}
	
	public function getProductData() {
		$this->load->model('tool/image');
		$this->load->model('batch_editor/setting');
		
		$option = $this->model_batch_editor_setting->get('option');
		$table = $this->model_batch_editor_setting->get('table');
		$list = $this->model_batch_editor_setting->get('list');
		
		$data = array ();
		
		if (isset ($this->request->post['field'])) {
			$field = $this->request->post['field'];
		} else {
			$field = '';
		}
		
		if (isset ($this->request->post['selected'])) {
			$product_id = $this->request->post['selected'];
		} else {
			$product_id = array ();
		}
		
		if (isset ($this->request->post['language_id'])) {
			$language_id = (int) $this->request->post['language_id'];
		} else {
			$language_id = (int) $this->config->get('config_language_id');
		}
		
		if (VERSION < '2.0.0.0') {
			$no_image = 'no_image.jpg';
		} else {
			$no_image = 'no_image.png';
		}
		
		if (isset ($table[$field]) && $product_id) {
			$sql = '';
			
			if ($table[$field]['table'] == 'p') {
				if (isset ($list[$field])) {
					$value = $list[$field];
					unset ($list);
					
					$sql .= "SELECT p.product_id AS product_id, " . $value['name'] . "." . $value['field'] . " AS " . $field . " FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "" . $value['table'] . " " . $value['name'] . " ON (p." . $field . " = " . $value['name'] . "." . $field . ") WHERE p.product_id IN (" . implode (', ', $product_id) . ")";
					
					if (isset ($value['lang'])) {
						$sql .= " AND " . $value['name'] . ".language_id = '" . (int) $this->config->get('config_language_id') . "'";
					}
				} else {
					$sql .= "SELECT `product_id`, `" . $field . "` FROM `" . DB_PREFIX . "product` WHERE `product_id` IN (" . implode (', ', $product_id) . ")";
				}
			} else {
				if ($table[$field]['table'] == 'ua') {
					$ua_query = array ();
					
					foreach ($product_id as $id) {
						$ua_query[] = "'product_id=" . $id . "'";
					}
					
					$sql .= "SELECT `query`, `keyword` FROM `" . DB_PREFIX . "url_alias` WHERE `query` IN (" . implode (', ', $ua_query) . ")";
				} else if ($table[$field]['table'] == 'pt') {
					$sql .= "SELECT `product_id`, `tag` FROM `" . DB_PREFIX . "product_tag` WHERE `product_id` IN (" . implode (', ', $product_id) . ") AND `language_id` = '" . $language_id . "'";
				} else {
					$sql .= "SELECT `product_id`, `" . $field . "` FROM `" . DB_PREFIX . "product_description` WHERE `product_id` IN (" . implode (', ', $product_id) . ") AND `language_id` = '" . $language_id . "'";
				}
			}
			
			$query = $this->db->query($sql);
			
			foreach ($query->rows as $value) {
				if ($field == 'url_alias') {
					$data[str_replace ('product_id=', '', $value['query'])] = $value['keyword'];
				} else {
					if ($table[$field]['type'] == 'tinyint') {
						if ($field == 'status') {
							if ($value[$field]) {
								$data[$value['product_id']] = $this->language->get('text_enabled');
							} else {
								$data[$value['product_id']] = $this->language->get('text_disabled');
							}
						} else {
							if ($value[$field]) {
								$data[$value['product_id']] = $this->language->get('text_yes');
							} else {
								$data[$value['product_id']] = $this->language->get('text_no');
							}
						}
					} else {
						if ($field == 'image') {
							if ($value['image'] && file_exists (DIR_IMAGE . $value['image'])) {
								$data[$value['product_id']]['image'] = $value['image'];
								$data[$value['product_id']]['thumb'] = $this->model_tool_image->resize($value['image'], $option['image']['width'], $option['image']['height']);
							} else {
								$data[$value['product_id']]['image'] = '';
								$data[$value['product_id']]['thumb'] = $this->model_tool_image->resize($no_image, $option['image']['width'], $option['image']['width']);
							}
						} else if ($field == 'tag' && VERSION < '1.5.4') {
							if (isset ($data[$value['product_id']])) {
								$data[$value['product_id']] .= ',' . $value[$field];
							} else {
								$data[$value['product_id']] = $value[$field];
							}
						} else {
							$data[$value['product_id']] = $value[$field];
						}
					}
				}
			}
			
			if ($field == 'url_alias' || ($field == 'tag' && VERSION < '1.5.4')) {
				foreach ($product_id as $id) {
					if (!isset ($data[$id])) {
						$data[$id] = '';
					}
				}
			}
		}
		
		echo json_encode ($data);
	}
	
	public function getProductDescription() {
		if (isset ($this->request->post['selected']) && is_array ($this->request->post['selected'])) {
			$product_id = $this->request->post['selected'];
		} else {
			$product_id = array ();
		}
		
		foreach ($product_id as $key=>$value) {
			$product_id[$key] = (int) $value;
		}
		
		if (isset ($this->request->post['language_id'])) {
			$language_id = (int) $this->request->post['language_id'];
		} else {
			$language_id = (int) $this->config->get('config_language_id');
		}
		
		$this->load->model('batch_editor/setting');
		
		$no_edit = $this->model_batch_editor_setting->get('no_edit');
		
		$json = array ();
		
		if ($product_id) {
			if (VERSION < '1.5.4') {
				$query = $this->db->query("SELECT pd.*, (SELECT GROUP_CONCAT(pt.tag) FROM " . DB_PREFIX . "product_tag pt WHERE pt.product_id = pd.product_id AND pt.language_id = '" . $language_id . "') AS tag FROM " . DB_PREFIX . "product_description pd WHERE pd.product_id IN (" . implode (',', $product_id) . ") AND pd.language_id = '" . $language_id . "'");
			} else {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id IN (" . implode (',', $product_id) . ") AND language_id = '" . $language_id . "'");
			}
			
			foreach ($query->rows as $data) {
				foreach ($data as $field=>$value) {
					if (isset ($no_edit['product_description'][$field])) {
						continue;
					}
					
					$json[$data['product_id']][$field] = $value;
				}
			}
		}
		
		echo json_encode ($json);
	}
	
	public function loadAttribute() {
		$this->load->language('batch_editor/index');
		$this->load->model('batch_editor/list');
		
		$this_data['text_none'] = $this->language->get('text_none');
		
		if (isset ($this->request->get['row'])) {
			$this_data['row'] = (int) $this->request->get['row'];
		} else {
			$this_data['row'] = 0;
		}
		
		if (isset ($this->request->get['attribute_group_id'])) {
			$this_data['attribute_group_id'] = (int) $this->request->get['attribute_group_id'];
		} else {
			$this_data['attribute_group_id'] = 0;
		}
		
		$this_data['attribute'] = $this->model_batch_editor_list->getAttributesByGroupId($this_data['attribute_group_id']);
		
		$this->setOutput('batch_editor/ajax/select_attribute.tpl', $this_data);
	}
	
	public function autocompleteAttributeValue() {
		if (isset ($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}
		
		if (isset ($this->request->get['language_id'])) {
			$language_id = (int) $this->request->get['language_id'];
		} else {
			$language_id = (int) $this->config->get('config_language_id');
		}
		
		if (isset ($this->request->get['attribute_id'])) {
			$attribute_id = (int) $this->request->get['attribute_id'];
		} else {
			$attribute_id = 0;
		}
		
		$data = array ();
		
		if ($attribute_id) {
			$query = $this->db->query("SELECT `text` FROM `" . DB_PREFIX . "product_attribute` WHERE LCASE(`text`) LIKE '%" . $this->db->escape(utf8_strtolower($filter_name)) . "%' AND `attribute_id` = '" . $attribute_id . "' AND `language_id` = '" . $language_id . "' GROUP BY `text` LIMIT 50");
			
			foreach ($query->rows as $key => $value) {
				if ($value['text']) {
					$data[] = array ('key' => $key, 'text' => html_entity_decode ($value['text'], ENT_QUOTES, 'UTF-8'));
				}
			}
		}
		
		echo json_encode ($data);
	}
	
	public function loadList() {
		if (isset ($this->request->get['id'])) {
			$this_data['id'] = (int) $this->request->get['id'];
		} else {
			$this_data['id'] = 0;
		}
		
		if (isset ($this->request->get['name'])) {
			$this_data['name'] = $this->request->get['name'];
		} else {
			$this_data['name'] = '';
		}
		
		if (isset ($this->request->get['field'])) {
			$this_data['field'] = $this->request->get['field'];
		} else {
			$this_data['field'] = '';
		}
		
		$this->load->model('batch_editor/setting');
		
		$list = $this->model_batch_editor_setting->get('list');
		$table = $this->model_batch_editor_setting->get('table');
		
		if (isset ($table[$this_data['field']])) {
			$this->load->model('batch_editor/list');
			
			if ($table[$this_data['field']]['type'] == 'tinyint') {
				$this_data['data'] = $this->model_batch_editor_list->getTinyintList($this_data['field']);
			} else if (isset ($list[$this_data['field']])) {
				$this_data['data'] = $this->model_batch_editor_list->{'get' . str_replace ('_', '', $this_data['field'])}();
			} else {
				$this_data['data'] = array ();
			}
			
			$this->setOutput('batch_editor/ajax/select_list.tpl', $this_data);
		}
	}
	
	public function getOcFilterOption() {
		if (isset ($this->request->post['category_id'])) {
			$category_id = (int) $this->request->post['category_id'];
		} else {
			$category_id = 0;
		}
		
		if (isset ($this->request->post['row'])) {
			$this_data['row'] = (int) $this->request->post['row'];
		} else {
			$this_data['row'] = 0;
		}
		
		$query = $this->db->query("SELECT ood.option_id, ood.name FROM " . DB_PREFIX . "ocfilter_option_description ood LEFT JOIN " . DB_PREFIX . "ocfilter_option_to_category oo2c ON (oo2c.option_id = ood.option_id) WHERE ood.language_id = '" . (int) $this->config->get('config_language_id') . "' AND oo2c.category_id = '" . (int) $category_id . "'");
		
		$this_data['data'] = $query->rows;
		$this_data['text_none'] = $this->language->get('text_none');
		
		$this->setOutput('batch_editor/ajax/select_ocfilter_option.tpl', $this_data);
	}
	
	public function getOcFilterValue() {
		if (isset ($this->request->post['option_id'])) {
			$this_data['option_id'] = (int) $this->request->post['option_id'];
		} else {
			$this_data['option_id'] = 0;
		}
		
		$query = $this->db->query("SELECT type AS type FROM " . DB_PREFIX . "ocfilter_option WHERE option_id = '" . $this_data['option_id'] . "'");
		
		if (isset ($query->row['type'])) {
			$this_data['type'] = $query->row['type'];
		} else {
			$this_data['type'] = '';
		}
		
		if ($this_data['type'] == 'checkbox' || $this_data['type'] == 'radio' || $this_data['type'] == 'select') {
			$query = $this->db->query("SELECT value_id, name FROM " . DB_PREFIX . "ocfilter_option_value_description WHERE option_id = '" . $this_data['option_id'] . "' AND language_id = '" . (int) $this->config->get('config_language_id') . "'");
		
			$this_data['data'] = $query->rows;
		}
		
		$this->load->model('localisation/language');
		
		$this_data['languages'] = $this->model_localisation_language->getLanguages();
		
		$this->setOutput('batch_editor/ajax/select_ocfilter_value.tpl', $this_data);
	}
	
	public function autocompleteOcFilter() {
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}
		
		$query = $this->db->query("SELECT `option_id`, `name` FROM `" . DB_PREFIX . "ocfilter_option_description` WHERE LCASE(`name`) LIKE '%" . $this->db->escape(utf8_strtolower($filter_name)) . "%' AND `language_id` = '" . (int) $this->config->get('config_language_id') . "' LIMIT 50");
		
		echo json_encode ($query->rows);
	}
	
	public function getImageManager() {
		if (isset ($this->request->get['keyword']) && isset ($this->request->get['directory'])) {
			$results = $images = array ();
			
			$directory = $this->request->get['directory'];
			$keyword = trim ($this->request->get['keyword']);
			
			if (is_dir (DIR_IMAGE . $directory) && $keyword) {
				$results = glob (DIR_IMAGE . $directory . $keyword . '*.{JPG,jpg,JPEG,jpeg,PNG,png,GIF,gif}', GLOB_BRACE);
			}
			
			if ($results) {
				$this->load->model('tool/image');
				$this->load->model('batch_editor/setting');
				
				$option = $this->model_batch_editor_setting->get('option');
				
				$i = 0;
				
				foreach ($results as $result) {
					if (!is_dir ($result)) {
						$file = str_replace (DIR_IMAGE . $directory, '', $result);
						
						$images[] = array (
							'file' => $file,
							'img' => $this->model_tool_image->resize($directory . $file, $option['image']['width'], $option['image']['height'])
						);
					}
					
					$i++;
					
					if ($i == 10) {
						break;
					}
				}
			}
			
			echo json_encode ($images);
		} else {
			$this->load->language('batch_editor/index');
			
			$this_data['text_fix'] = $this->language->get('text_fix');
			$this_data['button_remove'] = $this->language->get('button_remove');
			
			$this_data['directories'] = $this->cache->get('batch_editor.image_directories');
			
			if (!$this_data['directories']) {
				$this->load->model('batch_editor/function');
				
				$this_data['directories'] = $this->model_batch_editor_function->getImageDirectories();
				
				$this->cache->set('batch_editor.image_directories', $this_data['directories']);
			}
			
			$this->setOutput('batch_editor/ajax/image_manager.tpl', $this_data);
		}
	}
	
	public function getOptionValues() {
		if (isset ($this->request->post['option_id'])) {
			$option_id = (int) $this->request->post['option_id'];
		} else {
			$option_id = 0;
		}
		
		$data = array ();
		
		if ($option_id) {
			$this->load->model('batch_editor/list');
			
			$data = $this->model_batch_editor_list->getOptionValues($option_id);
		}
		
		echo json_encode ($data);
	}
	
	public function getLinkToColumn() {
		$this->load->model('batch_editor/data');
		
		if (isset ($this->request->post['selected']) && is_array ($this->request->post['selected'])) {
			$selected = $this->request->post['selected'];
		} else {
			$selected = array ();
		}
		
		if (isset ($this->request->post['link'])) {
			$link = $this->request->post['link'];
		} else {
			$link = false;
		}
		
		$data = array ();
		
		if ($link == 'product_to_category') {
			$result = $this->model_batch_editor_data->getProductCategory($selected);
			
			foreach ($selected as $product_id) {
				$data[$product_id] = '';
				
				if (isset ($result[$product_id])) {
					$html = '';
					
					foreach ($result[$product_id] as $array) {
						if (isset ($array['main_category']) && $array['main_category']) {
							$html .= '<tr><td nowrap="nowrap"><b>' . $array['name'] . '</b></td></tr>';
						} else {
							$html .= '<tr><td nowrap="nowrap">' . $array['name'] . '</td></tr>';
						}
					}
					
					$data[$product_id] = '<table>' . $html . '</table>';
				}
			}
		}
		
		if ($link == 'product_option') {
			$result = $this->model_batch_editor_data->getProductOption($selected);
			
			$text_no = $this->language->get('text_no');
			$text_yes = $this->language->get('text_yes');
			
			foreach ($selected as $product_id) {
				$data[$product_id] = '';
				
				if (isset ($result['product_options'][$product_id])) {
					$html = '';
					
					foreach ($result['product_options'][$product_id] as $array) {
						$html .= '<tr><td nowrap="nowrap"><b>' . $array['name'] . '</b></td><td>&nbsp;&nbsp;' . ($array['required'] ? $text_yes : $text_no) . '</td><td nowrap="nowrap" colspan="4">&nbsp;&nbsp;' . ((VERSION < '2.0.0.0') ? $array['option_value'] : $array['value']) . '</td></tr>';
						
						if (isset ($array['product_option_value'])) {
							foreach ($array['product_option_value'] as $array_1) {
								$option_value_name = '';
								
								foreach ($result['option_values'][$array_1['option_id']] as $array_2) {
									if ($array_1['option_value_id'] == $array_2['option_value_id']) {
										$option_value_name = $array_2['name'];
									}
								}
								
								$html .= '<tr><td nowrap="nowrap">&nbsp;&nbsp;' . $option_value_name . '</td><td>&nbsp;&nbsp;' . $array_1['quantity'] . '</td><td>&nbsp;&nbsp;' . ($array_1['subtract'] ? $text_yes : $text_no) . '</td><td>&nbsp;&nbsp;' . $array_1['price_prefix'] . $array_1['price'];
								
								if (isset ($array_1['base_price'])) {
									$html .= '&nbsp;/&nbsp;' . $array_1['base_price'];
								}
								
								$html .= '</td><td>&nbsp;&nbsp;'. $array_1['points_prefix'] . $array_1['points'] . '</td><td>&nbsp;&nbsp;' . $array_1['weight_prefix'] . $array_1['weight'] . '</td></tr>';
							}
						}
					}
					
					$data[$product_id] = '<table>' . $html . '</table>';
				}
			}
		}
		
		if ($link == 'product_attribute') {
			$result = $this->model_batch_editor_data->getProductAttribute($selected);
			
			$language_id = (int) $this->config->get('config_language_id');
			
			foreach ($selected as $product_id) {
				$data[$product_id] = '';
				
				if (isset ($result[$product_id])) {
					$html = '';
					
					foreach ($result[$product_id] as $array) {
						$text = '';
						
						if (isset ($array['attribute_description'][$language_id]['text'])) {
							$text = $array['attribute_description'][$language_id]['text'];
						}
						
						$html .= '<tr><td nowrap="nowrap">' . $array['name'] . '</td><td nowrap="nowrap">&nbsp;:&nbsp;' . $text . '</td></tr>';
					}
					
					$data[$product_id] = '<table>' . $html . '</table>';
				}
			}
		}
		
		echo json_encode ($data);
	}
	
	public function autocomplete() {
		$json = array ();
		
		if (isset ($this->request->get['autocomplete'])) {
			$autocomplete = $this->request->get['autocomplete'];
		} else {
			$autocomplete = false;
		}
		
		if (isset ($this->request->get['keyword'])) {
			$keyword = trim ($this->request->get['keyword']);
		} else {
			$keyword = false;
		}
		
		if ($keyword) {
			$this->load->model('batch_editor/list');
			
			$data = array ('keyword' => $keyword);
			
			if ($autocomplete == 'category_id') {
				$json = $this->model_batch_editor_list->getCategoryName($data);
			}
			
			if ($autocomplete == 'product_id') {
				$json = $this->model_batch_editor_list->getProductName($data);
			}
			
			if ($autocomplete == 'coupon_id') {
				$json = $this->model_batch_editor_list->getCouponName($data);
			}
			
			if ($autocomplete == 'sizechart_id') {
				$json = $this->model_batch_editor_list->getSizeChartName($data);
			}
		}
		
		echo json_encode ($json);
	}
	
	public function autocompleteByTableField() {
		$data = array ();
		
		if (isset ($this->request->get['table'])) {
			$table = $this->request->get['table'];
		} else {
			$table = '';
		}
		
		if (isset ($this->request->get['field'])) {
			$field = $this->request->get['field'];
		} else {
			$field = '';
		}
		
		if (isset ($this->request->get['keyword'])) {
			$keyword = $this->request->get['keyword'];
		} else {
			$keyword = '';
		}
		
		if (isset ($this->request->get['language_id'])) {
			$language_id = (int) $this->request->get['language_id'];
		} else {
			$language_id = (int) $this->config->get('config_language_id');
		}
		
		if ($table & $field & $keyword) {
			$sql = '';
			
			if ($field == 'url_alias') {
				$sql = "SELECT `keyword` AS `value` FROM `" . DB_PREFIX . "url_alias` WHERE LCASE(`keyword`) LIKE '%" . $this->db->escape(utf8_strtolower($keyword)) . "%' AND `query` LIKE 'product_id=%' GROUP BY `keyword` LIMIT 50";
			} else if ($field == 'tag' && VERSION < '1.5.4') {
				$sql = "SELECT `tag` AS `value` FROM `" . DB_PREFIX . "product_tag` WHERE LCASE(`tag`) LIKE '%" . $this->db->escape(utf8_strtolower($keyword)) . "%' AND `language_id` = '" . $language_id . "' GROUP BY `tag` LIMIT 50";
			} else {
				$this->load->model('batch_editor/setting');
				
				$setting = $this->model_batch_editor_setting->getTableField($table);
				
				if (isset ($setting[$field])) {
					$sql = "SELECT `" . $field . "` AS `value` FROM `" . DB_PREFIX . $table . "` WHERE LCASE(`" . $field . "`) LIKE '%" . $this->db->escape(utf8_strtolower($keyword)) . "%' ";
					
					if (isset ($setting['language_id'])) {
						$sql .= "AND `language_id` = '" . $language_id . "'";
					}
					
					$sql .= "GROUP BY `" . $field . "` LIMIT 50";
				}
			}
			
			if ($sql) {
				$query = $this->db->query($sql);
				
				foreach ($query->rows as $key => $array) {
					$data[] = array ('key' => $key, 'value' => html_entity_decode ($array['value'], ENT_QUOTES, 'UTF-8'));
				}
			}
		}
		
		echo json_encode ($data);
	}
	
	public function getAttributeValue() {
		if (isset ($this->request->post['attribute_id'])) {
			$attribute_id = (int) $this->request->post['attribute_id'];
		} else {
			$attribute_id = 0;
		}
		
		if (isset ($this->request->post['language_id'])) {
			$language_id = (int) $this->request->post['language_id'];
		} else {
			$language_id = 0;
		}
		
		if ($attribute_id && $language_id) {
			$query = $this->db->query("SELECT DISTINCT(`text`) FROM `" . DB_PREFIX . "product_attribute` WHERE `attribute_id` = '" . $attribute_id . "' AND `language_id` = '" . $language_id . "' ORDER BY `text` ASC");
			
			$this->json = $query->rows;
		}
		
		echo json_encode ($this->json);
	}
	
	private function setOutput($template, $data, $children = false) {
		if (VERSION < '2.0.0.0') {
			$this->data = $data;
			$this->template = $template;
			
			if ($children) {
				$this->children = array ('common/header', 'common/footer');
			}
			
			$this->response->setOutput($this->render());
		} else {
			if ($children) {
				$data['header'] = $this->load->controller('common/header');
				$data['column_left'] = $this->load->controller('common/column_left');
				$data['footer'] = $this->load->controller('common/footer');
			}
			
			$this->response->setOutput($this->load->view($template, $data));
		}
	}
}
?>
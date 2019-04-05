<?php
class ModelModuleQCQuote extends Model {
	private $tables = array(
		"download" => array("download_id", "filename", "mask", "date_added", "constraint", "total_downloads", "duration",
							"is_free", "file_size", "login", "status", "downloaded", "sort_order", "date_modified"),
		"download_to_customer_group" => array("download_id", "customer_group_id"),
		"download_tag" => array("download_tag_id", "administrative", "sort_order"),
		"download_tag_description" => array("download_tag_id", "language_id", "name"),
		"download_to_tag" => array("download_id", "download_tag_id"),
		"download_stats" => array("download_date", "download_id", "customer_id", "download_count"),
		"download_sample" => array("download_sample_id", "hash", "download_id", "constraint", "remaining", "end_time",
								   "store_id", "customer_id", "language_id", "name", "email", "last_accessed",
								   "date_added", "date_modified"),
		"order_product_download" => array("order_product_download_id", "order_product_id", "download_id", "constraint", "remaining", "duration", "end_time", "last_accessed"),
		"order_option_download" => array("order_option_download_id", "order_option_id", "download_id", "constraint", "remaining", "duration", "end_time", "last_accessed"),
		"option_value" => array("download_id"),
	);

	public function upgradeDatabaseStructure($from_version, &$alert) {
		$errors = false;

		switch ($from_version) {
			case '3.7.5':
				$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'is_free'");
				if (!$column_exists->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` ADD COLUMN is_free TINYINT(1) NOT NULL DEFAULT '0'");
				}
				$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'file_size'");
				if (!$column_exists->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` ADD COLUMN file_size INT(11) NOT NULL DEFAULT '-1'");
				}
				$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'login'");
				if (!$column_exists->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` ADD COLUMN login TINYINT(1) NOT NULL DEFAULT '0'");
				}
				$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'status'");
				if (!$column_exists->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` ADD COLUMN status TINYINT(1) NOT NULL DEFAULT '1'");
				}
				$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'downloaded'");
				if (!$column_exists->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` ADD COLUMN downloaded INT(6) NOT NULL DEFAULT '0'");
				}

				$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "download_to_customer_group (
					download_id INT(11) NOT NULL,
					customer_group_id INT(11) NOT NULL,
					PRIMARY KEY (download_id, customer_group_id),
					INDEX fk_d2cg_cg (customer_group_id)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
				);

				$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "download_tag (
					download_tag_id INT(11) NOT NULL AUTO_INCREMENT,
					administrative TINYINT(1) NOT NULL DEFAULT '0',
					sort_order INT(3) NOT NULL,
					PRIMARY KEY (download_tag_id)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
				);

				$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download_tag` LIKE 'administrative'");
				if (!$column_exists->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "download_tag` ADD COLUMN administrative TINYINT(1) NOT NULL DEFAULT '0'");
				}

				$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "download_tag_description (
					download_tag_id INT(11) NOT NULL,
					language_id INT(11) NOT NULL,
					name VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL,
					PRIMARY KEY (download_tag_id, language_id),
					INDEX fk_dtd_lang (language_id)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
				);

				$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "download_to_tag (
					download_id INT(11) NOT NULL,
					download_tag_id INT(11) NOT NULL,
					PRIMARY KEY (download_id, download_tag_id),
					INDEX fk_d2t_dl_tag (download_tag_id)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
				);
			case '3.7.6':
			case '3.7.7':
				$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'sort_order'");
				if (!$column_exists->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "download`
						ADD COLUMN sort_order INT(11) NOT NULL DEFAULT '0',
						ADD COLUMN date_modified DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'"
					);
				}

				$this->db->query("ALTER TABLE `" . DB_PREFIX . "download`
					CHANGE is_free is_free TINYINT(1) NOT NULL DEFAULT '0'"
				);

				$index_exists = $this->db->query("SHOW INDEX FROM `" . DB_PREFIX . "download_to_customer_group` WHERE Key_name = 'fk_d2cg_cg'");
				if (!$index_exists->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "download_to_customer_group`
						ADD INDEX fk_d2cg_cg (customer_group_id)"
					);
				}

				$index_exists = $this->db->query("SHOW INDEX FROM `" . DB_PREFIX . "download_tag_description` WHERE Key_name = 'fk_dtd_lang'");
				if (!$index_exists->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "download_tag_description`
						ADD INDEX fk_dtd_lang (language_id)"
					);
				}

				$index_exists = $this->db->query("SHOW INDEX FROM `" . DB_PREFIX . "download_to_tag` WHERE Key_name = 'fk_d2t_dl_tag'");
				if (!$index_exists->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "download_to_tag`
						ADD INDEX fk_d2t_dl_tag (download_tag_id)"
					);
				}

				$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download_tag` LIKE 'id'");
				if ($column_exists->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "download_tag`
						CHANGE `id` `download_tag_id` INT(11) NOT NULL AUTO_INCREMENT"
					);
				}

				$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "download_sample (
					download_sample_id INT(11) NOT NULL AUTO_INCREMENT,
					hash VARCHAR(64) NOT NULL,
					download_id INT(11) NOT NULL,
					`constraint` TINYINT(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 0,
					remaining INT(3) NOT NULL DEFAULT 0,
					end_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
					store_id INT(11) NOT NULL DEFAULT 0,
					customer_id INT(11),
					language_id INT(11) NOT NULL,
					name VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL,
					email VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL,
					last_accessed datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					date_added datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					date_modified datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					PRIMARY KEY (download_sample_id),
					UNIQUE INDEX uq_ds_hash (hash),
					INDEX fk_ds_d (download_id),
					INDEX fk_ds_l (language_id),
					INDEX fk_ds_c (customer_id)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
				);

				$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "download_stats (
					download_date DATE NOT NULL DEFAULT '0000-00-00',
					download_id INT(11) NOT NULL,
					download_count INT(11) NOT NULL DEFAULT 1,
					PRIMARY KEY (download_date, download_id),
					INDEX fk_ds_download (download_id)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
				);

				// Convert collation to utf8_unicode_ci
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "download_to_customer_group` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci");
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "download_tag` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci");
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "download_tag_description` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci");
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "download_to_tag` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci");
			case '4.0.0':
			case '4.0.1':
			case '4.0.2':
				$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'constraint'");
				if (!$column_exists->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` ADD COLUMN `constraint` TINYINT(1) NOT NULL DEFAULT '0'");
				}
				$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'total_downloads'");
				if (!$column_exists->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` ADD COLUMN `total_downloads` INT(4) NOT NULL DEFAULT '-1'");
				}
				$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'duration'");
				if (!$column_exists->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` ADD COLUMN `duration` INT(11) NOT NULL DEFAULT '0'");
				}

				$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "option_value` LIKE 'download_id'");
				if (!$column_exists->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "option_value` ADD COLUMN `download_id` INT(11)");
				}

				$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download_stats` LIKE 'customer_id'");
				if ($column_exists->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "download_stats` ADD COLUMN `customer_id` INT(11) NOT NULL DEFAULT '0' AFTER `download_id`");
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "download_stats` DROP PRIMARY KEY, ADD PRIMARY KEY(download_date, download_id, customer_id)");
				}

				$this->db->query("ALTER TABLE `" . DB_PREFIX . "download_description` CHANGE `name` `name` VARCHAR(128) NOT NULL");

				$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "order_product_download (
					order_product_download_id INT(11) NOT NULL AUTO_INCREMENT,
					order_product_id INT(11) NOT NULL,
					download_id INT(11) NOT NULL,
					`constraint` TINYINT(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 0,
					remaining INT(3) NOT NULL DEFAULT 0,
					duration INT(11) NOT NULL DEFAULT 0,
					end_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
					last_accessed datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					PRIMARY KEY (order_product_download_id),
					UNIQUE KEY uq_opd_opi_di (order_product_id, download_id),
					INDEX fk_opd_op (order_product_id),
					INDEX fk_opd_d (download_id)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
				);

				$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "order_option_download (
					order_option_download_id INT(11) NOT NULL AUTO_INCREMENT,
					order_option_id INT(11) NOT NULL,
					download_id INT(11) NOT NULL,
					`constraint` TINYINT(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 0,
					remaining INT(3) NOT NULL DEFAULT 0,
					duration INT(11) NOT NULL DEFAULT 0,
					end_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
					last_accessed datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					PRIMARY KEY (order_option_download_id),
					UNIQUE KEY uq_ood_ooi_di (order_option_id, download_id),
					INDEX fk_ood_op (order_option_id),
					INDEX fk_ood_d (download_id)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
				);

				// Add current downloads to order_product_download table
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_product_download (order_product_id, download_id, `constraint`, remaining, duration, end_time) SELECT op.order_product_id, p2d.download_id, d.constraint, IF(d.total_downloads < 0, d.total_downloads, d.total_downloads * op.quantity), d.duration * op.quantity, UTC_TIMESTAMP + INTERVAL d.duration * op.quantity SECOND FROM " . DB_PREFIX . "order_product op JOIN " . DB_PREFIX . "product_to_download p2d ON (op.product_id = p2d.product_id) JOIN " . DB_PREFIX . "download d ON (p2d.download_id = d.download_id) WHERE NOT EXISTS (SELECT * FROM " . DB_PREFIX . "order_product_download opd WHERE d.download_id = opd.download_id AND op.order_product_id = opd.order_product_id)");
			default:
				break;
		}

		return !$errors;
	}

	public function applyDatabaseChanges() {
		$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'constraint'");
		if (!$column_exists->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` ADD COLUMN `constraint` TINYINT(1) NOT NULL DEFAULT '0'");
		}
		$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'total_downloads'");
		if (!$column_exists->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` ADD COLUMN `total_downloads` INT(4) NOT NULL DEFAULT '-1'");
		}
		$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'duration'");
		if (!$column_exists->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` ADD COLUMN `duration` INT(11) NOT NULL DEFAULT '0'");
		}
		$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'is_free'");
		if (!$column_exists->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` ADD COLUMN `is_free` TINYINT(1) NOT NULL DEFAULT '0'");
		}
		$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'file_size'");
		if (!$column_exists->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` ADD COLUMN `file_size` INT(11) NOT NULL DEFAULT '-1'");
		}
		$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'login'");
		if (!$column_exists->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` ADD COLUMN `login` TINYINT(1) NOT NULL DEFAULT '0'");
		}
		$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'status'");
		if (!$column_exists->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` ADD COLUMN `status` TINYINT(1) NOT NULL DEFAULT '1'");
		}
		$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'downloaded'");
		if (!$column_exists->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` ADD COLUMN `downloaded` INT(6) NOT NULL DEFAULT '0'");
		}
		$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'sort_order'");
		if (!$column_exists->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` ADD COLUMN `sort_order` INT(11) NOT NULL DEFAULT '0'");
		}
		$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'date_modified'");
		if (!$column_exists->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` ADD COLUMN `date_modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'");
		}

		$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "option_value` LIKE 'download_id'");
		if (!$column_exists->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "option_value` ADD COLUMN `download_id` INT(11)");
		}

		$this->db->query("ALTER TABLE `" . DB_PREFIX . "download_description` CHANGE `name` `name` VARCHAR(128) NOT NULL");

		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "download_to_customer_group (
			download_id INT(11) NOT NULL,
			customer_group_id INT(11) NOT NULL,
			PRIMARY KEY (download_id, customer_group_id),
			INDEX fk_d2cg_cg (customer_group_id)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "download_tag (
			download_tag_id INT(11) NOT NULL AUTO_INCREMENT,
			administrative TINYINT(1) NOT NULL DEFAULT '0',
			sort_order INT(3) NOT NULL,
			PRIMARY KEY (download_tag_id)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "download_tag_description (
			download_tag_id INT(11) NOT NULL,
			language_id INT(11) NOT NULL,
			name VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL,
			PRIMARY KEY (download_tag_id, language_id),
			INDEX fk_dtd_lang (language_id)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "download_to_tag (
			download_id INT(11) NOT NULL,
			download_tag_id INT(11) NOT NULL,
			PRIMARY KEY (download_id, download_tag_id),
			INDEX fk_d2t_dl_tag (download_tag_id)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "download_stats (
			download_date DATE NOT NULL DEFAULT '0000-00-00',
			download_id INT(11) NOT NULL,
			customer_id INT(11) NOT NULL DEFAULT 0,
			download_count INT(11) NOT NULL DEFAULT 1,
			PRIMARY KEY (download_date, download_id, customer_id),
			INDEX fk_ds_download (download_id)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "download_sample (
			download_sample_id INT(11) NOT NULL AUTO_INCREMENT,
			hash VARCHAR(64) NOT NULL,
			download_id INT(11) NOT NULL,
			`constraint` TINYINT(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 0,
			remaining INT(3) NOT NULL DEFAULT 0,
			end_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
			store_id INT(11) NOT NULL DEFAULT 0,
			customer_id INT(11),
			language_id INT(11) NOT NULL,
			name VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL,
			email VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL,
			last_accessed datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			date_added datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			date_modified datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY (download_sample_id),
			UNIQUE INDEX uq_ds_hash (hash),
			INDEX fk_ds_d (download_id),
			INDEX fk_ds_l (language_id),
			INDEX fk_ds_c (customer_id)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "order_product_download (
			order_product_download_id INT(11) NOT NULL AUTO_INCREMENT,
			order_product_id INT(11) NOT NULL,
			download_id INT(11) NOT NULL,
			`constraint` TINYINT(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 0,
			remaining INT(3) NOT NULL DEFAULT 0,
			duration INT(11) NOT NULL DEFAULT 0,
			end_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
			last_accessed datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY (order_product_download_id),
			UNIQUE KEY uq_opd_opi_di (order_product_id, download_id),
			INDEX fk_opd_op (order_product_id),
			INDEX fk_opd_d (download_id)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "order_option_download (
			order_option_download_id INT(11) NOT NULL AUTO_INCREMENT,
			order_option_id INT(11) NOT NULL,
			download_id INT(11) NOT NULL,
			`constraint` TINYINT(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 0,
			remaining INT(3) NOT NULL DEFAULT 0,
			duration INT(11) NOT NULL DEFAULT 0,
			end_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
			last_accessed datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY (order_option_download_id),
			UNIQUE KEY uq_ood_ooi_di (order_option_id, download_id),
			INDEX fk_ood_op (order_option_id),
			INDEX fk_ood_d (download_id)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
		);

		// Set the file sizes for current downloads
		$this->load->model('catalog/download');
		$downloads = $this->model_catalog_download->getDownloads();
		foreach ($downloads as $download) {
			if (file_exists(DIR_DOWNLOAD . $download['filename'])) {
				$size = filesize(DIR_DOWNLOAD . $download['filename']);
				$this->db->query("UPDATE " . DB_PREFIX . "download SET file_size = '" . (int)$size . "' WHERE download_id = '" . (int)$download['download_id'] . "'");
			}
		}

		// Set the default customer group to 0
		$this->db->query("INSERT INTO " . DB_PREFIX . "download_to_customer_group (download_id, customer_group_id) SELECT d.download_id, '0' FROM " . DB_PREFIX . "download d WHERE NOT EXISTS (SELECT * FROM " . DB_PREFIX . "download_to_customer_group d2cg WHERE d.download_id = d2cg.download_id)");

		// Add current downloads to order_product_download table
		$this->db->query("INSERT INTO " . DB_PREFIX . "order_product_download (order_product_id, download_id, `constraint`, remaining, duration, end_time) SELECT op.order_product_id, p2d.download_id, d.constraint, IF(d.total_downloads < 0, d.total_downloads, d.total_downloads * op.quantity), d.duration * op.quantity, UTC_TIMESTAMP + INTERVAL d.duration * op.quantity SECOND FROM " . DB_PREFIX . "order_product op JOIN " . DB_PREFIX . "product_to_download p2d ON (op.product_id = p2d.product_id) JOIN " . DB_PREFIX . "download d ON (p2d.download_id = d.download_id) WHERE NOT EXISTS (SELECT * FROM " . DB_PREFIX . "order_product_download opd WHERE d.download_id = opd.download_id AND op.order_product_id = opd.order_product_id)");
	}

	public function revertDatabaseChanges() {
		$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'constraint'");
		if ($column_exists->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` DROP COLUMN `constraint`");
		}
		$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'total_downloads'");
		if ($column_exists->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` DROP COLUMN `total_downloads`");
		}
		$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'duration'");
		if ($column_exists->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` DROP COLUMN `duration`");
		}
		$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'is_free'");
		if ($column_exists->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` DROP COLUMN `is_free`");
		}
		$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'file_size'");
		if ($column_exists->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` DROP COLUMN `file_size`");
		}
		$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'status'");
		if ($column_exists->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` DROP COLUMN `status`");
		}
		$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'login'");
		if ($column_exists->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` DROP COLUMN `login`");
		}
		$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'downloaded'");
		if ($column_exists->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` DROP COLUMN `downloaded`");
		}
		$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'sort_order'");
		if ($column_exists->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` DROP COLUMN `sort_order`");
		}
		$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "download` LIKE 'date_modified'");
		if ($column_exists->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "download` DROP COLUMN `date_modified`");
		}

		$column_exists = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "option_value` LIKE 'download_id'");
		if ($column_exists->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "option_value` DROP COLUMN `download_id`");
		}

		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "download_to_customer_group`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "download_tag`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "download_tag_description`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "download_to_tag`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "download_stats`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "download_sample`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "order_product_download`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "order_option_download`");
	}

	public function checkDatabaseStructure(&$alert) {
		$errors = false;

		foreach ($this->tables as $tbl => $fields) {
			$table_exists = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "{$tbl}'");
			if (!$table_exists->num_rows) {
				$errors = true;
				$alert['error']["missing_table_{$tbl}"] = sprintf($this->language->get('error_missing_table'), DB_PREFIX . $tbl);
			} else { // Check for table fields
				foreach($fields as $field) {
					$column_exists = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "{$tbl} LIKE '{$field}'");
					if (!$column_exists->num_rows) {
						$errors = true;
						$alert['error']["missing_column_{$field}"] = sprintf($this->language->get('error_missing_column'), DB_PREFIX . $tbl, $field);
					}
				}
			}
		}

		return !$errors;
	}

	public function checkDatabaseConsistency(&$alert) {
		$errors = false;

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "download d WHERE NOT EXISTS (SELECT * FROM " . DB_PREFIX . "download_to_customer_group d2cg WHERE d.download_id = d2cg.download_id)");

		if ($query->num_rows != 0) {
			$errors = true;
			$alert['warning']['db_error'] = $this->language->get('error_db_inconsistency');
		}

		return !$errors;
	}

	public function fixDatabaseConsistency(&$alert) {
		$errors = false;

		$this->db->query("INSERT INTO " . DB_PREFIX . "download_to_customer_group (download_id, customer_group_id) SELECT d.download_id, '0' FROM " . DB_PREFIX . "download d WHERE NOT EXISTS (SELECT * FROM " . DB_PREFIX . "download_to_customer_group d2cg WHERE d.download_id = d2cg.download_id)");

		$temp = array();
		if (!$this->checkDatabaseConsistency($temp)) {
			$errors = true;
			$alert['error']['fix_db'] = $this->language->get('error_fix_db_inconsistency');
		}

		return !$errors;
	}
}

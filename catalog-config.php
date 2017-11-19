<?php
// HTTP
define('HTTP_SERVER', 'http://$siteurl');

// HTTPS
define('HTTPS_SERVER', 'http://$siteurl');

// DIR
define('DIR_APPLICATION', '$installpath/upload/catalog/');
define('DIR_SYSTEM', '$installpath/upload/system/');
define('DIR_LANGUAGE', '$installpath/upload/catalog/language/');
define('DIR_TEMPLATE', '$installpath/upload/catalog/view/theme/');
define('DIR_CONFIG', '$installpath/upload/system/config/');
define('DIR_IMAGE', '$installpath/upload/image/');
define('DIR_CACHE', '$installpath/upload/system/cache/');
define('DIR_DOWNLOAD', '$installpath/upload/system/download/');
define('DIR_UPLOAD', '$installpath/upload/system/upload/');
define('DIR_MODIFICATION', '$installpath/upload/system/modification/');
define('DIR_LOGS', '$installpath/upload/system/logs/');
define('DIR_QC', '$installpath/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', '$username');
define('DB_PASSWORD', '$userpass');
define('DB_DATABASE', '$dbname');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc2_');
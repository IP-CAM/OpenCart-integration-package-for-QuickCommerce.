<?php

define ('URL_LOGIN', 'URL_LOGIN');
define ('URL_VERSION', 'URL_VERSION');
define ('URL_INSTALL', 'URL_INSTALL');
define ('URL_USER', 'URL_USER');
define ('URL_REFERENCE_DATA', 'URL_REFERENCE_DATA');
define ('URL_SETTINGS', 'URL_SETTINGS');
define ('URL_CATEGORIES', 'URL_CATEGORIES');
define ('URL_CATEGORY', 'URL_CATEGORY');
define ('URL_PRODUCTS', 'URL_PRODUCTS');
define ('URL_PRODUCT', 'URL_PRODUCT');
define ('URL_PRODUCT_OPTIONS', 'URL_PRODUCT_OPTIONS');
define ('URL_ORDERS', 'URL_ORDERS');
define ('URL_ORDER', 'URL_ORDER');
define ('URL_CUSTOMERS', 'URL_CUSTOMERS');
define ('URL_CUSTOMER', 'URL_CUSTOMER');
define ('URL_ORDER_PAYMENTS', 'URL_ORDER_PAYMENTS');
define ('URL_ORDER_PAYMENT', 'URL_ORDER_PAYMENT');
define ('URL_ENDOFDAY_REPORT', 'URL_ENDOFDAY_REPORT');
define ('URL_ZONES', 'URL_ZONES');

$_pos_api = array();

$_pos_api[URL_LOGIN] = '/login';
$_pos_api[URL_VERSION] = '/version';
$_pos_api[URL_INSTALL] = '/install';
$_pos_api[URL_USER] = '/user';
$_pos_api[URL_REFERENCE_DATA] = '/referenceData';
$_pos_api[URL_SETTINGS] = '/settings';
$_pos_api[URL_CATEGORIES] = '/category';
$_pos_api[URL_CATEGORY] = '/category/{categoryId}';
$_pos_api[URL_PRODUCTS] = '/product';
$_pos_api[URL_PRODUCT] = '/product/{productId}';
$_pos_api[URL_PRODUCT_OPTIONS] = '/product/{productId}/productOption';
$_pos_api[URL_ORDERS] = '/order';
$_pos_api[URL_ORDER] = '/order/{orderId}';
$_pos_api[URL_CUSTOMERS] = '/customer';
$_pos_api[URL_CUSTOMER] = '/customer/{customerId}';
$_pos_api[URL_ORDER_PAYMENTS] = '/orderPayment';
$_pos_api[URL_ORDER_PAYMENT] = '/orderPayment/{orderPaymentId}';
$_POS_API[URL_ENDOFDAY_REPORT] = '/report/endofday';
$_pos_api[URL_ZONES] = '/zone';
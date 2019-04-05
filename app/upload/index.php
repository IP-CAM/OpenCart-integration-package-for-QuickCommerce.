<?php
//ini_set('error_reporting','E_ALL & ~E_WARNING & ~E_DEPRECATED & ~E_NOTICE');
//ini_set('display_errors', 'Off');
// Version
define('VERSION', '2.3.0.2');

// Configuration
if (is_file('config.php')) {
	require_once('config.php');
}

// Install
if (!defined('DIR_APPLICATION')) {
	header('Location: install/index.php');
	exit;
}

// Startup
require_once(DIR_SYSTEM . 'startup.php');

start('catalog');
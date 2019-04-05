<?php

/**
 * Product Downloads PRO file uploader
 */

$key = "default";

// Start a session if needed
if (!session_id()) {
	ini_set('session.cookie_lifetime', '0');
	ini_set('session.use_cookies', 'On');
	ini_set('session.use_only_cookies', 'Off');
	ini_set('session.cookie_httponly', 'On');
	ini_set('session.use_strict_mode', 'On');
	ini_set('session.use_trans_sid', 'Off');

	if (isset($_COOKIE[session_name()]) && !preg_match('/^[a-zA-Z0-9,\-]{22,40}$/', $_COOKIE[session_name()])) {
		die("Aborting: invalid session name!");
	}

	session_set_cookie_params(0, '/');
	session_start();
}

if (isset($_SESSION[$key])) {
	$session = $_SESSION[$key];
} else {
	$session = $_SESSION;
}

if (!isset($session['dir_application'])) {
	die("Aborting: missing application dir!");
}

if (!isset($session['oc_version'])) {
	die("Aborting: missing OpenCart version!");
}

// Check for a valid session
if (!isset($session['user_id']) || !isset($_GET['token']) || !isset($session['token']) || ($_GET['token'] != $session['token'])) {
	die("Aborting: invalid session!");
}

$token = $session['token'];
$dir_application = $session['dir_application'];

if(file_exists($dir_application . 'config.php')) {
	require_once($dir_application . 'config.php');
} else {
	die("Aborting: config.php not found!");
}

if (extension_loaded('mbstring')) {
	mb_internal_encoding('UTF-8');

	function utf8_strlen($string) {
		return mb_strlen($string);
	}
} elseif (function_exists('iconv')) {
	function utf8_strlen($string) {
		return iconv_strlen($string, 'UTF-8');
	}
}

require(DIR_SYSTEM . 'library/UploadHandler.php');

// Determine and load language
$language = isset($session['dir_language']) ? $session['dir_language'] : 'english';
$language_file = DIR_LANGUAGE . $language . '/catalog/download_ext.php';

$lang = array();

if (file_exists($language_file)) {
	$_ = array();

	require($language_file);

	$lang = array_merge($lang, $_);
} else {
	$language_file = DIR_LANGUAGE . 'english/catalog/download_ext.php';

	if (file_exists($language_file)) {
		$_ = array();

		require($language_file);

		$lang = array_merge($lang, $_);
	} else {
		die('Error: Could not load language file!');
	}
}

function get_translation($key) {
    return isset($lang[$key]) ? $lang[$key] : $key;
}

$options = array(
	'script_url' => HTTPS_CATALOG . 'system/library/upload.php?token=' . $token,
	'upload_dir' => DIR_DOWNLOAD,
	'upload_url' => HTTPS_CATALOG . (version_compare($session['oc_version'], '2.1.0') < 0) ? 'system/download/' : 'system/storage/download/',
	'discard_aborted_uploads' => false,
	'detect_mime_type' => true,
);

$error_messages = array(
	1 => sprintf(get_translation('error_upload_1'), ini_get("upload_max_filesize")),
	2 => get_translation('error_upload_2'),
	3 => get_translation('error_upload_3'),
	4 => get_translation('error_upload_4'),
	6 => get_translation('error_upload_6'),
	7 => get_translation('error_upload_7'),
	8 => get_translation('error_upload_8'),
	'post_max_size' => sprintf(get_translation('error_post_max_size'), ini_get("post_max_size")),
	'max_file_size' => get_translation('error_max_file_size'),
	'min_file_size' => get_translation('error_min_file_size'),
	'accept_file_types' => get_translation('error_accept_file_types'),
	'max_number_of_files' => get_translation('error_max_number_of_files'),
	'max_width' => get_translation('error_max_width'),
	'min_width' => get_translation('error_min_width'),
	'max_height' => get_translation('error_max_height'),
	'min_height' => get_translation('error_min_height'),
	'abort' => get_translation('error_abort'),
	'image_resize' => get_translation('error_image_resize'),
	'upload_dir_not_writable' => get_translation('error_upload_dir_not_writable'),
	'file_name' => get_translation('error_file_name'),
	'missing_file' => get_translation('error_missing_file'),
);

$upload_handler = new UploadHandler($options, true, $error_messages);

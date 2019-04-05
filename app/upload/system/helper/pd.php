<?php

if (!defined('TA_PREFETCH')) {
	define('TA_PREFETCH', 1000);
}

if (!defined('JSON_UNESCAPED_SLASHES')) {
	define('JSON_UNESCAPED_SLASHES', 0xFFFF);
}

/**
  * Validate an email address.
  * Provide email address (raw input)
  * Returns true if the email address has the email
  * address format and the domain exists.
  *
  **/
if (!function_exists("validate_email")) {
	function validate_email($email) {
		$isValid = true;
		$atIndex = strrpos($email, "@");
		if (is_bool($atIndex) && !$atIndex) {
			$isValid = false;
		} else {
			$domain = substr($email, $atIndex+1);
			$local = substr($email, 0, $atIndex);
			$localLen = strlen($local);
			$domainLen = strlen($domain);
			if ($localLen < 1 || $localLen > 64) {
				// local part length exceeded
				$isValid = false;
			} else if ($domainLen < 1 || $domainLen > 255) {
				// domain part length exceeded
				$isValid = false;
			} else if ($local[0] == '.' || $local[$localLen-1] == '.') {
				// local part starts or ends with '.'
				$isValid = false;
			} else if (preg_match('/\\.\\./', $local)) {
				// local part has two consecutive dots
				$isValid = false;
			} else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
				// character not valid in domain part
				$isValid = false;
			} else if (preg_match('/\\.\\./', $domain)) {
				// domain part has two consecutive dots
				$isValid = false;
			} else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
						  str_replace("\\\\","",$local)))
			{
				// character not valid in local part unless
				// local part is quoted
				if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local))) {
					$isValid = false;
				}
			}
			if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {
				// domain not found in DNS
				$isValid = false;
			}
		}
		return $isValid;
	}
}

/**
  * Return file extension and FontAwesome icon mapping
  *
  **/
if (!function_exists("get_fa_icons")) {
	function get_fa_icons() {
		$fa_icons = array(
			"unknown"   => "fa-file-o",
			// audio
			"mp3"       => "fa-file-audio-o",
			"ogg"       => "fa-file-audio-o",
			"wav"       => "fa-file-audio-o",
			"flac"      => "fa-file-audio-o",
			"midi"      => "fa-file-audio-o",
			// video
			"avi"       => "fa-file-video-o",
			"mkv"       => "fa-file-video-o",
			"mov"       => "fa-file-video-o",
			"mp4"       => "fa-file-video-o",
			// image
			"bmp"       => "fa-file-image-o",
			"tif"       => "fa-file-image-o",
			"jpg"       => "fa-file-image-o",
			"png"       => "fa-file-image-o",
			"gif"       => "fa-file-image-o",
			// archive
			"zip"       => "fa-file-archive-o",
			"rar"       => "fa-file-archive-o",
			"7z"        => "fa-file-archive-o",
			"tgz"       => "fa-file-archive-o",
			"tar"       => "fa-file-archive-o",
			// code
			"js"        => "fa-file-code-o",
			"c"         => "fa-file-code-o",
			"h"         => "fa-file-code-o",
			"cpp"       => "fa-file-code-o",
			"cs"        => "fa-file-code-o",
			"php"       => "fa-file-code-o",
			"html"      => "fa-file-code-o",
			"xml"       => "fa-file-code-o",
			// other
			"txt"       => "fa-file-text-o",
			"pdf"       => "fa-file-pdf-o",
			"ods"       => "fa-file-excel-o",
			"csv"       => "fa-file-excel-o",
			"xls"       => "fa-file-excel-o",
			"xlsx"      => "fa-file-excel-o",
			"odt"       => "fa-file-word-o",
			"doc"       => "fa-file-word-o",
			"docx"      => "fa-file-word-o",
			"odp"       => "fa-file-powerpoint-o",
			"ppt"       => "fa-file-powerpoint-o",
			"pptx"      => "fa-file-powerpoint-o",
		);
		ksort($fa_icons);
		return $fa_icons;
	}
}

/**
  * Return file mime types
  *
  **/
if (!function_exists("get_mime_types")) {
	function get_mime_types() {
		return array(
			// Applications
			"pdf"   => "application/pdf",
			"exe"   => "application/octet-stream",
			"zip"   => "application/zip",
			"gz"    => "application/gzip",
			"rar"   => "application/x-rar-compressed",
			"tar"   => "application/x-tar",
			"doc"   => "application/msword",
			"docx"  => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
			"xls"   => "application/vnd.ms-excel",
			"xlsx"  => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
			"ppt"   => "application/vnd.ms-powerpoint",
			"pps"   => "application/vnd.ms-powerpoint",
			"pptx"  => "application/vnd.openxmlformats-officedocument.presentationml.presentation",
			"ppsx"  => "application/vnd.openxmlformats-officedocument.presentationml.slideshow",
			"odt"   => "application/vnd.oasis.opendocument.text",
			"odp"   => "application/vnd.oasis.opendocument.presentation",
			"ods"   => "application/vnd.oasis.opendocument.spreadsheet",
			"odg"   => "application/vnd.oasis.opendocument.graphics",
			"jsc"   => "application/javascript",
			"js"    => "application/javascript",
			"ps"    => "application/postscript",
			"ttf"   => "application/x-font-ttf",
			// Image
			"gif"   => "image/gif",
			"jpeg"  => "image/jpeg",
			"jpg"   => "image/jpeg",
			"png"   => "image/png",
			"svg"   => "image/svg+xml",
			"djvu"  => "image/vnd.djvu",
			"djv"   => "image/vnd.djvu",
			// Audio
			"mp3"   => "audio/mpeg",
			"mp4"   => "audio/mp4",
			"wav"   => "audio/vnd.wave",
			"mka"   => "audio/x-matroska",
			"wma"   => "audi/x-ms-wma",
			// Video
			"avi"   => "video/avi",
			"divx"  => "video/avi",
			"mpeg"  => "video/mpeg",
			"mpg"   => "video/mpeg",
			"mpe"   => "video/mpeg",
			"mov"   => "video/quicktime",
			"mkv"   => "video/x-matroska",
			"3gp"   => "video/3gpp",
			"wmv"   => "video/x-ms-wmv",
			"flv"   => "video/x-flv",
			// Text
			"css"   => "text/css",
			"csv"   => "text/csv",
			"php"   => "text/plain",
			"htm"   => "text/html",
			"html"  => "text/html",
			"rtf"   => "text/rtf",
			"xml"   => "text/xml",
		);
	}
}

/**
  * Build a URL from associative array like parse_url() returns
  *
  **/
if (!function_exists("build_url")) {
	function build_url($url, $parts=array(), $reset_query_params=array()) {
		if (is_string($url)) {
			$url = parse_url($url);
		}
		if (is_string($parts)) {
			$parts = parse_url($parts);
		}
		$scheme   = isset($parts['scheme']) ? $parts['scheme'] . '://' : (isset($url['scheme']) ? $url['scheme'] . '://' : '');
		$host     = isset($parts['host']) ? $parts['host'] : (isset($url['host']) ? $url['host'] : '');
		$port     = isset($parts['port']) ? ':' . $parts['port'] : (isset($url['port']) ? ':' . $url['port'] : '');
		$user     = isset($parts['user']) ? $parts['user'] : (isset($url['user']) ? $url['user'] : '');
		$pass     = isset($parts['pass']) ? ':' . $parts['pass'] : (isset($url['pass']) ? ':' . $url['pass']  : '');
		$pass     = ($user || $pass) ? "$pass@" : '';
		$path     = isset($parts['path']) ? $parts['path'] : (isset($url['path']) ? $url['path'] : '');
		if (isset($parts['query']) && isset($url['query']) && $url['query']) {
			parse_str($parts['query'], $parts_query);
			parse_str($url['query'], $url_query);
			foreach ($reset_query_params as $param) {
				unset($url_query[$param]);
			}
			$new_query = str_replace("%2F", "/", http_build_query(array_merge($url_query, $parts_query)));
			$query = $new_query ? '?' . $new_query : '';
		} else {
			$query = !empty($parts['query']) ? '?' . $parts['query'] : (!empty($url['query']) ? '?' . $url['query'] : '');
		}
		$fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : (isset($url['fragment']) ? '#' . $url['fragment'] : '');
		return "$scheme$user$pass$host$port$path$query$fragment";
	}
}

/**
  * Use our custom json_encode function in case of older PHP versions
  *
  **/
if (!function_exists("json_enc")) {
	function json_enc($value, $options = 0, $depth = 512) {
		if (version_compare(phpversion(), '5.5.0') >= 0) {
			return json_encode($value, $options, $depth);
		} elseif (version_compare(phpversion(), '5.4.0') >= 0) {
			return json_encode($value, $options);
		} else {
			return json_encode($value);
		}
	}
}

/**
  * Validate date against given format
  *
  **/
if (!function_exists("validate_date")) {
	function validate_date($datetime, $format="Y-m-d H:i:s") {
		try {
			if (!$datetime) return false;
			$d = new DateTime($datetime);
			return $d->format("Y-m-d H:i:s") == $datetime;
		} catch (Exception $e) {
			// echo $e->getMessage();
		}
		return false;
	}
}

/**
  * Remaps an array keys to SQL id fields
  *
  **/
if (!function_exists("array_remap_key_to_id")) {
	function array_remap_key_to_id($key, $results) {
		$new_array = array();

		foreach ($results as $result) {
			if (isset($result[$key])) {
				$new_array[$result[$key]] = $result;
			}
		}

		return $new_array;
	}
}

/**
  * Sort columns by index key
  *
  **/
if (!function_exists("column_sort")) {
	function column_sort($a, $b) {
		if ($a['index'] == $b['index']) {
			return 0;
		}
		return ($a['index'] < $b['index']) ? -1 : 1;
	}
}

/**
  * Filter columns by display value
  *
  **/
if (!function_exists("column_display")) {
	function column_display($a) {
		return (isset($a['display'])) ? (int)$a['display'] : false;
	}
}


/**
  * Convert file size eg "2M" to bytes
  *
  **/
if (!function_exists("to_bytes")) {
	function to_bytes($str){
		$val = trim($str);
		$last = strtolower($str[strlen($str) - 1]);
		switch($last) {
			case 't': $val *= 1000;
			case 'g': $val *= 1000;
			case 'm': $val *= 1000;
			case 'k': $val *= 1000;
		}
		return $val;
	}
}

/**
  * Format file size in SI format
  *
  **/
if (!function_exists("format_file_size")) {
	function format_file_size($size) {
		$size_bytes = $size;
		$i = 0;

		$suffix = array('B', 'KiB', 'MiB', 'GiB', 'TiB');

		while (($size / 1024) > 1) {
			$size = $size / 1024;
			$i++;
		}

		return round(substr($size, 0, strpos($size, '.') + 4), 2) . ' ' . $suffix[$i];
	}
}


if (!function_exists("replace_commas")) {
	function replace_commas($str) {
		return str_replace(",", ".", $str);
	}
}

/**
  * Decode data string
  *
  **/
if (!function_exists("bdecode")) {
	function bdecode($s) {
		return json_decode(base64_decode($s));
	}
}

/**
  * Sanitize a string
  *
  **/
if (!function_exists("str_sanitize")) {
	function str_sanitize($str) {
		// Transliteration for known non-ascii Estonian characters
		$from_to = array(
			"Ü" => "U",
			"Õ" => "O",
			"Ö" => "O",
			"Ä" => "A",
			"ü" => "u",
			"õ" => "o",
			"ö" => "o",
			"ä" => "a",
			"Š" => "S",
			"Ž" => "Z",
			"š" => "s",
			"ž" => "z",
		);
		$str = strtr($str, $from_to);

		// Translitetration for Russian characters
		$cyrillic = array(
			"Щ",   "Ш", "Ч", "Ц","Ю", "Я", "Ж", "А","Б","В","Г","Д","Е", "Ё", "З","И","Й","К","Л","М","Н","О","П","Р","С","Т","У","Ф","Х", "Ь","Ы","Ъ","Э",
			"щ",   "ш", "ч", "ц","ю", "я", "ж", "а","б","в","г","д","е", "ё", "з","и","й","к","л","м","н","о","п","р","с","т","у","ф","х", "ь","ы","ъ","э");
		$latin = array(
			"Shch","Sh","Ch","C","Ju","Ja","Zh","A","B","V","G","D","Je","Jo","Z","I","J","K","L","M","N","O","P","R","S","T","U","F","Kh","", "Y","", "E",
			"shch","sh","ch","c","ju","ja","zh","a","b","v","g","d","je","jo","z","i","j","k","l","m","n","o","p","r","s","t","u","f","kh","", "y","", "e");
		$str = str_replace($cyrillic, $latin, $str);

		// Transliterate Czech non-ascii characters
		$from_to = array(
			"á" => "a",
			"Á" => "A",
			"č" => "c",
			"Č" => "C",
			"ď" => "d",
			"Ď" => "D",
			"é" => "e",
			"É" => "E",
			"ě" => "e",
			"Ě" => "E",
			"í" => "i",
			"Í" => "I",
			"ň" => "n",
			"Ň" => "N",
			"ó" => "o",
			"Ó" => "O",
			"ř" => "r",
			"Ř" => "R",
			"ť" => "t",
			"Ť" => "T",
			"ú" => "u",
			"Ú" => "U",
			"ů" => "u",
			"Ů" => "U",
			"ý" => "y",
			"Ý" => "Y",
		);
		$str = strtr($str, $from_to);

		// Translitetrate German characters
		$from_to = array (
			'ä' => 'ae',
			'ë' => 'e',
			'ï' => 'i',
			'ö' => 'oe',
			'ü' => 'ue',
			'Ä' => 'Ae',
			'Ë' => 'E',
			'Ï' => 'I',
			'Ö' => 'Oe',
			'Ü' => 'Ue',
			'ß' => 'ss',
		);
		$str = strtr($str, $from_to);

		// Translitetrate French characters
		$from_to = array (
			'â' => 'a',
			'ê' => 'e',
			'î' => 'i',
			'ô' => 'o',
			'û' => 'u',
			'Â' => 'A',
			'Ê' => 'E',
			'Î' => 'I',
			'Ô' => 'O',
			'Û' => 'U',
			'œ' => 'oe',
			'æ' => 'ae',
			'Ÿ' => 'Y',
			'ç' => 'c',
			'Ç' => 'C',
		);
		$str = strtr($str, $from_to);

		// Translitetrate Hungarian characters
		$from_to = array (
			'á' => 'a',
			'é' => 'e',
			'í' => 'i',
			'ó' => 'o',
			'ö' => 'o',
			'ő' => 'o',
			'ú' => 'u',
			'ü' => 'u',
			'ű' => 'u',
		);
		$str = strtr($str, $from_to);

		// Translitetrate Polish characters
		$from_to = array (
			'ą' => 'a',
			'ę' => 'e',
			'ó' => 'o',
			'ć' => 'c',
			'ł' => 'l',
			'ń' => 'n',
			'ś' => 's',
			'ż' => 'z',
			'ź' => 'z',
			'Ó' => 'O',
			'Ć' => 'C',
			'Ł' => 'L',
			'Ś' => 'S',
			'Ż' => 'Z',
			'Ź' => 'Z'
		);
		$str = strtr($str, $from_to);

		// Translitetrate Danish characters
		$from_to = array (
			'æ' => 'ae',
			'ø' => 'oe',
			'å' => 'aa',
			'Æ' => 'Ae',
			'Ø' => 'Oe',
			'Å' => 'Aa'
		);
		$str = strtr($str, $from_to);

		// Translitetrate Croatian characters
		$from_to = array (
			'Č' => 'C',
			'Ć' => 'C',
			'Ž' => 'Z',
			'Š' => 'S',
			'Đ' => 'D',
			'č' => 'c',
			'ć' => 'c',
			'ž' => 'z',
			'š' => 's',
			'đ' => 'd',
		);
		$str = strtr($str, $from_to);

		// Translitetrate Slovak characters
		$from_to = array (
			'á' => 'a',
			'Á' => 'A',
			'ä' => 'a',
			'Ä' => 'A',
			'č' => 'c',
			'Č' => 'C',
			'ď' => 'd',
			'Ď' => 'D',
			'é' => 'e',
			'É' => 'E',
			'í' => 'i',
			'Í' => 'I',
			'ĺ' => 'l',
			'Ĺ' => 'L',
			'ľ' => 'l',
			'Ľ' => 'L',
			'ň' => 'n',
			'Ň' => 'N',
			'ó' => 'o',
			'Ó' => 'O',
			'ô' => 'o',
			'Ô' => 'O',
			'ŕ' => 'r',
			'Ŕ' => 'R',
			'š' => 's',
			'Š' => 'S',
			'ť' => 't',
			'Ť' => 'T',
			'ú' => 'u',
			'Ú' => 'U',
			'Ý' => 'Y',
			'ý' => 'y',
			'ž' => 'z',
			'Ž' => 'Z',
		);
		$str = strtr($str, $from_to);

		// Translitetrate Georgian characters
		$from_to = array (
			'ა' => 'a',
			'ბ' => 'b',
			'გ' => 'g',
			'დ' => 'd',
			'ე' => 'e',
			'ვ' => 'v',
			'ზ' => 'z',
			'თ' => 't',
			'ი' => 'i',
			'კ' => 'k',
			'ლ' => 'l',
			'მ' => 'm',
			'ნ' => 'n',
			'ო' => 'o',
			'პ' => 'p',
			'ჟ' => 'zh',
			'რ' => 'r',
			'ს' => 's',
			'ტ' => 't',
			'უ' => 'u',
			'ფ' => 'p',
			'ქ' => 'k',
			'ღ' => 'gh',
			'ყ' => 'q',
			'შ' => 'sh',
			'ჩ' => 'ch',
			'ც' => 'ts',
			'ძ' => 'dz',
			'წ' => 'ts',
			'ჭ' => 'ch',
			'ხ' => 'kh',
			'ჯ' => 'j',
			'ჰ' => 'h',
		);
		$str = strtr($str, $from_to);

		// $str = mb_convert_encoding($str, "HTML-ENTITIES", "UTF-8");
		return $str;
	}
}

if (!function_exists("sort_file_list")) {
	function sort_file_list($a, $b) {
		$a_p = pathinfo($a);
		$b_p = pathinfo($b);
		if (strnatcmp($a_p["dirname"], $b_p["dirname"]) == 0) {
			return strnatcmp($a_p["basename"], $b_p["basename"]);
		} else {
			return strnatcmp($a_p["dirname"], $b_p["dirname"]);
		}
	}
}

if (!function_exists("format_file_types")) {
	function format_file_types($types) {
		$file_types = array_map("trim", explode(",", $types));
		$fts = array();
		foreach ($file_types as $ft) {
			if ($ft) {
				$t = utf8_strtolower($ft);
				if (strpos($t, ".") === 0)
					$t = substr($t, 1);
				$fts[] = $t;
			}
		}
		return implode(",", $fts);
	}
}

if (!function_exists("format_excludes")) {
	function format_excludes($excluded) {
		$excluded = array_map("trim", explode(",", $excluded));
		$excl = array();
		foreach ($excluded as $ex) {
			if ($ex) {
				$ex = utf8_strtolower($ex);
				$excl[] = $ex;
			}
		}
		return implode(",", $excl);
	}
}

/**
  * Convert another base value to its decimal value
  *
  **/
if (!function_exists("base2dec")) {
	function base2dec($value, $base, $digits=false) {
		if ($base < 2 || $base > 62 && !$digits || $digits && $base > strlen($digits)) return $value;
		bcscale(0);
		if (!$digits) {
			$chars = array_merge(range(0,9), range('a','z'), range('A','Z'));
			$digits = implode('', $chars);
		}
		$size = strlen($value);
		$dec = "0";
		for ($i = 0; $i < $size; $i++) {
			$element = strpos($digits, $value[$i]);
			$power = bcpow($base, $size - $i - 1);
			$dec = bcadd($dec, bcmul($element, $power));
		}
		return (string) $dec;
	}
}

/**
  * Convert a decimal value to any other base value
  *
  **/
if (!function_exists("dec2base")) {
	function dec2base($dec, $base, $digits=false) {
		if ($base < 2 || $base > 62 && !$digits || $digits && $base > strlen($digits)) return $dec;
		bcscale(0);
		if (!$digits) {
			$chars = array_merge(range(0,9), range('a','z'), range('A','Z'));
			$digits = implode('', $chars);
		}
		$value = "";
		while ($dec > $base - 1) {
			$rest = bcmod($dec, $base);
			$dec = bcdiv($dec, $base);
			$value = $digits[$rest] . $value;
		}
		$value = $digits[(int)$dec] . $value;
		return (string) $value;
	}
}

/**
  * Convert HTML line breaks <br> to new lines \n
  *
  **/
if (!function_exists("br2nl")) {
	function br2nl($text) {
		return preg_replace('/<br\\s*?\/??>/i', '\n', $text);
	}
}

/**
  * Handles 'Add To Cart' button replacement action
  *
  **/
class CartButtonAction {
	protected $registry;

	public function __construct($registry) {
		$this->registry = $registry;
	}

	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	public function get($product_info, &$replace_with, $action_setting) {
		if (!(int)$this->config->get("pd_status") || !isset($product_info['price']) || !isset($product_info['total_downloads']) || !isset($product_info['free_downloads']) || !array_key_exists('download_id', $product_info) || !array_key_exists('login_required', $product_info) || !in_array($this->config->get('config_store_id'), bdecode($this->config->get('pd_as')))) {
			return 0;
		}

		$this->language->load('download/download');

		$total_downloads = $product_info['total_downloads'];
		$free_downloads = $product_info['free_downloads'];

		if (!(float)$product_info['price']) {
			if ((int)$this->config->get($action_setting) == 3) {
				$logged = $this->customer->isLogged();
				$login_text = $this->config->get('pd_show_login_required_text');
				$login = $this->config->get('pd_require_login');
				$no_link = $this->config->get('pd_show_download_without_link');
				$login_free = $this->config->get('pd_require_login_free');
				$login_regular = $this->config->get('pd_require_login_regular');
				$purchasable = $this->config->get('pd_show_purchasable_downloads');

				$show_downloads = ($logged || $no_link || $purchasable && !$login_regular && !$login || !$login && !$login_free || !$logged && $login_text);

				if ($show_downloads) {
					if ($free_downloads == 1 && $total_downloads == 1 && $product_info['download_id']) {
						if ($logged || !(int)$product_info['login_required'] && !$login && !$login_free) {
							$replace_with = array("link" => $this->url->link('module/product_downloads/get', 'did=' . $product_info['download_id']), "button" => $this->language->get('button_download'));
						} else {
							$replace_with = array("link" => $this->url->link('account/login', 'redirect=' . urlencode('product/product&product_id='. $product_info['product_id']), 'SSL'), "button" => $this->language->get('button_login'));
						}
						return (int)$this->config->get($action_setting);
					} else if ($free_downloads == $total_downloads && $total_downloads > 1) {
						$replace_with = array("link" => $this->url->link('product/product', 'product_id=' . $product_info['product_id']), "button" => $this->language->get('button_view'));
						return (int)$this->config->get($action_setting);
					}
				}
			}

			if ((int)$this->config->get($action_setting) == 1) {
				return (int)$this->config->get($action_setting);
			} else if ((int)$this->config->get($action_setting) == 2 && $free_downloads == $total_downloads) {
				return (int)$this->config->get($action_setting);
			}
		}
		return 0;
	}
}

/**
  * Handles file download
  *
  **/
class DownloadHandler {
	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->request = $registry->get('request');
		$this->log = $registry->get('log');
	}

	public function download($file, $mask, $callback=null) {
		// Avoid sending unexpected errors to the client as we don't want to corrupt the data
		@error_reporting(0);

		$mime_types = get_mime_types();
		$path_parts = pathinfo($mask);

		if (function_exists("finfo_open")) {
			$finfo = finfo_open(defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME);
			$mime = finfo_file($finfo, $file);
			finfo_close($finfo);
		} else if (isset($path_parts['extension']) && array_key_exists(strtolower($path_parts['extension']), $mime_types))
			$mime = $mime_types[strtolower($path_parts['extension'])];
		else
			$mime = 'application/octet-stream';

		if (!headers_sent()) {
			$file_size = filesize($file);
			$handle = @fopen($file, "rb");
			if ($handle) {
				// Clean up any buffers and turn off output buffering

				// ob_gzhandler manages http headers.  When undoing it with
				// ob_end_flush, the result is "Warning - headers already sent ..."
				// just bail
				if (in_array('ob_gzhandler', ob_list_handlers())) {
					ob_flush();
				} else {
					// Tell apache to send an uncompressed non-chunked response
					if (!headers_sent() && function_exists('apache_setenv')) {
						apache_setenv('no-gzip', '1');
					}

					// Turn off any default output handlers
					ini_set('output_handler', '');
					ini_set('output_buffering', false);
					ini_set('implicit_flush', true);

					// Clean the output buffer
					$levels = ob_get_level();
					for ($i = 0; $i < $levels; $i++) {
						@ob_end_clean();
					}

					// Turn off the zlib compression handler
					if (!headers_sent() && ini_get('zlib.output_handler')) {
						ini_set('zlib.output_handler', '');
						ini_set('zlib.output_compression', 0);
					}
				}

				// Default to send entire file
				// Offset signifies where we should begin to read the file
				$byte_offset = 0;
				//Length is for how long we should read the file according to the browser, and can never go beyond the file size
				$byte_length = $file_size;

				// Remove headers that might unnecessarily clutter up the output
				header_remove('Cache-Control');
				header_remove('Pragma');

				header('Accept-Ranges: bytes', true);
				header("Content-Type: $mime", true);

				if ($this->config->get("pd_force_download")) {
					header(sprintf('Content-Disposition: attachment; filename="%s"', $mask ? $mask : basename($file)));
				} else {
					// Allow streaming
					header(sprintf('Content-Disposition: inline; filename="%s"', $mask ? $mask : basename($file)));
				}

				// Check if http_range is sent by browser (or download manager)
				if (isset($this->request->server['HTTP_RANGE'])) {
					$range = $this->request->server['HTTP_RANGE']; // IIS/Some Apache versions
				} else if (function_exists('apache_request_headers') && $apache = apache_request_headers()) { // Try Apache again
					$headers = array();
					foreach ($apache as $header => $val) {
						$headers[strtolower($header)] = $val;
					}
					if (isset($headers['range'])) {
						$range = $headers['range'];
					} else {
						$range = FALSE; // We can't get the header/there isn't one set
					}
				} else {
					$range = FALSE; // We can't find the http_range headers/they are not set
				}

				if ($range) {
					// Validate range request
					if (!preg_match('/^bytes=(\d*-\d*)(,\d*-\d*)*$/', $range)) {
						header('HTTP/1.1 416 Requested Range Not Satisfiable');
						header('Content-Range: bytes */' . $file_size); // Required in 416.
						exit;
					}
					// Figure out download piece from range (if set)
					// Multiple ranges could be specified at the same time, but for simplicity only serve the first range
					// http://tools.ietf.org/id/draft-ietf-http-range-retrieval-00.txt
					$ranges = explode(',', substr($range, 6));
					$parts = explode('-', $ranges[0]);

					// Set offset and length based on range
					// Also check for invalid ranges.
					if ($parts[0] === '') { // First number missing, return last $parts[1] bytes
						$byte_length = min((int)$parts[1], $file_size);
						$byte_offset = $file_size - $byte_length;
					} else if ($parts[1] === '') { // Second number missing, return from byte $parts[0] to end
						$byte_offset = min((int)$parts[0], $file_size - 1);
						$byte_length = $file_size - $byte_offset;
					} else {
						$byte_offset = min((int)$parts[0], $file_size - 1);
						$byte_length = min(abs((int)$parts[1] + 1 - $byte_offset), $file_size - $byte_offset);
					}

					header("HTTP/1.1 206 Partial content");
					header(sprintf('Content-Range: bytes %d-%d/%d', $byte_offset, $byte_offset + $byte_length - 1, $file_size)); // Decrease by 1 on byte-length since this definition is zero-based index of bytes being sent
				}

				$byte_range = $byte_length;// - $byte_offset;

				header(sprintf('Content-Length: %d', $byte_range));

				function handleError($errno, $errstr, $errfile, $errline, array $errcontext) {
					// error was suppressed with the @-operator
					if (0 === error_reporting()) {
						return false;
					}

					throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
				}
				set_error_handler('handleError');

				try {
					set_time_limit(0);
				} catch (ErrorException $e) {
					if ($this->config->get('config_error_log')) {
						$this->log->write("PD PRO exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
					}
				}
				restore_error_handler();

				$buffer = '';   // Variable containing the buffer
				$buffer_size = 1024 * 8; // Just a reasonable buffer size
				$byte_pool = $byte_range; // Contains how much is left to read of the byte_range

				if (fseek($handle, $byte_offset, SEEK_SET) == -1) {
					header("HTTP/1.0 500 Internal Server Error");
					if ($this->config->get('config_error_log')) {
						$this->log->write('Error: Could not seek to position ' . $byte_offset . ' in file ' . $file . '!');
					}
					exit;
				}

				while (!feof($handle) && $byte_pool > 0) {
					$chunk_size_requested = min($buffer_size, $byte_pool); // How many bytes we request on this iteration

					// Try reading $chunk_size_requested bytes from $handle and put data in $buffer
					$buffer = fread($handle, $chunk_size_requested);

					// Store how many bytes were actually read
					$chunk_size_actual = strlen($buffer);

					// If we didn't get any bytes that means something unexpected has happened since $byte_pool should be zero already
					if ($chunk_size_actual == 0) {
						// For production servers this should go in your php error log, since it will break the output
						if ($this->config->get('config_error_log')) {
							$this->log->write(sprintf('Error: Chunksize became 0 while downloading %d-%d/%d of file %s!', $byte_offset, $byte_length - 1, $file_size, $file));
						}
						break;
					}

					// Decrease byte pool with amount of bytes that were read during this iteration
					$byte_pool -= $chunk_size_actual;

					// Write the buffer to output
					print $buffer;

					// Try to output the data to the client immediately
					// Only flush when the output buffering is still active and there is data in the buffer
					if (ob_get_level() > 0) ob_flush();
					flush();

					// Stop if connection is closed already
					if (connection_status() != 0) {

						// Update downloaded count only if end of file has been reached
						if (ftell($handle) == $file_size) {
							if (is_callable($callback)) {
								$callback();
							}
						}

						@fclose($handle);
						exit;
					}
				}

				// Update downloaded count only if end of file has been reached
				if (ftell($handle) == $file_size) {
					if (is_callable($callback)) {
						$callback();
					}
				}

				// File save was a success
				@fclose($handle);
				exit;
			} else {
				// File couldn't be opened
				header("HTTP/1.0 500 Internal Server Error");
				if ($this->config->get('config_error_log')) {
					$this->log->write('Error: Could not open file ' . $file . '!');
				}
				exit;
			}
		} else {
			exit('Error: Headers already sent!');
		}
	}
}

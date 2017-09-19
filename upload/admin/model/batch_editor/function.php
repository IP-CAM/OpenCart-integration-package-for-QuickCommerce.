<?php
class ModelBatchEditorFunction extends Model {
	public function getImageDirectories($path = 'data/') {
		static $directories = array ();
		
		if (!$directories) {
			if (VERSION < '2.0.0.0') {
				$directories = array ('data/');
			} else {
				$directories = array ('catalog/');
				
				if ($path == 'data/') {
					$path = 'catalog/';
				}
			}
		}
		
		$results = scandir (DIR_IMAGE . $path);
		
		foreach ($results as $result) {
			if ($result != '.' && $result != '..' && is_dir (DIR_IMAGE . $path . $result)) {
				$directories[] = $path . $result . '/';
				
				$this->getImageDirectories($path . $result . '/');
			}
		}
		
		return $directories;
	}
	
	public function translit($string, $space = '_') {
		$symbols = array(
			'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ь' => '', 'ы' => 'y', 'ъ' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
			'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I', 'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch', 'Ь' => '', 'Ы' => 'Y', 'Ъ' => '', 'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya'
		);
		
		$string = strip_tags (html_entity_decode ($string, ENT_QUOTES, 'UTF-8'));
		$string = strtr ($string, $symbols);
		$string = strtolower ($string);
		$string = preg_replace ('~[^a-z0-9]+~u', $space, $string);
		$string = trim ($string, $space);
		$string = preg_replace ('/\\' . $space . '{2,}/', $space, $string);
		
		return $string;
	}
}
?>
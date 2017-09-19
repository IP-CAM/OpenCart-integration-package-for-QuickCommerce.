<?php 
/**
 * download.php
 *
 * DataSyncs management
 *
 * @author     Lucas Lopatka
 * @copyright  2015
 * @version    1.0
 */
require_once(DIR_SYSTEM . 'engine/restcontroller.php');

class MyRecursiveFilterIterator extends RecursiveFilterIterator {
	public function accept() {
		$filename = $this->current()->getFilename();
		// Skip hidden files and directories.
		if ($name[0] === '.') {
			return FALSE;
		}
		if ($this->isDir()) {
			// Only recurse into intended subdirectories.
			//return $name === 'wanted_dirname';
		}
		else {
			// Only consume files of interest.
			//return strpos($name, 'wanted_filename') === 0;
		}
		
		return true;
	}
}

class ControllerRestData extends RestController {
	
	private $error = array();
	
	public function buildControlFile() {
		$path = '/var/www/html/datasync/'

		$directory = new RecursiveDirectoryIterator($path, FilesystemIterator::FOLLOW_SYMLINKS);
		$filter = new MyRecursiveFilterIterator($directory);
		$iterator = new RecursiveIteratorIterator($filter);
		$files = array();
		
		foreach ($iterator as $info) {
			var_dump($info);
			$files[] = $info->getPathname();
		}

		if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}					
	}
	
	public function files() {
		//$this->checkPlugin();
		
		if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
			if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
				
			} else {
				// Build control file
				$this->buildControlFile();
			}
		}
		/*else if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
			//add download
			$requestjson = file_get_contents('php://input');
		
			$requestjson = json_decode($requestjson, true);

			if (!empty($requestjson)) {
				//$this->save($requestjson);
			} else {
				$this->response->setOutput(json_encode(array('success' => false)));
			} 

		}*/

    }
}
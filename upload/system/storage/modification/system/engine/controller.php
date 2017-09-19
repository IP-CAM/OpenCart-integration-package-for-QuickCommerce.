<?php
abstract class Controller {
	protected $registry;

    // These have been included via mod in the base controller class
    // They are redeclared/overridden in QCController
	protected function getPostVar($key, $default = null) {
		return $this->getRequestVar($key, $default, 'post');
	}

	protected function getRequestVar($key, $default = null, $type = 'get') {
		$types = array('get', 'post');
		if (!in_array($type, $types)) {
			throw new Exception('Invalid request type');
		}

		if (isset($this->request->{$type}[$key])) {
			if (isset($this->request->{$type}[$key])) {
				return $this->request->{$type}[$key];
			}
		}

		return $default;
	}
            

	public function __construct($registry) {
		$this->registry = $registry;
	}

	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}
}
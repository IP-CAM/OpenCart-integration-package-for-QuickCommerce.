<?php
// At a minimum, invoices and cash sales must be supported
/**
 * Class NameList
 */
class NameList extends QcResource {
	protected $className = 'OcNameList';
    
    public function search($params = array(), $serialize = true, $tableize = true) {
        throw new BadMethodCallException();
    }
}
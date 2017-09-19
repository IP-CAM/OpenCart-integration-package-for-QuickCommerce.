<?php
// At a minimum, invoices and cash sales must be supported
/**
 * Class Transaction
 */
class Transaction extends QcResource {
	protected $className = 'OcTransaction';
    
    public function search($params = array(), $serialize = true, $tableize = true) {
        throw new BadMethodCallException();
    }
}
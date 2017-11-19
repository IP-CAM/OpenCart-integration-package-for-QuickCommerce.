<?php
define('BLOCK_JOURNAL', true); // This version of Journal only works on up to OpenCart 2.2

final class Front {
	private $registry;
	private $pre_action = array();
	private $error;
    
    /* Journal2 Theme modification */
	public static $IS_INSTALLER = false;
	public static $IS_JOURNAL   = false;
	public static $IS_OC2       = true;
	public static $IS_ADMIN     = false;
	/* End of Journal2 Theme modification */    

	public function __construct($registry) {
		$this->registry = $registry;
	}
	
	public function addPreAction(Action $pre_action) {
		$this->pre_action[] = $pre_action;
	}
	
	public function dispatch(Action $action, Action $error) {
		$this->error = $error;

		foreach ($this->pre_action as $pre_action) {
			$result = $this->execute($pre_action);

			if ($result instanceof Action) {
				$action = $result;

				break;
			}
		}
        
        /* QuickCommerce Journal2 Theme modification */
		/*if (defined('HTTP_OPENCART')) {
			self::$IS_INSTALLER = true;
		} else if (defined('VERSION')) {
			global $config;
            
            if ($config != null) {
                self::$IS_OC2 = version_compare(VERSION, '2', '>=');
                if (file_exists(DIR_APPLICATION . 'model/journal2/journal2.php')) {
                    require_once DIR_APPLICATION . 'model/journal2/journal2.php';
                }
                self::$IS_ADMIN = defined('JOURNAL_IS_ADMIN');
                self::$IS_JOURNAL = true; //$config->get('config_template') === 'journal2' || $config->get('theme_default_directory') === 'journal2';                
            } else {
                // If config is null, it usually means that the modification directory isn't writeable
                // Check if DIR_MODIFICATION is writable
                if (!is_writable(DIR_MODIFICATION)) {
                    echo '
                        <h3>Journal Installation Error</h3>
                        <p><b>' . DIR_MODIFICATION . '</b> is not writable. Contact your hosting provider for more information.</p>
                    ';
                    exit();
                }
                
                // Allow script to run... there's most likely going to be a runtime error popping up
                // and we're going to need to correct it before proceeding anyway
                self::$IS_OC2 = version_compare(VERSION, '2', '>=');
                if (file_exists(DIR_APPLICATION . 'model/journal2/journal2.php')) {
                    require_once DIR_APPLICATION . 'model/journal2/journal2.php';
                }
                self::$IS_ADMIN = defined('JOURNAL_IS_ADMIN');
                self::$IS_JOURNAL = true; //$config->get('config_template') === 'journal2' || $config->get('theme_default_directory') === 'journal2';
            }
            
		}*/
        
		//require_once(DIR_SYSTEM . 'journal2/startup.php');
		/* End of Journal2 Theme modification */

		while ($action instanceof Action) {
			$action = $this->execute($action);
		}
	}

	private function execute(Action $action) {
		$result = $action->execute($this->registry);

		if ($result instanceof Action) {
			return $result;
		} 
		
		if ($result instanceof Exception) {
			$action = $this->error;
			
			$this->error = null;
			
			return $action;
		}
	}
}

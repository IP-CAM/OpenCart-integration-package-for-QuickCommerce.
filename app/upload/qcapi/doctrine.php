<?php
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\NamingStrategy;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Tools\EntityGenerator;
use Doctrine\ORM\Tools\DisconnectedClassMetadataFactory;
use Doctrine\Common\Util\Inflector;
use Doctrine\Common\Util\Debug;

class DoctrineInitializer
{
	function __construct(&$context, &$registry) {
		spl_autoload_register('self::autoload');
		spl_autoload_register('self::autoloadEntities');
		spl_autoload_extensions('.php');

		$paths = array(DIR_QC . 'vendor/quickcommerce/doctrine/orm/mappings/');
		$isDevMode = false;

		// the connection configuration
		$dbParams = array(
			'host'	   => (defined('DB_HOSTNAME')) ? DB_HOSTNAME : '127.0.0.1',
			'driver'   => 'pdo_mysql',
			'user'     => (defined('DB_USERNAME')) ? DB_USERNAME : 'root',
			'password' => (defined('DB_PASSWORD')) ? DB_PASSWORD : 'v@der!4201986',
			'dbname'   => (defined('DB_DATABASE')) ? DB_DATABASE : 'quickcommerce',
			'port'	   => (defined('DB_PORT')) ? DB_PORT : 3306
		);

		$config = Setup::createXMLMetadataConfiguration($paths, $isDevMode);
		$config->setAutoGenerateProxyClasses(true);

		//$namingStrategy = new OpenCartNamingStrategy();
		//$namingStrategy->setPrefix('oc2_');
		//$config->setNamingStrategy($namingStrategy);

		$entityManager = EntityManager::create($dbParams, $config);
		$entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

		$context->em = $entityManager;

		// Load definition
		$context->feedMap = DIR_QC . 'vendor/quickcommerce/feeds/mappings/QBO.fcm.xml';

		if (!is_file($context->feedMap)) {
			//throw new \Slim\Exception\Exception("Oh crap something's not right with the feed map");
			echo '<pre>Error reading feed</pre>';
			exit;
		}

		$context->mapXml = simplexml_load_file($context->feedMap);

		$context->db = $registry->get('db');
		$context->tax = $registry->get('tax');
		$context->weight = $registry->get('weight');
	}

	// Yeah, this isn't great but whatever for now
	/**
	 * @param $class
	 * @return bool
	 */
	public static function autoloadEntities($class) {
		$file = DIR_QC . 'vendor/quickcommerce/src/Entity/' . str_replace('\\', '/', $class) . '.php';
		if (is_file($file)) {
			//var_dump(true);
			include_once($file);
			return true;
		}

		return false;
	}

	/**
	 * @param $class
	 * @return bool
	 */
	public static function autoload($class) {
		$file = DIR_SYSTEM . 'library/quickcommerce/vendor/' . str_replace('\\', '/', $class) . '.php';
		//var_dump($file);

		if (is_file($file)) {
			//var_dump(true);
			include_once($file);
			return true;
		}

		return false;
	}
}
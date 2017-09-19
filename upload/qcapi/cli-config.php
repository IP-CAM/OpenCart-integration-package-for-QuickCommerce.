<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$paths = array("lib/Entities");
$isDevMode = false;

// the connection configuration
$dbParams = array(
		'driver'   => 'mysqli',
		'user'     => 'root',
		'password' => '',
		'dbname'   => 'oc22',
		'host'     => 'localhost',
		'port'     => 3306
);

$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
$entityManager = EntityManager::create($dbParams, $config);

$classes = $entityManager->getMetadataFactory()->getAllMetadata();

$platform = $entityManager->getConnection()->getDatabasePlatform();
$platform->registerDoctrineTypeMapping('enum', 'string');

return ConsoleRunner::createHelperSet($entityManager);
<?php

define ('POS_VERSION', '2.0.1');

require_once dirname(__FILE__) . '/../../vendor/autoload.php';
require_once dirname(__FILE__) . '/apis.php';


require_once dirname(__FILE__) . '/../../vendor/quickcommerce/src/Adapter/IOrderDriver.php';
require_once dirname(__FILE__) . '/../../vendor/quickcommerce/src/Adapter/IAdapter.php';

use QuickCommerce\API\Application;

// Init application mode
if (empty($_ENV['SLIM_MODE'])) {
    $_ENV['SLIM_MODE'] = (getenv('SLIM_MODE'))
        ? getenv('SLIM_MODE') : 'development';
}


// Init and load configuration
$configFile = dirname(__FILE__) . '/config/'
    . $_ENV['SLIM_MODE'] . '.php';

if (is_readable($configFile)) {
  require_once $configFile;
} else {
  require_once dirname(__FILE__) . '/config/default.php';
}

// Create Application
$app = new Application($_pos_api, $config['app']['logger']);

// Cache Middleware (inner)
// $app->add(new API\Middleware\Cache('/api/v1'));

// Parses JSON body
// $app->add(new \Slim\Middleware\ContentTypes());

// Manage Rate Limit
// $app->add(new API\Middleware\RateLimit('/api/v1'));

// JSON Middleware
// $app->add(new JSON('/api/v1'));

// Auth Middleware (outer)
// $app->add(new API\Middleware\TokenOverBasicAuth(array('root' => '/api/v1')));

// JWT Token support
//$secretKey = $app->retrieve('JWT', null);
/*$app->add(new \Slim\Middleware\JwtAuthentication([
		'path' => '/api',
		'passthrough' => ['/api/v1/urls', '/api/v1/version', '/api/v1/login', '/api/v1/user'],
		'secure' => false,
		'secret' => '' // $secretKey['secretKey'],
]));*/
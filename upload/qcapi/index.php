<?php
require_once dirname(__FILE__) . '/bootstrap.php';

// General API group
$app->group('/api', function () use ($app) {
	
	$this->group('/res', function () use ($app) {
		$this->get('/', function () {
			echo "<h1>Firebrand's QuickCommerce Resource API</h1>";
			echo "<p>This RESTful API provides an interface to GET/PUT/POST/DELETE serialized raw (unmodified) entities. Use this API when communicating with 3rd-party services.</p>";
		});

		// Get
		$this->get('/{resource}[/{id:\d+}]', function($req, $res, $args) use ($app) {
			$app->setDriverType('metadata'); // TODO: Use constant

			$service = \App\ResourceService::load($args['resource']);
			$service->setEntityManager($app->getEntityManager());
			//$service->setSlim($this);
			$service->init();

			if ($service === null) {
				\App\ResourceService::response($req, $res, \App\ResourceService::STATUS_NOT_FOUND);
			} else {
				if (!empty($args['id'])) {
					$service->get($req, $res, $args['id']);
				} else {
					$service->get($req, $res, null);
				}
			}
		});

		// Get
		$this->get('/{resource}/{id:\d+}/{attribute}', function($req, $res, $args) use ($app) {
			$app->setDriverType('metadata'); // TODO: Use constant

			$service = \App\ResourceService::load($args['resource']);
			$service->setEntityManager($app->getEntityManager());
			//$service->setSlim($this);
			$service->init();

			if ($service === null) {
				\App\ResourceService::response($req, $res, \App\ResourceService::STATUS_NOT_FOUND);
			} else {
				if (!empty($args['id'])) {
					// TODO: Sanitize attribute, use regex and some other tests
					$service->get($req, $res, $args['id'], $args['attribute']);
				} else {
					//$service->get($req, $res, null); TODO: Throw user error - did not provide an attribute id to filter the collection on
				}
			}

			// This is the way the other api works
			//return $response->withJson($app->retrieve($args['resource'], $request->getQueryParams(), $args['id'], $args['attribute']));
			//echo json_encode($app->retrieve($args['resource'], $request->getQueryParams(), $args['id'], $args['attribute']));
		});

		// Post
		$this->post('/{resource}[/{id:\d+}]', function($req, $res, $args) use ($app) {
			$app->setDriverType('metadata'); // TODO: Use constant

			$service = \App\ResourceService::load($args['resource']);
			if ($service === null) {
				\App\ResourceService::response(\App\ResourceService::STATUS_NOT_FOUND);
			} else {
				$service->post();
			}
		});

		// Put
		$this->put('/{resource}[/{id:\d+}]', function($req, $res, $args) use ($app) {
			$app->setDriverType('metadata'); // TODO: Use constant

			$service = \App\ResourceService::load($args['resource']);
			if ($service === null) {
				\App\ResourceService::response(\App\ResourceService::STATUS_NOT_FOUND);
			} else {
				$service->put($args['id']);
			}
		});

		// Delete
		$this->delete('/{resource}[/{id:\d+}]', function($req, $res, $args) use ($app) {
			$app->setDriverType('metadata'); // TODO: Use constant

			$service = \App\ResourceService::load($args['resource']);
			if ($service === null) {
				\App\ResourceService::response(\App\ResourceService::STATUS_NOT_FOUND);
			} else {
				$service->delete($args['id']);
			}
		});

		// Options
		$this->options('/{resource}[/{id:\d+}]', function($req, $res, $args) use ($app) {
			$app->setDriverType('metadata'); // TODO: Use constant

			$service = \App\ResourceService::load($args['resource']);
			if ($service === null) {
				\App\ResourceService::response(\App\ResourceService::STATUS_NOT_FOUND);
			} else {
				$service->options();
			}
		});
	});

	// Group for API Version 1
	$this->group('/v1', function () use ($app) {
		$this->get('/', function () {
			echo "<h1>Firebrand's QuickCommerce POS API</h1>";
			echo "<p>This API is used to communicate with the QuickCommerce Point-of-Sale app. Do not use for 3rd-party service integrations.</p>";
		});
		
		// fixed routes first!
		
		// reports
		$this->get('/report/endofday', function($request, $response, $args) use ($app) {
			$app->setDriverType('annotation'); // TODO: Use constant
			
			try {
				return $response->withJson($app->retrieve('EndOfDayReport', $request->getQueryParams()));
			} catch (\Exception $exception) {
				$app->getLogger()->addDebug($exception->getMessage());
			}
		});

		// pattern match routes for resources

		// api pattern (resource only, support get and post)
		$this->get('/{resource}', function($request, $response, $args) use ($app) {
			$app->setDriverType('annotation'); // TODO: Use constant

			try {
				return $response->withJson($app->retrieve($args['resource'], $request->getQueryParams()));
			} catch (\Exception $exception) {
				$app->getLogger()->addDebug($exception->getMessage());
			}
			//echo json_encode($app->retrieve($args['resource'], $request->getQueryParams()));
		});
		$this->post('/{resource}', function($request, $response, $args) use ($app) {
			$app->setDriverType('annotation'); // TODO: Use constant

			return $response->withJson($app->create($args['resource'], $request->getParsedBody()));
			//echo json_encode($app->create($args['resource'], $request->getParsedBody()));
		});

		// api pattern (resource + id support get, put, patch and delete)
		$this->get('/{resource}/{id}', function($request, $response, $args) use ($app) {
			$app->setDriverType('annotation'); // TODO: Use constant

			return $response->withJson($app->retrieve($args['resource'], $request->getQueryParams(), $args['id']));
			//echo json_encode($app->retrieve($args['resource'], $request->getQueryParams(), $args['id']));
		});
		$this->put('/{resource}/{id}', function($request, $response, $args) use ($app) {
			$app->setDriverType('annotation'); // TODO: Use constant

			return $response->withJson($app->update($args['resource'], $request->getParsedBody(), $args['id']));
			//echo json_encode($app->update($args['resource'], $request->getParsedBody(), $args['id']));
		});
		$this->patch('/{resource}/{id}', function($request, $response, $args) use ($app) {
			$app->setDriverType('annotation'); // TODO: Use constant

			return $response->withJson($app->partialUpdate($args['resource'], $request->getParsedBody(), $args['id']));
			//echo json_encode($app->partialUpdate($args['resource'], $request->getParsedBody(), $args['id']));
		});
		$this->delete('/{resource}/{id}', function($request, $response, $args) use ($app) {
			$app->setDriverType('annotation'); // TODO: Use constant

			return $response->withJson($app->remove($args['resource'], $request->getParsedBody(), $args['id']));
			//echo json_encode($app->remove($args['resource'], $request->getParsedBody(), $args['id']));
		});

		// api pattern (resource + id + attribute, support get and patch only)
		$this->get('/{resource}/{id}/{attribute}', function($request, $response, $args) use ($app) {
			$app->setDriverType('annotation'); // TODO: Use constant

			return $response->withJson($app->retrieve($args['resource'], $request->getQueryParams(), $args['id'], $args['attribute']));
			//echo json_encode($app->retrieve($args['resource'], $request->getQueryParams(), $args['id'], $args['attribute']));
		});
		$this->patch('/{resource}/{id}/{attribute}', function($request, $response, $args) use ($app) {
			$app->setDriverType('annotation'); // TODO: Use constant

			return $response->withJson($app->partialUpdate($args['resource'], $request->getParsedBody(), $args['id'], $args['attribute']));
			//echo json_encode($app->partialUpdate($args['resource'], $request->getParsedBody(), $args['id'], $args['attribute']));
		});
	});
});


// JSON friendly errors
// NOTE: debug must be false
// or default error template will be printed
$container = $app->getContainer();
$container['errorHandler'] = function ($container) {
	return function ($request, $response, $exception) use($container) {
		$mediaType = $request->getMediaType();
		
		$isAPI = ( bool ) preg_match('|^/api/v.*$|', $request->getUri());
		
		// Standard exception data
		$error = array (
				'code' => $exception->getCode(),
				'message' => $exception->getMessage(),
				'file' => $exception->getFile(),
				'line' => $exception->getLine() 
		);
		
		// Graceful error data for production mode
		if (! in_array(get_class($exception), array (
				'API\\Exception',
				'API\\Exception\ValidationException' 
		))) {
			//$error['message'] = 'There was an internal error';
			//unset($error['file'], $error['line']);
		}
		
		if ('application/json' === $mediaType || true === $isAPI) {
			//$response->headers->set('Content-Type', 'application/json');
			echo json_encode($error, JSON_PRETTY_PRINT);
		} else {
			echo '<html><head><title>Error</title></head><body><h1>Error: ' . $error['code'] . '</h1><p>' . $error['message'] . '</p></body></html>';
		}
	};
};

$container['notFoundHandler'] = function ($container) {
	return function ($request, $response) use($container) {
		$mediaType = $request->getMediaType();

	    $isAPI = (bool) preg_match('|^/api/v.*$|', $request->getUri());
	
	
	    if ('application/json' === $mediaType || true === $isAPI) {
	
	        $response->headers->set(
	            'Content-Type',
	            'application/json'
	        );
	
	        echo json_encode(
	            array(
	                'code' => 404,
	                'message' => 'Not found'
	            ),
	            JSON_PRETTY_PRINT
	        );
	
	    } else {
	        echo '<html><head><title>404 Page Not Found</title></head><body><h1>404 Page Not Found</h1><p>The page you are looking for could not be found.</p></body></html>';
	    }
	};
};

$app->run();

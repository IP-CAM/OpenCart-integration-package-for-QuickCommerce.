<?php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Common configuration
 */
$config = array();

$config['app']['mode'] = $_ENV['SLIM_MODE'];

// Cache TTL in seconds
$config['app']['cache.ttl'] = 60;

// Max requests per hour
$config['app']['rate.limit'] = 1000;

$log_path = realpath(__DIR__ . '/../logs') . '/'. $_ENV['SLIM_MODE'] . '_' .date('Y-m-d').'.log';

$logger = new Logger('pos_logger');
$logger->pushHandler(new StreamHandler($log_path, Logger::DEBUG));

$config['app']['logger'] = $logger;

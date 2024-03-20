<?php

require_once '../vendor/autoload.php';

use Dotenv\Dotenv;
use Src\Core\Request;
use Src\Core\RouteCollection;
use Src\Core\Dispatcher;

/**
 * Load environment variables
 */
if (strtolower($_ENV['ENVIRONMENT']) !== "production") {
  if (file_exists('../.env')) {
    $dotenv = Dotenv::createImmutable('../');
    $dotenv->load();
  }

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
}

/**
 * Start session
 */
if (isset($_COOKIE['PHPSESSID']) && !empty($_COOKIE['PHPSESSID'])) {
  session_id($_COOKIE['PHPSESSID']);
  session_start();
} else {
  session_start();
  setcookie('PHPSESSID', session_id(), time() + 3600, '/', '', false, true);
}

/**
 * Dispatch the request
 */
$request = new Request();
$routes  = new RouteCollection();
$dispatcher = new Dispatcher($request, $routes);

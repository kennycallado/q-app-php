<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Src\Core\Dispatcher;
use Src\Core\Request;
use Src\Core\RouteCollection;

/** Load environment variables */
if (!isset($_ENV['ENVIRONMENT']) || strtolower($_ENV['ENVIRONMENT']) !== 'production') {
    if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js)$/', $_SERVER['REQUEST_URI'])) {
        handle_statics($_SERVER['REQUEST_URI']);
        return;
    }

    if (file_exists(__DIR__ . '/../.env')) {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
    }

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

/** Start session */
if (isset($_COOKIE['PHPSESSID']) && !empty($_COOKIE['PHPSESSID'])) {
    session_id($_COOKIE['PHPSESSID']);
    session_start();
} else {
    session_start();
    setcookie('PHPSESSID', session_id(), time() + 3600, '/', '', false, true);
}

/** Dispatch the request */
$request = new Request();
$routes = new RouteCollection();
$dispatcher = new Dispatcher($request, $routes);

function handle_statics($temp_uri)
{
    if (preg_match('/^\/public\/assets\/bootstrap\/(.+)$/', $temp_uri)) {
        $temp_path = str_replace('/public/assets/bootstrap/', '', $temp_uri);

        set_headers($temp_uri);

        readfile(__DIR__ . '/../vendor/twbs/bootstrap/' . $temp_path);
        return;
    } else if (preg_match('/^\/public\/assets\/@microsoft\/(.+)$/', $temp_uri)) {
        $temp_path = str_replace('/public/assets/@microsoft/', '', $temp_uri);

        set_headers($temp_uri);

        readfile(__DIR__ . '/../node_modules/@microsoft/' . $temp_path);
        return;
    } else {
        set_headers($temp_uri);

        readfile(__DIR__ . "/..$temp_uri");
        return;
    }
}

function set_headers($temp_uri)
{
    switch (true) {
        case preg_match('/\.(?:css)$/', $temp_uri):
            header('Content-type: text/css');
            break;

        case preg_match('/\.(?:js)$/', $temp_uri):
            header('Content-Type: text/javascript');
            break;

        default:
            break;
    }
}

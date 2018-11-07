<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 07.11.2018
 * Time: 05:33
 */

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use Whoops\Run;
use Controller\HomeController;


// Set up error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set up Whoops
$whoops = new Run();
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

// Set up routing
$router = new AltoRouter();
$router->setBasePath('/public/');

// Load .env file
$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

// Create session
session_name('__Secure-PHPSESSID');
session_set_cookie_params(
    0,
    '/',
    'sfnw.online',
    true,
    true
);
session_start();
session_regenerate_id(true);

// Map routes
try {
    $router->map('GET', '', function (){
        require 'public/controllers/Home.php';}, 'home');
} catch (Exception $e) {
    throw new RuntimeException($e->getMessage());
}

$match = $router->match();

// Handle routing
if( $match && is_callable( $match['target'] ) ) {
    call_user_func_array( $match['target'], $match['params'] );
} else {
    // no route was matched
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    die('404');
}

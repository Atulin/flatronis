<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 07.11.2018
 * Time: 05:33
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/backend/models/Post.php';

use Symfony\Component\Dotenv\Dotenv;
use Whoops\Run;


// Set up error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Create session
session_name('__Secure-PHPSESSID');
session_set_cookie_params(
    0,
    '/',
    'flatronis.test',
    true,
    true
);
session_start();

// Set up Whoops
$whoops = new Run();
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

// Set up routing
$router = new AltoRouter();

// Load .env file
$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');



// Map routes
try {
    $router->addRoutes(array(
        array('GET', '/', function (){require 'public/controllers/Home.php';}, 'home'),
        array('GET', '/post/[i:id]/[:slug]?', function ($id){$id; require 'public/controllers/Post.php';}, 'post'),

        // Registration
        array('GET|POST', '/admin/register', function (){require 'public/controllers/admin/Register.php';}, 'register'),
    ));
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

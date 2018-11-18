<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 07.11.2018
 * Time: 05:33
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/backend/models/Post.php';
require_once __DIR__ . '/backend/models/Category.php';
require_once __DIR__ . '/backend/models/User.php';

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

// Check user status
$ipcheck = false;
if (isset($_SESSION['userid']) && !empty($_SESSION['userid'])) {
    $user = User::GetById($_SESSION['userid']);
    $ipcheck = password_verify($_SERVER['REMOTE_ADDR'], $user->getDevice());
}



// Map routes
try {
    $router->addRoutes(array(
        array('GET', '/', function (){require 'public/controllers/Home.php';}, 'home'),
        array('GET', '/post/[i:id]/[:slug]?', function ($id){$id; require 'public/controllers/Post.php';}, 'post'),

        // Registration
        array('GET|POST', '/admin/register', function (){
            require 'public/controllers/user/Register.php';}, 'register'),
    ));
} catch (Exception $e) {
    throw new RuntimeException($e->getMessage());
}

if (isset($_SESSION['userid']) && !empty($_SESSION['userid'])) {
    try {
        $router->addRoutes(array(
            array('GET', '/admin/dashboard', function () {require 'public/controllers/admin/Dashboard.php';}, 'dashboard'),
            array('GET|POST', '/admin/categories', function () {require 'public/controllers/admin/Categories.php';}, 'categories'),
        ));
    } catch (Exception $e) {
        throw new RuntimeException($e->getMessage());
    }
} else {
    try {
        $router->addRoutes(array(
            // Login
            array('GET|POST', '/admin/login', function () {require 'public/controllers/user/Login.php';}, 'login'),));
    } catch (Exception $e) {
        throw new RuntimeException($e->getMessage());
    }
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

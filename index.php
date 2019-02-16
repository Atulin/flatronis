<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 07.11.2018
 * Time: 05:33
 */

// Define directories
define('ROOT', __DIR__);
define('MODELS', ROOT.'/backend/models');
define('ASSETS', ROOT.'/public/assets');
define('CONTROLLERS', ROOT.'/public/controllers');
define('VIEWS', ROOT.'/public/views');

require_once ROOT . '/vendor/autoload.php';

use App\Models\User;

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Yaml\Yaml;
use Whoops\Run;

// Load settings file
define('SETTINGS', Yaml::parseFile('config.yaml'));

// Set up error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Create session
session_name('__Secure-PHPSESSID');
session_set_cookie_params(
    0,
    '/',
    SETTINGS['domain'],
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
$dotenv->load(ROOT.'/.env');

// Check user status
$ipcheck = false;
if (isset($_SESSION['userid']) && !empty($_SESSION['userid'])) {
    $user = User::GetById($_SESSION['userid']);
    $ipcheck = password_verify($_SERVER['REMOTE_ADDR'], $user->device);
}



// Map routes
try {
    $router->addRoutes([
        ['GET', '/', function (){require CONTROLLERS.'/Home.php';}, 'home'],
        ['GET', '/[i:p]?', function ($p=null){$p; require CONTROLLERS.'/Home.php';}, 'home-page'],
        ['GET', '/post/[i:id]/[:slug]?', function ($id){$id; require CONTROLLERS.'/Post.php';}, 'Models\Post'],

        // Registration
        ['GET|POST', '/admin/register', function (){require CONTROLLERS.'/user/Register.php';}, 'register'],
        ['GET|POST', '/admin/mfa', function (){require CONTROLLERS.'/user/MFA.php';}, 'mfa'],

//        ['GET|POST', '/d', function (){require __DIR__.'/dev.php';}]
    ]);
} catch (Exception $e) {
    throw new RuntimeException($e->getMessage());
}

// Logged-in only
if (isset($_SESSION['userid']) && !empty($_SESSION['userid'])) {
    try {
        $router->addRoutes([
            ['GET', '/admin', function () {require CONTROLLERS.'/admin/Dashboard.php';}, 'dashboard'],
            ['GET|POST', '/admin/categories', function () {require CONTROLLERS.'/admin/Categories.php';}, 'categories'],
            ['GET|POST', '/admin/posts', function () {require CONTROLLERS.'/admin/Posts.php';}, 'posts'],
            ['GET|POST', '/admin/editor', function () {require CONTROLLERS.'/admin/Editor.php';}, 'editor'],
        ]);
    } catch (Exception $e) {
        throw new RuntimeException($e->getMessage());
    }
} else {
    try {
        $router->addRoutes([
            // Login
            ['GET|POST', '/admin/login', function () {require CONTROLLERS.'/user/Login.php';}, 'login'],]);
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

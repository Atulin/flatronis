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
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

// Load settings file
define('SETTINGS', Yaml::parseFile('config.yaml'));

// Set up error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set UTF8
mb_internal_encoding('UTF-8');
$utf_set = ini_set('default_charset', 'utf-8');
if (!$utf_set) {
    echo "<pre>could not set default_charset to utf-8, please ensure it's set on your system!</pre>";
}
mb_http_output('UTF-8');
header('Content-Type: text/html; charset=UTF-8');

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
$whoops->pushHandler(new PrettyPageHandler);
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
    $ipcheck = password_verify($_SERVER['REMOTE_ADDR'], $user->getDevice());
}


// Map routes
try {
    $router->addRoutes([
        ['GET', '/', static function (){require CONTROLLERS.'/Home.php';}, 'home'],
        ['GET', '/[i:p]?', static function ($p=null){$p; require CONTROLLERS.'/Home.php';}, 'home-page'],
        ['GET', '/post/[i:id]/[:slug]?', static function ($id){$id; require CONTROLLERS.'/Post.php';}, 'Models\Post'],

        // API
        ['GET', '/api/posts', static function(){require CONTROLLERS.'/api/Posts.php';}, 'api-posts'],
        ['GET|POST', '/api/sitemap', static function(){require CONTROLLERS.'/api/Sitemap.php';}, 'api-sitemap'],

        // Registration
        ['GET|POST', '/admin/register', static function (){require CONTROLLERS.'/user/Register.php';}, 'register'],
        ['GET|POST', '/admin/mfa', static function (){require CONTROLLERS.'/user/MFA.php';}, 'mfa'],

//        ['GET|POST', '/d', function (){require __DIR__.'/dev.php';}]
    ]);
} catch (Exception $e) {
    throw new RuntimeException($e->getMessage());
}

// Logged-in only
if (isset($_SESSION['userid']) && !empty($_SESSION['userid'])) {
    try {
        $router->addRoutes([
            ['GET', '/admin', static function () {require CONTROLLERS.'/admin/Dashboard.php';}, 'dashboard'],
            ['GET|POST', '/admin/categories', static function () {require CONTROLLERS.'/admin/Categories.php';}, 'categories'],
            ['GET|POST', '/admin/posts', static function () {require CONTROLLERS.'/admin/Posts.php';}, 'posts'],
            ['GET|POST', '/admin/editor', static function () {require CONTROLLERS.'/admin/Editor.php';}, 'editor'],
            ['GET|POST', '/admin/access', static function () {require CONTROLLERS.'/admin/Access.php';}, 'access']
        ]);
    } catch (Exception $e) {
        throw new RuntimeException($e->getMessage());
    }
} else {
    try {
        $router->addRoutes([
            // Login
            ['GET|POST', '/admin/login', static function () {require CONTROLLERS.'/user/Login.php';}, 'login']]);
    } catch (Exception $e) {
        throw new RuntimeException($e->getMessage());
    }
}

$match = $router->match();

// Handle routing
if ( is_array($match) && is_callable( $match['target']) ) {
    call_user_func_array( $match['target'], $match['params'] );
} else {
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    die('404');
}

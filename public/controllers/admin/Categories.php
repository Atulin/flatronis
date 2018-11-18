<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 18.11.2018
 * Time: 06:23
 */

// Load up Twig stuff
$loader = new Twig_Loader_Filesystem(array(
        dirname(__DIR__,2).'/views',
        dirname(__DIR__,2).'/assets')
);
$twig = new Twig_Environment($loader);
$twig->addExtension(new Twig_Extensions_Extension_Text());
$twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) {
    return sprintf('/public/%s', ltrim($asset, '/'));
}));

// Set up variables
$user = User::GetById($_SESSION['userid']);

// Render Twig template
try {
    // Render the actual Twig template
    echo $twig->render('admin/categories.twig', array(
        'user' => $user,
        'categories' => Category::GetAll()
    ));

// Handle all possible errors
} catch (Twig_Error $e) {
    die('<pre>' . var_export($e, true) . '</pre>');
}

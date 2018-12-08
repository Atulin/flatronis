<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 17.11.2018
 * Time: 04:51
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
    echo $twig->render('admin/dashboard.twig', array(
        'navbar'  => SETTINGS['navbar'],
        'user'    => $user
    ));

// Handle all possible errors
} catch (Twig_Error $e) {
    die('<pre>'.var_export($e, true).'</pre>');
}

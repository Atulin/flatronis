<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 07.11.2018
 * Time: 06:11
 */

// Load up Twig stuff
$loader = new Twig_Loader_Filesystem(array(
        dirname(__DIR__,1).'/views',
        dirname(__DIR__,1).'/assets')
);
$twig = new Twig_Environment($loader);
$twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) {
    return sprintf('/public/%s', ltrim($asset, '/'));
}));

// Set up variables
$msg = (new Database())->connect();

// Render Twig template
try {
    // Render the actual Twig template
    echo $twig->render('home.twig', array(
        var_export($msg, true),
    ));

    // Handle all possible errors
} catch (Twig_Error_Loader $e) {
    header('Content-type: application/json');
    echo json_encode('Error [1]: '.$e);
} catch (Twig_Error_Runtime $e) {
    header('Content-type: application/json');
    echo json_encode('Error [2]: '.$e);
} catch (Twig_Error_Syntax $e) {
    header('Content-type: application/json');
    echo json_encode('Error [3]: '.$e);
}

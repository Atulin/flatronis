<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 17.11.2018
 * Time: 04:51
 */

// Load up Twig stuff
$loader = new Twig_Loader_Filesystem(array(
        dirname(__DIR__,1).'/views',
        dirname(__DIR__,1).'/assets')
);
$twig = new Twig_Environment($loader);
$twig->addExtension(new Twig_Extensions_Extension_Text());
$twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) {
    return sprintf('/public/%s', ltrim($asset, '/'));
}));

// Set up variables
try {
    $posts = Post::GetAll();
} catch (PDOException $e) {
    $posts[0]['body'] = $e;
}

// Render Twig template
try {
    // Render the actual Twig template
    echo $twig->render('home.twig', array(
        'posts' => $posts
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

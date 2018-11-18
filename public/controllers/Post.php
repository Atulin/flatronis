<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 11.11.2018
 * Time: 02:04
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
    $post = Post::Get($id);
} catch (PDOException $e) {
    $post['body'] = $e;
}

// Render Twig template
try {
    // Render the actual Twig template
    echo $twig->render('post.twig', array(
        'post' => $post
    ));


// Handle all possible errors
} catch (Twig_Error $e) {
    die('<pre>'.var_export($e, true).'</pre>');
}


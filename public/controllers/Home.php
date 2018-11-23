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
        'posts' => $posts,
        'parallax' => SETTINGS['parallax']
    ));


// Handle all possible errors
} catch (Twig_Error$e) {
    echo '<pre>'.var_export($e, true).'</pre>';
}

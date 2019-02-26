<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 11.11.2018
 * Time: 02:04
 */

// Load up Twig stuff
use App\Helpers\Twig;
use App\Models\Post;

$twig = Twig::Load();

// Set up variables
try {
    $post = Post::Get($id);
} catch (PDOException $e) {
    $post['body'] = $e;
}

// Render Twig template
try {
    // Render the actual Twig template
    echo $twig->render('post.twig', [
        'navbar' => SETTINGS['navbar'],
        'analytics'=> $_ENV['ANALYTICS'],
        'post' => $post
    ]);


// Handle all possible errors
} catch (Twig_Error $e) {
    die('<pre>'.var_export($e, true).'</pre>');
}


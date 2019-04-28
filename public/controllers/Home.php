<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 07.11.2018
 * Time: 06:11
 */

// Load up Twig stuff
use App\Models\Post;
use App\Helpers\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

$twig = Twig::Load();


// Set up variables
$total_pages = ceil(Post::Count() / SETTINGS['posts']);

try {
    $posts = Post::GetAll(SETTINGS['posts'], SETTINGS['posts'] * (($p ?? 1) - 1));
} catch (PDOException $e) {
    $posts[0]['body'] = $e;
}

if (empty($posts)) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    die('404');
}

// Render Twig template

// Render the actual Twig template
try {
    echo $twig->render('home.twig', [
        'posts' => $posts,
        'navbar' => SETTINGS['navbar'],
        'header' => true,
        'analytics' => $_ENV['ANALYTICS'],
        'parallax' => SETTINGS['parallax'],
        'page' => $p ?? 1,
        'total' => $total_pages
    ]);
} catch (LoaderError $e) {
    echo '<pre>'.var_export($e, true).'</pre>';
} catch (RuntimeError $e) {
    echo '<pre>'.var_export($e, true).'</pre>';
} catch (SyntaxError $e) {
    echo '<pre>'.var_export($e, true).'</pre>';
}


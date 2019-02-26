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

$twig = Twig::Load();


/**
 * @param $search
 * @param $replace
 * @param $subject
 * @return mixed
 */
function str_lreplace($search, $replace, $subject)
{
    $pos = strrpos($subject, $search);
    if($pos !== false)
    {
        $subject = substr_replace($subject, $replace, $pos, \strlen($search));
    }
    return $subject;
}


// Set up variables
try {
    $posts = Post::GetAll(SETTINGS['posts'], SETTINGS['posts'] * (($p ?? 1) - 1));
} catch (PDOException $e) {
    $posts[0]['body'] = $e;
}

// Render Twig template
try {
    // Render the actual Twig template
    echo $twig->render('home.twig', [
        'posts'    => $posts,
        'navbar'   => SETTINGS['navbar'],
        'header'   => true,
        'analytics'=> $_ENV['ANALYTICS'],
        'parallax' => SETTINGS['parallax'],
        'page'     => $p ?? 1
    ]);


// Handle all possible errors
} catch (Twig_Error$e) {
    echo '<pre>'.var_export($e, true).'</pre>';
}

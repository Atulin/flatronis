<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 19.11.2018
 * Time: 03:20
 */

// Load up Twig stuff
use App\Models\Post;
use App\Models\User;
use App\Helpers\Twig;

$twig = Twig::Load();


//Delete
if (isset($_GET['del']) && !empty($_GET['token']) && !empty($_GET['del'])) {
    // Check XCSRF
    if ($_GET['token'] === $_SESSION['token']) {
        Post::Delete($_GET['del']);
        header('Location: /admin/posts');
    } else {
        die('XCSRF triggered');
    }
}


// Set up variables
$user = User::GetById($_SESSION['userid']);

// Token
try {
    $token = bin2hex(random_bytes(64));
} catch (Exception $e) {
    echo $e->getMessage();
}
$_SESSION['token'] = $token;

// Render Twig template
try {
    // Render the actual Twig template
    echo $twig->render('admin/posts.twig', [
        'user'   => $user,
        'navbar' => SETTINGS['navbar'],
        'token'  => $token,
        'posts'  => Post::GetAll()
    ]);

// Handle all possible errors
} catch (Twig_Error $e) {
    die('<pre>'.var_export($e, true).'</pre>');
}

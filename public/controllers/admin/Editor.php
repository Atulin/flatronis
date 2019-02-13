<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 25.11.2018
 * Time: 03:58
 */

// Load up Twig stuff
$loader = new Twig_Loader_Filesystem(array(VIEWS, ASSETS));

$twig = new Twig_Environment($loader);
$twig->addExtension(new Twig_Extensions_Extension_Text());
$twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) {
    return sprintf('/public/%s', ltrim($asset, '/'));
}));


//Delete
if (!empty($_POST['token'])) {
    // Check XCSRF
    if ($_POST['token'] === $_SESSION['token']) {

        if (!empty($_POST['id'])) {
            $p = new Post(
                $_POST['id'],
                $_POST['title'],
                User::GetById($_POST['author']),
                DateTime::createFromFormat('d.m.Y H:i:s', $_POST['date']),
                Category::Get($_POST['category']),
                $_POST['body']);
            $p->Update();
        } else {
            $p = new Post(0,
                $_POST['title'],
                User::GetById($_POST['author']),
                DateTime::createFromFormat('d.m.Y H:i:s', $_POST['date']),
                Category::Get($_POST['category']),
                $_POST['body']);
            $p->Add();
        }

    } else {
        die('XCSRF triggered');
    }
}


// Set up variables
$user = User::GetById($_SESSION['userid']);
$post = !empty($_GET['id']) ? Post::Get($_GET['id']) : ($p ?? null);

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
    echo $twig->render('admin/editor.twig', array(
        'user'       => $user,
        'token'      => $token,
        'navbar'     => SETTINGS['navbar'],
        'now'        => date('d.m.Y H:i:s'),
        'users'      => User::GetAll(),
        'categories' => Category::GetAll(),
        'post'       => $post
    ));

// Handle all possible errors
} catch (Twig_Error $e) {
    die('<pre>'.var_export($e, true).'</pre>');
}

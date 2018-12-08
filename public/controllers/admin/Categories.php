<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 18.11.2018
 * Time: 06:23
 */

// Load up Twig stuff
$loader = new Twig_Loader_Filesystem(array(
        dirname(__DIR__, 2) . '/views',
        dirname(__DIR__, 2) . '/assets')
);
$twig = new Twig_Environment($loader);
$twig->addExtension(new Twig_Extensions_Extension_Text());
$twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) {
    return sprintf('/public/%s', ltrim($asset, '/'));
}));


// Add or Edit
if (!empty($_POST) && isset($_POST)) {
    // Check XCSRF
    if ($_POST['token'] === $_SESSION['token']) {

        // Add
        if (empty($_POST['id'])) {
            $c = new Category(
                0,
                $_POST['name'],
                $_POST['description']
            );
            $c->Add();
        // Edit
        } else {
            $c = new Category(
                $_POST['id'],
                $_POST['name'],
                $_POST['description']
            );
            $c->Update();
        }

    } else {
        die('XCSRF triggered');
    }

    header('Location: /admin/categories');
}

//Delete
if (isset($_GET['del']) && !empty($_GET['token']) && !empty($_GET['del'])) {
    // Check XCSRF
    if ($_GET['token'] === $_SESSION['token']) {
        Category::Delete($_GET['del']);
        header('Location: /admin/categories');
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
    echo $twig->render('admin/categories.twig', array(
        'user'       => $user,
        'navbar'     => SETTINGS['navbar'],
        'categories' => Category::GetAll(),
        'token'      => $token
    ));

// Handle all possible errors
} catch (Twig_Error $e) {
    die('<pre>' . var_export($e, true) . '</pre>');
}

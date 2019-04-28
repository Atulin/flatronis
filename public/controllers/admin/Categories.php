<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 18.11.2018
 * Time: 06:23
 */

// Load up Twig stuff
use App\Models\Category;
use App\Models\User;
use App\Helpers\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

$twig = Twig::Load();


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
    echo $twig->render('admin/categories.twig', [
        'user'       => $user,
        'navbar'     => SETTINGS['navbar'],
        'categories' => Category::GetAll(),
        'token'      => $token
    ]);

// Handle all possible errors
} catch (LoaderError $e) {
    echo '<pre>'.var_export($e, true).'</pre>';
} catch (RuntimeError $e) {
    echo '<pre>'.var_export($e, true).'</pre>';
} catch (SyntaxError $e) {
    echo '<pre>'.var_export($e, true).'</pre>';
}

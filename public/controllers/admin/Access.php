<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 03.03.2019
 * Time: 02:18
 */

// Load up Twig stuff
use App\Models\APIAuth;
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
            try {
                $api = new APIAuth(
                    null,
                    $_POST['name'],
                    $_POST['comment']
                );
                $api->GenerateKeys();
                $api->Add();
            } catch (Exception $e) {
                die($e->getMessage());
            }
            // Edit
        } else {
            try {
                $api = APIAuth::Get($_POST['id']);
            } catch (Exception $e) {
                die($e->getMessage());
            }
            $api->name = $_POST['name'];
            $api->comment = $_POST['comment'];
            $api->Update();
        }

    } else {
        die('XCSRF triggered');
    }

    header('Location: /admin/access');
}

//Delete
if (isset($_GET['del']) && !empty($_GET['token']) && !empty($_GET['del'])) {
    // Check XCSRF
    if ($_GET['token'] === $_SESSION['token']) {
        APIAuth::Delete($_GET['del']);
        header('Location: /admin/access');
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
    echo $twig->render('admin/access.twig', [
        'user'       => $user,
        'navbar'     => SETTINGS['navbar'],
        'keys'       => APIAuth::GetAll(),
        'token'      => $token
    ]);

// Handle all possible errors
} catch (LoaderError $e) {
    echo '<pre>'.var_export($e, true).'</pre>';
} catch (RuntimeError $e) {
    echo '<pre>'.var_export($e, true).'</pre>';
} catch (SyntaxError $e) {
    echo '<pre>'.var_export($e, true).'</pre>';
} catch (Exception $e) {
    echo '<pre>'.var_export($e, true).'</pre>';
}

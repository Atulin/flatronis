<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 17.11.2018
 * Time: 03:24
 */
// Load up Twig stuff
use App\Helpers\Twig;
use App\Models\User;
use RobThree\Auth\TwoFactorAuthException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

$twig = Twig::Load();

// Set up 2FA
try {
    $tfa = new RobThree\Auth\TwoFactorAuth('Erronis Games');
} catch (TwoFactorAuthException $e) {
    echo '<pre>' . var_export($e, true) . '</pre>';
}

// Login
if (!empty($_POST) && isset($_POST)) {

    // Get user
    $u = User::GetByName($_POST['login']);

//    echo '<pre>'.var_export($u, true).'</pre>';
//    echo '<pre>'.var_export($_POST, true).'</pre>';

    // Check device
    $device = password_hash($_SERVER['REMOTE_ADDR'], PASSWORD_ARGON2I);
    $same_device = password_verify($_SERVER['REMOTE_ADDR'], $u->getDevice());

    // Verify 2FA
    $result = $tfa->verifyCode($u->getMfa(), $_POST['2fa'] ?? null);

//    echo '<pre>'.var_export($result, true).'</pre>';
//    echo '<pre>'.var_export($same_device, true).'</pre>';
//    echo '<pre>'.var_export($_SESSION, true).'</pre>';

    if ($result || $same_device) {

        // Check XCSRF
        if ($_POST['token'] === $_SESSION['token']) {

            // Check password
            if (password_verify($_POST['password'], $u->getPassword())) {

                $_SESSION['userid'] = $u->id;
                $u->UpdateDevice($device);
                header('Location: /');
                die();
            }

            header('Location: /admin/login');
            die();
        }

        die('XCSRF triggered');
    }
}

// Set up 2FA secret
try {
    $secret = $tfa->createSecret();
} catch (TwoFactorAuthException $e) {
    $secret = null;
    echo '<pre>' . var_export($e, true) . '</pre>';
}
$_SESSION['secret'] = $secret;

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
    echo $twig->render('user/login.twig', [
        'token'  => $token,
        'navbar' => SETTINGS['navbar']
    ]);

// Handle all possible errors
} catch (LoaderError $e) {
    echo '<pre>'.var_export($e, true).'</pre>';
} catch (RuntimeError $e) {
    echo '<pre>'.var_export($e, true).'</pre>';
} catch (SyntaxError $e) {
    echo '<pre>'.var_export($e, true).'</pre>';
}

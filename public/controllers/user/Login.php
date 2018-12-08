<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 17.11.2018
 * Time: 03:24
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

// Set up 2FA
try {
    $tfa = new RobThree\Auth\TwoFactorAuth('Erronis Games');
} catch (\RobThree\Auth\TwoFactorAuthException $e) {
    echo '<pre>' . var_export($e, true) . '</pre>';
}

// Login
if (!empty($_POST) && isset($_POST)) {

    // Get user
    $u = User::GetByName($_POST['login']);

    // Verify 2FA
    $result = $tfa->verifyCode($u->mfa, $_POST['2fa']);
    if ($result) {

        // Check XCSRF
        echo '<pre>'.var_export($_POST, true).'</pre>';
        echo '<pre>'.var_export($_SESSION, true).'</pre>';
        if ($_POST['token'] === $_SESSION['token']) {
            $_SESSION['userid'] = $u->id;
            header('Location: /');
            die();
        }

        die('XCSRF triggered');

    }

    echo var_export($result, true);

}

// Set up 2FA secret
try {
    $secret = $tfa->createSecret();
} catch (\RobThree\Auth\TwoFactorAuthException $e) {
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
    echo $twig->render('user/login.twig', array(
        'token' => $token,
    ));

// Handle all possible errors
} catch (Twig_Error $e) {
    die('<pre>'.var_export($e, true).'</pre>');
}

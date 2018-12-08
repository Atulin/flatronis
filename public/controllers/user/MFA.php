<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 25.11.2018
 * Time: 03:12
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

    // Check XCSRF
    if ($_POST['token'] === $_SESSION['token']) {

        // Get user
        $u = User::GetByName($_SESSION['name']);

        // Verify 2FA
        $result = $tfa->verifyCode($_SESSION['secret'], $_POST['2fa']);
        if ($result) {
            $u->mfa = $_SESSION['secret'];
            $u->Modify();
        }
    } else {
        die('XCSRF triggered');
    }

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

// Set up variables
try {
    $qr = $tfa->getQRCodeImageAsDataUri('Erronis Games', $secret);
} catch (\RobThree\Auth\TwoFactorAuthException $e) {
    echo '<pre>' . var_export($e, true) . '</pre>';
}

// Render Twig template
try {
    // Render the actual Twig template
    echo $twig->render('user/mfa.twig', array(
        'token'  => $token,
        'navbar' => SETTINGS['navbar'],
        'qr'     => $qr,
    ));

// Handle all possible errors
} catch (Twig_Error $e) {
    die('<pre>' . var_export($e, true) . '</pre>');
}

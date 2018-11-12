<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 11.11.2018
 * Time: 23:07
 */

// Load up Twig stuff
$loader = new Twig_Loader_Filesystem(array(
        dirname(__DIR__,2).'/views',
        dirname(__DIR__,2).'/assets')
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
    echo '<pre>'.var_export($e, true).'</pre>';
}

// Register
if (!empty($_POST) && isset($_POST)) {
    echo '<pre>'.var_export($_SESSION, true).'</pre>';
    //echo $tfa->verifyCode($_SESSION['secret'], $_POST['2fa']);
}

// Set up 2FA secret
try {
    $secret = $tfa->createSecret();
} catch (\RobThree\Auth\TwoFactorAuthException $e) {
    $secret = null;
    echo '<pre>'.var_export($e, true).'</pre>';
}
$_SESSION['secret'] = $secret;

// Set up variables
try {
    $qr = $tfa->getQRCodeImageAsDataUri('Erronis Games', $secret);
} catch (\RobThree\Auth\TwoFactorAuthException $e) {
    echo '<pre>'.var_export($e, true).'</pre>';
}


// Render Twig template
try {
    // Render the actual Twig template
    echo $twig->render('admin/register.twig', array(
        'qr' => $qr,
    ));

    // Handle all possible errors
} catch (Twig_Error_Loader $e) {
    header('Content-type: application/json');
    echo json_encode('Error [1]: '.$e);
} catch (Twig_Error_Runtime $e) {
    header('Content-type: application/json');
    echo json_encode('Error [2]: '.$e);
} catch (Twig_Error_Syntax $e) {
    header('Content-type: application/json');
    echo json_encode('Error [3]: '.$e);
}

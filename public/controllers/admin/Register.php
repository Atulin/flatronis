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
    // Verify captcha
    $recaptcha = new \ReCaptcha\ReCaptcha($_ENV['CAPTCHA_S']);
    $resp = $recaptcha->verify($_POST['g-recaptcha-response']);
    if ($resp->isSuccess()) {

        // Check XCSRF
        if ($_POST['token'] === $_SESSION['token']) {
            $u = new User('',
                $_POST['login'],
                $_POST['email'],
                password_hash($_POST['password'], PASSWORD_BCRYPT),
                $_POST['2fa'],
                password_hash($_SERVER['REMOTE_ADDR'], PASSWORD_BCRYPT)
            );
            $u->Add();
        } else {
            die('XCSRF triggered');
        }

    } else {
        $errors = $resp->getErrorCodes();
        echo var_export($errors, true);
    }
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
    echo $twig->render('admin/register.twig', array(
        'qr'    => $qr,
        'token' => $token,
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

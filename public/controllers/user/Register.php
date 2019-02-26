<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 11.11.2018
 * Time: 23:07
 */

// Load up Twig stuff
use App\Helpers\Twig;
use App\Models\User;

$twig = Twig::Load();

// Set up 2FA
try {
    $tfa = new RobThree\Auth\TwoFactorAuth('Erronis Games');
} catch (\RobThree\Auth\TwoFactorAuthException $e) {
    echo '<pre>'.var_export($e, true).'</pre>';
}



// Register
if (!empty($_POST) && isset($_POST)) {

    // Validate and sanitize POST
    $POST = filter_input_array(INPUT_POST, [
        'token'    => FILTER_SANITIZE_STRING,
        'login'    => FILTER_SANITIZE_STRING,
        'email'    => FILTER_VALIDATE_EMAIL,
        'password' => FILTER_DEFAULT
    ]);

    // Verify captcha
    $recaptcha = new \ReCaptcha\ReCaptcha($_ENV['CAPTCHA_S']);
    $resp = $recaptcha->verify($_POST['g-recaptcha-response']);
    if ($resp->isSuccess()) {

        // Check XCSRF
        if ($POST['token'] === $_SESSION['token']) {
            $u = new User(0, $POST['login']);
            $u->setEmail($POST['email']);
            $u->setPassword(password_hash($POST['password'], PASSWORD_ARGON2I));
            $u->setDevice(password_hash($_SERVER['REMOTE_ADDR'], PASSWORD_ARGON2I));
            $u->Add();
            $_SESSION['name'] = $POST['login'];
            header('Location: /admin/mfa');
            die();

        }

        die('XCSRF triggered');

    }

    $errors = $resp->getErrorCodes();
    echo var_export($errors, true);
}


// Set up variables

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
    echo $twig->render('user/register.twig', [
        'token'   => $token,
        'navbar'  => SETTINGS['navbar'],
        'captcha' => $_ENV['CAPTCHA']
    ]);

// Handle all possible errors
} catch (Twig_Error $e) {
    die('<pre>'.var_export($e, true).'</pre>');
}

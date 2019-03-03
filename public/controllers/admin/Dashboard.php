<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 17.11.2018
 * Time: 04:51
 */

// Load up Twig stuff
use App\Models\User;
use App\Helpers\Twig;

$twig = Twig::Load();

// Set up variables
$user = User::GetById($_SESSION['userid']);


// Render Twig template
try {
    // Render the actual Twig template
    echo $twig->render('admin/dashboard.twig', [
        'navbar'  => SETTINGS['navbar'],
        'user'    => $user
    ]);

// Handle all possible errors
} catch (Twig_Error $e) {
    die('<pre>'.var_export($e, true).'</pre>');
}

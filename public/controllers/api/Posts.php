<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 17.02.2019
 * Time: 04:11
 */

use App\Models\APIAuth;
use App\Models\Post;

header('Content-type: application/json');

$errors = [];

// Check if any credentials are set
if (isset($_GET['client_id'], $_GET['client_key'])) {
    try {
        $auth = APIAuth::Get($_GET['client_id']);
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }

    // Check if key is valid
    $valid = $auth ? $auth->Check($_GET['client_key']) : false;
    if ($valid) {

        // Check if client requested any specific post
        if (isset($_GET['id'])) {
            $out = Post::Get(
                $_GET['id']
            );
        } else {
            $out = Post::GetAll(
                $_GET['limit'] ?? null,
                $_GET['offset'] ?? null
            );
        }

        $json = json_encode($out, JSON_PRETTY_PRINT, 5);

        if($json) {
            echo $json;
        } else {
            $errors[] = check_error(json_last_error());
        }

    } else {
        $errors[] = 'Invalid credentials';
    }
} else {
    $errors[] = 'No user credentials';
}

if ($errors) {
    echo json_encode($errors);
}
die();


/**
 * Translate json errors into human-readable format
 * @param $json_last_error
 * @return string
 */
function check_error($json_last_error): string
{
    switch ($json_last_error) {
        case JSON_ERROR_NONE:
            return 'No errors';
            break;
        case JSON_ERROR_DEPTH:
            return 'Maximum stack depth exceeded';
            break;
        case JSON_ERROR_STATE_MISMATCH:
            return 'Underflow or the modes mismatch';
            break;
        case JSON_ERROR_CTRL_CHAR:
            return 'Unexpected control character found';
            break;
        case JSON_ERROR_SYNTAX:
            return 'Syntax error, malformed JSON';
            break;
        case JSON_ERROR_UTF8:
            return 'Malformed UTF-8 characters, possibly incorrectly encoded';
            break;
        default:
            return 'Unknown error';
            break;
    }
}

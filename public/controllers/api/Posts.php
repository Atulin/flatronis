<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 17.02.2019
 * Time: 04:11
 */

use App\Models\APIAuth;
use App\Models\Post;

// Validate and sanitize GET
$GET = filter_input_array(INPUT_GET, [
    'client_id'  => FILTER_SANITIZE_STRING,
    'client_key' => FILTER_SANITIZE_STRING,
    'id'         => FILTER_VALIDATE_INT,
    'limit'      => FILTER_VALIDATE_INT,
    'offset'     => FILTER_VALIDATE_INT
]);

header('Content-type: application/json');

$errors = [];

// Check if any credentials are set
if (isset($GET['client_id'], $GET['client_key'])) {
    try {
        $auth = APIAuth::Get($GET['client_id']);
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }

    // Check if key is valid
    $valid = $auth ? $auth->Check($GET['client_key']) : false;
    if ($valid) {

        // Check if client requested any specific post
        if (isset($GET['id'])) {
            $out = Post::Get(
                $GET['id']
            );
        } else {
            $out = Post::GetAll(
                $GET['limit'] ?? null,
                $GET['offset'] ?? null
            );
        }

        $json = json_encode($out, JSON_PRETTY_PRINT, 5);

        if ($json) {
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

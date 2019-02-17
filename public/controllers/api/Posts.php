<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 17.02.2019
 * Time: 04:11
 */

use App\Models\Post;

header('Content-type: application/json');


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
    check_error(json_last_error());
}
die();


/**
 * @param $json_last_error
 */
function check_error($json_last_error): void
{
    switch ($json_last_error) {
        case JSON_ERROR_NONE:
            echo ' - No errors';
            break;
        case JSON_ERROR_DEPTH:
            echo ' - Maximum stack depth exceeded';
            break;
        case JSON_ERROR_STATE_MISMATCH:
            echo ' - Underflow or the modes mismatch';
            break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Unexpected control character found';
            break;
        case JSON_ERROR_SYNTAX:
            echo ' - Syntax error, malformed JSON';
            break;
        case JSON_ERROR_UTF8:
            echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
            break;
        default:
            echo ' - Unknown error';
            break;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 07.03.2019
 * Time: 21:58
 */

use App\Helpers\Log;
use App\Helpers\Sitemap;

$log = new Log();

$log->Write('post: '.var_export($_REQUEST, true));

if (isset($_POST) && !empty($_POST)) {
    Sitemap::Create($_POST['data']);
} else {
    try {
        $sitemap = new Sitemap('http://' . SETTINGS['domain'] . '/api/sitemap');
    } catch (Exception $e) {
        $log->Write($e->getMessage());
    }
//    $sitemap->GetSitemap();
}

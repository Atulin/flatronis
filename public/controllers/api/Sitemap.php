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

if ($_POST) {
    Sitemap::Create($_POST['data']);
} else {
    try {
        $sitemap = new Sitemap('https://' . SETTINGS['domain'] . '/api/sitemap');
    } catch (Exception $e) {
        $log->Write($e->getMessage());
    }
    $sitemap->GetSitemap();
}

<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 26.02.2019
 * Time: 00:19
 */

namespace App\Helpers;

use Twig_Environment;
use Twig_Extensions_Extension_Text;
use Twig_Loader_Filesystem;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

/**
 * Class Twig
 * @package App\Helpers
 */
class Twig
{
    /**
     * @return Twig_Environment
     */
    public static function Load(): Twig_Environment
    {
        $loader = new Twig_Loader_Filesystem([VIEWS, ASSETS]);

        $twig = new Twig_Environment($loader);
        $twig->addExtension(new Twig_Extensions_Extension_Text());

        $twig->addFunction(new Twig_SimpleFunction('asset', function ($asset) {
            return sprintf('/public/%s', ltrim($asset, '/'));
        }));

        $twig->addFilter(new Twig_SimpleFilter('break', function ($string) {
            $out = explode('<p><!-- pagebreak --></p>', $string)[0];
            $out = str_lreplace('</p>', ' ...</p>', $out);
            return $out;
        }));

        $twig->addFunction(new Twig_SimpleFunction('versioned', function ($asset, $extension){
            $filename = ASSETS . "/css/$asset.$extension";
            if (file_exists($filename)) {
                $timestamp = filemtime($filename);
                return ASSETS . "/css/$asset.$timestamp.$extension";
            }
            return null;
        }));

        return $twig;
    }
}

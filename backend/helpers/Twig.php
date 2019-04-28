<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 26.02.2019
 * Time: 00:19
 */

namespace App\Helpers;

use function strlen;
use Twig\Environment;
use Twig_Extensions_Extension_Text;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class Twig
 * @package App\Helpers
 */
class Twig
{
    /**
     * @return Environment
     */
    public static function Load(): Environment
    {
        $loader = new FilesystemLoader([VIEWS, ASSETS]);

        $twig = new Environment($loader);
        $twig->addExtension(new Twig_Extensions_Extension_Text());

        // Load an asset
        $twig->addFunction(new TwigFunction('asset', static function ($asset) {
            return sprintf('/public/%s', ltrim($asset, '/'));
        }));

        // Load a versioned asset
        $twig->addFunction(new TwigFunction('versioned', static function ($asset, $extension) {
            $filename = ASSETS."/$asset.$extension";
            if (file_exists($filename)) {
                $timestamp = filemtime($filename);
                return "/public/assets/$asset.$timestamp.$extension";
            }
            return "/public/assets/$asset.$extension";
        }));

        $twig->addFilter(new TwigFilter('break', static function ($string) {
            $out = explode('<p><!-- pagebreak --></p>', $string)[0];
            $out = str_lreplace('</p>', ' ...</p>', $out);
            return $out;
        }));

        return $twig;
    }
}

/**
 * @param $search
 * @param $replace
 * @param $subject
 * @return mixed
 */
function str_lreplace($search, $replace, $subject)
{
    $pos = strrpos($subject, $search);
    if($pos !== false)
    {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject;
}

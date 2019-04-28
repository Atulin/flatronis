<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 07.03.2019
 * Time: 21:50
 */

namespace App\Helpers;


/**
 * Class Log
 * @package App\Helpers
 */
class Logger
{
    private $handler;

    /**
     * Log constructor.
     */
    public function __construct()
    {
        $this->handler = fopen(ROOT.'/log.txt', 'ab') or die('Unable to open file!');
    }

    /**
     * @param string $data
     */
    public function Write(string $data): void
    {
        $now = date('r');
        fwrite($this->handler, "$now >> $data\r\n");
        fclose($this->handler);
    }
}

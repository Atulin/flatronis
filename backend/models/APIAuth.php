<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 18.02.2019
 * Time: 04:37
 */

namespace App\Models;

/**
 * Class APIAuth
 * @package App\Models
 */
class APIAuth
{
    public $name;
    public $comment;
    private $public_key;
    private $private_key;

    /**
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->public_key;
    }

    /**
     * @return string
     */
    public function getPrivateKey(): string
    {
        return $this->private_key;
    }

    /**
     * ApiAuth constructor.
     * @param $name
     * @param $comment
     * @param int $power
     * @throws \Exception
     */
    public function __construct($name, $comment, $power = 1024)
    {
        $this->name        = $name;
        $this->comment     = $comment;
        $this->public_key  = bin2hex(random_bytes($power));
        $this->private_key = bin2hex(random_bytes($power));
    }


}

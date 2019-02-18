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
    private $cipher = 'aes-128-gcm';


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


    /**
     * @return string
     * @throws \Exception
     */
    public function Encrypt(): string
    {
        $data       = $this->public_key;
        $key        = $this->private_key;
        $cipher     = $this->cipher;
        $iv_len     = openssl_cipher_iv_length($cipher);
        $tag_length = 16;
        $iv         = random_bytes($iv_len);
        $tag        = ''; // will be filled by openssl_encrypt

        $ciphertext = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag, '', $tag_length);

        return base64_encode($iv.$tag.$ciphertext);
    }

    /**
     * @param string $received
     * @return bool
     */
    public function Check(string $received): bool
    {
        $public     = $this->public_key;
        $key        = $this->private_key;
        $cipher     = $this->cipher;
        $encrypted  = base64_decode($received);
        $iv_len     = openssl_cipher_iv_length($cipher);
        $tag_length = 16;
        $iv         = substr($encrypted, 0, $iv_len);
        $tag        = substr($encrypted, $iv_len, $tag_length);
        $ciphertext = substr($encrypted, $iv_len + $tag_length);

        $decrypted = openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag);

        return $public === $decrypted;
    }
}

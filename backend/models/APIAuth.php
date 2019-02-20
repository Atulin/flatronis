<?php

/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 18.02.2019
 * Time: 04:37
 */

namespace App\Models;

use PDO;
use PDOException;

/**
 * Class APIAuth
 * @package App\Models
 */
class APIAuth
{
    public $id;
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
     * @param string $id
     * @param string $name
     * @param string $comment
     * @param int $power
     * @param string $public
     * @param string $private
     * @throws \Exception
     */
    public function __construct(string $id, string $name, string $comment,
                                ?int $power = 1024, string $public = null, string $private = null)
    {
        $this->id          = $id;
        $this->name        = $name;
        $this->comment     = $comment;
        $this->public_key  = $public ?? bin2hex(random_bytes($power));
        $this->private_key = $private ?? bin2hex(random_bytes($power));
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

        $cipher_text = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag, '', $tag_length);

        return base64_encode($iv.$tag.$cipher_text);
    }

    /**
     * @param string $received
     * @return bool
     */
    public function Check(string $received): bool
    {
        $public      = $this->public_key;
        $key         = $this->private_key;
        $cipher      = $this->cipher;
        $encrypted   = base64_decode($received);
        $iv_len      = openssl_cipher_iv_length($cipher);
        $tag_length  = 16;
        $iv          = substr($encrypted, 0, $iv_len);
        $tag         = substr($encrypted, $iv_len, $tag_length);
        $cipher_text = substr($encrypted, $iv_len + $tag_length);

        $decrypted = openssl_decrypt($cipher_text, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag);

        return $public === $decrypted;
    }

    /**
     * @param string $id
     * @return APIAuth
     * @throws \Exception
     */
    public static function Get(string $id): APIAuth
    {
        $dbh = Database::Get();
        $sql = 'SELECT * FROM api_keys
                WHERE id = :id
                LIMIT 1';

        $sth = $dbh->prepare($sql);

        $sth->bindParam(':id', $id);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw $e;
        }

        $api_auth = $sth->fetch(PDO::FETCH_ASSOC);

        return new APIAuth(
            $api_auth['id'],
            $api_auth['name'],
            $api_auth['comment'],
            null,
            $api_auth['public_key'],
            $api_auth['private_key']
        );
    }

    /**
     *
     */
    public function Add(): void
    {
        $dbh = Database::Get();
        $sql = 'INSERT INTO api_keys (id, name, comment, public_key, private_key)
                VALUES (:id, :name, :comment, :pubkey, :privkey)';

        $sth = $dbh->prepare($sql);

        $sth->bindParam(':id',      $this->id);
        $sth->bindParam(':name',    $this->name);
        $sth->bindParam(':comment', $this->comment);
        $sth->bindParam(':pubkey',  $this->public_key);
        $sth->bindParam(':privkey', $this->private_key);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * @param string $id
     */
    public static function Delete(string $id): void
    {
        $dbh = Database::Get();
        $sql = 'DELETE FROM api_keys WHERE `id` = :id';
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':id', $id);
        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     *
     */
    public function Update(): void
    {
        $dbh = Database::Get();
        $sql = 'UPDATE api_keys 
                SET name = :name, comment = :comment
                WHERE id = :id';

        $sth = $dbh->prepare($sql);

        $sth->bindParam(':id',      $this->id);
        $sth->bindParam(':name',    $this->name);
        $sth->bindParam(':comment', $this->comment);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }
}

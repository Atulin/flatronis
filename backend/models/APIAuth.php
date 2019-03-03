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
     * @param string $public_key
     */
    public function setPublicKey(string $public_key): void
    {
        $this->public_key = $public_key;
    }

    /**
     * @param string $private_key
     */
    public function setPrivateKey(string $private_key): void
    {
        $this->private_key = $private_key;
    }

    /**
     * ApiAuth constructor.
     * @param string $id
     * @param string $name
     * @param string $comment
     * @throws \Exception
     */
    public function __construct(?string $id, string $name, string $comment)
    {
        $this->id          = $id ?? bin2hex(random_bytes(16));
        $this->name        = $name;
        $this->comment     = $comment;
    }

    /**
     * @param int $power
     * @throws \Exception
     */
    public function GenerateKeys(int $power = 1024): void
    {
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
    public static function Get(string $id): ?APIAuth
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

        if($api_auth) {
            $auth = new APIAuth(
                $api_auth['id'],
                $api_auth['name'],
                $api_auth['comment']
            );
            $auth->setPrivateKey($api_auth['private_key']);
            $auth->setPublicKey($api_auth['public_key']);
            return $auth;
        }

        return null;
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     * @throws \Exception
     */
    public static function GetAll(int $limit = null, int $offset = null): array
    {
        $dbh = Database::Get();
        $sql = 'SELECT * FROM api_keys LIMIT :l OFFSET :o';

        $sth = $dbh->prepare($sql);

        $sth->bindValue(':l', $limit ?: 10000, PDO::PARAM_INT);
        $sth->bindValue(':o', $offset ?: 0, PDO::PARAM_INT);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw $e;
        }

        return $sth->fetchAll();
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
        $sql = 'DELETE FROM api_keys WHERE id = :id';
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

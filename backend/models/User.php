<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 14.11.2018
 * Time: 04:11
 */

namespace App\Models;

use PDO;
use PDOException;

/**
 * Class User
 * Describes an User
 */
class User
{
    /**
     * @var int ID of the user
     */
    public $id;
    /**
     * @var string Name of the user
     */
    public $name;
    /**
     * @var string Email of the user
     */
    private $email;
    /**
     * @var string Password of the user
     */
    private $password;
    /**
     * @var string Multi-factor authentication key
     */
    private $mfa;
    /**
     * @var string Hashed IP of the device last login was performed from
     */
    private $device;



    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getMfa(): string
    {
        return $this->mfa;
    }

    /**
     * @param string $mfa
     */
    public function setMfa(string $mfa): void
    {
        $this->mfa = $mfa;
    }

    /**
     * @return string
     */
    public function getDevice(): string
    {
        return $this->device;
    }

    /**
     * @param string $device
     */
    public function setDevice(string $device): void
    {
        $this->device = $device;
    }

    /**
     * User constructor method
     * @param int $id ID
     * @param string $name Name
     */
    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }


    /**
     * Gets User from database by specified ID
     * @param int $id ID of the desired user
     * @param bool|null $safe
     * @return User Returns User object
     */
    public static function GetById(int $id, bool $safe = null): User
    {
        return self::GetBy($id, 'id', $safe);
    }

    /**
     * Gets User from database by specified name
     * @param string $name Name of the desired user
     * @param bool|null $safe
     * @return User Returns User object
     */
    public static function GetByName(string $name, bool $safe = null): User
    {
        return self::GetBy($name, 'name', $safe);
    }

    /**
     * Generic getter method for User
     * @param int $param Parameter to get by
     * @param string $field Property to get by
     * @param bool|null $safe
     * @return User Returns User object
     */
    private static function GetBy($param, string $field, bool $safe = null): User
    {
        $dbh = Database::Get();
        $sql = 'SELECT * FROM `users`';

        switch ($field) {
            case 'id':
                $sql .= 'WHERE `id` = :param';
                $type = PDO::PARAM_INT;
                break;
            case 'name':
                $sql .= 'WHERE `name` = :param';
                $type = PDO::PARAM_STR;
                break;
        }
        $sql .= ' LIMIT 1';

        $sth = $dbh->prepare($sql);

        $sth->bindParam(':param', $param, $type ?? PDO::PARAM_INT);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw $e;
        }

        return self::Build($sth->fetch(PDO::FETCH_ASSOC), $safe);
    }

    /**
     * Gets a selected amount of Users from the database
     * @param int $limit [optional] How many results to return
     * @param int $offset [optional] How offset the results should be
     * @param bool|null $safe
     * @return array Returns an array of User objects
     */
    public static function GetAll(int $limit = null, int $offset = null, bool $safe = null): array
    {
        $dbh = Database::Get();
        $sql = 'SELECT * FROM `users`
                ORDER BY `id` DESC
                LIMIT :l OFFSET :o';

        $sth = $dbh->prepare($sql);

        $sth->bindValue(':l', $limit ?: 10000, PDO::PARAM_INT);
        $sth->bindValue(':o', $offset ?: 0, PDO::PARAM_INT);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw $e;
        }

        $users = $sth->fetchAll();
        foreach ($users as $key => $user) {
            $users[$key] = self::Build($user, $safe);
        }
        return $users;
    }

    /**
     * Adds a constructed User object to the database
     */
    public function Add(): void
    {
        $dbh = Database::Get();
        $sql = 'INSERT INTO `users` (id, name, email, password, `2fa`, device)
                VALUES (:id, :name, :email, :pass, :mfa, :device)';

        $sth = $dbh->prepare($sql);

        $sth->bindParam(':id', $this->id, PDO::PARAM_INT);
        $sth->bindParam(':name', $this->name);
        $sth->bindParam(':email', $this->email);
        $sth->bindParam(':pass', $this->password);
        $sth->bindParam(':mfa', $this->mfa);
        $sth->bindParam(':device', $this->device);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * Updates the desired User object in the database
     */
    public function Modify(): void
    {
        $dbh = Database::Get();
        $sql = 'UPDATE `users`
                SET `name` = :name, `email` = :email, `password` = :pass, `2fa` = :mfa, `device` = :device
                WHERE `id` = :id';

        $sth = $dbh->prepare($sql);

        $sth->bindParam(':id', $this->id, PDO::PARAM_INT);
        $sth->bindParam(':name', $this->name);
        $sth->bindParam(':email', $this->email);
        $sth->bindParam(':pass', $this->password);
        $sth->bindParam(':mfa', $this->mfa);
        $sth->bindParam(':device', $this->device);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * Updates the device hash for the user
     * @param string $device Device hash
     */
    public function UpdateDevice(string $device): void
    {
        $dbh = Database::Get();
        $sql = 'UPDATE `users`
                SET `device` = :device
                WHERE `id` = :id';

        $sth = $dbh->prepare($sql);

        $sth->bindParam(':id', $this->id, PDO::PARAM_INT);
        $sth->bindParam(':device', $device);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * Transforms an associative array into an User object
     * @param array $user Associative array representing the User object
     * @param bool|null $safe
     * @return User Returns an User object
     */
    private static function Build(array $user, bool $safe = null): User
    {
        $u =  new User(
            $user['id'],
            $user['name']
        );
        if (!$safe) {
            $u->setDevice($user['device']);
            $u->setPassword($user['password']);
            $u->setMfa($user['2fa']);
            $u->setEmail($user['email']);
        }
        return $u;
    }

}

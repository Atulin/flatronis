<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 14.11.2018
 * Time: 04:11
 */

require_once __DIR__.'/Database.php';

class User
{
    private $id;
    private $name;
    private $email;
    private $password;
    private $mfa;
    private $device;

    /**
     * User constructor.
     * @param $id
     * @param $name
     * @param $email
     * @param $password
     * @param $mfa
     * @param $device
     */
    public function __construct($id, $name, $email, $password, $mfa, $device)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->mfa = $mfa;
        $this->device = $device;
    }

    //region Getters & Setters
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getMfa()
    {
        return $this->mfa;
    }

    /**
     * @param mixed $mfa
     */
    public function setMfa($mfa)
    {
        $this->mfa = $mfa;
    }

    /**
     * @return mixed
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * @param mixed $device
     */
    public function setDevice($device)
    {
        $this->device = $device;
    }
    //endregion

    /**
     * @param int $id
     * @return User
     */
    public static function GetById(int $id): \User
    {
        return self::GetBy($id, 'id', PDO::PARAM_INT);
    }

    /**
     * @param string $name
     * @return User
     */
    public static function GetByName(string $name): \User
    {
        return self::GetBy($name, 'name', PDO::PARAM_STR);
    }

    /**
     * @param int $param
     * @param string $field
     * @param int $type
     * @return User
     */
    private static function GetBy($param, string $field, int $type): \User
    {
        $dbh = Database::Get();
        $sql = 'SELECT * FROM `users`';

        switch ($field) {
            case 'id':
                $sql .= 'WHERE `id` = :param';
                break;
            case 'name':
                $sql .= 'WHERE `name` = :param';
                break;
        }
        $sql .= ' LIMIT 1';

        $sth = $dbh->prepare($sql);

        $sth->bindParam(':param', $param, $type);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw $e;
        }

        return self::Build($sth->fetch(PDO::FETCH_ASSOC));
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function GetAll(int $limit = 10000, int $offset = 0): array
    {
        $dbh = Database::Get();
        $sql = 'SELECT * FROM `users`
                ORDER BY `id` DESC
                LIMIT :l OFFSET :o';

        $sth = $dbh->prepare($sql);

        $sth->bindParam(':l', $limit, PDO::PARAM_INT);
        $sth->bindParam(':o', $offset, PDO::PARAM_INT);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw $e;
        }

        $users = $sth->fetchAll();
        foreach ($users as $key => $user) {
            $users[$key] = self::Build($user);
        }
        return $users;
    }

    /**
     *
     */
    public function Add() {
        $dbh = Database::Get();
        $sql = 'INSERT INTO `users` (id, name, email, password, `2fa`, device)
                VALUES (:id, :name, :email, :pass, :mfa, :device)';

        $sth = $dbh->prepare($sql);

        $sth->bindParam(':id',     $this->id,       PDO::PARAM_INT);
        $sth->bindParam(':name',   $this->name,     PDO::PARAM_STR);
        $sth->bindParam(':email',  $this->email,    PDO::PARAM_STR);
        $sth->bindParam(':pass',   $this->password, PDO::PARAM_STR);
        $sth->bindParam(':mfa',    $this->mfa,      PDO::PARAM_STR);
        $sth->bindParam(':device', $this->device,   PDO::PARAM_STR);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * @param array $user
     * @return User
     */
    private static function Build(array $user): \User
    {
        return new User(
            $user['id'],
            $user['name'],
            $user['email'],
            $user['password'],
            $user['2fa'],
            $user['device']
        );
    }

}

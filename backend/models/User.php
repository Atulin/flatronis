<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 14.11.2018
 * Time: 04:11
 */

require_once __DIR__.'/Database.php';

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
    public $email;
    /**
     * @var string Password of the user
     */
    public $password;
    /**
     * @var string Multi-factor authentication key
     */
    public $mfa;
    /**
     * @var string Hashed IP of the device last login was performed from
     */
    public $device;


    /**
     * User constructor method
     * @param int $id ID
     * @param string $name  Name
     * @param string $email  Name
     * @param string $password  Password
     * @param string $mfa  Multi-factor authentication token
     * @param string $device Hashed IP of the last known device
     */
    public function __construct(int $id, string $name, string $email, string $password, string $mfa, string $device)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->mfa = $mfa;
        $this->device = $device;
    }


    /**
     * Gets User from database by specified ID
     * @param int $id ID of the desired user
     * @return User Returns User object
     */
    public static function GetById(int $id): \User
    {
        return self::GetBy($id, 'id', PDO::PARAM_INT);
    }

    /**
     * Gets User from database by specified name
     * @param string $name Name of the desired user
     * @return User Returns User object
     */
    public static function GetByName(string $name): \User
    {
        return self::GetBy($name, 'name', PDO::PARAM_STR);
    }

    /**
     * Generic getter method for User
     * @param int $param Parameter to get by
     * @param string $field Property to get by
     * @param int $type Type of the parameter in PDO:: class
     * @return User Returns User object
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
     * Gets a selected amount of Users from the database
     * @param int $limit[optional] How many results to return
     * @param int $offset[optional] How offset the results should be
     * @return array Returns an array of User objects
     */
    public static function GetAll(int $limit = null, int $offset = null): array
    {
        $dbh = Database::Get();
        $sql = 'SELECT * FROM `users`
                ORDER BY `id` DESC
                LIMIT :l OFFSET :o';

        $sth = $dbh->prepare($sql);

        $sth->bindValue(':l', $limit  ?: 10000, PDO::PARAM_INT);
        $sth->bindValue(':o', $offset ?: 0,     PDO::PARAM_INT);

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
     * Adds a constructed User object to the database
     */
    public function Add(): void
    {
        $dbh = Database::Get();
        $sql = 'INSERT INTO `users` (id, name, email, password, `2fa`, device)
                VALUES (:id, :name, :email, :pass, :mfa, :device)';

        $sth = $dbh->prepare($sql);

        $sth->bindParam(':id',     $this->id, PDO::PARAM_INT);
        $sth->bindParam(':name',   $this->name);
        $sth->bindParam(':email',  $this->email);
        $sth->bindParam(':pass',   $this->password);
        $sth->bindParam(':mfa',    $this->mfa);
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

        $sth->bindParam(':id',     $this->id,       PDO::PARAM_INT);
        $sth->bindParam(':name',   $this->name);
        $sth->bindParam(':email',  $this->email);
        $sth->bindParam(':pass',   $this->password);
        $sth->bindParam(':mfa',    $this->mfa);
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

        $sth->bindParam(':id',     $this->id, PDO::PARAM_INT);
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
     * @return User Returns an User object
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

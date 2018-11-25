<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 09.11.2018
 * Time: 05:10
 */

require_once __DIR__.'/Database.php';

class Category
{
    public $id;
    public $name;
    public $description;

    /**
     * Post constructor.
     * @param int $id
     * @param string $name string
     * @param string $description
     */
    public function __construct(int $id, string $name, string $description)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @param int $id
     * @return Category
     */
    public static function Get(int $id): \Category
    {
        $dbh = Database::Get();
        $sql = 'SELECT * FROM `categories`
                WHERE `id` = :id
                LIMIT 1';

        $sth = $dbh->prepare($sql);

        $sth->bindParam(':id', $id, PDO::PARAM_INT);

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
        $sql = 'SELECT * FROM `categories`
                ORDER BY `id` ASC
                LIMIT :l OFFSET :o';

        $sth = $dbh->prepare($sql);

        $sth->bindParam(':l', $limit,  PDO::PARAM_INT);
        $sth->bindParam(':o', $offset, PDO::PARAM_INT);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw $e;
        }

        $categories = $sth->fetchAll();
        foreach ($categories as $key => $cat) {
            $categories[$key] = self::Build($cat);
        }
        return $categories;
    }

    /**
     *
     */
    public function Add() {
        $dbh = Database::Get();
        $sql = 'INSERT INTO `categories` (id, name, description)
                VALUES (:id, :name, :desc)';

        $sth = $dbh->prepare($sql);

        $sth->bindParam(':id',    $this->id,         PDO::PARAM_INT);
        $sth->bindParam(':name',  $this->name,       PDO::PARAM_STR);
        $sth->bindParam(':desc',  $this->description,PDO::PARAM_STR);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * @param int $id
     */
    public static function Delete(int $id) {
        $dbh = Database::Get();
        $sql = 'DELETE FROM `categories` WHERE `id` = :id';
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':id', $id, PDO::PARAM_INT);
        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     *
     */
    public function Update() {
        $dbh = Database::Get();
        $sql = 'UPDATE `categories`
                SET `name` = :name, `description` = :desc
                WHERE `id` = :id';

        $sth = $dbh->prepare($sql);

        $sth->bindParam(':id',    $this->id,         PDO::PARAM_INT);
        $sth->bindParam(':name',  $this->name,       PDO::PARAM_STR);
        $sth->bindParam(':desc',  $this->description,PDO::PARAM_STR);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * @param array $category
     * @return Category
     */
    private static function Build(array $category): \Category
    {
        return new Category(
            $category['id'],
            $category['name'],
            $category['description']
        );
    }

}

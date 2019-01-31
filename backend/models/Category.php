<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 09.11.2018
 * Time: 05:10
 */

require_once __DIR__.'/Database.php';

/**
 * Class Category
 * Describes a post category
 */
class Category
{
    /**
     * @var int ID of the category
     */
    public $id;
    /**
     * @var string Name of the category
     */
    public $name;
    /**
     * @var string Description of the category
     */
    public $description;


    /**
     * Category constructor method
     * @param int $id ID
     * @param string $name string Name
     * @param string $description Describes the category
     */
    public function __construct(int $id, string $name, string $description)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }


    /**
     * Gets a Category object from database based on provided ID
     * @param int $id ID of the desired Category
     * @return Category Returns a Category object
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
     * Gets a selected amount of Categories from the database
     * @param int $limit[optional] How many results to return
     * @param int $offset[optional] How offset the results should be
     * @return array Returns an array of Category objects
     */
    public static function GetAll(int $limit = null, int $offset = null): array
    {
        $dbh = Database::Get();
        $sql = 'SELECT * FROM `categories`
                ORDER BY `id` ASC
                LIMIT :l OFFSET :o';

        $sth = $dbh->prepare($sql);

        $sth->bindValue(':l', $limit  ?: 10000, PDO::PARAM_INT);
        $sth->bindValue(':o', $offset ?: 0,     PDO::PARAM_INT);

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
     * Adds a constructed Category object to the database
     */
    public function Add(): void
    {
        $dbh = Database::Get();
        $sql = 'INSERT INTO `categories` (id, name, description)
                VALUES (:id, :name, :desc)';

        $sth = $dbh->prepare($sql);

        $sth->bindParam(':id',   $this->id, PDO::PARAM_INT);
        $sth->bindParam(':name', $this->name);
        $sth->bindParam(':desc', $this->description);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }


    /**
     * Deletes a Category of the specified ID from the database
     * @param int $id ID of the desired Category
     */
    public static function Delete(int $id): void
    {
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
     * Updates the desired Category object in the database
     */
    public function Update(): void
    {
        $dbh = Database::Get();
        $sql = 'UPDATE `categories`
                SET `name` = :name, `description` = :desc
                WHERE `id` = :id';

        $sth = $dbh->prepare($sql);

        $sth->bindParam(':id',   $this->id,PDO::PARAM_INT);
        $sth->bindParam(':name', $this->name);
        $sth->bindParam(':desc', $this->description);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }


    /**
     * Transforms an associative array into a Category object
     * @param array $category Associative array representing the Category object
     * @return Category Returns a Category object
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

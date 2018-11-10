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
    private $id;
    private $name;
    private $description;

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

    //region Getters & Setters
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }
    //endregion

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
                ORDER BY `name` DESC
                LIMIT :l OFFSET :o';

        $sth = $dbh->prepare($sql);

        $sth->bindParam(':l', $limit, PDO::PARAM_INT);
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

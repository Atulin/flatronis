<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 09.11.2018
 * Time: 05:10
 */

namespace App\Models;

use DateTime;
use App\Models;
use PDO;
use PDOException;

/**
 * Class Post
 * Describes a post
 */
class Post
{
    /**
     * @var int ID of the post
     */
    public $id;
    /**
     * @var string Title of the post
     */
    public $title;
    /**
     * @var User Author of the post
     */
    public $author;
    /**
     * @var datetime Date and time of creation
     */
    public $date;
    /**
     * @var Category Category of the post
     */
    public $category;
    /**
     * @var string Body of the post
     */
    public $body;


    /**
     * Post constructor.
     * @param int $id ID
     * @param string $title Title
     * @param User $author Author
     * @param datetime $date Date and time of creation
     * @param Category $category int Category
     * @param string $body Body
     */
    public function __construct(int $id, string $title, User $author, DateTime $date, Category $category, string $body)
    {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->date = $date;
        $this->category = $category;
        $this->body = $body;
    }


    /**
     * Gets a Post object from database based on provided ID
     * @param int $id ID of the desired Post
     * @return Post Returns a Post object
     */
    public static function Get(int $id): Post
    {
        $dbh = Models\Database::Get();
        $sql = 'SELECT * FROM `posts`
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
     * Gets a selected amount of Posts from the database
     * @param int $limit [optional] How many results to return
     * @param int $offset [optional] How offset the results should be
     * @return array Returns an array of Post objects
     */
    public static function GetAll(int $limit = null, int $offset = null): array
    {
        $dbh = Database::Get();
        $sql = 'SELECT * FROM `posts`
                ORDER BY `date` DESC
                LIMIT :l OFFSET :o';

        $sth = $dbh->prepare($sql);

        $sth->bindValue(':l', $limit ?: 10000, PDO::PARAM_INT);
        $sth->bindValue(':o', $offset ?: 0, PDO::PARAM_INT);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw $e;
        }

        $posts = $sth->fetchAll();
        foreach ($posts as $key => $post) {
            $posts[$key] = self::Build($post);
        }
        return $posts;
    }


    /**
     * Adds a constructed Post element to the database
     */
    public function Add(): void
    {
        $dbh = Models\Database::Get();
        $sql = 'INSERT INTO `posts` (id, title, author, date, category, body)
                VALUES (:id, :title, :author, :date, :category, :body)';

        $sth = $dbh->prepare($sql);

        $date = $this->date->format('Y-m-d H:i:s');
        $sth->bindParam(':id', $this->id, PDO::PARAM_INT);
        $sth->bindParam(':title', $this->title);
        $sth->bindParam(':author', $this->author->id, PDO::PARAM_INT);
        $sth->bindParam(':date', $date);
        $sth->bindParam(':category', $this->category->id, PDO::PARAM_INT);
        $sth->bindParam(':body', $this->body);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }


    /**
     * Deletes a Post of the specified ID from the database
     * @param int $id ID of the desired Post
     */
    public static function Delete(int $id): void
    {
        $dbh = Models\Database::Get();
        $sql = 'DELETE FROM `posts` WHERE `id` = :id';
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':id', $id, PDO::PARAM_INT);
        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }


    /**
     * Updates the desired Post object in the database
     */
    public function Update(): void
    {
        $dbh = Models\Database::Get();
        $sql = 'UPDATE `posts`
                SET `title` = :title, `author` = :author, `date` = :date, `category` =:category, `body` = :body
                WHERE `id` = :id';

        $sth = $dbh->prepare($sql);

        $date = $this->date->format('Y-m-d H:i:s');
        $sth->bindParam(':id', $this->id, PDO::PARAM_INT);
        $sth->bindParam(':title', $this->title);
        $sth->bindParam(':author', $this->author->id, PDO::PARAM_INT);
        $sth->bindParam(':date', $date);
        $sth->bindParam(':category', $this->category->id, PDO::PARAM_INT);
        $sth->bindParam(':body', $this->body);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }


    /**
     * Transforms an associative array into a Post object
     * @param array $post Associative array representing the Post object
     * @return Post Returns a Post object
     */
    private static function Build(array $post): Post
    {
        return new Post(
            $post['id'],
            $post['title'],
            User::GetById($post['author']),
            DateTime::createFromFormat('Y-m-d H:i:s', $post['date']),
            Category::Get($post['category']),
            $post['body']
        );
    }

}

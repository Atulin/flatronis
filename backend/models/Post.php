<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 09.11.2018
 * Time: 05:10
 */

require_once __DIR__.'/Database.php';
require_once __DIR__.'/Category.php';
require_once __DIR__.'/User.php';

class Post
{
    public $id;
    public $title;
    public $author;
    public $date;
    public $category;
    public $body;

    /**
     * Post constructor.
     * @param int $id
     * @param $title string
     * @param User $author
     * @param $date datetime
     * @param Category $category int
     * @param $body string
     */
    public function __construct(int $id, string $title, User $author, datetime $date, Category $category, string $body)
    {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->date = $date;
        $this->category = $category;
        $this->body = $body;
    }

    /**
     * @param int $id
     * @return Post
     */
    public static function Get(int $id): \Post
    {
        $dbh = Database::Get();
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
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function GetAll(int $limit = 10000, int $offset = 0): array
    {
        $dbh = Database::Get();
        $sql = 'SELECT * FROM `posts`
                ORDER BY `date` DESC
                LIMIT :l OFFSET :o';

        $sth = $dbh->prepare($sql);

        $sth->bindParam(':l', $limit, PDO::PARAM_INT);
        $sth->bindParam(':o', $offset, PDO::PARAM_INT);

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
     *
     */
    public function Add() {
        $dbh = Database::Get();
        $sql = 'INSERT INTO `posts` (id, title, author, date, category, body)
                VALUES (:id, :title, :author, :date, :category, :body)';

        $sth = $dbh->prepare($sql);

        $date = $this->date->format('Y-m-d H:i:s');
        $sth->bindParam(':id',      $this->id,          PDO::PARAM_INT);
        $sth->bindParam(':title',   $this->title,       PDO::PARAM_STR);
        $sth->bindParam(':author',  $this->author->id,  PDO::PARAM_INT);
        $sth->bindParam(':date',    $date,              PDO::PARAM_STR);
        $sth->bindParam(':category',$this->category->id,PDO::PARAM_INT);
        $sth->bindParam(':body',    $this->body,        PDO::PARAM_STR);

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
     *
     */
    public function Update() {
        $dbh = Database::Get();
        $sql = 'UPDATE `posts`
                SET `title` = :title, `author` = :author, `date` = :date, `category` =:category, `body` = :body
                WHERE `id` = :id';

        $sth = $dbh->prepare($sql);

        $date = $this->date->format('Y-m-d H:i:s');
        $sth->bindParam(':id',      $this->id,          PDO::PARAM_INT);
        $sth->bindParam(':title',   $this->title,       PDO::PARAM_STR);
        $sth->bindParam(':author',  $this->author->id,  PDO::PARAM_INT);
        $sth->bindParam(':date',    $date,              PDO::PARAM_STR);
        $sth->bindParam(':category',$this->category->id,PDO::PARAM_INT);
        $sth->bindParam(':body',    $this->body,        PDO::PARAM_STR);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }


    /**
     * @param array $post
     * @return Post
     */
    private static function Build(array $post): \Post
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

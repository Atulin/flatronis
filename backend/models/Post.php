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
    private $id;
    private $title;
    private $author;
    private $date;
    private $category;
    private $body;

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
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return datetime
     */
    public function getDate(): datetime
    {
        return $this->date;
    }

    /**
     * @param datetime $date
     */
    public function setDate(datetime $date)
    {
        $this->date = $date;
    }

    /**
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @param int $category
     */
    public function setCategory(int $category)
    {
        $this->category = $category;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->author;
    }

    /**
     * @param int $author
     */
    public function setUser(int $author)
    {
        $this->author = $author;
    }
    //endregion

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
     * @param array $post
     * @return Post
     */
    private static function Build(array $post): \Post
    {
        return new Post(
            $post['id'],
            $post['title'],
            User::Get($post['author']),
            DateTime::createFromFormat('Y-m-d h:i:s', $post['date']),
            Category::Get($post['category']),
            $post['body']
        );
    }

}

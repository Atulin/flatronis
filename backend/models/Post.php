<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 09.11.2018
 * Time: 05:10
 */

class Post
{
    private $body;
    private $title;
    private $date;
    private $category;

    /**
     * Post constructor.
     * @param $body string
     * @param $title string
     * @param $date datetime
     * @param $category int
     */
    public function __construct(string $body, string $title, datetime $date, int $category)
    {
        $this->body = $body;
        $this->title = $title;
        $this->date = $date;
        $this->category = $category;
    }

    //region Getters & Setters
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
     * @return int
     */
    public function getCategory(): int
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
    //endregion

    public static function get(int $id) {

    }

}

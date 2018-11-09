<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 09.11.2018
 * Time: 05:24
 */

class Database
{
    /**
     * PDO instance
     * @var \PDO
     */
    private $pdo;

    /**
     * return in instance of the PDO object that connects to the SQLite database
     * @return PDO|string
     */
    public function connect() {
        if ($this->pdo === null) {
            try {
                $this->pdo = new \PDO('sqlite:' . $_ENV['DB']);
            } catch (\PDOException $e) {
                return $e->getMessage();
            }
        }
        return $this->pdo;
    }
}

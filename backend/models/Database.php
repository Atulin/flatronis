<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 09.11.2018
 * Time: 05:24
 */

namespace App\Models;

use PDO;
use PDOException;

/**
 * Class Database
 * @package App\Models
 */
class Database
{
    /**
     * Creates a connection to database and gets the PDO object
     * @return PDO Returns PDO element
     */
    public static function Get(): PDO
    {
        try {
            return new PDO("mysql:host={$_ENV['HOST']};dbname={$_ENV['DB']};charset=utf8mb4",
                $_ENV['USER'],
                $_ENV['PASS'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_PERSISTENT => false
                ]);
        } catch (PDOException $e) {
            throw $e;
        }
    }
}

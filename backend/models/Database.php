<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 09.11.2018
 * Time: 05:24
 */

class Database
{
    public static function Get() : \PDO
    {
        try {
            return new PDO("mysql:host={$_ENV['HOST']};dbname={$_ENV['DB']}", $_ENV['USER'], $_ENV['PASS']);
        } catch (PDOException $e) {
            var_dump($e);
            throw $e;
        }
    }
}

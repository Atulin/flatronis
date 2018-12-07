<?php
///**
// * Created by PhpStorm.
// * User: Angius
// * Date: 05.12.2018
// * Time: 09:16
// */
//
//require_once __DIR__ . '/vendor/autoload.php';
//require_once __DIR__ . '/backend/models/Post.php';
//require_once __DIR__ . '/backend/models/Category.php';
//require_once __DIR__ . '/backend/models/User.php';
//
//use Symfony\Component\Dotenv\Dotenv;
//// Load .env file
//$dotenv = new Dotenv();
//$dotenv->load(__DIR__.'/.env');
//
//$posts = '';
//
//try {
//    $pdo = new PDO("mysql:host=78.46.37.186;dbname=angius_wp", 'angius_wp', 'dupa666');
//} catch (PDOException $e) {
//    var_dump($e);
//    throw $e;
//}
//
//$sql = 'SELECT post_title, post_content, post_date_gmt
//        FROM `wp2_posts` WHERE `post_type` = "post"';
//
//$sth2 = $pdo->prepare($sql);
//
//try {
//    $sth2->execute();
//} catch (PDOException $e) {
//    throw $e;
//}
//
//$posts = $sth2->fetchAll();
//
//foreach ($posts as $po) {
//    $p = new Post(
//        0,
//        $po['post_title'],
//        User::GetById(9),
//        date_create_from_format('Y-m-d H:i:s', $po['post_date_gmt']),
//        Category::Get(1),
//        $po['post_content']
//    );
//    $p->Add();
//    echo "Post \"{$po['post_title']}\" has been added</br>";
//}

<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 05.12.2018
 * Time: 09:16
 */

$pass = password_hash('Hello darkness my old friend', PASSWORD_ARGON2I);
echo $pass;
echo password_verify('Hello darkness my old friend', $pass);

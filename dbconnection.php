<?php

function createDbConnection()
{
/*     $ini = parse_ini_file('myconf.ini');

    $host = $ini['host'];
    $db = $ini['db'];
    $username = $ini['username'];
    $pw = $ini['pw']; */

    try {
        $dbcon = new PDO('mysql:host=localhost;dbname=chinook;charset=utf8','root','');
        return $dbcon;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    return null;
}

<?php

$type = "mysql";
$host = "localhost";
$port = "";
$dbname = "lost_and_found";
$charset = "utf8mb4";

$username = "root";
$password = "";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //for error handling in pdo
    pdo::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // for fetching in associative array
    PDO::ATTR_EMULATE_PREPARES => false // for not emulating prepare by itself
];



try {
    $pdo = new PDO("$type:host=$host;dbname=$dbname;port=$port;charset=$charset", $username, $password, $options);

} catch (PDOException $e) {
    throw new PDOException($e->getMessage(),$e->getCode());
}

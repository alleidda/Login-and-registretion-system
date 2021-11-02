<?php

$host = "host name";
$user = "user name";
$pass = "your password";
$db = "database name";
$charset = "utf8";
$port = "3306";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=$port";

//global $cnn;
try {
       $cnn = new PDO($dsn, $user, $pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

     if ($cnn) {
        //echo "Connected to the $db database successfully!";
        //die();
    }

} catch (Exception $e) {
     die(" Erreur : ".$e->getMessage());
}


?>
<?php

try {
    $db = new PDO('mysql:host=localhost;dbname=pigeon', "root", "", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}

?>
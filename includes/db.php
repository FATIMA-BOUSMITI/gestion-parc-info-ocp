<?php
try {
    $conn = new PDO("mysql:host=localhost;dbname=parc_info;charset=utf8", "root", "");
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<?php
$host = 'localhost';
$dbname = 'parc_info';
$user = 'root';
$pass = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Si erreur, $conn n'est pas créé, le script logic.php passera en mode démo
}
?>
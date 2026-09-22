<?php
session_start();
// On détruit toutes les variables de session
$_SESSION = array();
session_destroy();

// On redirige vers la page de login à la racine
header("Location: test.php");
exit;
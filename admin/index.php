<?php
// Activation du débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// 1. Charger la logique
// Assure-toi que logic.php ne contient pas d'erreurs fatales
require_once __DIR__ . '/logic.php';

// 2. Charger l'entête et la barre latérale
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/partials/sidebar.php';

echo '<div class="main-content">';
    
    // 3. Charger la barre supérieure
    require_once __DIR__ . '/partials/topbar.php';

    // 4. Affichage des messages d'alerte
    if (!empty($message)) {
        echo '<div class="alert alert-success alert-dismissible fade show">' . $message . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }

    // 5. Chargement dynamique de la vue
    // Sécurité : on vérifie que la variable $page est définie, sinon dashboard par défaut
    $page_name = isset($page) ? $page : 'dashboard';
    $view_path = __DIR__ . '/views/' . $page_name . '.php';

    if (file_exists($view_path)) {
        include $view_path;
    } else {
        echo "<div class='container-fluid p-4'><h2>Erreur 404 : La vue '$page_name' n'existe pas.</h2><p>Vérifiez le dossier /views/</p></div>";
    }

echo '</div>'; // Fin main-content

// 6. Pied de page
require_once __DIR__ . '/partials/footer.php';
?>
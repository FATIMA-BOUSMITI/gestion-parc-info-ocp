<?php
session_start();
if (!isset($_SESSION['user_id'])) {
   
    header("Location: login.php");
    exit(); 
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/includes/db.php';

$error = "";

// SI LE FORMULAIRE EST ENVOYÉ
if (isset($_POST['login_btn'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // 1. Chercher l'utilisateur par email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // 2. Vérifier le mot de passe
    // NOTE : Pour l'instant, on compare en texte clair pour tes tests.
    // Plus tard, on mettra password_verify() pour la sécurité.
    if ($user && $password == $user['password']) {
        // C'est gagné ! On crée la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['avatar'] = $user['avatar'];

        // Redirection vers le tableau de bord
        header("Location: index.php");
        exit();
    } else {
        $error = "❌ Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7fe; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .btn-custom { background-color: #4318ff; color: white; }
        .btn-custom:hover { background-color: #3311cc; color: white; }
    </style>
</head>
<body>

<div class="login-card">
    <h3 class="text-center fw-bold mb-4">Bienvenue 👋</h3>
    
    <?php if($error): ?>
        <div class="alert alert-danger text-center p-2"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" placeholder="admin@local.host" required>
        </div>
        <div class="mb-3">
            <label>Mot de passe</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" name="login_btn" class="btn btn-custom w-100 py-2">Se connecter</button>
    </form>
    
    <div class="text-center mt-3">
        <small class="text-muted">Email test: admin@local.host / MDP: (vide)</small>
    </div>
</div>

</body>
</html>
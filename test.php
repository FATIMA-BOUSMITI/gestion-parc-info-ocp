<?php
session_start();
include __DIR__ . '/includes/db.php'; // $conn = PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Récupère l'utilisateur (requête préparée)
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Optionnel : rehash si l'algorithme a changé
        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $upd->execute([$newHash, $user['id']]);
        }

        // Connexion réussie
        session_regenerate_id(true);
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirection selon rôle
        if ($user['role'] === 'admin') {
            header("Location: dashboard_admin.php");
        } elseif ($user['role'] === 'technicien') {
            header("Location: dashboard_technicien.php");
        } else {
            header("Location: dashboard_utilisateur.php");
        }
        exit();
    } else {
        $error = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Connexion</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-container">
    <h2>🔐 Connexion</h2>
    <form method="POST" action="">
        <label for="username">Nom d'utilisateur</label>
        <input type="text" id="username" name="username" placeholder="Votre nom d'utilisateur" required>

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>

        <button type="submit">Se connecter</button>
    </form>

    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
</div>
</body>
</html>

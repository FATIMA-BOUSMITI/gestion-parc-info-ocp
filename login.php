<?php
session_start();
include __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE email = ? AND mot_de_passe = ?");
    $stmt->execute([$email, $password]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['role'] = $user['role'];

        // Redirection selon le rôle
        if ($user['role'] == 'admin') {
            header("Location: dashboard_admin.php");
        } elseif ($user['role'] == 'technicien') {
            header("Location: dashboard_technicien.php");
        } else {
            header("Location: dashboard_utilisateur.php");
        }
        exit();
    } else {
        $error = "Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion - Parc Informatique OCP</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="login-container">
    <h2>🔐 Connexion</h2>
    <form method="POST" action="">
        <label for="email">Adresse e-mail</label>
        <input type="email" id="email" name="email" placeholder="exemple@ocp.ma" required>
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>
        <button type="submit">Se connecter</button>
    </form>

    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
</div>

</body>
</html>

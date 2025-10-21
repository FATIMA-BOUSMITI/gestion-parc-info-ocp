<?php
session_start();
include __DIR__ . '/includes/db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'utilisateur') {
    die("Accès refusé");
}

$message = "";

// Récupérer infos utilisateur
$stmt = $conn->prepare("SELECT username, role FROM users WHERE username = ?");
$stmt->execute([$_SESSION['username']]);
$user = $stmt->fetch();

// Modifier mot de passe
if (isset($_POST['change_pass'])) {
    $newPass = $_POST['new_password'];
    if ($newPass === "") {
        $message = "❌ Veuillez entrer un mot de passe.";
    } else {
        try {
            $hash = password_hash($newPass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
            $stmt->execute([$hash, $_SESSION['username']]);
            $message = "✅ Mot de passe modifié !";
        } catch (PDOException $e) {
            $message = "Erreur lors de la modification : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
</head>
<body>
    <h2>User Dashboard</h2>

    <?php if($message !== ""): ?>
        <p><?= $message ?></p>
    <?php endif; ?>

    <p>Username: <?= $user['username'] ?></p>
    <p>Role: <?= $user['role'] ?></p>

    <h3>Changer le mot de passe</h3>
    <form method="POST" action="">
        <input type="password" name="new_password" placeholder="Nouveau mot de passe" required>
        <button name="change_pass">Modifier</button>
    </form>
</body>
</html>

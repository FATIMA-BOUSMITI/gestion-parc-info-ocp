<?php
session_start();
include __DIR__ . '/includes/db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    die("Accès refusé");
}

$message = "";

// Ajouter un utilisateur
if (isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($username === "" || $password === "") {
        $message = "❌ Veuillez remplir tous les champs.";
    } else {
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $checkStmt->execute([$username]);

        if ($checkStmt->rowCount() > 0) {
            $message = "❌ L'utilisateur '$username' existe déjà !";
        } else {
            try {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                $stmt->execute([$username, $hash, $role]);
                $message = "✅ Utilisateur ajouté !";
            } catch (PDOException $e) {
                $message = "Erreur lors de l'ajout : " . $e->getMessage();
            }
        }
    }
}

// Supprimer un utilisateur
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $message = "✅ Utilisateur supprimé !";
    } catch (PDOException $e) {
        $message = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// Afficher tous les utilisateurs
$stmt = $conn->query("SELECT id, username, role FROM users");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Admin Dashboard</h2>

    <?php if($message !== ""): ?>
        <p><?= $message ?></p>
    <?php endif; ?>

    <h3>Ajouter un utilisateur</h3>
    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role">
            <option value="admin">Admin</option>
            <option value="utilisateur">User</option>
            <option value="technicien">Technicien</option>
        </select>
        <button name="add_user">Ajouter</button>
    </form>

    <h3>Liste des utilisateurs</h3>
    <table border="1">
        <tr><th>ID</th><th>Username</th><th>Role</th><th>Actions</th></tr>
        <?php foreach ($users as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= $u['username'] ?></td>
            <td><?= $u['role'] ?></td>
            <td>
                <a href="?delete=<?= $u['id'] ?>" onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>

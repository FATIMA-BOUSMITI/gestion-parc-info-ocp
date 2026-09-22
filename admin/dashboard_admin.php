<?php
session_start();
include __DIR__ . '/includes/db.php'; // connexion à la base
include __DIR__ . '/includes/header.php'; // header
include __DIR__ . '/includes/footer.php'; // footer

// Vérification de l'admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
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

// Modifier le rôle
if (isset($_POST['update_user'])) {
    $id = $_POST['id'];
    $role = $_POST['role'];
    if (!empty($id) && in_array($role, ['admin','technicien','utilisateur'])) {
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$role, $id]);
        $message = "✏️ Rôle mis à jour avec succès !";
    } else {
        $message = "⚠️ Données invalides.";
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

// Récupérer tous les utilisateurs
$stmt = $conn->query("SELECT id, username, role FROM users");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { padding: 8px; border: 1px solid #ccc; text-align: center; }
        form { margin: 0; }
        select { padding: 4px; }
        button { padding: 4px 8px; cursor: pointer; }
        input { padding: 4px; }
        .message { margin-top: 10px; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Admin Dashboard</h2>

    <?php if($message !== ""): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <h3>Ajouter un utilisateur</h3>
    <form method="POST" action="">
        <input type="text" name="username" placeholder="Nom d'utilisateur" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <select name="role" required>
            <option value="admin">Admin</option>
            <option value="technicien">Technicien</option>
            <option value="utilisateur">Utilisateur</option>
        </select>
        <button type="submit" name="add_user">Ajouter</button>
    </form>

    <h3>Liste des utilisateurs</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Rôle</th>
            <th>Modifier</th>
            <th>Supprimer</th>
        </tr>
        <?php foreach ($users as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= $u['role'] ?></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                    <select name="role">
                        <option value="admin" <?= $u['role']=='admin'?'selected':'' ?>>Admin</option>
                        <option value="technicien" <?= $u['role']=='technicien'?'selected':'' ?>>Technicien</option>
                        <option value="utilisateur" <?= $u['role']=='utilisateur'?'selected':'' ?>>Utilisateur</option>
                    </select>
                    <button type="submit" name="update_user">💾</button>
                </form>
            </td>
            <td>
                <a href="?delete=<?= $u['id'] ?>" onclick="return confirm('Supprimer cet utilisateur ?')">❌</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>

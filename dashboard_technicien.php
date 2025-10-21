<?php
session_start();
include __DIR__ . '/includes/db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'technicien') {
    die("Accès refusé");
}

$message = "";

// Exemple : récupérer les machines ou tâches
$stmt = $conn->query("SELECT * FROM machines"); // filtrer par technicien si nécessaire
$machines = $stmt->fetchAll();

// Marquer tâche terminée
if (isset($_GET['done'])) {
    $id = $_GET['done'];
    try {
        $stmt = $conn->prepare("UPDATE machines SET status = 'Terminée' WHERE id = ?");
        $stmt->execute([$id]);
        $message = "✅ Tâche marquée comme terminée !";
    } catch (PDOException $e) {
        $message = "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Technicien Dashboard</title>
</head>
<body>
    <h2>Technicien Dashboard</h2>

    <?php if($message !== ""): ?>
        <p><?= $message ?></p>
    <?php endif; ?>

    <h3>Liste des machines / tâches</h3>
    <table border="1">
        <tr><th>ID</th><th>Machine</th><th>Status</th><th>Actions</th></tr>
        <?php foreach ($machines as $m): ?>
        <tr>
            <td><?= $m['id'] ?></td>
            <td><?= $m['name'] ?></td>
            <td><?= $m['statu'] ?></td>
            <td>
                <?php if($m['status'] !== 'Terminée'): ?>
                <a href="?done=<?= $m['id'] ?>" onclick="return confirm('Marquer cette tâche comme terminée ?')">Terminer</a>
                <?php else: ?>
                ✅ Terminé
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>

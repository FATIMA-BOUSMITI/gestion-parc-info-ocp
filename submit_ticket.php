<?php
session_start();
include __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['id'])) {
    $machine_id = intval($_POST['machine_id']);
    $description = $_POST['description'];
    $user_id = $_SESSION['id'];

    // Insertion du ticket
    $stmt = $conn->prepare("INSERT INTO tickets (machine_id, user_id, description, status, created_at) VALUES (?, ?, ?, 'ouvert', NOW())");
    
    if ($stmt->execute([$machine_id, $user_id, $description])) {
        // Mettre à jour le statut de la machine en 'en panne'
        $updateMac = $conn->prepare("UPDATE machines SET status = 'en panne' WHERE id = ?");
        $updateMac->execute([$machine_id]);

        $_SESSION['success'] = "Votre ticket a été envoyé avec succès au service technique.";
    } else {
        $_SESSION['error'] = "Une erreur est survenue lors de l'envoi.";
    }

    header("Location: dashboard_utilisateur.php");
    exit();
}
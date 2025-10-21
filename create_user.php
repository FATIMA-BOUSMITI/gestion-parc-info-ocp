<?php
// create_users.php — exécuter une seule fois depuis le navigateur
// Exemple: http://localhost/gestion_parc_info/create_users.php

try {
    include __DIR__ . '/includes/db.php'; // doit définir $conn (PDO)

    $users = [
        ['username' => 'fatima', 'password' => 'admin', 'role' => 'admin'],
        ['username' => 'ali',      'password' => '123456',     'role' => 'technicien'],
        ['username' => 'sara',      'password' => '1234567',     'role' => 'utilisateur']
    ];

    $inserted = 0;
    $skipped = 0;

    foreach ($users as $u) {
        // Vérifier si existe déjà
        $check = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $check->execute([$u['username']]);
        $exists = (int)$check->fetchColumn();

        if ($exists) {
            echo "⏭️ Utilisateur '{$u['username']}' existe déjà — ignoré.<br>";
            $skipped++;
            continue;
        }

        // Hachage du mot de passe
        $hash = password_hash($u['password'], PASSWORD_DEFAULT);

        // Insertion sécurisée
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$u['username'], $hash, $u['role']]);

        echo "✅ Utilisateur '{$u['username']}' créé (mot de passe initial: {$u['password']}).<br>";
        $inserted++;
    }

    echo "<hr>Résultat: $inserted créés, $skipped ignorés.<br>";
    echo "→ Vérifie dans phpMyAdmin: SELECT username, role, created_at FROM users;<br>";

} catch (PDOException $e) {
    echo "Erreur PDO : " . htmlspecialchars($e->getMessage());
} catch (Exception $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
}

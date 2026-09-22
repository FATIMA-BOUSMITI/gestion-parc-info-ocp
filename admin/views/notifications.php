<?php
// Exemple de données de notifications (normalement issues de votre base de données)
$notifications = [
    [
        'id' => 1,
        'titre' => 'Alerte Stock',
        'message' => 'Le stock de souris USB est inférieur à 5 unités.',
        'date' => 'Il y a 10 minutes',
        'type' => 'danger', // rouge
        'icone' => 'fas fa-exclamation-triangle'
    ],
    [
        'id' => 2,
        'titre' => 'Nouvelle Maintenance',
        'message' => 'L\'ordinateur PC-004 a été mis à jour avec succès.',
        'date' => 'Il y a 2 heures',
        'type' => 'success', // vert
        'icone' => 'fas fa-check-circle'
    ],
    [
        'id' => 3,
        'titre' => 'Demande de Support',
        'message' => 'L\'utilisateur Jean Dupont a signalé un écran noir.',
        'date' => 'Hier',
        'type' => 'info', // bleu
        'icone' => 'fas fa-user-clock'
    ]
];
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Centre de Notifications</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Notifications</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-bell me-1"></i>
                Toutes les notifications
            </div>
            <button class="btn btn-sm btn-outline-secondary">Tout marquer comme lu</button>
        </div>
        <div class="card-body">
            <?php if (empty($notifications)): ?>
                <p class="text-center text-muted">Aucune nouvelle notification.</p>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($notifications as $notif): ?>
                        <a href="#" class="list-group-item list-group-item-action p-3">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-<?php echo $notif['type']; ?> text-white p-2 me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        <i class="<?php echo $notif['icone']; ?>"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-bold"><?php echo $notif['titre']; ?></h6>
                                        <p class="mb-1 small text-muted"><?php echo $notif['message']; ?></p>
                                    </div>
                                </div>
                                <small class="text-muted"><?php echo $notif['date']; ?></small>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
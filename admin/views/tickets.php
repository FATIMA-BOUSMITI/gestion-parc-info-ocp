<?php
// 1. LOGIQUE DE MISE À JOUR (Traitement du formulaire d'assignation)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_tech'])) {
    $t_id = $_POST['ticket_id'];
    $tech_id = $_POST['technicien_id'];

    // Mise à jour : on assigne le tech et on change le statut en "en cours"
    $stmt = $conn->prepare("UPDATE tickets SET assigned_to = ?, status = 'en cours' WHERE id = ?");
    $stmt->execute([$tech_id, $t_id]);
    
    // Rafraîchir la page pour voir les changements
    echo "<script>window.location.href='?page=tickets';</script>";
    exit;
}

// 2. RÉCUPÉRATION DES DONNÉES
// On récupère les tickets avec le nom du tech (jointure)
$sql_tickets = "SELECT t.*, u.username AS assignee_name 
                FROM tickets t 
                LEFT JOIN users u ON t.assigned_to = u.id 
                ORDER BY t.created_at DESC";
$tickets = $conn->query($sql_tickets)->fetchAll(PDO::FETCH_ASSOC);

// On récupère la liste des techniciens pour les menus déroulants
$techniciens = $conn->query("SELECT id, username FROM users WHERE role = 'technicien'")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold">Support Tickets</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#ticketModal">
        <i class="bi bi-plus-lg"></i> Create Ticket
    </button>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="bg-light text-muted small text-uppercase">
                <tr>
                    <th class="ps-4">ID</th>
                    <th>Description</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Assignee</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $t): 
                    // Couleurs des badges
                    $prioClass = match(strtolower($t['priority']??'low')) { 
                        'high'=>'bg-danger', 
                        'medium'=>'bg-warning text-dark', 
                        default=>'bg-success' 
                    };
                    $statusClass = ($t['status'] == 'ouvert') ? 'bg-info text-dark' : 'bg-primary text-white';
                ?>
                <tr>
                    <td class="ps-4 fw-bold">#<?= $t['id'] ?></td>
                    <td>
                        <span class="text-dark small"><?= htmlspecialchars(substr($t['description'], 0, 60)) ?>...</span>
                    </td>
                    <td><span class="badge <?= $prioClass ?> rounded-pill small"><?= ucfirst($t['priority']??'Low') ?></span></td>
                    <td><span class="badge <?= $statusClass ?> rounded-pill small"><?= ucfirst($t['status']) ?></span></td>
                    <td>
                        <?php if (!empty($t['assignee_name'])): ?>
                            <div class="d-flex align-items-center text-primary fw-bold small">
                                <i class="bi bi-person-check-fill me-1"></i> <?= htmlspecialchars($t['assignee_name']) ?>
                            </div>
                        <?php else: ?>
                            <button class="btn btn-sm btn-outline-secondary py-0 px-2" data-bs-toggle="modal" data-bs-target="#assignModal<?= $t['id'] ?>">
                                <i class="bi bi-person-plus"></i> Assigner
                            </button>

                            <div class="modal fade" id="assignModal<?= $t['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-sm modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <form method="POST">
                                            <div class="modal-header py-2">
                                                <h6 class="modal-title fw-bold">Assigner Ticket #<?= $t['id'] ?></h6>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="ticket_id" value="<?= $t['id'] ?>">
                                                <input type="hidden" name="assign_tech" value="1">
                                                <select name="technicien_id" class="form-select form-select-sm" required>
                                                    <option value="" selected disabled>Choisir un tech...</option>
                                                    <?php foreach ($techniciens as $tech): ?>
                                                        <option value="<?= $tech['id'] ?>"><?= htmlspecialchars($tech['username']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="modal-footer py-1">
                                                <button type="submit" class="btn btn-primary btn-sm w-100">Confirmer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
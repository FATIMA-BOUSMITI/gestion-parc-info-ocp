<?php
// ==========================================
// 1. TRAITEMENT DU FORMULAIRE
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_machine'])) {
    
    // Vérification : L'utilisateur est-il choisi ?
    if (empty($_POST['user_id'])) {
        echo "<script>alert('Erreur : Vous devez obligatoirement assigner la machine à un utilisateur !');</script>";
    } else {
        // Récupération des données (On utilise 'name' et 'ip' pour calmer logic.php)
        // Mais on les insère dans les colonnes 'service' et 'ip_address' de la BDD
        $service = htmlspecialchars($_POST['name']); // Corrigé pour éviter l'erreur "Undefined array key name"
        $ip      = htmlspecialchars($_POST['ip']);   // Corrigé pour éviter l'erreur "Undefined array key ip"
        $type    = htmlspecialchars($_POST['type']);
        $role    = htmlspecialchars($_POST['role']);
        $cpu     = htmlspecialchars($_POST['cpu']);
        $ram     = htmlspecialchars($_POST['ram']);
        $disk    = htmlspecialchars($_POST['disk']);
        $date    = $_POST['date_achat']; 
        $user_id = $_POST['user_id'];

        // Insertion SQL
        $stmt = $conn->prepare("INSERT INTO machines (service, ip_address, type, role_machine, cpu_info, ram_info, stockage, date_achat, id_utilisateur, status, created_at) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'online', NOW())");
        
        if($stmt->execute([$service, $ip, $type, $role, $cpu, $ram, $disk, $date, $user_id])) {
            // SUCCESS : Redirection via JavaScript (Car header() est bloqué ici)
            echo "<script>window.location.href = '?page=machines';</script>";
            exit;
        } else {
            echo "<script>alert('Erreur SQL lors de l\'ajout.');</script>";
        }
    }
}

// ==========================================
// 2. RECUPERATION DES DONNEES
// ==========================================
$usersList = $conn->query("SELECT id, username FROM users ORDER BY username ASC")->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT m.*, u.username AS proprietaire, u.email AS contact_email 
        FROM machines m 
        LEFT JOIN users u ON m.id_utilisateur = u.id 
        ORDER BY m.id DESC";
$machines = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Calcul KPI
$total = count($machines);
$online = 0; $warning = 0; $offline = 0;
foreach ($machines as $m) {
    $st = strtolower($m['status'] ?? ''); 
    if ($st == 'online') $online++; elseif ($st == 'offline') $offline++; elseif ($st == 'warning') $warning++; else $online++;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold m-0">Gestion du Parc & Affectations</h5>
        <small class="text-muted">Ajoutez des machines et assignez-les aux utilisateurs</small>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMachineModal">
        <i class="bi bi-person-plus-fill me-2"></i>Nouvelle Machine
    </button>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3"><div class="card border-0 shadow-sm p-3"><div class="d-flex justify-content-between align-items-center"><div><span class="text-muted small fw-bold">TOTAL</span><h3 class="fw-bold mb-0 mt-1"><?= $total ?></h3></div><div class="bg-light text-primary p-2 rounded"><i class="bi bi-hdd-stack fs-4"></i></div></div></div></div>
    <div class="col-6 col-xl-3"><div class="card border-0 shadow-sm p-3"><div class="d-flex justify-content-between align-items-center"><div><span class="text-muted small fw-bold">ONLINE</span><h3 class="fw-bold mb-0 mt-1 text-success"><?= $online ?></h3></div><div class="bg-success bg-opacity-10 text-success p-2 rounded"><i class="bi bi-activity fs-4"></i></div></div></div></div>
    <div class="col-6 col-xl-3"><div class="card border-0 shadow-sm p-3"><div class="d-flex justify-content-between align-items-center"><div><span class="text-muted small fw-bold">WARNING</span><h3 class="fw-bold mb-0 mt-1 text-warning"><?= $warning ?></h3></div><div class="bg-warning bg-opacity-10 text-warning p-2 rounded"><i class="bi bi-exclamation-triangle fs-4"></i></div></div></div></div>
    <div class="col-6 col-xl-3"><div class="card border-0 shadow-sm p-3"><div class="d-flex justify-content-between align-items-center"><div><span class="text-muted small fw-bold">OFFLINE</span><h3 class="fw-bold mb-0 mt-1 text-danger"><?= $offline ?></h3></div><div class="bg-danger bg-opacity-10 text-danger p-2 rounded"><i class="bi bi-x-octagon fs-4"></i></div></div></div></div>
</div>

<div class="d-flex gap-2 mb-4 overflow-auto">
    <button class="btn btn-primary px-4 rounded-3 filter-btn" onclick="filterMachines('all', this)">Tous</button>
    <button class="btn btn-light text-secondary px-4 rounded-3 filter-btn" onclick="filterMachines('online', this)">Online</button>
    <button class="btn btn-light text-secondary px-4 rounded-3 filter-btn" onclick="filterMachines('warning', this)">Warning</button>
    <button class="btn btn-light text-secondary px-4 rounded-3 filter-btn" onclick="filterMachines('offline', this)">Offline</button>
</div>

<div class="row g-3" id="machinesGrid">
    <?php foreach ($machines as $m): 
        // Variables
        $cpu_pct = intval($m['cpu_usage'] ?? 0);
        $ram_pct = intval($m['ram_usage'] ?? 0);
        $disk_pct = intval($m['disk_usage'] ?? 0);
        $cpu_info = htmlspecialchars($m['cpu_info'] ?? 'Inconnu');
        $ram_info = htmlspecialchars($m['ram_info'] ?? 'Inconnu');
        $disk_info = htmlspecialchars($m['stockage'] ?? 'Inconnu');
        
        $statusRaw = strtolower($m['status'] ?? 'online');
        if ($statusRaw == 'offline') { $badgeClass = 'bg-danger bg-opacity-10 text-danger'; $iconColor = 'text-danger'; }
        elseif ($statusRaw == 'warning') { $badgeClass = 'bg-warning bg-opacity-10 text-warning'; $iconColor = 'text-warning'; }
        else { $badgeClass = 'bg-success bg-opacity-10 text-success'; $iconColor = 'text-success'; }

        $type = strtolower($m['type'] ?? '');
        $typeIcon = match(true) {
            str_contains($type, 'laptop') || str_contains($type, 'portable') => 'bi-laptop',
            str_contains($type, 'fixe') || str_contains($type, 'station') || $type == 'pc' => 'bi-pc-display',
            str_contains($type, 'server') || str_contains($type, 'serveur') => 'bi-hdd-rack',
            default => 'bi-hdd-network'
        };

        $ownerName = !empty($m['proprietaire']) ? htmlspecialchars($m['proprietaire']) : 'Non assigné';
        $ownerEmail = !empty($m['contact_email']) ? htmlspecialchars($m['contact_email']) : '';
        $hasOwner = !empty($m['proprietaire']);
    ?>
    <div class="col-md-6 col-xl-4 machine-item" data-status="<?= $statusRaw ?>">
        <div class="card border-0 shadow-sm h-100 p-3 machine-card">
            <div class="d-flex justify-content-between mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded bg-light d-flex align-items-center justify-content-center" style="width:50px; height:50px;">
                        <i class="bi <?= $typeIcon ?> fs-4 <?= $iconColor ?>"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold m-0"><?= htmlspecialchars($m['service']) ?></h6>
                        <small class="text-secondary" style="font-size:0.8rem"><?= htmlspecialchars($m['ip_address']) ?></small>
                    </div>
                </div>
                <span class="badge <?= $badgeClass ?>" style="height:fit-content;"><?= ucfirst($statusRaw) ?></span>
            </div>
            
            <hr class="text-muted opacity-25 my-3">
            <div class="mb-3 d-flex align-items-center small">
                <i class="bi bi-person-circle me-2 text-secondary"></i>
                <span class="<?= $hasOwner ? 'text-dark fw-bold' : 'text-danger fw-bold' ?>">
                    <?= $hasOwner ? $ownerName : 'NON AFFECTÉ' ?>
                </span>
            </div>

            <div class="d-flex justify-content-between small text-muted mb-1">
                <span><i class="bi bi-cpu me-1"></i>CPU <span style="font-size:0.7rem; opacity:0.7">(<?= $cpu_info ?>)</span></span>
                <span><?= $cpu_pct ?>%</span>
            </div>
            <div class="progress mb-2" style="height: 5px;"><div class="progress-bar <?= $cpu_pct>80?'bg-danger':'bg-success' ?>" style="width: <?= $cpu_pct ?>%"></div></div>

            <div class="d-flex justify-content-between small text-muted mb-1">
                <span><i class="bi bi-memory me-1"></i>RAM <span style="font-size:0.7rem; opacity:0.7">(<?= $ram_info ?>)</span></span>
                <span><?= $ram_pct ?>%</span>
            </div>
            <div class="progress mb-2" style="height: 5px;"><div class="progress-bar bg-primary" style="width: <?= $ram_pct ?>%"></div></div>

            <div class="d-flex justify-content-between small text-muted mb-1">
                <span><i class="bi bi-device-hdd me-1"></i>Disk <span style="font-size:0.7rem; opacity:0.7">(<?= $disk_info ?>)</span></span>
                <span><?= $disk_pct ?>%</span>
            </div>
            <div class="progress mb-2" style="height: 5px;"><div class="progress-bar <?= $disk_pct>90?'bg-danger':'bg-info' ?>" style="width: <?= $disk_pct ?>%"></div></div>

            <div class="d-flex justify-content-between mt-auto pt-3 border-top border-light align-items-center">
                <small class="text-muted"><i class="bi bi-clock me-1"></i><?= $m['uptime_days'] ?? 0 ?>j</small>
                <div class="d-flex gap-2">
                    <a href="?page=machines&delete=machine&id=<?= $m['id'] ?>" class="btn btn-sm btn-light text-danger" onclick="return confirm('Supprimer définitivement ?')"><i class="bi bi-trash"></i></a>
                    <button class="btn btn-sm btn-primary px-3" data-bs-toggle="modal" data-bs-target="#infoModal<?= $m['id'] ?>"><i class="bi bi-eye me-1"></i> Détails</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="infoModal<?= $m['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 bg-light"><h6 class="modal-title fw-bold">Détails de l'affectation</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <?php if($hasOwner): ?>
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm mb-2" style="width: 50px; height: 50px; font-size: 1.2rem;"><?= strtoupper(substr($ownerName, 0, 1)) ?></div>
                            <h5 class="fw-bold mb-0"><?= $ownerName ?></h5>
                            <p class="text-muted small"><?= $ownerEmail ?></p>
                        <?php else: ?>
                            <div class="text-danger fw-bold">Aucun propriétaire</div>
                        <?php endif; ?>
                    </div>
                    <hr class="opacity-25">
                    <div class="row g-3 small">
                        <div class="col-6"><div class="p-2 bg-light rounded"><span class="d-block text-muted" style="font-size:0.7rem">Type</span><strong><?= $m['type'] ?></strong></div></div>
                        <div class="col-6"><div class="p-2 bg-light rounded"><span class="d-block text-muted" style="font-size:0.7rem">Date Achat</span><strong><?= $m['date_achat'] ?></strong></div></div>
                        <div class="col-6"><div class="p-2 bg-light rounded"><span class="d-block text-muted" style="font-size:0.7rem">CPU</span><strong><?= $cpu_info ?></strong></div></div>
                        <div class="col-6"><div class="p-2 bg-light rounded"><span class="d-block text-muted" style="font-size:0.7rem">RAM</span><strong><?= $ram_info ?></strong></div></div>
                        <div class="col-12"><div class="p-2 bg-light rounded"><span class="d-block text-muted" style="font-size:0.7rem">Disque</span><strong><?= $disk_info ?></strong></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="modal fade" id="addMachineModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-hdd-network me-2"></i>Ajouter une machine</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form method="POST">
                <input type="hidden" name="add_machine" value="1">
                
                <div class="modal-body p-4 bg-light bg-opacity-10">
                    <div class="alert alert-info py-2 small"><i class="bi bi-info-circle me-1"></i> Remplissez les infos techniques puis choisissez le propriétaire.</div>

                    <div class="row">
                        <div class="col-md-7 border-end">
                            <h6 class="text-uppercase fw-bold text-primary small mb-3">1. Informations Techniques</h6>
                            
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Nom Machine <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control form-control-sm" placeholder="ex: PC-COMPTA" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Adresse IP <span class="text-danger">*</span></label>
                                    <input type="text" name="ip" class="form-control form-control-sm" placeholder="192.168.x.x" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Type <span class="text-danger">*</span></label>
                                    <select name="type" class="form-select form-select-sm" required>
                                        <option value="Laptop">PC Portable</option>
                                        <option value="PC Fixe">Tour / Fixe</option>
                                        <option value="Serveur">Serveur</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Rôle / Bureau <span class="text-danger">*</span></label>
                                    <input type="text" name="role" class="form-control form-control-sm" placeholder="ex: Accueil" required>
                                </div>
                                <div class="col-4">
                                    <label class="form-label small fw-bold">CPU <span class="text-danger">*</span></label>
                                    <input type="text" name="cpu" class="form-control form-control-sm" placeholder="i5" required>
                                </div>
                                <div class="col-4">
                                    <label class="form-label small fw-bold">RAM <span class="text-danger">*</span></label>
                                    <input type="text" name="ram" class="form-control form-control-sm" placeholder="8GB" required>
                                </div>
                                <div class="col-4">
                                    <label class="form-label small fw-bold">Disk <span class="text-danger">*</span></label>
                                    <input type="text" name="disk" class="form-control form-control-sm" placeholder="256GB" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Date Achat <span class="text-danger">*</span></label>
                                    <input type="date" name="date_achat" class="form-control form-control-sm" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5 d-flex flex-column justify-content-center">
                            <div class="p-3 rounded bg-primary bg-opacity-10 border border-primary">
                                <h6 class="text-uppercase fw-bold text-primary small mb-3">2. Affectation (Propriétaire)</h6>
                                
                                <p class="small text-muted mb-2">Qui est responsable de ce matériel ?</p>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Utilisateur <span class="text-danger">*</span></label>
                                    
                                    <select name="user_id" class="form-select border-primary shadow-sm" size="5" required>
                                        <option value="" disabled selected class="text-muted">-- Cliquer pour choisir --</option>
                                        <?php foreach($usersList as $u): ?>
                                            <option value="<?= $u['id'] ?>" class="py-1">
                                                👤 <?= htmlspecialchars($u['username']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text small text-danger mt-2">* Sélection obligatoire</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <button type="button" class="btn btn-light text-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Enregistrer la machine</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function filterMachines(status, btn) {
    document.querySelectorAll('.machine-item').forEach(c => {
        c.classList.toggle('d-none', status !== 'all' && c.dataset.status !== status);
    });
    document.querySelectorAll('.filter-btn').forEach(b => {
        b.className = `btn ${b === btn ? 'btn-primary text-white' : 'btn-light text-secondary'} px-4 rounded-3 filter-btn`;
    });
}
</script>
<div class="card-custom">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold m-0">Gestion des Utilisateurs</h5>
        <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#userModal">
            <i class="bi bi-plus-lg me-2"></i>Ajouter un utilisateur
        </button>
    </div>
    
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th class="text-secondary text-uppercase small">Utilisateur</th>
                    <th class="text-secondary text-uppercase small">Contact</th>
                    <th class="text-secondary text-uppercase small">Role</th>
                    <th class="text-secondary text-uppercase small">Status</th>
                    <th class="text-end text-secondary text-uppercase small">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): 
                    $init = strtoupper(substr($u['username'],0,2));
                    
                    $badge = match($u['role']) {
                        'admin' => 'bg-soft-primary',
                        'technicien' => 'bg-soft-warning',
                        'utilisateur' => 'bg-soft-info',
                        default => 'bg-light'
                    };

                    $currentStatus = $u['status'] ?? 'active';
                    $statusBadge = ($currentStatus === 'active') ? 'bg-soft-success' : 'bg-soft-danger';
                    $statusLabel = ($currentStatus === 'active') ? 'Actif' : 'Inactif';
                ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-initials me-3"><?= $init ?></div>
                            <div>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($u['username']) ?></div>
                                <div class="small text-muted">ID: #<?= $u['id'] ?></div>
                            </div>
                        </div>
                    </td>
                    <td><div class="small text-secondary"><?= htmlspecialchars($u['email'] ?? 'N/A') ?></div></td>
                    <td><span class="badge-soft <?= $badge ?>"><?= ucfirst($u['role']) ?></span></td>
                    <td><span class="badge-soft <?= $statusBadge ?>"><?= $statusLabel ?></span></td>
                    
                    <td class="text-end">
                        <button class="btn btn-sm btn-light text-primary edit-user-btn me-2"
                                data-bs-toggle="modal" 
                                data-bs-target="#editUserModal"
                                data-id="<?= $u['id'] ?>"
                                data-username="<?= htmlspecialchars($u['username']) ?>"
                                data-email="<?= htmlspecialchars($u['email'] ?? '') ?>"
                                data-role="<?= $u['role'] ?>"
                                data-status="<?= $currentStatus ?>">
                            <i class="bi bi-pencil-square"></i>
                        </button>

                        <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != $u['id']): ?>
                            <a href="logic.php?delete=user&id=<?= $u['id'] ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Supprimer cet utilisateur ?')">
                                <i class="bi bi-trash text-white"></i>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Modifier l'utilisateur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="logic.php" method="POST">
                    <input type="hidden" name="action" value="edit_user">
                    <input type="hidden" name="user_id" id="edit_user_id">

                    <div class="mb-3">
                        <label class="form-label small text-secondary">Nom d'utilisateur</label>
                        <input type="text" name="username" id="edit_username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-secondary">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-secondary">Rôle</label>
                            <select name="role" id="edit_role" class="form-select">
                                <option value="admin">Admin</option>
                                <option value="technicien">Technicien</option>
                                <option value="utilisateur">Utilisateur</option> 
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-secondary">Statut</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="active">Actif</option>
                                <option value="inactive">Inactif</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-secondary">Nouveau Mot de passe (optionnel)</label>
                        <input type="password" name="password" class="form-control" placeholder="Laisser vide pour garder l'actuel">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.edit-user-btn');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Extraction des données du bouton
            const data = {
                id: this.getAttribute('data-id'),
                username: this.getAttribute('data-username'),
                email: this.getAttribute('data-email'),
                role: this.getAttribute('data-role'),
                status: this.getAttribute('data-status')
            };

            // Injection dans le formulaire avec vérification de l'existence des éléments
            if(document.getElementById('edit_user_id'))   document.getElementById('edit_user_id').value = data.id;
            if(document.getElementById('edit_username'))  document.getElementById('edit_username').value = data.username;
            if(document.getElementById('edit_email'))     document.getElementById('edit_email').value = data.email;
            if(document.getElementById('edit_role'))      document.getElementById('edit_role').value = data.role;
            if(document.getElementById('edit_status'))    document.getElementById('edit_status').value = data.status;
        });
    });
});
</script>
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" action="logic.php" method="POST">
            <input type="hidden" name="action" value="add_user">
            
            <div class="modal-header border-0">
                <h5 class="fw-bold">New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small text-secondary">Nom complet <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" placeholder="Full Name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-secondary">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-secondary">Mot de passe <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Create password" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-secondary">Rôle <span class="text-danger">*</span></label>
                    <select name="role" class="form-select" required>
                        <option value="" selected disabled>Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="technicien">Technicien</option> <option value="utilisateur">Utilisateur</option> </select>
                </div>
            </div>
            
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary-custom">Add User</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="machineModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" action="logic.php" method="POST">
            <input type="hidden" name="action" value="add_machine">

            <div class="modal-header border-0">
                <h5 class="fw-bold">Add Server</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small text-secondary">Nom du serveur <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Server Name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small text-secondary">Adresse IP <span class="text-danger">*</span></label>
                    <input type="text" name="ip" class="form-control" placeholder="192.168.1.1" required>
                </div>
            </div>
            
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary-custom">Provision Machine</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="ticketModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" action="logic.php" method="POST">
            <input type="hidden" name="action" value="add_ticket">

            <div class="modal-header border-0">
                <h5 class="fw-bold">Create Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small text-secondary">Titre <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="Issue Title" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small text-secondary">Description</label>
                    <textarea name="description" class="form-control" placeholder="Describe the issue..." rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small text-secondary">Priorité</label>
                    <select name="priority" class="form-select">
                        <option value="Low">Low</option>
                        <option value="Medium" selected>Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
            </div>
            
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary-custom">Create Ticket</button>
            </div>
        </form>
    </div>
</div>
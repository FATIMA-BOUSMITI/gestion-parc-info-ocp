<div class="row g-3 mb-4">
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <p class="text-muted small text-uppercase fw-bold mb-1">Parc Utilisateurs</p>
                        <h3 class="fw-bold mb-0 text-dark"><?= $total_users ?></h3>
                    </div>
                    <div class="icon-shape bg-soft-primary text-primary rounded-circle p-3">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                </div>
                
                <div class="small">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted"><i class="bi bi-shield-lock me-1"></i>Admins</span>
                        <span class="fw-bold"><?= $nb_admins ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted"><i class="bi bi-tools me-1"></i>Techs</span>
                        <span class="fw-bold"><?= $nb_techs ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-person me-1"></i>Utilisateurs</span>
                        <span class="fw-bold"><?= $nb_users ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <p class="text-muted small text-uppercase fw-bold mb-1">Mouvements (7j)</p>
                        <span class="text-muted small">Activité récente</span>
                    </div>
                    <div class="icon-shape bg-soft-info text-info rounded-circle p-3">
                        <i class="bi bi-activity fs-4"></i>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mt-3">
                    <div class="text-center w-50 border-end">
                        <h4 class="fw-bold text-success mb-0">+<?= $new_users_week ?></h4>
                        <span class="badge bg-soft-success text-success rounded-pill px-2 py-1 small">
                            Nouveaux
                        </span>
                    </div>

                    <div class="text-center w-50">
                        <h4 class="fw-bold text-danger mb-0">-<?= $deleted_users_week ?></h4>
                        <span class="badge bg-soft-danger text-danger rounded-pill px-2 py-1 small">
                            Supprimés
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <p class="text-muted small text-uppercase fw-bold mb-1">Machines Online</p>
                        <h3 class="fw-bold mb-0 text-dark"><?= $active_machines ?></h3>
                    </div>
                    <div class="icon-shape bg-soft-success text-success rounded-circle p-3">
                        <i class="bi bi-hdd-network fs-4"></i>
                    </div>
                </div>
                <div class="mt-3 small text-muted">
                    Serveurs et postes connectés.
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <p class="text-muted small text-uppercase fw-bold mb-1">Tickets Ouverts</p>
                        <h3 class="fw-bold mb-0 text-dark"><?= $open_tickets ?></h3>
                    </div>
                    <div class="icon-shape bg-soft-warning text-warning rounded-circle p-3">
                        <i class="bi bi-ticket-fill fs-4"></i>
                    </div>
                </div>
                <div class="mt-3 small text-muted">
                    Incidents en cours de traitement.
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-4">Aperçu de l'activité</h6>
                <canvas id="dashboardChart" height="120"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Activité Récente</h6>
                
                <div class="d-flex gap-3 mb-3">
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width:40px; height:40px; font-weight:bold;">
                        JD
                    </div>
                    <div>
                        <div class="small fw-bold">John Doe</div>
                        <div class="text-secondary small">A créé un nouveau ticket</div>
                    </div>
                </div>

                <div class="d-flex gap-3 mb-3">
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width:40px; height:40px; font-weight:bold;">
                        SY
                    </div>
                    <div>
                        <div class="small fw-bold">Système</div>
                        <div class="text-secondary small">Backup terminé avec succès</div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
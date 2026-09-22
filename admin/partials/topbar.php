<div class="topbar">
    <div>
        <span class="text-secondary small">Pages / <?= ucfirst($page ?? 'Dashboard') ?></span>
        <h4 class="fw-bold m-0"><?= ucfirst($page ?? 'Dashboard') ?></h4>
    </div>

    <div class="d-flex align-items-center gap-4">
        
        <div class="search-box d-none d-md-block">
            <input type="text" placeholder="Recherche..." aria-label="Search">
        </div>

        <i class="bi bi-bell fs-5 text-secondary" style="cursor: pointer;"></i>

        <div class="dropdown">
            <div class="user-profile" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                AD
            </div>

            <ul class="dropdown-menu dropdown-menu-end animate slideIn" aria-labelledby="userDropdown">
                <li><h6 class="dropdown-header text-uppercase small text-muted">Compte</h6></li>
                <li>
                    <a class="dropdown-item" href="index.php?page=profile">
                        <i class="bi bi-person"></i> Mon Profil
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="index.php?page=settings">
                        <i class="bi bi-gear"></i> Paramètres
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="logout.php">
                        <i class="bi bi-box-arrow-right"></i> Déconnexion
                    </a>
                </li>
            </ul>
        </div>

    </div>
</div>
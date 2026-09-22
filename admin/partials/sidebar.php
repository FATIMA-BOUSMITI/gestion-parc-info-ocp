<div class="sidebar">
    <div class="sidebar-logo">
        <i class="bi bi-grid-fill text-primary me-2"></i> Admin Panel
    </div>

    <a href="?page=dashboard" class="nav-link <?= $page=='dashboard'?'active':'' ?>">
        <i class="bi bi-speedometer2 me-2"></i> Dashboard
    </a>

    <a href="?page=users" class="nav-link <?= $page=='users'?'active':'' ?>">
        <i class="bi bi-people me-2"></i> Users
    </a>

    <a href="?page=machines" class="nav-link <?= $page=='machines'?'active':'' ?>">
        <i class="bi bi-server me-2"></i> Machines
    </a>

    <a href="?page=tickets" class="nav-link <?= $page=='tickets'?'active':'' ?>">
        <i class="bi bi-ticket-detailed me-2"></i> Tickets
    </a>

    <a href="?page=settings" class="nav-link <?= $page=='settings'?'active':'' ?>">
        <i class="bi bi-gear me-2"></i> Paramètres
    </a>

    <a href="?page=notifications" class="nav-link <?= $page=='notifications'?'active':'' ?>">
        <i class="bi bi-bell me-2"></i> Notifications
        <span class="badge bg-danger ms-2">3</span>
    </a>

    <div style="margin-top: auto; position: absolute; bottom: 20px;">
        <a href="../logout.php" class="nav-link text-danger">
            <i class="bi bi-box-arrow-right me-2"></i> Déconnexion
        </a>
    </div>
</div>
<?php
// On simule des variables de configuration (normalement issues de la base de données)
$site_name = "Gestion Parc Info";
$contact_email = "admin@societe.com";
$maintenance_mode = false;
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Paramètres du Système</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Paramètres</li>
    </ol>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cog me-1"></i>
                    Configuration Générale
                </div>
                <div class="card-body">
                    <form action="logic.php?action=update_settings" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nom de l'application</label>
                            <input type="text" class="form-control" name="site_name" value="<?php echo $site_name; ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email de notification</label>
                            <input type="email" class="form-control" name="contact_email" value="<?php echo $contact_email; ?>">
                        </div>

                        <hr>

                        <h5 class="mb-3">Sécurité & Maintenance</h5>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="maintenanceMode" name="maintenance" <?php echo $maintenance_mode ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="maintenanceMode">Activer le mode maintenance (coupe l'accès aux utilisateurs)</label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Délai d'expiration de session (minutes)</label>
                            <input type="number" class="form-control" name="session_timeout" value="30">
                        </div>

                        <button type="submit" class="btn btn-primary">Enregistrer les paramètres</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title text-info"><i class="fas fa-info-circle"></i> Informations</h5>
                    <p class="card-text small">
                        Ces réglages affectent l'ensemble de l'application de gestion de parc. 
                        Assurez-vous de vérifier les emails avant de valider.
                    </p>
                    <p class="small text-muted">Version du script : 1.2.0</p>
                </div>
            </div>
        </div>
    </div>
</div>
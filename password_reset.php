<?php 
session_start(); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récupération de mot de passe - Gestion Parc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #0d6efd; }
        .card { border-radius: 1rem; }
        .card-header { background-color: #f8f9fa; border-bottom: none; border-radius: 1rem 1rem 0 0 !important; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-5">
                <div class="card shadow-lg border-0 mt-5">
                    <div class="card-header text-center py-4">
                        <i class="fas fa-lock-open fa-3x text-primary mb-3"></i>
                        <h3 class="font-weight-light">Récupération de compte</h3>
                    </div>
                    <div class="card-body p-4">
                        
                        <?php if(isset($_SESSION['message'])): ?>
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <div class="small mb-4 text-muted">
                            Entrez votre adresse email ci-dessous. Nous vous enverrons un lien pour réinitialiser votre mot de passe.
                        </div>

                        <form action="admin/logic.php?action=reset_password" method="POST">
                            <div class="form-floating mb-3">
                                <input class="form-control" id="inputEmail" name="email" type="email" placeholder="nom@exemple.com" required />
                                <label for="inputEmail"><i class="fas fa-envelope me-2"></i>Adresse Email</label>
                            </div>
                            
                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                <a class="btn btn-link btn-sm text-decoration-none" href="test.php">
                                    <i class="fas fa-arrow-left me-1"></i> Retour à la connexion
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    Envoyer le lien
                                </button>
                            </div>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
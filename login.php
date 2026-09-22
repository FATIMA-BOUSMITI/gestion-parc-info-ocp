<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Connexion - Gestion Parc</title>
</head>
<body class="bg-primary">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-4">
                
                <?php if(isset($_GET['error'])): ?>
                    <div class="alert alert-danger border-0 shadow-sm mb-3">
                        Identifiants incorrects.
                    </div>
                <?php endif; ?>

                <div class="card shadow-lg border-0 rounded-lg mt-5">
                    <div class="card-header">
                        <h3 class="text-center font-weight-light my-4">Connexion</h3>
                    </div>
                    <div class="card-body">
                        <form action="admin/logic.php?action=login" method="POST">
                            
                            <div class="form-floating mb-3">
                                <input class="form-control" name="login_identity" type="text" placeholder="Email ou Pseudo" required />
                                <label>Email ou Nom d'utilisateur</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input class="form-control" name="password" type="password" placeholder="Mot de passe" required />
                                <label>Mot de passe</label>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                <a class="small text-decoration-none" href="password_reset.php">Mot de passe oublié ?</a>
                                <button type="submit" class="btn btn-primary px-4">Se connecter</button>
                            </div>
                        </form>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</body>
</html>
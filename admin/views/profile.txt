<?php
// On récupère les infos de l'utilisateur depuis la session (déjà démarrée dans votre index)
$user_name = $_SESSION['user_name'] ?? 'Utilisateur';
$user_email = $_SESSION['user_email'] ?? 'non@defini.com';
$user_role = $_SESSION['user_role'] ?? 'Administrateur';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Mon Profil</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Profil</li>
    </ol>

    <div class="row">
        <div class="col-xl-4">
            <div class="card mb-4 mb-xl-0">
                <div class="card-header">Photo de profil</div>
                <div class="card-body text-center">
                    <img class="img-account-profile rounded-circle mb-2" src="https://sb-admin-pro.startbootstrap.com/assets/img/illustrations/profiles/profile-1.png" alt="" style="width: 150px;">
                    <div class="small font-italic text-muted mb-4">JPG ou PNG (max 5 Mo)</div>
                    <button class="btn btn-primary" type="button">Changer l'image</button>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">Détails du compte</div>
                <div class="card-body">
                    <form action="logic.php?action=update_profile" method="POST">
                        <div class="row gx-3 mb-3">
                            <div class="col-md-6">
                                <label class="small mb-1" for="inputUsername">Nom d'utilisateur</label>
                                <input class="form-control" id="inputUsername" name="username" type="text" value="<?php echo htmlspecialchars($user_name); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="small mb-1" for="inputRole">Rôle</label>
                                <input class="form-control" id="inputRole" type="text" value="<?php echo $user_role; ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="small mb-1" for="inputEmailAddress">Adresse Email</label>
                            <input class="form-control" id="inputEmailAddress" name="email" type="email" value="<?php echo htmlspecialchars($user_email); ?>">
                        </div>

                        <hr>
                        <h5>Changer le mot de passe</h5>
                        <div class="row gx-3 mb-3">
                            <div class="col-md-6">
                                <label class="small mb-1" for="inputPassword">Nouveau mot de passe</label>
                                <input class="form-control" id="inputPassword" name="password" type="password" placeholder="Laissez vide pour ne pas changer">
                            </div>
                            <div class="col-md-6">
                                <label class="small mb-1" for="inputConfirmPassword">Confirmer le mot de passe</label>
                                <input class="form-control" id="inputConfirmPassword" name="confirm_password" type="password">
                            </div>
                        </div>
                        
                        <button class="btn btn-success" type="submit">Enregistrer les modifications</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
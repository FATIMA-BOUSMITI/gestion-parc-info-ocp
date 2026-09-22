<?php
session_start();
include __DIR__ . '/includes/db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['id'] = $user['id']; 
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: admin/");
        } elseif ($user['role'] === 'technicien') {
            header("Location: dashboard_technicien.php");
        } else {
            header("Location: dashboard_utilisateur.php");
        }
        exit();
    } else {
        $error = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Gestion Parc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .login-card {
            background: #ffffff;
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        .login-card h2 {
            font-weight: 600;
            color: #333;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .form-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #555;
        }
        .form-control {
            border-radius: 0.5rem;
            padding: 0.75rem;
            border: 1px solid #ddd;
        }
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
            border-color: #667eea;
        }
        .btn-login {
            background: #667eea;
            border: none;
            padding: 0.75rem;
            border-radius: 0.5rem;
            font-weight: 600;
            width: 100%;
            margin-top: 1rem;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            background: #5a6fd6;
            transform: translateY(-1px);
        }
        .forgot-password {
            display: block;
            text-align: center;
            margin-top: 1.2rem;
            font-size: 0.85rem;
            color: #667eea;
            text-decoration: none;
            transition: color 0.2s;
        }
        .forgot-password:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        .error-msg {
            background: #fff5f5;
            color: #c53030;
            padding: 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            margin-top: 1rem;
            text-align: center;
            border: 1px solid #feb2b2;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h2>🔐 Connexion</h2>
    
    <form method="POST" action="">
        <div class="mb-3">
            <label for="username" class="form-label">Nom d'utilisateur</label>
            <input type="text" class="form-control" id="username" name="username" 
                   placeholder="Votre pseudo" required autofocus>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" 
                   placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-login">Se connecter</button>
        
        <a href="password_reset.php" class="forgot-password">Mot de passe oublié ?</a>
    </form>

    <?php if (isset($error)): ?>
        <div class="error-msg">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
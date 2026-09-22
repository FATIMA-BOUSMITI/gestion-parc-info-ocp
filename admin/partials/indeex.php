<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Deskora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fc;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 40px;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            background: white;
        }
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
        }
        .logo-icon {
            background-color: #4e5bf2;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .logo-text {
            font-weight: 600;
            font-size: 22px;
            color: #333;
        }
        .welcome-text {
            font-weight: 700;
            font-size: 28px;
            color: #1a1a1a;
            margin-bottom: 5px;
        }
        .subtitle {
            color: #6c757d;
            margin-bottom: 35px;
        }
        .form-label {
            font-weight: 500;
            color: #444;
            margin-bottom: 8px;
        }
        .input-group-text {
            background-color: transparent;
            border-right: none;
            color: #adb5bd;
        }
        .form-control {
            border-left: none;
            padding: 12px;
            color: #495057;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }
        .btn-login {
            background-color: #4255ff;
            border: none;
            padding: 12px;
            font-weight: 500;
            border-radius: 8px;
            margin-top: 20px;
            transition: 0.3s;
        }
        .btn-login:hover {
            background-color: #3646d9;
        }
        .forgot-password {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #6366f1;
            text-decoration: none;
            font-weight: 500;
        }
        .forgot-password:hover {
            color: #4e5bf2;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="logo-container">
        <div class="logo-icon">
            <i class="fa-solid fa-lock-open"></i>
        </div>
        <span class="logo-text">Deskora</span>
    </div>

    <div class="text-center">
        <h1 class="welcome-text">Welcome Back</h1>
        <p class="subtitle">Sign in to your account</p>
    </div>

    <form action="login_process.php" method="POST">
        <div class="mb-4">
            <label class="form-label">Email or Username</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
                <input type="text" name="username" class="form-control" placeholder="admin@example.com" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 btn-login">Login</button>
        
        <a href="#" class="forgot-password">Forgot password?</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
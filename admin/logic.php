<?php
// =========================================================
// --- 1. CONFIGURATION & BASE DE DONNÉES ---
// =========================================================
$db_connected = false;
if (file_exists(__DIR__ . '/includes/db.php')) {
    include __DIR__ . '/includes/db.php';
    if (isset($conn)) $db_connected = true;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Paramètres de base
$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? '';
$message = "";

// =========================================================
// --- 2. AUTHENTIFICATION (LOGIN / LOGOUT / RESET) ---
// =========================================================

// A. CONNEXION (Supporte Email OU Username)
if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST' && $db_connected) {
    
    // On récupère l'identifiant unique (qui peut être l'email ou le pseudo)
    // Assurez-vous que l'attribut 'name' dans votre HTML est bien 'login_identity'
    $login_input = trim($_POST['login_identity'] ?? $_POST['email']); 
    $password = $_POST['password'];

    try {
        // On cherche l'utilisateur où l'email correspond OU le username correspond
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ? LIMIT 1");
        $stmt->execute([$login_input, $login_input]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérification du mot de passe
        if ($user && password_verify($password, $user['password'])) {
            // Création de la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['username'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            // Redirection vers le dashboard
            header("Location: index.php?page=dashboard");
            exit;
        } else {
            // Identifiants incorrects
            header("Location: ../test.php?error=1");
            exit;
        }
    } catch (PDOException $e) {
        // En cas d'erreur SQL (ex: colonne manquante)
        die("Erreur de base de données : " . $e->getMessage());
    }
}

// B. DÉCONNEXION
// Dans admin/logic.php
if ($action === 'logout') {
    session_destroy(); // On détruit la session
    header("Location: ../test.php"); // On remonte d'un cran pour trouver login.php
    exit;
}

// C. RÉCUPÉRATION MOT DE PASSE (SIMULATION)
if ($action === 'reset_password' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $_SESSION['message'] = "✅ Si cet email existe, un lien de récupération a été envoyé à : " . htmlspecialchars($email);
    header("Location: ../password_reset.php");
    exit;
}

// Sécurité : redirection si non connecté (sauf pour le login)
if (!isset($_SESSION['user_id']) && !in_array($action, ['test', 'reset_password'])) {
    // Décommentez la ligne suivante pour forcer la connexion
    // header("Location: ../login.php"); exit;
}

// =========================================================
// --- 3. TRAITEMENT DES ACTIONS (POST) ---
// =========================================================
// =========================================================
// --- 3. TRAITEMENT DES ACTIONS (POST) ---
// =========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $db_connected) {
    
   
    // --- A. AJOUT UTILISATEUR ---
    if (isset($_POST['add_user'])) {
        $username = trim($_POST['username']);
        $email    = trim($_POST['email']);
        $role     = $_POST['role'];
        $status   = $_POST['status'] ?? 'active';
        $password = !empty($_POST['password']) ? $_POST['password'] : '123456';
        $hashed   = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Vérification doublon
            $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
            $checkStmt->execute([$email, $username]);
            
            if ($checkStmt->fetch()) {
                $_SESSION['message'] = "⛔ Erreur : Email ou Nom d'utilisateur déjà utilisé.";
            } else {
                $stmt = $conn->prepare("INSERT INTO users (username, role, email, password, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$username, $role, $email, $hashed, $status]);
                $_SESSION['message'] = "✅ Utilisateur ajouté avec succès !";
            }
        } catch (Exception $e) {
            $_SESSION['message'] = "⛔ Erreur technique : " . $e->getMessage();
        }
        
        header("Location: index.php?page=users");
        exit;
    }

    // --- B. MODIFICATION UTILISATEUR (EDIT) ---
    if (isset($_POST['action']) && $_POST['action'] === 'edit_user') {
        $id       = $_POST['user_id'] ?? null;
        $username = trim($_POST['username']);
        $email    = trim($_POST['email']);
        $role     = $_POST['role'];
        $status   = $_POST['status'];

        if ($id) {
            try {
                if (!empty($_POST['password'])) {
                    // Mise à jour AVEC mot de passe
                    $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET username=?, email=?, role=?, status=?, password=? WHERE id=?");
                    $stmt->execute([$username, $email, $role, $status, $hashed, $id]);
                } else {
                    // Mise à jour SANS toucher au mot de passe
                    $stmt = $conn->prepare("UPDATE users SET username=?, email=?, role=?, status=? WHERE id=?");
                    $stmt->execute([$username, $email, $role, $status, $id]);
                }
                
                $_SESSION['message'] = "✅ Modification enregistrée avec succès !";
            } catch (Exception $e) {
                $_SESSION['message'] = "⛔ Erreur SQL : " . $e->getMessage();
            }
        } else {
            $_SESSION['message'] = "⛔ Erreur : ID utilisateur introuvable.";
        }
        
        header("Location: index.php?page=users");
        exit;
    }


    // AJOUT MACHINE / TICKET
    if (isset($_POST['add_machine'])) {
        $stmt = $conn->prepare("INSERT INTO machines (service, type, status, created_at) VALUES (?, ?, 'online', NOW())");
        $stmt->execute([$_POST['name'], $_POST['ip']]);
        $message = "✅ Machine ajoutée !";
    }
    if (isset($_POST['add_ticket'])) {
        $stmt = $conn->prepare("INSERT INTO tickets (description, priority, status, created_at) VALUES (?, ?, 'ouvert', NOW())");
        $stmt->execute([$_POST['title'], $_POST['priority']]);
        $message = "✅ Ticket créé !";
    }
}

// =========================================================
// --- 4. SUPPRESSION (DELETE) ---
// =========================================================
if (isset($_GET['delete']) && isset($_GET['id']) && $db_connected) {
    $id_to_delete = $_GET['id'];
    $type_to_delete = $_GET['delete'];
    $table = match($type_to_delete) { 'user'=>'users', 'machine'=>'machines', 'ticket'=>'tickets', default=>null };
    
    if ($table && ($type_to_delete !== 'user' || $id_to_delete != $_SESSION['user_id'])) {
        $stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
        $stmt->execute([$id_to_delete]);
        header("Location: ?page=$page&msg=deleted"); 
        exit;
    }
}

// =========================================================
// --- 5. CALCULS & KPI ---
// =========================================================
if ($db_connected) {
    // Listes
    $users = $conn->query("SELECT * FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    $machines = $conn->query("SELECT * FROM machines ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    $tickets = $conn->query("SELECT * FROM tickets ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

    // KPI
    $active_machines = count(array_filter($machines, fn($m) => ($m['status']??'') == 'online'));
    $open_tickets = count(array_filter($tickets, fn($t) => ($t['status']??'') == 'ouvert'));
    $total_users = count($users);
    
    try {
        $nb_admins = $conn->query("SELECT COUNT(*) FROM users WHERE role LIKE '%admin%'")->fetchColumn();
        $nb_techs  = $conn->query("SELECT COUNT(*) FROM users WHERE role LIKE '%tech%' OR role = 'manager'")->fetchColumn();
        $nb_users  = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'user' OR role = 'utilisateur'")->fetchColumn();
        $new_users_week = $conn->query("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
        $deleted_users_week = $conn->query("SELECT COUNT(*) FROM logs WHERE action = 'user_deleted' AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
    } catch (Exception $e) {
        $nb_admins = $nb_techs = $nb_users = $new_users_week = $deleted_users_week = 0;
    }
} else {
    $users = $machines = $tickets = [];
    $total_users = $nb_admins = $nb_techs = $nb_users = $new_users_week = $deleted_users_week = 0;
    $active_machines = $open_tickets = 0;
}
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Admin Panel</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* --- VARIABLES & BASE --- */
    :root { --primary: #4318ff; --secondary: #A3AED0; --dark-bg: #111c44; --light-bg: #f4f7fe; --success: #05CD99; --warning: #FFB547; --danger: #EE5D50; }
    body { background: var(--light-bg); font-family: 'DM Sans', sans-serif; color: #2B3674; overflow-x: hidden; }
    
    /* --- SIDEBAR --- */
    .sidebar { position: fixed; width: 260px; height: 100vh; background: var(--dark-bg); color: white; padding: 20px; z-index: 1000; }
    .sidebar-logo { font-size: 24px; font-weight: 700; margin-bottom: 40px; display: flex; align-items: center; gap: 10px; padding-left: 10px; }
    .nav-link { color: #A3AED0; padding: 12px 20px; border-radius: 10px; margin-bottom: 5px; display: flex; align-items: center; gap: 15px; transition: 0.3s; }
    .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.1); color: white; border-right: 4px solid var(--primary); }
    .nav-link i { font-size: 1.2rem; }
    
    /* --- MAIN LAYOUT --- */
    .main-content { margin-left: 260px; padding: 30px; }

    /* --- TOPBAR --- */
    .topbar { 
        display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; 
        background-color: rgba(255, 255, 255, 0.8); 
        backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px);
        padding: 15px 25px; border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        position: sticky; top: 20px; z-index: 900;
        overflow: visible; /* CRUCIAL pour le menu */
    }

    .search-box input { border: none; background: #F4F7FE; padding: 10px 20px; border-radius: 30px; width: 250px; transition: all 0.3s; }
    .search-box input:focus { box-shadow: 0 0 0 3px rgba(67, 24, 255, 0.1); outline: none; }

    .user-profile { 
        width: 45px; height: 45px; border-radius: 50%; background: var(--primary); color: white; 
        display: flex; align-items: center; justify-content: center; font-weight: bold; cursor: pointer;
        transition: transform 0.2s;
    }
    .user-profile:hover { transform: scale(1.05); }

    /* --- MENU DÉROULANT (CORRIGÉ) --- */
    .dropdown-menu { 
        border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); padding: 10px; 
        margin-top: 10px !important; 
        z-index: 9999 !important; /* Force le menu au-dessus de tout */
    }
    .dropdown-item { border-radius: 8px; padding: 10px 15px; font-weight: 500; color: #2B3674; }
    .dropdown-item:hover { background: #F4F7FE; color: var(--primary); }

    /* Animation Keyframes */
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate.slideIn { animation: slideIn 0.2s ease forwards; }

    /* --- STYLES DASHBOARD --- */
    .card-custom { background: white; border-radius: 20px; border: none; padding: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.02); height: 100%; }
    .stat-card { display: flex; align-items: center; justify-content: space-between; }
    .stat-icon { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .table thead th { color: var(--secondary); font-weight: 500; font-size: 0.85rem; border-bottom: 1px solid #f0f0f0; padding-bottom: 15px; }
    .table td { vertical-align: middle; padding: 15px 5px; border-bottom: 1px solid #f9f9f9; }
    .avatar-initials { width: 35px; height: 35px; border-radius: 50%; background: linear-gradient(135deg, #868CFF 0%, #4318FF 100%); color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; margin-right: 10px; }
    .badge-soft { padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 0.75rem; }
    .bg-soft-success { background: #E6FFFA; color: var(--success); }
    .bg-soft-warning { background: #FFF7E6; color: var(--warning); }
    .bg-soft-danger { background: #FFF2F2; color: var(--danger); }
    .bg-soft-primary { background: #E6F7FF; color: var(--primary); }
    .bg-soft-info {
        background-color: rgba(13, 202, 240, 0.15) !important; /* Fond bleu très clair */
        color: #0dcaf0 !important;       /* Texte bleu cyan */
        font-weight: 600;
        padding: 0.35em 0.65em;
        border-radius: 0.50rem;
    }
    .machine-card { margin-bottom: 20px; transition: transform 0.2s; }
    .machine-card:hover { transform: translateY(-5px); }
    .progress { height: 6px; border-radius: 10px; background: #E9EDF7; margin-bottom: 15px; }
    .progress-label { display: flex; justify-content: space-between; font-size: 0.8rem; color: var(--secondary); margin-bottom: 5px; }
    .btn-primary-custom { background: var(--primary); color: white; border-radius: 10px; padding: 8px 20px; border: none; }
    .btn-primary-custom:hover { background: #3311cc; color: white; }
</style>
</head>
<body>
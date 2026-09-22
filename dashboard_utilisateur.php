<?php
session_start();
include __DIR__ . '/includes/db.php';

// Sécurité : Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['id'];
$username = $_SESSION['username'] ?? 'Utilisateur';

// --- STATISTIQUES PERSONNELLES ---
$stats = $conn->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'réparée' THEN 1 ELSE 0 END) as resolus,
        SUM(CASE WHEN status != 'réparée' THEN 1 ELSE 0 END) as en_cours
    FROM tickets WHERE user_id = ?
");
$stats->execute([$user_id]);
$s = $stats->fetch(PDO::FETCH_ASSOC);

// --- RÉCUPÉRATION DES TICKETS ---
$limit = 6;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$start = ($page - 1) * $limit;

$stmt = $conn->prepare("
    SELECT t.*, m.service as machine_name 
    FROM tickets t 
    LEFT JOIN machines m ON t.machine_id = m.id 
    WHERE t.user_id = :uid 
    ORDER BY t.created_at DESC 
    LIMIT :start, :limit
");
$stmt->bindValue(':uid', $user_id, PDO::PARAM_INT);
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- LISTE DES MACHINES ---
$machinesList = $conn->query("SELECT id, service FROM machines ORDER BY service ASC")->fetchAll(PDO::FETCH_ASSOC);

$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UserPortal | Support IT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-slate-50 text-slate-900 font-sans">

    <nav class="bg-indigo-900 text-white sticky top-0 z-50 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <div class="bg-white/10 p-2 rounded-lg text-indigo-200">
                        <i class="bi bi-person-circle text-xl"></i>
                    </div>
                    <span class="text-xl font-black tracking-tighter italic text-white">USER<span class="text-indigo-400">PORTAL</span></span>
                </div>
                <div class="flex items-center gap-4 text-sm font-bold">
                    <span class="hidden md:block text-indigo-200">Session : <?= htmlspecialchars($username) ?></span>
                    <div class="h-6 w-px bg-indigo-700 mx-2"></div>
                    <a href="logout.php" class="text-indigo-300 hover:text-white transition text-lg"><i class="bi bi-power"></i></a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 lg:px-8 py-8">

        <div class="mb-10">
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Mon Espace Support</h1>
            <p class="text-slate-500 font-medium italic">Suivez vos demandes et signalez vos pannes</p>
        </div>

        <?php if($success): ?>
        <div class="mb-8 bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-4 animate-pulse">
            <i class="bi bi-check-all text-2xl"></i>
            <span class="font-bold text-sm"><?= $success ?></span>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <div class="lg:col-span-1 space-y-6">
                
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 ring-1 ring-slate-200/50">
                    <h3 class="font-black text-indigo-900 mb-6 flex items-center gap-2 uppercase text-xs tracking-widest">
                        <i class="bi bi-plus-circle-fill"></i> Signaler une panne
                    </h3>
                    <form action="submit_ticket.php" method="POST" class="space-y-4">
                        <div>
                            <select name="machine_id" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition font-medium">
                                <option value="" disabled selected>Sélectionner machine...</option>
                                <?php foreach($machinesList as $m): ?>
                                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['service']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <textarea name="description" rows="4" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition font-medium" placeholder="Décrivez le problème..."></textarea>
                        </div>
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 rounded-2xl shadow-xl shadow-indigo-100 transition active:scale-95 uppercase text-xs tracking-widest">
                            Envoyer le ticket
                        </button>
                    </form>
                </div>

                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <div class="relative h-48">
                        <canvas id="personalChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3">
                
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-indigo-900 p-5 rounded-3xl text-white shadow-lg">
                        <p class="text-[10px] font-bold text-indigo-300 uppercase tracking-widest">Total</p>
                        <p class="text-2xl font-black"><?= $s['total'] ?></p>
                    </div>
                    <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm border-l-4 border-l-amber-500">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">En cours</p>
                        <p class="text-2xl font-black text-slate-800"><?= $s['en_cours'] ?></p>
                    </div>
                    <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm border-l-4 border-l-emerald-500">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Résolus</p>
                        <p class="text-2xl font-black text-slate-800"><?= $s['resolus'] ?></p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-6 border-b border-slate-50 bg-slate-50/30">
                        <h3 class="font-bold text-slate-800 italic">Mes interventions récentes</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50/50 text-slate-400 text-[10px] uppercase font-black tracking-tighter">
                                <tr>
                                    <th class="p-5">Équipement</th>
                                    <th class="p-5">Description</th>
                                    <th class="p-5">Statut</th>
                                    <th class="p-5 text-right">Date</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm font-medium">
                                <?php foreach($tickets as $t): ?>
                                <tr class="border-b border-slate-50 hover:bg-indigo-50/30 transition last:border-0">
                                    <td class="p-5">
                                        <div class="text-indigo-900 font-black">#<?= $t['id'] ?> - <?= htmlspecialchars($t['machine_name']) ?></div>
                                    </td>
                                    <td class="p-5 text-slate-500"><?= htmlspecialchars($t['description']) ?></td>
                                    <td class="p-5">
                                        <?php if($t['status'] == 'réparée'): ?>
                                            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-black uppercase">Réparé</span>
                                        <?php else: ?>
                                            <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-[10px] font-black uppercase italic tracking-tighter">Traitement...</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-5 text-right text-slate-400 text-xs">
                                        <?= date('d M Y', strtotime($t['created_at'])) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // GRAPHIQUE PERSONNEL
        const ctx = document.getElementById('personalChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Résolus', 'En cours'],
                datasets: [{
                    data: [<?= (int)$s['resolus'] ?>, <?= (int)$s['en_cours'] ?>],
                    backgroundColor: ['#10b981', '#f59e0b'],
                    borderWidth: 4,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                cutout: '80%',
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 8, font: { size: 10, weight: '900' } } }
                }
            }
        });
    </script>
</body>
</html>
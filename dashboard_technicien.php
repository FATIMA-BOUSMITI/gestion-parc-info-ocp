<?php
session_start();
// Connexion à la base de données
include __DIR__ . '/includes/db.php'; 

// --- SÉCURITÉ : Vérification du rôle ---
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'technicien') {
    header("Location: login.php");
    exit();
}

$tech_id = (int) $_SESSION['id'];
$username = $_SESSION['username'] ?? 'Technicien';
$toast = "";

// --- LOGIQUE MÉTIER : Actions du technicien ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Prendre un ticket (Assignation)
    if (isset($_POST['take_ticket'])) {
        $tid = (int)$_POST['ticket_id'];
        $stmt = $conn->prepare("UPDATE tickets SET assigned_to = ?, status = 'en cours' WHERE id = ?");
        $stmt->execute([$tech_id, $tid]);
        $toast = "Ticket #$tid est maintenant à votre charge.";
    } 
    // 2. Résoudre un ticket
    elseif (isset($_POST['mark_resolved'])) {
        $tid = (int)$_POST['ticket_id'];
        $mid = (int)$_POST['machine_id'];
        $note = $_POST['resolution_note'] ?? '';

        $stmt = $conn->prepare("UPDATE tickets SET status = 'réparée', resolution_note = ?, resolved_at = NOW() WHERE id = ?");
        $stmt->execute([$note, $tid]);
        
        // On remet la machine en état opérationnel (Statut : online)
        $stmt2 = $conn->prepare("UPDATE machines SET status = 'online' WHERE id = ?");
        $stmt2->execute([$mid]);
        $toast = "Intervention terminée pour le ticket #$tid.";
    }
}

// --- RÉCUPÉRATION DES DONNÉES ---

// 1. Liste des tickets (Fusion entre Tickets, Machines et Users)
// Note : m.service est utilisé ici car m.name n'existe pas
$stmt = $conn->prepare("
    SELECT t.*, 
           m.service as machine_name, 
           u.username as declare_par
    FROM tickets t
    LEFT JOIN machines m ON t.machine_id = m.id
    LEFT JOIN users u ON t.user_id = u.id
    WHERE t.assigned_to = :tid OR (t.assigned_to IS NULL AND t.status = 'ouvert')
    ORDER BY t.status DESC, t.created_at DESC
");
$stmt->execute([':tid' => $tech_id]);
$all_tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Statistiques pour les compteurs du haut
$stats = $conn->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'en cours' AND assigned_to = ? THEN 1 ELSE 0 END) as mes_en_cours,
        SUM(CASE WHEN status = 'ouvert' AND assigned_to IS NULL THEN 1 ELSE 0 END) as en_attente,
        SUM(CASE WHEN status = 'réparée' AND assigned_to = ? THEN 1 ELSE 0 END) as mes_resolus
    FROM tickets
");
$stats->execute([$tech_id, $tech_id]);
$s = $stats->fetch(PDO::FETCH_ASSOC);

// Fonction de protection contre les failles XSS
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechPanel | Espace Technicien</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-slate-50 text-slate-900">

<div class="flex min-h-screen">
    <aside class="w-64 bg-slate-900 text-white flex flex-col hidden md:flex">
        <div class="p-6 text-2xl font-bold border-b border-slate-800">
            <span class="text-blue-500">🔧</span> TechPanel
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="#" class="flex items-center gap-3 p-3 bg-blue-600 rounded-lg"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="logout.php" class="flex items-center gap-3 p-3 hover:bg-red-900 text-red-400 rounded-lg transition mt-auto"><i class="bi bi-box-arrow-left"></i> Déconnexion</a>
        </nav>
    </aside>

    <main class="flex-1 p-8">
        <header class="mb-8">
            <h1 class="text-2xl font-bold">Espace Technicien</h1>
            <p class="text-slate-500">Bienvenue, <?= e($username) ?>. Gérez vos pannes et interventions.</p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <div class="text-slate-400 text-xs font-bold uppercase mb-1">Total à traiter</div>
                <div class="text-2xl font-black"><?= (int)$s['total'] ?></div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-amber-500">
                <div class="text-amber-500 text-xs font-bold uppercase mb-1">Mes tickets en cours</div>
                <div class="text-2xl font-black"><?= (int)$s['mes_en_cours'] ?></div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-red-500">
                <div class="text-red-500 text-xs font-bold uppercase mb-1">Pannes non assignées</div>
                <div class="text-2xl font-black"><?= (int)$s['en_attente'] ?></div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-green-500">
                <div class="text-green-500 text-xs font-bold uppercase mb-1">Mes résolutions</div>
                <div class="text-2xl font-black"><?= (int)$s['mes_resolus'] ?></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b font-bold text-lg">Liste des interventions</div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-slate-400 text-[10px] uppercase font-bold">
                        <tr>
                            <th class="p-4">Machine</th>
                            <th class="p-4">Description</th>
                            <th class="p-4">Statut</th>
                            <th class="p-4">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php foreach($all_tickets as $t): 
                            $is_mine = ($t['assigned_to'] == $tech_id);
                        ?>
                        <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                            <td class="p-4">
                                <div class="font-bold"><?= e($t['machine_name'] ?? 'Non spécifiée') ?></div>
                                <div class="text-[10px] text-slate-400">Signalé par: <?= e($t['declare_par']) ?></div>
                            </td>
                            <td class="p-4 text-slate-600"><?= e($t['description']) ?></td>
                            <td class="p-4">
                                <?php if($t['status'] == 'ouvert'): ?>
                                    <span class="px-2 py-1 bg-red-100 text-red-600 rounded-full text-[10px] font-bold">OUVERT</span>
                                <?php elseif($t['status'] == 'en cours'): ?>
                                    <span class="px-2 py-1 bg-amber-100 text-amber-600 rounded-full text-[10px] font-bold">EN COURS</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-600 rounded-full text-[10px] font-bold">RÉPARÉE</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4">
                                <?php if(!$t['assigned_to']): ?>
                                    <form method="POST">
                                        <input type="hidden" name="ticket_id" value="<?= $t['id'] ?>">
                                        <button name="take_ticket" class="bg-slate-900 text-white px-4 py-1.5 rounded-lg text-xs font-bold">Prendre</button>
                                    </form>
                                <?php elseif($is_mine && $t['status'] != 'réparée'): ?>
                                    <button onclick="openModal(<?= $t['id'] ?>, <?= $t['machine_id'] ?>)" class="bg-blue-600 text-white px-4 py-1.5 rounded-lg text-xs font-bold">Clôturer</button>
                                <?php else: ?>
                                    <span class="text-slate-300 italic">Terminé</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<div id="solveModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md p-6">
        <h3 class="font-bold text-xl mb-4">Résoudre la panne</h3>
        <form method="POST">
            <input type="hidden" id="modal_tid" name="ticket_id">
            <input type="hidden" id="modal_mid" name="machine_id">
            <textarea name="resolution_note" rows="4" class="w-full border rounded-xl p-3 text-sm mb-4 outline-none focus:ring-2 focus:ring-blue-500" placeholder="Décrivez ce que vous avez fait..." required></textarea>
            <div class="flex gap-2">
                <button type="button" onclick="closeModal()" class="flex-1 py-2 bg-slate-100 rounded-xl font-bold">Annuler</button>
                <button type="submit" name="mark_resolved" class="flex-1 py-2 bg-green-600 text-white rounded-xl font-bold">Confirmer</button>
            </div>
        </form>
    </div>
</div>

<?php if($toast): ?>
<div id="toast" class="fixed bottom-5 right-5 bg-slate-900 text-white px-6 py-4 rounded-xl shadow-2xl animate-bounce">
    <?= $toast ?>
</div>
<script>setTimeout(() => document.getElementById('toast').remove(), 3000);</script>
<?php endif; ?>

<script>
function openModal(tid, mid) {
    document.getElementById('modal_tid').value = tid;
    document.getElementById('modal_mid').value = mid;
    document.getElementById('solveModal').classList.remove('hidden');
}
function closeModal() {
    document.getElementById('solveModal').classList.add('hidden');
}
</script>

</body>
</html>
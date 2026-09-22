<?php 
// Inclusion des modals si vous en avez
if (file_exists(__DIR__ . '/modals.php')) {
    include __DIR__ . '/modals.php';
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const chartEl = document.getElementById('dashboardChart');
    if(chartEl) {
        new Chart(chartEl.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Sep','Oct','Nov','Dec','Jan','Feb','Mar'],
                datasets: [{
                    label: 'Revenue', data: [30, 45, 35, 60, 55, 75, 80],
                    borderColor: '#4318ff', backgroundColor: 'rgba(67, 24, 255, 0.05)',
                    tension: 0.4, fill: true, pointRadius: 0
                }]
            },
            options: { plugins: {legend: {display: false}}, scales: {x:{grid:{display:false}}, y:{grid:{borderDash:[5,5]}}} }
        });
    }
    document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.edit-user-btn');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            // 1. Récupérer les données stockées dans le bouton (data-attributes)
            const id = this.getAttribute('data-id');
            const username = this.getAttribute('data-username');
            const email = this.getAttribute('data-email');
            const role = this.getAttribute('data-role');

            // 2. Injecter ces données dans les inputs de la Modale #editUserModal
            document.getElementById('edit_user_id').value = id;
            document.getElementById('edit_username').value = username;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_role').value = role;
        });
    });
});
</script>

</body>
</html>
<?php
$title = 'Agendamentos - ClickBeard';

ob_start(); ?>
<h2 class="mb-4 text-center">Área do Cliente</h2>
<div class="alert alert-success text-center">
    Bem-vindo, <strong><?= htmlspecialchars($usuario['nome']) ?></strong>!
</div>
<p class="text-center">Em breve seus agendamentos aparecerão aqui.</p>

<button id="btnBuscarAgendamentos" class="btn btn-primary d-block mx-auto mt-4">Buscar Agendamentos</button>
<pre id="resultadoAgendamentos" class="mt-3 bg-light p-3 rounded"></pre>

<script>
document.getElementById('btnBuscarAgendamentos').addEventListener('click', async function() {
    const token = localStorage.getItem('token');
    if (!token) {
        document.getElementById('resultadoAgendamentos').innerText = 'Token não encontrado. Faça login.';
        return;
    }

    const response = await fetch('/agendamentos', {
        headers: {
            'Authorization': 'Bearer ' + token,
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    const data = await response.json();
    document.getElementById('resultadoAgendamentos').innerText = JSON.stringify(data, null, 2);
});
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
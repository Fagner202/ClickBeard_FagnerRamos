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
    const token = localStorage.getItem('token');
    console.log('Token no localStorage:', token);

    fetch('/agendamentos', {
    method: 'GET',
    headers: {
        'Authorization': `Bearer ${token}`,
        'X-Requested-With': 'XMLHttpRequest'
    }
    })
    .then(response => response.json())
    .then(data => {
    console.log('Resposta da API:', data);
    })
    .catch(e => console.error(e));
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
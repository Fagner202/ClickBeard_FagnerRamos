<?php
session_start();
require_once __DIR__ . '/../../utils/utils.php';

$usuario = autenticarUsuario();

ob_start();
?>

<h1>Lista de Barbeiros</h1>

<ul>
    <?php foreach ($barbeiros as $barbeiro): ?>
        <li>
            ID do Cliente: <?= htmlspecialchars($barbeiro['cliente_id']) ?><br>
            Idade: <?= htmlspecialchars($barbeiro['idade']) ?><br>
            Data de Contratação: <?= htmlspecialchars($barbeiro['data_contratacao']) ?><br>
            Status: <?= htmlspecialchars($barbeiro['status']) ?><br>

            <button onclick="mostrarEspecialidades(<?= $barbeiro['cliente_id'] ?>)">Especialidades</button>
        </li>
        <hr>
    <?php endforeach; ?>
</ul>

<!-- Função JavaScript -->
<script>
    function mostrarEspecialidades(clienteId) {
        alert("Mostrar especialidades para o barbeiro com ID: " + clienteId);
        // Aqui você pode futuramente fazer uma chamada AJAX ou redirecionar para outra página
    }
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/auth_layout.php';
?>

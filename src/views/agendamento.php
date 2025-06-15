<?php
session_start();
require_once __DIR__ . '/../utils/utils.php';
$usuario = autenticarUsuario();
$title = 'Agendamentos - ClickBeard';

// dd($usuario);
?>

<h2 class="mb-4 text-center">Área do Cliente</h2>
<div class="alert alert-success text-center">

    Olá, <?= htmlspecialchars($usuario['nome']) ?>! Bem-vindo à sua área de agendamentos.
   
</div>
<p class="text-center">Em breve seus agendamentos aparecerão aqui.</p>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
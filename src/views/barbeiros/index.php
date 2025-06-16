<?php
session_start();
// require_once __DIR__ . '/../utils/utils.php';
// require_once __DIR__ . '/../config.php';
$usuario = autenticarUsuario();
$title = 'Páigna de teste de rotas';
$cssPage = 'agendamentos'; // Para carregar o CSS específico

ob_start();
?>

<div class="row">
    <h1>Aqui será a index para Barbeiros</h1>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/auth_layout.php';
?>
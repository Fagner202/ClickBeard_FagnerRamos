<?php
session_start();
require_once __DIR__ . '/../utils/utils.php';
// require_once __DIR__ . '/../config.php';
$usuario = autenticarUsuario();
$title = 'Agendamentos - ClickBeard';
$cssPage = 'agendamentos'; // Para carregar o CSS específico

ob_start();
?>


<?php
$content = ob_get_clean();
require __DIR__ . '/layouts/auth_layout.php';
?>
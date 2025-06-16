<?php
session_start();
require_once __DIR__ . '/../utils/utils.php';
$usuario = autenticarUsuario();
$title = 'Agendamentos - ClickBeard';

// dd($usuario);
?>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
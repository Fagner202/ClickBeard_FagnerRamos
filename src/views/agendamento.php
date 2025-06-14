<?php
session_start();
$usuario = $_SESSION['usuario'] ?? null;
$title = 'Agendamentos - ClickBeard';

ob_start();
?>

<h1>teste</h1>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
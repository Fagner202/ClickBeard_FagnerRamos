<?php
session_start();
require_once __DIR__ . '/../../utils/utils.php';

$usuario = autenticarUsuario();

// dd($barbeiros);

ob_start();
?>



<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/auth_layout.php';
?>


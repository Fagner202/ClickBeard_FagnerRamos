<?php
$_SESSION = autenticarUsuario();
$usuario = $_SESSION['nome'] ?? null;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'ClickBeard' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles.css">
    <?php if (!empty($cssPage)): ?>
        <link rel="stylesheet" href="/css/<?= $cssPage ?>.css">
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="d-flex">
    <?php if ($usuario): ?>
    <!-- Menu Vertical -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h3 class="text-white">ClickBeard</h3>
            <p class="text-light mb-0">Sistema de Agendamento</p>
        </div>
        <ul class="nav flex-column pt-3">
            <li class="nav-item"><a class="nav-link active" href="/agendamentos"><i class="bi bi-house-door"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="/teste"><i class="bi bi-calendar-plus"></i> Novo Agendamento</a></li>
            <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-scissors"></i> Barbeiros</a></li>
            <li class="nav-item"><a class="nav-link" href="/teste"><i class="bi bi-tags"></i> Especialidades</a></li>
            <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-people"></i> Clientes</a></li>
            <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-clock-history"></i> Histórico</a></li>
            <li class="nav-item mt-4"><a class="nav-link" href="#"><i class="bi bi-gear"></i> Configurações</a></li>
            <li class="nav-item"><a class="nav-link" href="/logout"><i class="bi bi-box-arrow-right"></i> Sairdd</a></li>
        </ul>
    </nav>
    <?php endif; ?>

    <div class="main-content">
        <?= $content ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

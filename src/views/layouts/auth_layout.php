<?php
$currentUrl = $_SERVER['REQUEST_URI'];
// dd($currentUrl);
$_SESSION = autenticarUsuario();
$usuario = $_SESSION['nome'] ?? null;

// dd(eBarbeiro());
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
            <li class="nav-item">
                <a class="nav-link <?= ($currentUrl === '/') ? 'active' : '' ?>" href="/">
                    <i class="bi bi-house-door"></i> Home
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= ($currentUrl === '/agendamento') ? 'active' : '' ?>" href="/agendamento">
                    <i class="bi bi-calendar-plus"></i> Agendamento
                </a>
            </li>

            <?php if (eBarbeiro() || ($_SESSION['tipo'] ?? null) === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link <?= ($currentUrl === '/barbeiro') ? 'active' : '' ?>" href="/barbeiro">
                <i class="bi bi-scissors"></i> Barbeiro
                </a>
            </li>
            <?php endif; ?>

            <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentUrl === '/administrador') ? 'active' : '' ?>" href="/administrador">
                        <i class="bi bi-diamond"></i> Administrador
                    </a>
                </li>
            <?php endif; ?>


            <!-- <li class="nav-item">
                <a class="nav-link <?= ($currentUrl === '/teste') ? 'active' : '' ?>" href="/teste">
                    <i class="bi bi-calendar-plus"></i> Página Teste
                </a>
            </li> -->

            <!-- <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-people"></i> Clientes</a></li> -->
            <!-- <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-clock-history"></i> Histórico</a></li> -->
            <!-- <li class="nav-item mt-4"><a class="nav-link" href="#"><i class="bi bi-gear"></i> Configurações</a></li> -->
            <li class="nav-item"><a class="nav-link" href="/logout"><i class="bi bi-box-arrow-right"></i> Sair</a></li>
        </ul>
    </nav>
    <?php endif; ?>

    <div class="main-content">
        <?= $content ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
session_start();
require_once __DIR__ . '/../../utils/utils.php';

$usuario = autenticarUsuario();
$title = 'Gerenciar Barbeiros';
$cssPage = ''; // Nenhum CSS específico por enquanto

// dd($barbeiro);

ob_start();
?>

<p>Usuário autenticado: <?= htmlspecialchars($usuario['nome'] ?? 'Desconhecido') ?></p>

<?php if ($barbeiro['status'] === 'ativo'): ?>
    <form action="/barbeiros/inativar" method="POST">
        <input type="hidden" name="cliente_id" value="<?= $usuario['id'] ?>">
        <button type="submit">Deixar de ser barbeiro</button>
    </form>
<?php else: ?>
    <form action="/barbeiros/criar" method="POST">
        <input type="hidden" name="cliente_id" value="<?= $usuario['id'] ?>">
        <input type="hidden" name="idade" value="30">
        <input type="hidden" name="data_contratacao" value="<?= date('Y-m-d') ?>">
        <button type="submit">Deseja se tornar um barbeiro?</button>
    </form>
<?php endif; ?>



<p><a href="/agendamentos">Voltar ao Dashboard</a></p>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/auth_layout.php';
?>

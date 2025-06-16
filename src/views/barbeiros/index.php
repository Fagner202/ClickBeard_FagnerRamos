<?php
session_start();
require_once __DIR__ . '/../../utils/utils.php';

$usuario = autenticarUsuario();
$title = 'Gerenciar Barbeiros';
$cssPage = ''; // Nenhum CSS específico por enquanto

ob_start();
?>

<h1>Lista de Barbeiros</h1>
<p>Usuário autenticado: <?= htmlspecialchars($usuario['nome'] ?? 'Desconhecido') ?></p>

<form action="/barbeiros/criar" method="POST">
    <input type="hidden" name="cliente_id" value="<?= $usuario['id'] ?>">
    <input type="hidden" name="idade" value="30"> <!-- Altere para capturar de forma dinâmica depois -->
    <input type="hidden" name="data_contratacao" value="<?= date('Y-m-d') ?>">
    <button type="submit">Deseja se tornar um barbeiro?</button>
</form>

<?php if (!empty($barbeiros)) : ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Idade</th>
                <th>Data de Contratação</th>
                <th>Especialidades</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($barbeiros as $barbeiro) : ?>
                <tr>
                    <td><?= htmlspecialchars($barbeiro['id']) ?></td>
                    <td><?= htmlspecialchars($barbeiro['nome']) ?></td>
                    <td><?= htmlspecialchars($barbeiro['email']) ?></td>
                    <td><?= htmlspecialchars($barbeiro['idade']) ?></td>
                    <td><?= htmlspecialchars($barbeiro['data_contratacao']) ?></td>
                    <td><?= htmlspecialchars($barbeiro['especialidades'] ?? 'Nenhuma') ?></td>
                    <td>
                        <a href="/barbeiros/editar?id=<?= $barbeiro['id'] ?>">Editar</a> |
                        <a href="/barbeiros/deletar?id=<?= $barbeiro['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir este barbeiro?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else : ?>
    <p>Nenhum barbeiro cadastrado.</p>
<?php endif; ?>

<p><a href="/agendamentos">Voltar ao Dashboard</a></p>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/auth_layout.php';
?>

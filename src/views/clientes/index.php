<?php
session_start();
require_once __DIR__ . '/../../utils/utils.php';

$usuario = autenticarUsuario();
$title = 'Gerenciar Barbeiros';
$cssPage = ''; // Nenhum CSS específico por enquanto

ob_start();
?>

<p>Usuário autenticado: <?= htmlspecialchars($usuario['nome'] ?? 'Desconhecido') ?></p>

<?php if (isset($barbeiro) && is_array($barbeiro) && $barbeiro['status'] === 'ativo'): ?>
    <form action="/barbeiros/inativar" method="POST">
        <input type="hidden" name="cliente_id" value="<?= $usuario['id'] ?>">
        <button type="submit">Deixar de ser barbeiro</button>
    </form>

    <h2>Suas especialidades:</h2>
    <?php if (!empty($especialidades)): ?>
        <ul>
            <?php foreach ($especialidades as $especialidade): ?>
                <li><?= htmlspecialchars($especialidade['nome']) ?></li>
                <button type="button" onclick="vincularEspecialidade(<?= $especialidade['id'] ?>)">
                    Vincular
                </button>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Você ainda não tem especialidades cadastradas.</p>
    <?php endif; ?>

<?php else: ?>
    <form action="/barbeiros/criar" method="POST">
        <input type="hidden" name="cliente_id" value="<?= $usuario['id'] ?>">
        <input type="hidden" name="idade" value="30">
        <input type="hidden" name="data_contratacao" value="<?= date('Y-m-d') ?>">
        <button type="submit">Deseja se tornar um barbeiro?</button>
    </form>
<?php endif; ?>

<p><a href="/agendamentos">Voltar ao Dashboard</a></p>

<script>
function vincularEspecialidade(especialidadeId) {
    console.log("Especialidade selecionada:", especialidadeId);
    
    // Aqui você pode depois fazer algo como:
    // - enviar via fetch/AJAX
    // - montar um formulário oculto e submeter
    // - abrir um modal, etc.
    
    // Exemplo simples de exibição:
    alert("Especialidade com ID " + especialidadeId + " selecionada para vincular.");
}
</script>


<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/auth_layout.php';
?>

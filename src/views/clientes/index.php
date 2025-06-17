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
        <ul id="lista-especialidades">
            <?php foreach ($especialidades as $especialidade): ?>
                <li id="especialidade-<?= $especialidade['id'] ?>">
                    <?= htmlspecialchars($especialidade['nome']) ?>
                    <button 
                        onclick="toggleEspecialidade(this, <?= $especialidade['id'] ?>)" 
                        data-vinculado="false">
                        Vincular
                    </button>
                </li>
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
    const barbeiroId = <?= $barbeiro['cliente_id'] ?>;

    function toggleEspecialidade(botao, especialidadeId) {
        const vinculado = botao.dataset.vinculado === 'true';

        const url = vinculado 
            ? '/ajax/desvincular-especialidade' 
            : '/ajax/vincular-especialidade';

        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                barbeiro_id: barbeiroId,
                especialidade_id: especialidadeId
            })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.mensagem);

            if (data.sucesso) {
                botao.textContent = vinculado ? 'Vincular' : 'Desvincular';
                botao.dataset.vinculado = vinculado ? 'false' : 'true';
            }
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
        });
    }
</script>


<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/auth_layout.php';
?>

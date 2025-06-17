<?php
session_start();
require_once __DIR__ . '/../../utils/utils.php';

$usuario = autenticarUsuario();
$title = 'Gerenciar Barbeiros';
$cssPage = ''; // Nenhum CSS específico por enquanto

ob_start();
?>

<div class="container mt-5">
    <div class="mb-4">
        <h4>Bem-vindo, <?= htmlspecialchars($usuario['nome'] ?? 'Desconhecido') ?></h4>
    </div>

    <?php if (isset($barbeiro) && is_array($barbeiro) && $barbeiro['status'] === 'ativo'): ?>
        <form action="/barbeiros/inativar" method="POST" class="mb-4">
            <input type="hidden" name="cliente_id" value="<?= $usuario['id'] ?>">
            <button type="submit" class="btn btn-danger">Deixar de ser barbeiro</button>
        </form>

        <h5>Especialidades Disponíveis:</h5>
        <?php if (!empty($especialidades)): ?>
            <ul class="list-group" id="lista-especialidades">
                <?php foreach ($especialidades as $especialidade): 
                    $vinculado = in_array($especialidade['id'], $especialidadesVinculadas ?? []);
                ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center" id="especialidade-<?= $especialidade['id'] ?>">
                        <?= htmlspecialchars($especialidade['nome']) ?>
                        <button 
                            class="btn btn-sm <?= $vinculado ? 'btn-outline-danger' : 'btn-outline-primary' ?>"
                            onclick="toggleEspecialidade(this, <?= $especialidade['id'] ?>)" 
                            data-vinculado="<?= $vinculado ? 'true' : 'false' ?>">
                            <?= $vinculado ? 'Desvincular' : 'Vincular' ?>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="alert alert-warning mt-3">
                Você ainda não tem especialidades cadastradas.
            </div>
        <?php endif; ?>

    <?php else: ?>
        <form action="/barbeiros/criar" method="POST" class="mb-4">
            <input type="hidden" name="cliente_id" value="<?= $usuario['id'] ?>">
            <input type="hidden" name="idade" value="30">
            <input type="hidden" name="data_contratacao" value="<?= date('Y-m-d') ?>">
            <button type="submit" class="btn btn-success">Deseja se tornar um barbeiro?</button>
        </form>
    <?php endif; ?>

    <a href="/agendamentos" class="btn btn-secondary mt-3">Voltar ao Dashboard</a>
</div>


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

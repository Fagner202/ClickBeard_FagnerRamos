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
        <h4 class="fw-bold">Bem-vindo, <?= htmlspecialchars($usuario['nome'] ?? 'Desconhecido') ?></h4>
    </div>

    <?php if (isset($barbeiro) && is_array($barbeiro) && $barbeiro['status'] === 'ativo'): ?>
        <form action="/barbeiros/inativar" method="POST" class="mb-4">
            <input type="hidden" name="cliente_id" value="<?= $usuario['id'] ?>">
            <button type="submit" class="btn btn-danger">Deixar de ser barbeiro</button>
        </form>

        <div class="mb-3">
            <h5 class="mb-3">Especialidades Disponíveis:</h5>
            <?php if (!empty($especialidades)): ?>
                <ul class="list-group" id="lista-especialidades">
                    <?php foreach ($especialidades as $especialidade): 
                        $vinculado = in_array($especialidade['id'], $especialidadesVinculadas ?? []);
                    ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap" id="especialidade-<?= $especialidade['id'] ?>">
                            <div>
                                <strong><?= htmlspecialchars($especialidade['nome']) ?></strong>
                                <small class="text-muted ms-2">(R$ <span id="valor-<?= $especialidade['id'] ?>"><?= $valores[$especialidade['id']] ?? '0.00' ?></span>)</small>
                            </div>
                            <div class="btn-group mt-2 mt-sm-0">
                                <button 
                                    class="btn btn-sm <?= $vinculado ? 'btn-outline-danger' : 'btn-outline-success' ?>" 
                                    onclick="toggleEspecialidade(this, <?= $especialidade['id'] ?>, <?= $usuario['id'] ?>)" 
                                    data-vinculado="<?= $vinculado ? 'true' : 'false' ?>"
                                    data-valor="<?= $valores[$especialidade['id']] ?? '' ?>">
                                    <?= $vinculado ? 'Desvincular' : 'Vincular' ?>
                                </button>

                                <?php if ($vinculado): ?>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editarValor(<?= $especialidade['id'] ?>)">Editar Valor</button>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="alert alert-warning mt-3">
                    Você ainda não tem especialidades cadastradas.
                </div>
            <?php endif; ?>
        </div>

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

    function toggleEspecialidade(button, especialidadeId, barbeiroId) {
        const vinculado = button.getAttribute('data-vinculado') === 'true';

        if (!vinculado) {
            const valor = prompt("Digite o valor para essa especialidade (em R$):", "50.00");

            if (valor === null || valor.trim() === '') {
                alert("Vínculo cancelado. Valor não informado.");
                return;
            }

            fetch('/ajax/vincular-especialidade', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    especialidade_id: especialidadeId,
                    valor: valor,
                    barbeiro_id: barbeiroId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    button.textContent = "Desvincular";
                    button.setAttribute('data-vinculado', 'true');
                    button.setAttribute('data-valor', valor);
                } else {
                    alert("Erro ao vincular: " + data.mensagem);
                }

                location.reload();
            });
        } else {
            fetch('/ajax/desvincular-especialidade', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    especialidade_id: especialidadeId,
                    barbeiro_id: barbeiroId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    button.textContent = "Vincular";
                    button.setAttribute('data-vinculado', 'false');
                    button.removeAttribute('data-valor');
                } else {
                    alert("Erro ao desvincular: " + data.mensagem);
                }

                location.reload();
            });
        }
    }

    function editarValor(especialidadeId) {
        const novoValor = prompt("Digite o novo valor para essa especialidade:");

        if (novoValor === null || novoValor.trim() === '') {
            alert("Atualização cancelada.");
            return;
        }

        fetch('/ajax/atualizarValor', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                especialidade_id: especialidadeId,
                novo_valor: novoValor
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                document.getElementById('valor-' + especialidadeId).textContent = novoValor;
                alert("Valor atualizado com sucesso.");
            } else {
                alert("Erro ao atualizar valor: " + data.mensagem);
            }
        });

        document.getElementById('valor-' + especialidadeId).textContent = novoValor;
    }

</script>


<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/auth_layout.php';
?>

<?php
session_start();
require_once __DIR__ . '/../../utils/utils.php';

$usuario = autenticarUsuario();
// dd($usuario);

ob_start();
?>

<div class="container mt-5">
    <input type="hidden" class="barbeiro_id_value" value="<?php echo  $usuario['id'] ?>">
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

<script src="/js/barbeiro.js"></script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/auth_layout.php';
?>

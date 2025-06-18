<?php
session_start();
require_once __DIR__ . '/../../utils/utils.php';
$usuario = autenticarUsuario();
ob_start();
?>

<div class="container mt-5">
  <input type="hidden" class="barbeiro_id_value" value="<?php echo  $usuario['id'] ?>">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h4 class="fw-bold text-primary">Bem-vindo, <?= htmlspecialchars($usuario['nome'] ?? 'Desconhecido') ?></h4>
        
        <?php if (isset($barbeiro) && is_array($barbeiro) && $barbeiro['status'] === 'ativo'): ?>
            <button class="btn btn-outline-primary mt-2 mt-md-0" onclick="abrirModalAgendados()">
            <i class="bi bi-calendar-check"></i> Agendados
            </button>
        <?php endif; ?>
    </div>

  <?php if (isset($barbeiro) && is_array($barbeiro) && $barbeiro['status'] === 'ativo'): ?>

    <div class="text-end mb-4">
      <form action="/barbeiros/inativar" method="POST">
        <input type="hidden" name="cliente_id" value="<?= $usuario['id'] ?>">
        <button type="submit" class="btn btn-outline-danger rounded-pill">
          <i class="bi bi-x-circle"></i> Deixar de ser barbeiro
        </button>
      </form>
    </div>

    <div class="mb-4">
      <h4 class="mb-3">Suas Especialidades</h4>
      <?php if (!empty($especialidades)): ?>
        <ul class="list-group" id="lista-especialidades">
          <?php foreach ($especialidades as $especialidade): 
            $vinculado = in_array($especialidade['id'], $especialidadesVinculadas ?? []);
          ?>
            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap" id="especialidade-<?= $especialidade['id'] ?>">
              <div>
                <strong><?= htmlspecialchars($especialidade['nome']) ?></strong>
                <small class="text-muted ms-2">R$ <span id="valor-<?= $especialidade['id'] ?>"><?= $valores[$especialidade['id']] ?? '0.00' ?></span></small>
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
    <div class="text-center mb-5">
      <h4 class="fw-bold">Torne-se um barbeiro parceiro</h4>
      <p class="text-muted">Faça parte do nosso time e aumente sua visibilidade com clientes em sua região. Tenha acesso a uma plataforma intuitiva para gerenciar seus serviços, agendamentos e especialidades.</p>
      <button class="btn btn-success rounded-pill px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalInscricaoBarbeiro">
        <i class="bi bi-person-plus"></i> Quero me inscrever como barbeiro
      </button>
    </div>
  <?php endif; ?>
</div>

<!-- Modal de Inscrição -->
<div class="modal fade" id="modalInscricaoBarbeiro" tabindex="-1" aria-labelledby="modalInscricaoBarbeiroLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="modalInscricaoBarbeiroLabel">Inscrição como Barbeiro</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="/barbeiros/criar" method="POST">
        <div class="modal-body">
          <input type="hidden" name="cliente_id" value="<?= $usuario['id'] ?>">
          <input type="hidden" name="idade" value="30">
          <input type="hidden" name="data_contratacao" value="<?= date('Y-m-d') ?>">
          <p>Ao se cadastrar, você terá acesso à gestão de especialidades e poderá ser agendado por clientes.</p>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary rounded-pill">Confirmar Inscrição</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal de Agendados -->
<div class="modal fade" id="modalAgendados" tabindex="-1" aria-labelledby="modalAgendadosLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalAgendadosLabel">Clientes Agendados</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Cliente</th>
              <th>Serviço</th>
              <th>Data/Hora</th>
              <th>Ação</th>
            </tr>
          </thead>
          <tbody id="tabela-agendados">
            <!-- Conteúdo será inserido via JS -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>


<script src="/js/barbeiro.js"></script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/auth_layout.php';
?>

<?php
session_start();
require_once __DIR__ . '/../../utils/utils.php';

$usuario = autenticarUsuario();

// dd($barbeiros);

ob_start();
?>

<div class="container mt-5">
  <!-- Título da página -->
  <div class="text-center mb-5">
    <h1 class="display-5 fw-bold text-primary">
      <i class="bi bi-calendar-check"></i> Gerenciar Agendamentos
    </h1>
    <p class="lead">Escolha um barbeiro e agende um horário com facilidade.</p>
    <p class="text-muted">Bem-vindo, <strong><?= htmlspecialchars($usuario['nome'] ?? 'Desconhecido') ?></strong>
      <button class="btn btn-primary btn-sm ms-2" onclick="abrirMeusAgendamentos()">
        <i class="bi bi-calendar-week"></i> Meus Agendamentos
      </button>
    </p>
  </div>

  <!-- Lista de barbeiros -->
  <div class="row row-cols-1 row-cols-md-2 g-4">
    <?php foreach ($barbeiros as $barbeiro): ?>
      <div class="col">
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <h5 class="card-title">Barbeiro ID <?= htmlspecialchars($barbeiro['nome']) ?></h5>
            <p class="card-text mb-1"><strong>Idade:</strong> <?= htmlspecialchars($barbeiro['idade']) ?></p>
            <p class="card-text mb-1"><strong>Data de Contratação:</strong> <?= htmlspecialchars($barbeiro['data_contratacao']) ?></p>
            <p class="card-text mb-3"><strong>Status:</strong> <?= htmlspecialchars($barbeiro['status']) ?></p>

            <button class="btn btn-success w-100" onclick="abrirModalAgendamento(<?= $barbeiro['cliente_id'] ?>)">
              <i class="bi bi-clock"></i> Realizar Agendamento
            </button>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Modal Bootstrap 5.3 -->
<div class="modal fade" id="modalAgendamento" tabindex="-1" aria-labelledby="modalAgendamentoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="modalAgendamentoLabel">Realizar Agendamento</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <form id="formAgendamento">
          <input type="hidden" id="barbeiro_id">

          <div class="mb-3">
            <label for="especialidade_id" class="form-label">Especialidade</label>
            <select id="especialidade_id" class="form-select" required>
              <option value="">Carregando...</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="data_hora" class="form-label">Data e Hora</label>
            <input type="datetime-local" id="data_hora" class="form-control" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" onclick="enviarAgendamento()">Confirmar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Meus Agendamentos -->
<div class="modal fade" id="modalMeusAgendamentos" tabindex="-1" aria-labelledby="modalMeusAgendamentosLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalMeusAgendamentosLabel">Meus Agendamentos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-striped" id="tabelaAgendamentos">
            <thead>
              <tr>
                <th>Data/Hora</th>
                <th>Barbeiro</th>
                <th>Serviço</th>
                <th>Status</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody id="corpoTabelaAgendamentos">
              <!-- Os agendamentos serão carregados aqui via AJAX -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Editar Agendamento -->
<div class="modal fade" id="modalEditarAgendamento" tabindex="-1" aria-labelledby="modalEditarAgendamentoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditarAgendamentoLabel">Editar Agendamento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formEditarAgendamento">
          <input type="hidden" id="editar_agendamento_id">
          
          <div class="mb-3">
            <label for="editar_data_hora" class="form-label">Data e Hora</label>
            <input type="datetime-local" class="form-control" id="editar_data_hora" required>
          </div>
          
          <div class="mb-3">
            <label for="editar_barbeiro_id" class="form-label">Barbeiro</label>
            <select class="form-select" id="editar_barbeiro_id" required>
              <option value="">Selecione um barbeiro</option>
              <!-- Opções serão carregadas via JavaScript -->
            </select>
          </div>
          
          <div class="mb-3">
            <label for="editar_especialidade_id" class="form-label">Serviço</label>
            <select class="form-select" id="editar_especialidade_id" required>
              <option value="">Selecione um serviço</option>
              <!-- Opções serão carregadas via JavaScript -->
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="salvarEdicaoAgendamento()">Salvar Alterações</button>
      </div>
    </div>
  </div>
</div>

<script src="/js/agendamento.js"></script>

<script>
  
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/auth_layout.php';
?>

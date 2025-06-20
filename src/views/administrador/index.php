<?php
// session_start();
require_once __DIR__ . '/../../utils/utils.php';

$usuario = autenticarUsuario();

// dd($clientes);

ob_start();
?>

<div class="container py-4">
  <h2 class="fw-bold mb-4">Painel do Administrador</h2>

  <!-- Botões de navegação -->
  <div class="mb-4 d-flex gap-3">
    <button class="btn btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#collapseBarbeiros" aria-expanded="true" aria-controls="collapseBarbeiros">
      👨‍🔧 Gerenciar Barbeiros
    </button>
    <button class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#collapseEspecialidades" aria-expanded="false" aria-controls="collapseEspecialidades">
      💈 Gerenciar Especialidades
    </button>
    <button class="btn btn-outline-info" data-bs-toggle="collapse" data-bs-target="#collapseAgendamentos" aria-expanded="false" aria-controls="collapseAgendamentos">
      📅 Gerenciar Agendamentos
    </button>
  </div>

  <!-- Accordion para exibir apenas uma seção por vez -->
  <div class="accordion" id="adminAccordion">

    <!-- Barbeiros -->
    <div class="accordion-item">
      <div id="collapseBarbeiros" class="accordion-collapse collapse show" data-bs-parent="#adminAccordion">
        <div class="accordion-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>👨‍🔧 Barbeiros Cadastrados</h4>
            <button class="btn btn-success" onclick="abrirModalCriarBarbeiro()">
              <i class="bi bi-plus-circle"></i> Novo Barbeiro
            </button>
          </div>

          <div class="table-responsive">
            <table class="table table-striped align-middle">
              <thead class="table-dark">
                <tr>
                  <th>Nome</th>
                  <th>Idade</th>
                  <th>Data de Contratação</th>
                  <th>Especialidades</th>
                  <th class="text-end">Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($barbeiros as $barbeiro): ?>
                  <tr>
                    <td><?= htmlspecialchars($barbeiro['nome']) ?></td>
                    <td><?= htmlspecialchars($barbeiro['idade']) ?></td>
                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($barbeiro['data_contratacao']))) ?></td>
                    <td><?= !empty($barbeiro['especialidades']) 
                      ? implode(', ', array_map('htmlspecialchars', $barbeiro['especialidades'])) 
                      : '<span class="text-muted">Nenhuma</span>' ?>
                    </td>
                    <td class="text-end">
                      <button class="btn btn-sm btn-outline-primary" onclick="abrirModalEditarBarbeiro(
                        <?= $barbeiro['id'] ?>,
                        '<?= htmlspecialchars($barbeiro['nome']) ?>',
                        <?= $barbeiro['idade'] ?>,
                        '<?= $barbeiro['data_contratacao'] ?>'
                      )">
                        <i class="bi bi-pencil"></i>
                      </button>

                     <button class="btn btn-sm btn-outline-danger" onclick="confirmarExclusaoBarbeiro(<?= $barbeiro['id'] ?>, '<?= htmlspecialchars($barbeiro['nome']) ?>')">
                      <i class="bi bi-trash"></i>
                    </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>

    <!-- Especialidades -->
    <div class="accordion-item">
      <div id="collapseEspecialidades" class="accordion-collapse collapse" data-bs-parent="#adminAccordion">
        <div class="accordion-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>💈 Especialidades</h4>
            <button class="btn btn-success" onclick="abrirModalCriarEspecialidade()">
              <i class="bi bi-plus-circle"></i> Nova Especialidade
            </button>
          </div>

          <ul id="lista-especialidades" class="list-group">
            <!-- Preenchido via JS -->
          </ul>
        </div>
      </div>
    </div>

    <!-- Agendamentos -->
    <div class="accordion-item">
      <div id="collapseAgendamentos" class="accordion-collapse collapse" aria-labelledby="headingAgendamentos" data-bs-parent="#adminAccordion">
        <div class="accordion-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>📅 Agendamentos</h4>
            <!-- Botão para novo agendamento, se necessário futuramente -->
          </div>
          <div class="table-responsive">
            <table class="table table-striped align-middle" id="tabela-agendamentos">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>Cliente</th>
                  <th>Barbeiro</th>
                  <th>Especialidade</th>
                  <th>Data/Hora</th>
                  <th>Status</th>
                  <th>Cancelado</th>
                  <th class="text-end">Ações</th>
                </tr>
              </thead>
              <tbody>
                <!-- Preenchido via JavaScript -->
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Especialidade -->
<div class="modal fade" id="modalEspecialidade" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" onsubmit="salvarEspecialidade(event)">
      <div class="modal-header">
        <h5 class="modal-title" id="titulo-modal-especialidade">Nova Especialidade</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="especialidade_id">
        <div class="mb-3">
          <label for="nome_especialidade" class="form-label">Nome da Especialidade</label>
          <input type="text" class="form-control" id="nome_especialidade" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Salvar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Criar/Editar Barbeiro -->
<div class="modal fade" id="modalBarbeiro" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" onsubmit="salvarBarbeiro(event)">
      <div class="modal-header">
        <h5 class="modal-title" id="titulo-modal-barbeiro">Cadastrar Barbeiro</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="barbeiro_cliente_id">

        <!-- Selecionar Cliente Existente -->
        <div class="mb-3">
          <label for="cliente_id" class="form-label">Cliente</label>
          <select class="form-select" id="cliente_id" required onchange="preencherNomeCliente()">
            <option value="">Selecione um cliente</option>
            <?php foreach ($clientes as $cliente): ?>
              <option value="<?= $cliente['id'] ?>" data-nome="<?= htmlspecialchars($cliente['nome']) ?>">
                <?= htmlspecialchars($cliente['nome']) ?> (<?= htmlspecialchars($cliente['email']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Nome do cliente (preenchido automaticamente) -->
        <div class="mb-3">
          <label class="form-label">Nome</label>
          <input type="text" class="form-control" id="nome_cliente" disabled>
        </div>

        <div class="mb-3">
          <label for="idade" class="form-label">Idade</label>
          <input type="number" class="form-control" id="idade" required>
        </div>

        <div class="mb-3">
          <label for="data_contratacao" class="form-label">Data de Contratação</label>
          <input type="date" class="form-control" id="data_contratacao" required value="<?= date('Y-m-d') ?>">
        </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Salvar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<script src="/js/admin.js"></script>


<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/auth_layout.php';
?>


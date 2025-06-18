<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Painel do Administrador</h2>
    <span class="badge bg-dark fs-6">Gerencie barbeiros e especialidades</span>
  </div>

  <!-- Seção de Barbeiros -->
  <div class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4><i class="bi bi-scissors"></i> Barbeiros</h4>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
      <?php foreach ($barbeiros as $barbeiro): ?>
        <div class="col">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($barbeiro['nome']) ?></h5>
              <p class="card-text mb-1"><strong>Idade:</strong> <?= htmlspecialchars($barbeiro['idade']) ?></p>
              <p class="card-text mb-1"><strong>Contratado em:</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($barbeiro['data_contratacao']))) ?></p>
              <p class="card-text mb-2"><strong>Especialidades:</strong><br>
                <?= !empty($barbeiro['especialidades']) 
                  ? implode('<br>', array_map('htmlspecialchars', $barbeiro['especialidades'])) 
                  : '<span class="text-muted">Nenhuma cadastrada</span>' ?>
              </p>
              <div class="d-flex justify-content-end gap-2">
                <button class="btn btn-sm btn-outline-primary">Editar</button>
                <button class="btn btn-sm btn-outline-danger">Excluir</button>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Seção de Especialidades -->
  <div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4><i class="bi bi-star"></i> Especialidades</h4>
      <button class="btn btn-success" onclick="abrirModalCriarEspecialidade()">
        <i class="bi bi-plus-circle"></i> Nova Especialidade
      </button>
    </div>

    <ul id="lista-especialidades" class="list-group">
      <!-- Preenchido via JS -->
    </ul>
  </div>
</div>

<!-- Modal de Criar/Editar Especialidade -->
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

<script src="/js/admin.js"></script>


<script src="/js/admin.js"></script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/auth_layout.php';
?>


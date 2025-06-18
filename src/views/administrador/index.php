<h2>Lista de Barbeiros</h2>

<?php foreach ($barbeiros as $barbeiro): ?>
    <div>
        <h4><?= htmlspecialchars($barbeiro['nome']) ?></h4>
        <p>Idade: <?= htmlspecialchars($barbeiro['idade']) ?></p>
        <p>Data de Contratação: <?= htmlspecialchars($barbeiro['data_contratacao']) ?></p>
        <p>Especialidades: <?= !empty($barbeiro['especialidades']) 
            ? implode(', ', array_map('htmlspecialchars', $barbeiro['especialidades'])) 
            : 'Nenhuma' ?>
        </p>
        <hr>
    </div>
<?php endforeach; ?>

<h2>Especialidades</h2>

<!-- Botão para abrir o modal de criação -->
<button class="btn btn-success mb-3" onclick="abrirModalCriarEspecialidade()">+ Nova Especialidade</button>

<!-- Lista de especialidades -->
<ul id="lista-especialidades" class="list-group mb-5">
  <!-- Será preenchido via JavaScript -->
</ul>

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

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/auth_layout.php';
?>


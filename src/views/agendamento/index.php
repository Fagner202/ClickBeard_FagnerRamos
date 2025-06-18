<?php
session_start();
require_once __DIR__ . '/../../utils/utils.php';

$usuario = autenticarUsuario();

ob_start();
?>

<h1>Lista de Barbeiros</h1>

<ul class="list-group">
    <?php foreach ($barbeiros as $barbeiro): ?>
        <li class="list-group-item">
            <strong>ID do Cliente:</strong> <?= htmlspecialchars($barbeiro['cliente_id']) ?><br>
            <strong>Idade:</strong> <?= htmlspecialchars($barbeiro['idade']) ?><br>
            <strong>Data de Contratação:</strong> <?= htmlspecialchars($barbeiro['data_contratacao']) ?><br>
            <strong>Status:</strong> <?= htmlspecialchars($barbeiro['status']) ?><br>

            <button 
                class="btn btn-primary mt-2" 
                data-bs-toggle="modal" 
                data-bs-target="#modalAgendamento" 
                onclick="carregarEspecialidades(<?= $barbeiro['cliente_id'] ?>)"
            >
                Realizar Agendamento
            </button>
        </li>
    <?php endforeach; ?>
</ul>

<!-- Modal -->
<div class="modal fade" id="modalAgendamento" tabindex="-1" aria-labelledby="modalAgendamentoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formAgendamento" onsubmit="enviarAgendamento(event)">
        <div class="modal-header">
          <h5 class="modal-title" id="modalAgendamentoLabel">Agendar com Barbeiro</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="barbeiro_id" name="barbeiro_id">

          <div class="mb-3">
            <label for="especialidade_id" class="form-label">Especialidade</label>
            <select class="form-select" id="especialidade_id" name="especialidade_id" required>
              <option value="">Carregando...</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="data_hora" class="form-label">Data e Hora</label>
            <input type="datetime-local" class="form-control" id="data_hora" name="data_hora" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Confirmar Agendamento</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>



<!-- Função JavaScript -->
<script>
    function carregarEspecialidades(barbeiroId) {
        document.getElementById('barbeiro_id').value = barbeiroId;

        fetch(`/ajaxController.php?action=especialidades&barbeiro_id=${barbeiroId}`)
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById('especialidade_id');
                select.innerHTML = '';

                if (data.length === 0) {
                    select.innerHTML = '<option value="">Nenhuma especialidade</option>';
                    return;
                }

                data.forEach(esp => {
                    const option = document.createElement('option');
                    option.value = esp.especialidade_id;
                    option.textContent = `${esp.nome} - R$ ${parseFloat(esp.valor).toFixed(2)}`;
                    select.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Erro ao carregar especialidades:', error);
                alert('Erro ao carregar especialidades.');
            });
    }

    function enviarAgendamento(event) {
        event.preventDefault();

        const formData = new FormData(document.getElementById('formAgendamento'));

        fetch('/ajaxController.php?action=agendar', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.sucesso) {
                alert('Agendamento realizado com sucesso!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalAgendamento'));
                modal.hide();
            } else {
                alert('Erro: ' + data.mensagem);
            }
        })
        .catch(error => {
            console.error('Erro ao agendar:', error);
            alert('Erro ao tentar agendar.');
        });
    }
</script>


<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/auth_layout.php';
?>

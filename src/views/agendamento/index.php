<ul>
    <?php foreach ($barbeiros as $barbeiro): ?>
        <li>
            ID do Cliente: <?= htmlspecialchars($barbeiro['cliente_id']) ?><br>
            Idade: <?= htmlspecialchars($barbeiro['idade']) ?><br>
            Data de Contratação: <?= htmlspecialchars($barbeiro['data_contratacao']) ?><br>
            Status: <?= htmlspecialchars($barbeiro['status']) ?><br>

            <button class="btn btn-primary mt-2" onclick="abrirModalAgendamento(<?= $barbeiro['cliente_id'] ?>)">
                Realizar Agendamento
            </button>
        </li>
        <hr>
    <?php endforeach; ?>
</ul>

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

<script>
function abrirModalAgendamento(barbeiroId) {
    // Define barbeiro_id no input hidden
    document.getElementById('barbeiro_id').value = barbeiroId;

    // Carrega especialidades via AJAX
    fetch('/ajax/especialidades-barbeiro?barbeiro_id=' + barbeiroId, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        const select = document.getElementById('especialidade_id');
        select.innerHTML = '';

        if (data.length === 0) {
            select.innerHTML = '<option value="">Nenhuma especialidade disponível</option>';
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

    // Abre o modal
    const modal = new bootstrap.Modal(document.getElementById('modalAgendamento'));
    modal.show();
}

function enviarAgendamento() {
    const barbeiroId = document.getElementById('barbeiro_id').value;
    const especialidadeId = document.getElementById('especialidade_id').value;
    const dataHora = document.getElementById('data_hora').value;

    if (!especialidadeId || !dataHora) {
        alert('Por favor, preencha todos os campos.');
        return;
    }

    fetch('/ajax/criar-agendamento', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            barbeiro_id: barbeiroId,
            especialidade_id: especialidadeId,
            data_hora: dataHora
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.sucesso) {
            alert('Agendamento realizado com sucesso!');
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalAgendamento'));
            modal.hide();
            document.getElementById('formAgendamento').reset();
        } else {
            alert('Erro: ' + data.mensagem);
        }
    })
    .catch(error => {
        console.error('Erro ao tentar agendar:', error);
        alert('Erro ao tentar agendar.');
    });
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/auth_layout.php';
?>

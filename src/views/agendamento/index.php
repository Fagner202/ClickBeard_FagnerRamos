<?php
session_start();
require_once __DIR__ . '/../../utils/utils.php';

$usuario = autenticarUsuario();

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
            <h5 class="card-title">Barbeiro ID <?= htmlspecialchars($barbeiro['cliente_id']) ?></h5>
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

  function abrirMeusAgendamentos() {
    // Mostra o modal
    const modal = new bootstrap.Modal(document.getElementById('modalMeusAgendamentos'));
    modal.show();
    
    // Carrega os agendamentos
    carregarMeusAgendamentos();
  }

  function carregarMeusAgendamentos() {
    fetch('/ajax/meus-agendamentos', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json'
      }
    })
    .then(res => res.json())
    .then(data => {
      if (data.erro) {
        alert('Erro: ' + data.erro);
        return;
      }
      
      const corpoTabela = document.getElementById('corpoTabelaAgendamentos');
      corpoTabela.innerHTML = '';
      
      if (data.length === 0) {
        corpoTabela.innerHTML = '<tr><td colspan="5" class="text-center">Nenhum agendamento encontrado</td></tr>';
        return;
      }
      
      data.forEach(agendamento => {
        const linha = document.createElement('tr');
        linha.innerHTML = `
          <td>${formatarDataHora(agendamento.data_hora)}</td>
          <td>Barbeiro ID ${agendamento.barbeiro_id}</td>
          <td>${agendamento.especialidade_nome || 'Serviço não especificado'}</td>
          <td>${agendamento.status || 'Agendado'}</td>
          <td>
            <button class="btn btn-sm btn-warning" onclick="editarAgendamento(${agendamento.id})">
              <i class="bi bi-pencil"></i> Editar
            </button>
            <button class="btn btn-sm btn-danger ms-1" onclick="cancelarAgendamento(${agendamento.id})">
              <i class="bi bi-trash"></i> Cancelar
            </button>
          </td>
        `;
        corpoTabela.appendChild(linha);
      });
    })
    .catch(error => {
      console.error('Erro ao carregar agendamentos:', error);
      alert('Erro ao carregar agendamentos.');
    });
  }

  function formatarDataHora(dataHora) {
    if (!dataHora) return '';
    const data = new Date(dataHora);
    return data.toLocaleString('pt-BR');
  }

  function editarAgendamento(agendamentoId) {
    // Primeiro busca os dados do agendamento
    fetch(`/ajax/buscar-agendamento/${agendamentoId}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json'
      }
    })
    .then(res => res.json())
    .then(agendamento => {
      if (agendamento.erro) {
        alert('Erro: ' + agendamento.erro);
        return;
      }
      
      // Preenche o modal com os dados do agendamento
      document.getElementById('editar_agendamento_id').value = agendamento.id;
      
      // Formata a data para o input datetime-local (YYYY-MM-DDTHH:MM)
      const dataHora = new Date(agendamento.data_hora);
      const timezoneOffset = dataHora.getTimezoneOffset() * 60000; // offset em milissegundos
      const localISOTime = new Date(dataHora - timezoneOffset).toISOString().slice(0, 16);
      document.getElementById('editar_data_hora').value = localISOTime;
      
      // Carrega os barbeiros disponíveis
      carregarBarbeirosParaEdicao(agendamento.barbeiro_id);
      
      // Abre o modal de edição
      const modal = new bootstrap.Modal(document.getElementById('modalEditarAgendamento'));
      modal.show();
    })
    .catch(error => {
      console.error('Erro ao buscar agendamento:', error);
      alert('Erro ao buscar dados do agendamento.');
    });
  }

  function carregarBarbeirosParaEdicao(barbeiroAtualId) {
    fetch('/ajax/barbeiros-disponiveis', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json'
      }
    })
    .then(res => res.json())
    .then(barbeiros => {
      const select = document.getElementById('editar_barbeiro_id');
      select.innerHTML = '<option value="">Selecione um barbeiro</option>';
      
      barbeiros.forEach(barbeiro => {
        const option = document.createElement('option');
        option.value = barbeiro.id;
        option.textContent = `Barbeiro ID ${barbeiro.id}`;
        option.selected = (barbeiro.id == barbeiroAtualId);
        select.appendChild(option);
      });
      
      // Dispara o evento change para carregar as especialidades
      select.dispatchEvent(new Event('change'));
    })
    .catch(error => {
      console.error('Erro ao carregar barbeiros:', error);
    });
  }

  // Event listener para carregar especialidades quando um barbeiro é selecionado
  document.getElementById('editar_barbeiro_id').addEventListener('change', function() {
    const barbeiroId = this.value;
    if (!barbeiroId) return;
    
    fetch(`/ajax/especialidades-barbeiro/${barbeiroId}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json'
      }
    })
    .then(res => res.json())
    .then(especialidades => {
      const select = document.getElementById('editar_especialidade_id');
      select.innerHTML = '<option value="">Selecione um serviço</option>';
      
      especialidades.forEach(especialidade => {
        const option = document.createElement('option');
        option.value = especialidade.id;
        option.textContent = especialidade.nome;
        select.appendChild(option);
      });
    })
    .catch(error => {
      console.error('Erro ao carregar especialidades:', error);
    });
  });

  function salvarEdicaoAgendamento() {
    const agendamentoId = document.getElementById('editar_agendamento_id').value;
    const dataHora = document.getElementById('editar_data_hora').value;
    const barbeiroId = document.getElementById('editar_barbeiro_id').value;
    const especialidadeId = document.getElementById('editar_especialidade_id').value;
    
    if (!dataHora || !barbeiroId || !especialidadeId) {
      alert('Por favor, preencha todos os campos.');
      return;
    }
    
    fetch('/ajax/atualizar-agendamento', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        agendamento_id: agendamentoId,
        data_hora: dataHora,
        barbeiro_id: barbeiroId,
        especialidade_id: especialidadeId
      })
    })
    .then(res => res.json())
    .then(data => {
      if (data.sucesso) {
        alert('Agendamento atualizado com sucesso!');
        
        // Fecha o modal de edição
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarAgendamento'));
        modal.hide();
        
        // Atualiza a lista de agendamentos
        carregarMeusAgendamentos();
      } else {
        alert('Erro: ' + data.mensagem);
      }
    })
    .catch(error => {
      console.error('Erro ao atualizar agendamento:', error);
      alert('Erro ao atualizar agendamento.');
    });
  }

  function cancelarAgendamento(agendamentoId) {
    if (!confirm('Tem certeza que deseja cancelar este agendamento?')) return;
    
    fetch('/ajax/cancelar-agendamento', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        agendamento_id: agendamentoId
      })
    })
    .then(res => res.json())
    .then(data => {
      if (data.sucesso) {
        alert('Agendamento cancelado com sucesso!');
        carregarMeusAgendamentos(); // Atualiza a lista
      } else {
        alert('Erro: ' + data.mensagem);
      }
    })
    .catch(error => {
      console.error('Erro ao cancelar agendamento:', error);
      alert('Erro ao cancelar agendamento.');
    });
  }
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/auth_layout.php';
?>

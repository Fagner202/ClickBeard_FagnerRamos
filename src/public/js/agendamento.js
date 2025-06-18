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
        carregarBarbeirosParaEdicao(agendamento.barbeiro_id, agendamento.especialidade_id)
        
        // Abre o modal de edição
        const modal = new bootstrap.Modal(document.getElementById('modalEditarAgendamento'));
        modal.show();
    })
    .catch(error => {
        console.error('Erro ao buscar agendamento:', error);
        alert('Erro ao buscar dados do agendamento.');
    });
}

function carregarBarbeirosParaEdicao(barbeiroAtualId, especialidadeAtualId = null) {
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
        option.value = barbeiro.cliente_id;
        option.textContent = `Barbeiro #${barbeiro.cliente_id}`;
        option.selected = (barbeiro.cliente_id == barbeiroAtualId);
        select.appendChild(option);
        });

        // Aguarda o carregamento das especialidades
        carregarEspecialidadesPorBarbeiro(barbeiroAtualId, especialidadeAtualId);
    })
    .catch(error => {
        console.error('Erro ao carregar barbeiros:', error);
    });
}

function carregarEspecialidadesPorBarbeiro(barbeiroId, especialidadeAtualId = null) {
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
        option.value = especialidade.especialidade_id;
        option.textContent = especialidade.nome;
        option.selected = (especialidade.especialidade_id == especialidadeAtualId);
        select.appendChild(option);
      });
    })
    .catch(error => {
      console.error('Erro ao carregar especialidades:', error);
    });
}

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
        location.reload();
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
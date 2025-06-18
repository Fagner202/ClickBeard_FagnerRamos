const barbeiroId = document.getElementsByClassName('barbeiro_id_value')[0].value;

function toggleEspecialidade(button, especialidadeId, barbeiroId) {
    const vinculado = button.getAttribute('data-vinculado') === 'true';

    if (!vinculado) {
        const valor = prompt("Digite o valor para essa especialidade (em R$):", "50.00");

        if (valor === null || valor.trim() === '') {
            alert("Vínculo cancelado. Valor não informado.");
            return;
        }

        fetch('/ajax/vincular-especialidade', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                especialidade_id: especialidadeId,
                valor: valor,
                barbeiro_id: barbeiroId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                button.textContent = "Desvincular";
                button.setAttribute('data-vinculado', 'true');
                button.setAttribute('data-valor', valor);
            } else {
                alert("Erro ao vincular: " + data.mensagem);
            }

            location.reload();
        });
    } else {
        fetch('/ajax/desvincular-especialidade', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                especialidade_id: especialidadeId,
                barbeiro_id: barbeiroId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                button.textContent = "Vincular";
                button.setAttribute('data-vinculado', 'false');
                button.removeAttribute('data-valor');
            } else {
                alert("Erro ao desvincular: " + data.mensagem);
            }

            location.reload();
        });
    }
}

function editarValor(especialidadeId) {
    const novoValor = prompt("Digite o novo valor para essa especialidade:");

    if (novoValor === null || novoValor.trim() === '') {
        alert("Atualização cancelada.");
        return;
    }

    fetch('/ajax/atualizarValor', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            especialidade_id: especialidadeId,
            novo_valor: novoValor
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            document.getElementById('valor-' + especialidadeId).textContent = novoValor;
            alert("Valor atualizado com sucesso.");
        } else {
            alert("Erro ao atualizar valor: " + data.mensagem);
        }
    });

    document.getElementById('valor-' + especialidadeId).textContent = novoValor;
}

function abrirModalAgendados() {
  fetch('/ajax/agendamentos-barbeiro?barbeiro_id=' + barbeiroId)
    .then(response => response.json())
    .then(agendamentos => {
      const tabela = document.getElementById('tabela-agendados');
      tabela.innerHTML = '';

      if (agendamentos.length === 0) {
        tabela.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Nenhum agendamento encontrado.</td></tr>';
      }

      agendamentos.forEach(item => {
        const tr = document.createElement('tr');

        tr.innerHTML = `
          <td>${item.cliente_nome}</td>
          <td>${item.especialidade_nome}</td>
          <td>${item.data_hora}</td>
          <td>
            <button class="btn btn-sm btn-success" onclick="finalizarAtendimento(${item.id})">
              Finalizar
            </button>
          </td>
        `;

        tabela.appendChild(tr);
      });

      const modal = new bootstrap.Modal(document.getElementById('modalAgendados'));
      modal.show();
    })
    .catch(error => {
      console.error('Erro ao buscar agendamentos:', error);
      alert('Erro ao carregar agendamentos.');
    });
}

function finalizarAtendimento(agendamentoId) {
  if (!confirm('Deseja realmente finalizar este atendimento?')) return;

  fetch('/ajax/finalizar-agendamento', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ agendamento_id: agendamentoId })
  })
  .then(response => response.json())
  .then(data => {
    if (data.sucesso) {
      alert('Atendimento finalizado.');
      abrirModalAgendados(); // recarrega
    } else {
      alert('Erro: ' + data.mensagem);
    }
  })
  .catch(error => {
    console.error('Erro ao finalizar atendimento:', error);
  });
}

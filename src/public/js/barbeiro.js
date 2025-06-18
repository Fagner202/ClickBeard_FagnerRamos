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
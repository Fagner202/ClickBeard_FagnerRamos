document.addEventListener('DOMContentLoaded', carregarEspecialidades);

function carregarEspecialidades() {
  fetch('/ajax/especialidades/listar')
    .then(res => res.json())
    .then(especialidades => {
      const lista = document.getElementById('lista-especialidades');
      lista.innerHTML = '';

      especialidades.forEach(esp => {
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center';
        li.innerHTML = `
          ${esp.nome}
          <div>
            <button class="btn btn-sm btn-outline-primary me-1" onclick="abrirModalEditarEspecialidade(${esp.id}, '${esp.nome}')">Editar</button>
            <button class="btn btn-sm btn-outline-danger" onclick="excluirEspecialidade(${esp.id})">Excluir</button>
          </div>
        `;
        lista.appendChild(li);
      });
    });
}

function abrirModalCriarEspecialidade() {
  document.getElementById('especialidade_id').value = '';
  document.getElementById('nome_especialidade').value = '';
  document.getElementById('titulo-modal-especialidade').textContent = 'Nova Especialidade';
  new bootstrap.Modal(document.getElementById('modalEspecialidade')).show();
}

function abrirModalEditarEspecialidade(id, nome) {
  document.getElementById('especialidade_id').value = id;
  document.getElementById('nome_especialidade').value = nome;
  document.getElementById('titulo-modal-especialidade').textContent = 'Editar Especialidade';
  new bootstrap.Modal(document.getElementById('modalEspecialidade')).show();
}

function salvarEspecialidade(event) {
  event.preventDefault();

  const id = document.getElementById('especialidade_id').value;
  const nome = document.getElementById('nome_especialidade').value.trim();

  const url = id ? '/ajax/especialidades/editar' : '/ajax/especialidades/criar';
  const payload = id ? { id, nome } : { nome };

  fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  })
  .then(res => res.json())
  .then(data => {
    if (data.sucesso) {
      bootstrap.Modal.getInstance(document.getElementById('modalEspecialidade')).hide();
      carregarEspecialidades();
    } else {
      alert(data.erro || 'Erro ao salvar');
    }
  });
}

function excluirEspecialidade(id) {
  if (!confirm('Deseja realmente excluir esta especialidade?')) return;

  fetch('/ajax/especialidades/excluir', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id })
  })
  .then(res => res.json())
  .then(data => {
    if (data.sucesso) {
      carregarEspecialidades();
    } else {
      alert(data.erro || 'Erro ao excluir');
    }
  });
}
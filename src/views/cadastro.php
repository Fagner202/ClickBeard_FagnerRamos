<?php
// src/views/cadastro.php

$title = 'Cadastro - ClickBeard';

ob_start(); ?>

<h2 class="mb-4 text-center">Cadastro</h2>
<form id="cadastroForm" class="bg-white p-4 rounded shadow-sm">
    <div class="mb-3">
        <input type="text" name="nome" class="form-control" placeholder="Nome completo" required>
    </div>
    <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="E-mail" required>
    </div>
    <div class="mb-3">
        <input type="password" name="senha" class="form-control" placeholder="Senha" required>
    </div>
    <div class="mb-3">
        <input type="password" name="confirmar_senha" class="form-control" placeholder="Confirmar senha" required>
    </div>
    <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
</form>
<div id="mensagem" class="mt-3 text-center text-danger"></div>

<script>
    document.getElementById('cadastroForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        const response = await fetch('/cadastro', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        document.getElementById('mensagem').innerText = response.ok
            ? 'Cadastro realizado com sucesso!'
            : (result.erro || 'Erro no cadastro');

        if (response.ok) {
            localStorage.setItem('token', result.token);
        }
    });
</script>
<?php

$content = ob_get_clean();
require __DIR__ . '/layout.php';
<?php
// src/views/login.php

$title = 'Login - ClickBeard';

ob_start(); ?>
<h2 class="mb-4 text-center">Login</h2>
<form id="loginForm" class="bg-white p-4 rounded shadow-sm">
    <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="E-mail" required>
    </div>
    <div class="mb-3">
        <input type="password" name="senha" class="form-control" placeholder="Senha" required>
    </div>
    <button type="submit" class="btn btn-success w-100">Entrar</button>
</form>
<div id="mensagem" class="mt-3 text-center text-danger"></div>

<script>
    document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        const response = await fetch('/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        document.getElementById('mensagem').innerText = response.ok
            ? 'Login realizado com sucesso!'
            : (result.erro || 'Erro no login');

        if (response.ok) {
            localStorage.setItem('token', result.token);
        }
    });
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
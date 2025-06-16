<?php
// src/views/login.php

$title = 'Login - ClickBeard';
ob_start(); ?>
<div class="container min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
        <div class="text-center mb-4">
            <h2 class="fw-bold mb-1">ClickBeard</h2>
            <p class="text-muted mb-0">Sistema de Agendamento</p>
        </div>
        <h4 class="mb-3 text-center">Login</h4>
        <?php if (!empty($_GET['erro'])): ?>
            <div class="alert alert-danger text-center py-2"><?= htmlspecialchars($_GET['erro']) ?></div>
        <?php endif; ?>
        <form method="POST" action="/login" autocomplete="off">
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Digite seu e-mail" required autofocus>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" name="senha" id="senha" class="form-control" placeholder="Digite sua senha" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-2">Entrar</button>
        </form>
        <div class="text-center mt-2">
            <span class="text-muted">Não tem conta?</span>
            <a href="/register" class="btn btn-outline-secondary btn-sm ms-2">Cadastre-se</a>
        </div>
    </div>
</div>

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

        if (response.ok && result.token) {
            localStorage.setItem('token', result.token);
            window.location.href = '/agendamentos';
        } else {
            document.getElementById('mensagem').innerText = result.erro || 'Erro no login';
        }
    });
</script>

<?php if (!empty($_GET['erro'])): ?>
    <div class="mt-3 text-center text-danger"><?= htmlspecialchars($_GET['erro']) ?></div>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/layouts/guest_layout.php';

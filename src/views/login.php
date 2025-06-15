<?php
// src/views/login.php

$title = 'Login - ClickBeard';
ob_start(); ?>
<h2 class="mb-4 text-center">Login</h2>
<form method="POST" action="/login" class="bg-white p-4 rounded shadow-sm">
    <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="E-mail" required>
    </div>
    <div class="mb-3">
        <input type="password" name="senha" class="form-control" placeholder="Senha" required>
    </div>
    <button type="submit" class="btn btn-success w-100">Entrar</button>
</form>

<?php if (!empty($_GET['erro'])): ?>
    <div class="mt-3 text-center text-danger"><?= htmlspecialchars($_GET['erro']) ?></div>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';

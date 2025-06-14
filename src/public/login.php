<?php
// public/login.php
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - ClickBeard</title>
</head>
<body>
    <h2>Login</h2>
    <form id="loginForm">
        <input type="email" name="email" placeholder="E-mail" required><br>
        <input type="password" name="senha" placeholder="Senha" required><br>
        <button type="submit">Entrar</button>
    </form>

    <div id="mensagem"></div>

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

            if (response.ok) {
                document.getElementById('mensagem').innerText = 'Login realizado com sucesso!';
                localStorage.setItem('token', result.token);
            } else {
                document.getElementById('mensagem').innerText = result.erro || 'Erro no login';
            }
        });
    </script>
</body>
</html>

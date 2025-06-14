<?php
// public/cadastro.php
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - ClickBeard</title>
</head>
<body>
    <h2>Cadastro</h2>
    <form id="cadastroForm">
        <input type="text" name="nome" placeholder="Nome" required><br>
        <input type="email" name="email" placeholder="E-mail" required><br>
        <input type="password" name="senha" placeholder="Senha" required><br>
        <button type="submit">Cadastrar</button>
    </form>

    <div id="mensagem"></div>

    <script>
        document.getElementById('cadastroForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            const response = await fetch('/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                document.getElementById('mensagem').innerText = 'Cadastro realizado com sucesso!';
                localStorage.setItem('token', result.token);
            } else {
                document.getElementById('mensagem').innerText = result.erro || 'Erro no cadastro';
            }
        });
    </script>
</body>
</html>

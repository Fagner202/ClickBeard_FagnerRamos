<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/jwt.php';

// Define gerarJWT if not already defined
if (!function_exists('gerarJWT')) {
    function gerarJWT($payload) {
        // Exemplo simples de geração de JWT (substitua pela sua implementação real)
        // Você pode usar a biblioteca firebase/php-jwt, por exemplo
        return base64_encode(json_encode($payload));
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Validação simples
    if (empty($email) || empty($senha)) {
        $erro = 'Preencha todos os campos.';
        if (isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['erro' => $erro]);
            exit;
        }
        require __DIR__ . '/../views/login.php';
        exit;
    }

    // Busca usuário no banco
    $user = User::findByEmail($email);

    if ($user && password_verify($senha, $user['senha'])) {
        // Gera token JWT
        $token = gerarJWT(['user' => ['id' => $user['id'], 'nome' => $user['nome'], 'email' => $user['email']]]);

        if (isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['token' => $token, 'redirect' => '/agendamentos']);
            exit;
        } else {
            // Para requisição normal, pode salvar dados na sessão se desejar
            session_start();
            $_SESSION['usuario'] = $user;
            // Redireciona para agendamentos
            header('Location: /agendamentos');
            exit;
        }
    } else {
        $erro = 'E-mail ou senha inválidos.';
        if (isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['erro' => $erro]);
            exit;
        }
        require __DIR__ . '/../views/login.php';
        exit;
    }
}

// Função para detectar AJAX
function isAjax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}
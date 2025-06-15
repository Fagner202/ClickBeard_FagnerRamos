<?php
// src/controllers/login.php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/jwt.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    $user = new User();
    $usuario = $user->findByEmail($email);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $token = JWTHandler::gerarToken([
            'id' => $usuario['id'],
            'email' => $usuario['email'],
            'nome' => $usuario['nome']
        ]);

        // Define o token como cookie com 1h de validade
        setcookie('token', $token, [
            'expires' => time() + 3600,
            'path' => '/',
            'httponly' => true,
            'secure' => false // Altere para true em produção com HTTPS
        ]);

        header('Location: /agendamentos');
        exit;
    }

    // Redireciona de volta com mensagem de erro
    header('Location: /login?erro=Credenciais inválidas');
    exit;
}

// GET: mostrar view de login
require __DIR__ . '/../views/login.php';

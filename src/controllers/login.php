<?php
// controllers/login.php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/jwt.php';
// Ensure the gerarJWT function is defined in jwt.php or define it below if missing
if (!function_exists('gerarJWT')) {
    function gerarJWT($payload) {
        // Implement JWT generation logic here or throw an error if not implemented
        // Example placeholder:
        return base64_encode(json_encode($payload));
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    
    $user = new User();
    $usuario = $user->findByEmail($email);
    
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $token = gerarJWT(['id' => $usuario['id'], 'email' => $usuario['email']]);
        echo json_encode([
            'sucesso' => true,
            'token' => $token,
            'redirect' => '/agendamentos'
        ]);
        exit;
    }
    
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'erro' => 'Credenciais inválidas']);
    exit;
}

// GET: Exibir formulário
require __DIR__ . '/../views/login.php';
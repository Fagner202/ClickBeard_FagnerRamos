<?php
// src/controllers/login.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/jwt.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$email = $data['email'] ?? '';
$senha = $data['senha'] ?? '';

if (!$email || !$senha) {
    http_response_code(400);
    echo json_encode(['erro' => 'E-mail e senha são obrigatórios.']);
    exit;
}

// Buscar cliente
$stmt = $pdo->prepare('SELECT id, senha FROM clientes WHERE email = ?');
$stmt->execute([$email]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente || !password_verify($senha, $cliente['senha'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Credenciais inválidas.']);
    exit;
}

$token = JWTHandler::gerarToken(['id' => $cliente['id'], 'email' => $email]);

echo json_encode(['token' => $token]);

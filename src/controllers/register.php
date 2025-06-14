<?php
// src/controllers/register.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/jwt.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$nome = $data['nome'] ?? '';
$email = $data['email'] ?? '';
$senha = $data['senha'] ?? '';

if (!$nome || !$email || !$senha) {
    http_response_code(400);
    echo json_encode(['erro' => 'Nome, e-mail e senha são obrigatórios.']);
    exit;
}

// Verificar se o e-mail já está cadastrado
$stmt = $pdo->prepare('SELECT id FROM clientes WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['erro' => 'E-mail já cadastrado.']);
    exit;
}

// Inserir novo cliente
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO clientes (nome, email, senha) VALUES (?, ?, ?)');
$stmt->execute([$nome, $email, $senhaHash]);

$clienteId = $pdo->lastInsertId();
$token = JWTHandler::gerarToken(['id' => $clienteId, 'email' => $email]);

http_response_code(201);
echo json_encode(['token' => $token]);

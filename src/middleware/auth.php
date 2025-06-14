<?php

use Firebase\JWT\JWT;

require_once __DIR__ . '/../utils/jwt.php';

// Defina a chave secreta do JWT
define('JWT_KEY', 'your_jwt_secret');

function autenticarUsuario()
{
    error_log('Headers recebidos: ' . json_encode(getallheaders()));
    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['erro' => 'Token não enviado']);
        exit;
    }

    $token = str_replace('Bearer ', '', $headers['Authorization']);

    try {
        $decoded = JWT::decode($token, new \Firebase\JWT\Key(JWT_KEY, 'HS256'));
        return (array) $decoded->user;
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['erro' => 'Token inválido']);
        exit;
    }
}
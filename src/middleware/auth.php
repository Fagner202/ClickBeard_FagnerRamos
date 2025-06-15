<?php
// middleware/auth.php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . '/../utils/jwt.php';

$token = $_COOKIE['token'] ?? null;

if (!$token || !JWTHandler::validarJWT($token)) {
    header('Location: /login');
    exit;
}

function autenticarUsuario()
{
    $token = $_COOKIE['token'] ?? null;

    if (!$token) {
        http_response_code(401);
        echo json_encode(['erro' => 'Token não enviado']);
        exit;
    }

    try {
        // Aqui usamos a mesma chave e algoritmo da JWTHandler:
        $decoded = JWT::decode($token, new Key('chave_secreta_123', 'HS256'));
        return (array) $decoded->data; // usamos 'data' porque é assim que você definiu no payload
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['erro' => 'Token inválido']);
        exit;
    }
}
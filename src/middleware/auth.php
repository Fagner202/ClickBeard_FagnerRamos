<?php
// middleware/auth.php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . '/../utils/jwt.php';

function autenticarUsuario()
{
    $token = $_COOKIE['token'] ?? null;

    if (!$token || !JWTHandler::validarJWT($token)) {
        header('Location: /login');
        exit;
    }

    try {
        $decoded = JWT::decode($token, new Key('chave_secreta_123', 'HS256'));
        return (array) $decoded->data;
    } catch (Exception $e) {
        header('Location: /login');
        exit;
    }
}

<?php
// middleware/auth.php

require_once __DIR__ . '/../utils/jwt.php';

$token = $_COOKIE['token'] ?? null;

if (!$token || !JWTHandler::validarJWT($token)) {
    header('Location: /login');
    exit;
}
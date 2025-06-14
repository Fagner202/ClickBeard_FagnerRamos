<?php
// middleware/auth.php

require_once __DIR__ . '/../utils/jwt.php';

$token = $_COOKIE['token'] ?? null;

if (!$token || !validarJWT($token)) {
    header('HTTP/1.1 401 Unauthorized');
    header('Location: /login');
    exit;
}
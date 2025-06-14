<?php
// src/routes/routes.php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($uri === '/register' && $method === 'POST') {
    require __DIR__ . '/../controllers/register.php';
    exit;
}

if ($uri === '/login' && $method === 'POST') {
    require __DIR__ . '/../controllers/login.php';
    exit;
}

http_response_code(404);
echo json_encode(['erro' => 'Rota não encontrada']);

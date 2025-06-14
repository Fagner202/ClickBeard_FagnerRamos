<?php
// src/routes/routes.php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

switch ($uri) {
    case '/register':
        if ($method === 'POST') {
            require __DIR__ . '/../controllers/register.php';
        } else {
            require __DIR__ . '/../views/cadastro.php';
        }
        break;

    case '/login':
        if ($method === 'POST') {
            require __DIR__ . '/../controllers/login.php';
        } else {
            require __DIR__ . '/../views/login.php';
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['erro' => 'Rota não encontrada']);
        break;
}

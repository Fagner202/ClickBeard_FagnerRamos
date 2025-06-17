<?php
// src/routes/routes.php

require_once __DIR__ . '/../utils/utils.php';
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../controllers/BarbeiroController.php';
require_once __DIR__ . '/../controllers/AjaxController.php';

$pdo = require_once __DIR__ . '/../config/database.php';
$barbeiroController = new BarbeiroController($pdo);
$ajaxController = new AjaxController($pdo);

// Define rotas como: ['GET']['/caminho'] => callable
$routes = [
    'GET' => [
        '/register' => fn() => renderView('cadastro', ['title' => 'Cadastro'], false),
        '/login' => fn() => renderView('login', ['title' => 'Login'], false),
        '/agendamentos' => function () {
            require_once __DIR__ . '/../middleware/auth.php';
            $usuario = autenticarUsuario();
            renderView('agendamento', ['title' => 'Agendamentos', 'usuario' => $usuario], false);
        },
        '/logout' => fn() => require_once __DIR__ . '/../controllers/logout.php',
        '/teste' => function () {
            require_once __DIR__ . '/../middleware/auth.php';
            $usuario = autenticarUsuario();
            renderView('teste', ['title' => 'Home'], false);
        },
        '/usuario' => fn() => $barbeiroController->index(),
    ],

    'POST' => [
        '/register' => fn() => require __DIR__ . '/../controllers/register.php',
        '/login' => fn() => require __DIR__ . '/../controllers/login.php',
        '/barbeiros/criar' => fn() => $barbeiroController->create(),
        '/barbeiros/inativar' => fn() => $barbeiroController->inativar(),
    ],

    'ANY' => [
        '/ajax/vincular-especialidade' => fn() => $ajaxController->vincularEspecialidade(),
        '/ajax/desvincular-especialidade' => fn() => $ajaxController->desvincularEspecialidade(),
        '/ajax/atualizarValor' => fn() => $ajaxController->atualizarValor(),
    ]
];

// Obter a URI e método
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Executar a rota correspondente
if (isset($routes[$method][$uri])) {
    $routes[$method][$uri]();
} elseif (isset($routes['ANY'][$uri])) {
    $routes['ANY'][$uri]();
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Rota não encontrada']);
}

<?php
// src/routes/routes.php

require_once __DIR__ . '/../utils/utils.php';
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../controllers/BarbeiroController.php';
require_once __DIR__ . '/../controllers/AjaxController.php';
require_once __DIR__ . '/../controllers/AgendamentoController.php';
require_once __DIR__ . '/../controllers/AdminController.php';
require_once __DIR__ . '/../models/Especialidade.php';

$pdo = require_once __DIR__ . '/../config/database.php';
$barbeiroController    = new BarbeiroController($pdo);
$ajaxController        = new AjaxController($pdo);
$agendamentoController = new AgendamentoController($pdo);
$adminController       = new AdminController($pdo);

// Define rotas como: ['GET']['/caminho'] => callable
$routes = [
    'GET' => [
        '/register' => fn() => renderView('cadastro', ['title' => 'Cadastro'], false),
        '/login'    => fn() => renderView('login', ['title' => 'Login'], false),
        '/' => function () {
            require_once __DIR__ . '/../middleware/auth.php';
            $usuario = autenticarUsuario();
            renderView('index', ['title' => 'Agendamentos', 'usuario' => $usuario], false);
        },
        '/logout' => fn() => require_once __DIR__ . '/../controllers/logout.php',
        '/teste'  => function () {
            require_once __DIR__ . '/../middleware/auth.php';
            $usuario = autenticarUsuario();
            renderView('teste', ['title' => 'Home'], false);
        },
        '/barbeiro'      => fn() => $barbeiroController->index(),
        '/agendamento'   => fn() => $agendamentoController->index(),
        '/administrador' => fn() => $adminController->index(),
    ],

    'POST' => [
        '/register'           => fn() => require __DIR__ . '/../controllers/register.php',
        '/login'              => fn() => require __DIR__ . '/../controllers/login.php',
        '/barbeiros/criar'    => fn() => $barbeiroController->create(),
        '/barbeiros/inativar' => fn() => $barbeiroController->inativar(),
    ],

    'ANY' => [
        '/ajax/vincular-especialidade'        => fn() => $ajaxController->vincularEspecialidade(),
        '/ajax/desvincular-especialidade'     => fn() => $ajaxController->desvincularEspecialidade(),
        '/ajax/atualizarValor'                => fn() => $ajaxController->atualizarValor(),
        '/ajax/especialidades-barbeiro'       => fn() => $ajaxController->buscarEspecialidadesPorBarbeiro(),
        '/ajax/criar-agendamento'             => fn() => $ajaxController->criarAgendamento(),
        '/ajax/meus-agendamentos'             => fn() => $ajaxController->buscarAgendamentosUsuario(),
        '/ajax/buscar-agendamento/(\d+)'      => fn($matches) => $ajaxController->buscarAgendamento($matches),
        '/ajax/barbeiros-disponiveis'         => fn() => $ajaxController->listarBarbeirosDisponiveis(),
        '/ajax/especialidades-barbeiro/(\d+)' => fn($matches) => $ajaxController->buscarEspecialidadesPorBarbeiro($matches),
        '/ajax/atualizar-agendamento'         => fn() => $ajaxController->atualizarAgendamento(),
        '/ajax/agendamentos-barbeiro'         => fn() => $ajaxController->buscarAgendamentosPorBarbeiro($_GET),
        '/ajax/finalizar-agendamento'         => fn() => $ajaxController->finalizarAgendamento(),
        '/ajax/especialidades/listar'         => fn() => $ajaxController->listarEspecialidades(),
        '/ajax/especialidades/criar'          => fn() => $ajaxController->criarEspecialidade(),
        '/ajax/especialidades/editar'         => fn() => $ajaxController->editarEspecialidade(),
        '/ajax/especialidades/excluir'        => fn() => $ajaxController->excluirEspecialidade(),
        '/ajax/barbeiros/criar'               => fn() => $ajaxController->criarBarbeiro(),
        '/ajax/barbeiros/editar'              => fn() => $ajaxController->editarBarbeiro(),
        '/ajax/barbeiros/excluir'             => fn() => $ajaxController->excluirBarbeiro(),
        '/ajax/agendamentos-admin'            => fn() => $ajaxController->listarAgendamentosAdmin(),
        '/ajax/cancelar-agendamento'          => fn() => $ajaxController->cancelarAgendamento(),

    ]
];

// Obter a URI e método
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Executar a rota correspondente
if (isset($routes[$method][$uri])) {
    $routes[$method][$uri]();
} else {
    // Verifica rotas ANY com suporte a expressões regulares
    foreach ($routes['ANY'] as $pattern => $handler) {
        if (preg_match('#^' . $pattern . '$#', $uri, $matches)) {
            array_shift($matches); // Remove o primeiro item (a string completa)
            $handler($matches);    // Passa os parâmetros capturados
            exit;
        }
    }

    // Se nenhuma rota bater
    http_response_code(404);
    echo json_encode(['erro' => 'Rota não encontrada']);
}

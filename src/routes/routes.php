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

    case '/agendamentos':
        // Log dos headers recebidos para debug
        error_log('Headers recebidos: ' . json_encode(getallheaders()));

        require __DIR__ . '/../middleware/auth.php';
        $usuario = autenticarUsuario();

        // Se a requisição for AJAX (fetch), retorna JSON
        if (
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) {
            // Aqui você pode retornar os dados de agendamento em JSON
            header('Content-Type: application/json');
            echo json_encode(['mensagem' => 'Acesso autorizado', 'usuario' => $usuario]);
            exit;
        }

        // Se não for AJAX, renderiza a view normalmente
        require __DIR__ . '/../views/agendamento.php';
        break;

    default:
        http_response_code(404);
        echo json_encode(['erro' => 'Rota não encontrada']);
        break;
}

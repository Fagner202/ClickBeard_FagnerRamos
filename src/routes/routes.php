<?php
// src/routes/routes.php
require_once __DIR__ . '/../utils/utils.php';
require_once __DIR__ . '/../helpers.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

/**
 * Gerenciamento de rotas da aplicação.
 *
 * Este bloco de código utiliza uma estrutura switch para determinar qual ação executar
 * com base na URI da requisição ($uri) e no método HTTP ($method).
 *
 * - Para a rota '/register':
 *   - Se o método for POST, inclui o controlador responsável pelo registro de usuários.
 *   - Caso contrário, exibe a view de cadastro.
 *
 * - Para a rota '/login':
 *   - Se o método for POST, inclui o controlador responsável pelo login de usuários.
 *   - Caso contrário, exibe a view de login.
 *
 * - Para a rota '/agendamentos':
 *   - Inclui um middleware de autenticação para garantir que o usuário esteja autenticado.
 *   - Em seguida, exibe a view de agendamento.
 *
 * - Para qualquer outra rota:
 *   - Retorna um código de resposta HTTP 404 e uma mensagem de erro em JSON indicando que a rota não foi encontrada.
 *
 * Esta abordagem permite um roteamento simples e direto, facilitando o controle de acesso e a separação entre lógica de controle e apresentação.
 */
switch ($uri) {
    case '/register':
        if ($method === 'POST') {
            require __DIR__ . '/../controllers/register.php';
        } else {
            // require __DIR__ . '/../views/cadastro.php';
            renderView('cadastro', ['title' => 'Cadastro'], false);
        }
        break;

    case '/login':
        if ($method === 'POST') {
            require __DIR__ . '/../controllers/login.php';
        } else {
            renderView('login', ['title' => 'Login'],  false);
        }
        break;

    case '/agendamentos':
        require_once __DIR__ . '/../middleware/auth.php';
        $usuario = autenticarUsuario();
        require __DIR__ . '/../views/agendamento.php';
        break;

    case '/logout':
        require_once __DIR__ . '/../controllers/logout.php';
        break;

    default:
        http_response_code(404);
        echo json_encode(['erro' => 'Rota não encontrada']);
        break;
}

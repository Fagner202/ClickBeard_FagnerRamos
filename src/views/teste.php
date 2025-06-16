<?php
session_start();
require_once __DIR__ . '/../utils/utils.php';
// require_once __DIR__ . '/../config.php';
$usuario = autenticarUsuario();
$title = 'Páigna de teste de rotas';
$cssPage = 'agendamentos'; // Para carregar o CSS específico

ob_start();
?>

<div class="row">
    <div class="col-12">
        <h1 class="mb-4">Página de Teste de Rotas</h1>
        <p>Esta é uma página de teste para verificar o funcionamento das rotas e do layout.</p>
        <p>Usuário autenticado: <?= htmlspecialchars($usuario['nome'] ?? 'Desconhecido') ?></p>
    </div>
    <div class="col-12">
        <h2>Conteúdo Dinâmico</h2>
        <p>Você pode adicionar mais conteúdo aqui para testar o layout e as funcionalidades.</p>
        <p>Por exemplo, você pode testar a exibição de mensagens, formulários ou outros componentes da interface.</p>
        <p>Esta página está acessível apenas para usuários autenticados.</p>
        <p>Para voltar ao dashboard, clique <a href="/agendamentos">aqui</a>.</p>
    </div>

</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layouts/auth_layout.php';
?>
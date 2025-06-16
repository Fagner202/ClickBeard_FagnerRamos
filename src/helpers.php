<?php

function renderView(string $view, array $data = [], bool $auth = true): void
{
    // Extrai as variáveis do array para uso direto na view
    extract($data);

    // Caminho absoluto para a view
    $viewPath = __DIR__ . "/views/{$view}.php";
    // dd($viewPath);

    // Verifica se o arquivo da view existe
    if (!file_exists($viewPath)) {
        http_response_code(404);
        echo "Erro 404: View '{$view}' não encontrada.";
        exit;
    }

    // Captura o conteúdo da view
    ob_start();
    require $viewPath;
    $content = ob_get_clean();

    // Define o layout (auth ou guest)
    $layout = $auth ? 'auth_layout.php' : 'guest_layout.php';

    $layoutPath = __DIR__ . "/views/layouts/{$layout}";

    if (!file_exists($layoutPath)) {
        http_response_code(500);
        echo "Erro: Layout '{$layout}' não encontrado.";
        exit;
    }

    require $layoutPath;
}

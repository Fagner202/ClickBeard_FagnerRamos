<?php
function renderView(string $view, array $data = [], bool $auth = true): void
{
    // Extrai variáveis do array para serem acessadas como $title, $cssPage etc.
    extract($data);

    // Captura o conteúdo da view
    ob_start();
    require __DIR__ . "/views/{$view}.php";
    $content = ob_get_clean();

    // Define o layout a ser usado
    $layout = $auth ? 'auth_layout.php' : 'guest_layout.php';

    // dd($layout);
    require __DIR__ . "/views/layouts/{$layout}";
}
<?php
// src/config/database.php

$host = getenv('DB_HOST') ?: 'db';
$dbname = getenv('DB_NAME') ?: 'clickbeard';
$user = getenv('DB_USER') ?: 'clickuser';
$pass = getenv('DB_PASSWORD') ?: 'click123';

/**
 * Tenta estabelecer uma conexão PDO com o banco de dados MySQL utilizando as variáveis de configuração fornecidas.
 * Define o modo de erro do PDO para exceção.
 * Em caso de falha na conexão, retorna um erro HTTP 500 e uma mensagem JSON informando o erro de conexão.
 *
 * @var string $host   Host do banco de dados MySQL.
 * @var string $dbname Nome do banco de dados.
 * @var string $user   Usuário do banco de dados.
 * @var string $pass   Senha do banco de dados.
 * @var PDO    $pdo    Instância da conexão PDO.
 *
 * @throws PDOException Caso ocorra erro na conexão com o banco de dados.
 */
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro na conexão com o banco de dados.']);
    exit;
}
?>

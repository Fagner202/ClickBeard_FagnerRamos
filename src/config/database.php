<?php
// src/config/database.php

$host = getenv('DB_HOST', 'localhost');
$dbname = getenv('DB_NAME', 'clickbeard');
$user = getenv('DB_USER', 'clickuser');
$pass = getenv('DB_PASS', 'click123');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro na conexão com o banco de dados.']);
    exit;
} ?>

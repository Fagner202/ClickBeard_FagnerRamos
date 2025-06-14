<?php

require_once __DIR__ . '/../config/database.php';

class User
{
    public static function findByEmail($email)
    {
        global $pdo; // Supondo que $pdo é definido em database.php
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    // Outros métodos podem ser adicionados aqui
}
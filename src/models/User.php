<?php

class User
{
    /**
     * Busca um registro de cliente pelo endereço de e-mail.
     *
     * @param string $email O endereço de e-mail do cliente a ser buscado.
     * @return array|false Retorna um array associativo com os dados do cliente se encontrado, ou false caso contrário.
     */
    public static function findByEmail($email)
    {
        $pdo = require __DIR__ . '/../config/database.php';
        $stmt = $pdo->prepare('SELECT * FROM clientes WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    // Outros métodos podem ser adicionados aqui
}
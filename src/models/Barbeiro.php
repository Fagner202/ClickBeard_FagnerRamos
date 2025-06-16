<?php

require_once __DIR__ . '/../config/database.php';

class Barbeiro
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function listarAtivos()
    {
        $sql = "SELECT 
                    c.id,
                    c.nome,
                    c.email,
                    b.idade,
                    b.data_contratacao,
                    b.status,
                    GROUP_CONCAT(e.nome SEPARATOR ', ') AS especialidades
                FROM 
                    clientes c
                INNER JOIN 
                    barbeiros b ON c.id = b.cliente_id
                LEFT JOIN 
                    barbeiro_especialidade be ON b.cliente_id = be.barbeiro_id
                LEFT JOIN 
                    especialidades e ON be.especialidade_id = e.id
                WHERE
                    b.status = 'ativo'
                GROUP BY 
                    c.id, c.nome, c.email, b.idade, b.data_contratacao";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
<?php
require_once __DIR__ . '/../config/database.php';

class Especialidade
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllEspecialidade()
    {
        $sql = 'SELECT * FROM especialidades e';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}
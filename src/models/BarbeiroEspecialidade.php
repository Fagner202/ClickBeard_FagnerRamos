<?php
require_once __DIR__ . '/../config/database.php';

class BarbeiroEspecialidade
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function vincular($barbeiro_id, $especialidade_id)
    {
        $stmt = $this->pdo->prepare("INSERT INTO barbeiro_especialidade (barbeiro_id, especialidade_id) VALUES (?, ?)");
        return $stmt->execute([$barbeiro_id, $especialidade_id]);
    }

    public function desvincular($barbeiro_id, $especialidade_id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM barbeiro_especialidade WHERE barbeiro_id = ? AND especialidade_id = ?");
        return $stmt->execute([$barbeiro_id, $especialidade_id]);
    }
}
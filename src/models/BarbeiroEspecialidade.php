<?php
require_once __DIR__ . '/../config/database.php';

class BarbeiroEspecialidade
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function vincular($barbeiro_id, $especialidade_id, $valor)
    {
        $stmt = $this->pdo->prepare("INSERT INTO barbeiro_especialidade (barbeiro_id, especialidade_id, valor) VALUES (?, ?, ?)");
        return $stmt->execute([$barbeiro_id, $especialidade_id, $valor]);
    }


    public function desvincular($barbeiro_id, $especialidade_id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM barbeiro_especialidade WHERE barbeiro_id = ? AND especialidade_id = ?");
        return $stmt->execute([$barbeiro_id, $especialidade_id]);
    }

    public function getEspecialidadesVinculadas($barbeiro_id)
    {
        $stmt = $this->pdo->prepare("SELECT especialidade_id, valor FROM barbeiro_especialidade WHERE barbeiro_id = ?");
        $stmt->execute([$barbeiro_id]);

        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $valores = [];
        foreach ($resultados as $linha) {
            $valores[$linha['especialidade_id']] = $linha['valor'];
        }

        return $valores;
    }

}
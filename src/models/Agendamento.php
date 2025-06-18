<?php
require_once __DIR__ . '/../config/database.php';

class Agendamento
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function criar($clienteId, $barbeiroId, $especialidadeId, $dataHora)
    {
        $sql = "INSERT INTO agendamentos (cliente_id, barbeiro_id, especialidade_id, data_hora)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$clienteId, $barbeiroId, $especialidadeId, $dataHora]);
    }


}
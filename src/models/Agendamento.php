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

    public function buscarPorClienteId($clienteId)
    {
        $sql = "SELECT a.*, e.nome as especialidade_nome 
                FROM agendamentos a
                LEFT JOIN especialidades e ON a.especialidade_id = e.id
                WHERE a.cliente_id = ?
                ORDER BY a.data_hora ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$clienteId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cancelar($agendamentoId, $clienteId)
    {
        // Verifica se o agendamento pertence ao cliente antes de cancelar
        $sqlVerifica = "SELECT id FROM agendamentos WHERE id = ? AND cliente_id = ?";
        $stmtVerifica = $this->pdo->prepare($sqlVerifica);
        $stmtVerifica->execute([$agendamentoId, $clienteId]);
        
        if (!$stmtVerifica->fetch()) {
            return false;
        }
        
        $sql = "UPDATE agendamentos SET status = 'Cancelado' WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$agendamentoId]);
    }
}
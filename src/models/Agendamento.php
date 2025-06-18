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

    public function buscarPorIdEUsuario($agendamentoId, $usuarioId)
    {
        $sql = "SELECT a.*, e.nome as especialidade_nome 
                FROM agendamentos a
                LEFT JOIN especialidades e ON a.especialidade_id = e.id
                WHERE a.id = ? AND a.cliente_id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$agendamentoId, $usuarioId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function verificarPropriedade($agendamentoId, $usuarioId)
    {
        $sql = "SELECT id FROM agendamentos WHERE id = ? AND cliente_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$agendamentoId, $usuarioId]);
        
        return (bool)$stmt->fetch();
    }

    public function atualizar($agendamentoId, $dataHora, $barbeiroId, $especialidadeId)
    {
        $sql = "UPDATE agendamentos 
                SET data_hora = ?, barbeiro_id = ?, especialidade_id = ?
                WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$dataHora, $barbeiroId, $especialidadeId, $agendamentoId]);
    }

    public function buscarPorBarbeiro($barbeiro_id)
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                a.id,
                c.nome AS cliente_nome,
                e.nome AS especialidade_nome,
                a.data_hora
            FROM agendamentos a
            INNER JOIN clientes c ON c.id = a.cliente_id
            INNER JOIN especialidades e ON e.id = a.especialidade_id
            WHERE a.barbeiro_id = :barbeiro_id
            AND a.status = 'aberto'
            AND a.cancelado = FALSE
            ORDER BY a.data_hora ASC
        ");
        $stmt->execute(['barbeiro_id' => $barbeiro_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function finalizar($agendamento_id)
    {
        $stmt = $this->pdo->prepare("
            UPDATE agendamentos
            SET status = 'finalizado'
            WHERE id = :id
        ");
        return $stmt->execute(['id' => $agendamento_id]);
    }

}
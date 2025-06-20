<?php
require_once __DIR__ . '/../config/database.php';

class Agendamento
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Cria um novo agendamento.
     *
     * @param int $clienteId ID do cliente.
     * @param int $barbeiroId ID do barbeiro.
     * @param int $especialidadeId ID da especialidade.
     * @param string $dataHora Data e hora do agendamento (formato: 'Y-m-d H:i:s').
     * @return bool True em caso de sucesso, false caso contrário.
     */
    public function criar($clienteId, $barbeiroId, $especialidadeId, $dataHora)
    {
        $sql = "INSERT INTO agendamentos (cliente_id, barbeiro_id, especialidade_id, data_hora)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$clienteId, $barbeiroId, $especialidadeId, $dataHora]);
    }

    /**
     * Busca todos os agendamentos de um cliente.
     *
     * @param int $clienteId ID do cliente.
     * @return array Lista de agendamentos do cliente.
     */
    public function buscarPorClienteId($clienteId)
    {
        $sql = "SELECT a.*, e.nome as especialidade_nome 
                FROM agendamentos a
                LEFT JOIN especialidades e ON a.especialidade_id = e.id
                WHERE a.cliente_id = ?
                AND a.cancelado = 0;
                ORDER BY a.data_hora ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$clienteId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cancela um agendamento de um cliente.
     *
     * @param int $agendamentoId ID do agendamento.
     * @param int $clienteId ID do cliente.
     * @return bool True em caso de sucesso, false caso contrário.
     */
    public function cancelar($agendamentoId, $clienteId)
    {
        // Verifica se o agendamento pertence ao cliente antes de cancelar
        $sqlVerifica = "SELECT id FROM agendamentos WHERE id = ? AND cliente_id = ?";
        $stmtVerifica = $this->pdo->prepare($sqlVerifica);
        $stmtVerifica->execute([$agendamentoId, $clienteId]);
        
        if (!$stmtVerifica->fetch()) {
            return false;
        }

        // Atualiza apenas a flag de cancelamento, mantendo o status como 'aberto'
        $sql = "UPDATE agendamentos SET cancelado = 1 WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$agendamentoId]);
    }

    /**
     * Busca um agendamento por ID e usuário (cliente).
     *
     * @param int $agendamentoId ID do agendamento.
     * @param int $usuarioId ID do usuário (cliente).
     * @return array|null Dados do agendamento ou null se não encontrado.
     */
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

    /**
     * Verifica se o agendamento pertence ao usuário (cliente).
     *
     * @param int $agendamentoId ID do agendamento.
     * @param int $usuarioId ID do usuário (cliente).
     * @return bool True se pertence, false caso contrário.
     */
    public function verificarPropriedade($agendamentoId, $usuarioId)
    {
        $sql = "SELECT id FROM agendamentos WHERE id = ? AND cliente_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$agendamentoId, $usuarioId]);
        
        return (bool)$stmt->fetch();
    }

    /**
     * Atualiza os dados de um agendamento.
     *
     * @param int $agendamentoId ID do agendamento.
     * @param string $dataHora Nova data e hora (formato: 'Y-m-d H:i:s').
     * @param int $barbeiroId Novo ID do barbeiro.
     * @param int $especialidadeId Novo ID da especialidade.
     * @return bool True em caso de sucesso, false caso contrário.
     */
    public function atualizar($agendamentoId, $dataHora, $barbeiroId, $especialidadeId)
    {
        $sql = "UPDATE agendamentos 
                SET data_hora = ?, barbeiro_id = ?, especialidade_id = ?
                WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$dataHora, $barbeiroId, $especialidadeId, $agendamentoId]);
    }

    /**
     * Busca agendamentos por barbeiro.
     *
     * @param int $barbeiro_id ID do barbeiro.
     * @return array Lista de agendamentos do barbeiro.
     */
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

    /**
     * Finaliza um agendamento.
     *
     * @param int $agendamento_id ID do agendamento.
     * @return bool True em caso de sucesso, false caso contrário.
     */
    public function finalizar($agendamento_id)
    {
        $stmt = $this->pdo->prepare("
            UPDATE agendamentos
            SET status = 'finalizado'
            WHERE id = :id
        ");
        return $stmt->execute(['id' => $agendamento_id]);
    }

    /**
     * Lista todos os agendamentos para o administrador.
     *
     * @return array Lista de todos os agendamentos.
     */
    public function listarTodosAdmin()
    {
        $sql = "
            SELECT 
                a.id,
                c.nome AS cliente,
                b.nome AS barbeiro,
                e.nome AS especialidade,
                a.data_hora,
                a.status,
                a.cancelado
            FROM agendamentos a
            JOIN clientes c ON a.cliente_id = c.id
            JOIN clientes b ON a.barbeiro_id = b.id
            JOIN especialidades e ON a.especialidade_id = e.id
            ORDER BY a.data_hora DESC
        ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca um agendamento por ID e cliente.
     *
     * @param int $id ID do agendamento.
     * @param int $clienteId ID do cliente.
     * @return array|null Dados do agendamento ou null se não encontrado.
     */
    public function buscarPorIdECliente($id, $clienteId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM agendamentos WHERE id = ? AND cliente_id = ? AND cancelado = 0 AND status = 'aberto'");
        $stmt->execute([$id, $clienteId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }



}
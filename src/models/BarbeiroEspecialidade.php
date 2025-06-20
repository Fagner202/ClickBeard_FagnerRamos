<?php
require_once __DIR__ . '/../config/database.php';

class BarbeiroEspecialidade
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Vincula uma especialidade a um barbeiro com um valor específico.
     *
     * @param int $barbeiro_id ID do barbeiro.
     * @param int $especialidade_id ID da especialidade.
     * @param float $valor Valor do serviço.
     * @return bool True em caso de sucesso, false caso contrário.
     */
    public function vincular($barbeiro_id, $especialidade_id, $valor)
    {
        $stmt = $this->pdo->prepare("INSERT INTO barbeiro_especialidade (barbeiro_id, especialidade_id, valor) VALUES (?, ?, ?)");
        return $stmt->execute([$barbeiro_id, $especialidade_id, $valor]);
    }

    /**
     * Desvincula uma especialidade de um barbeiro.
     *
     * @param int $barbeiro_id ID do barbeiro.
     * @param int $especialidade_id ID da especialidade.
     * @return bool True em caso de sucesso, false caso contrário.
     */
    public function desvincular($barbeiro_id, $especialidade_id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM barbeiro_especialidade WHERE barbeiro_id = ? AND especialidade_id = ?");
        return $stmt->execute([$barbeiro_id, $especialidade_id]);
    }

    /**
     * Retorna as especialidades vinculadas a um barbeiro e seus respectivos valores.
     *
     * @param int $barbeiro_id ID do barbeiro.
     * @return array Array associativo [especialidade_id => valor].
     */
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

    /**
     * Retorna as especialidades vinculadas a um barbeiro com nome e valor.
     *
     * @param int $barbeiroId ID do barbeiro.
     * @return array Lista de especialidades com nome e valor.
     */
    public function getEspecialidadesComValor($barbeiroId)
    {
        $sql = "SELECT e.id AS especialidade_id, e.nome, be.valor
                FROM barbeiro_especialidade be
                JOIN especialidades e ON e.id = be.especialidade_id
                WHERE be.barbeiro_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$barbeiroId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
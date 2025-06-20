<?php
require_once __DIR__ . '/../config/database.php';

class Especialidade
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Retorna todas as especialidades cadastradas.
     *
     * @return array Lista de especialidades.
     */
    public function getAllEspecialidade()
    {
        $sql = 'SELECT * FROM especialidades e';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * Cria uma nova especialidade.
     *
     * @param string $nome Nome da especialidade.
     * @return void
     */
    public function create($nome)
    {
        $stmt = $this->pdo->prepare("INSERT INTO especialidades (nome) VALUES (:nome)");
        $stmt->execute(['nome' => $nome]);
    }

    /**
     * Atualiza o nome de uma especialidade.
     *
     * @param int $id ID da especialidade.
     * @param string $nome Novo nome da especialidade.
     * @return void
     */
    public function update($id, $nome)
    {
        $stmt = $this->pdo->prepare("UPDATE especialidades SET nome = :nome WHERE id = :id");
        $stmt->execute(['nome' => $nome, 'id' => $id]);
    }

    /**
     * Remove uma especialidade pelo ID.
     *
     * @param int $id ID da especialidade.
     * @return void
     */
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM especialidades WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
}
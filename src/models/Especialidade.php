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

    public function create($nome)
    {
        $stmt = $this->pdo->prepare("INSERT INTO especialidades (nome) VALUES (:nome)");
        $stmt->execute(['nome' => $nome]);
    }

    public function update($id, $nome)
    {
        $stmt = $this->pdo->prepare("UPDATE especialidades SET nome = :nome WHERE id = :id");
        $stmt->execute(['nome' => $nome, 'id' => $id]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM especialidades WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
}
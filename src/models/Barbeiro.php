<?php

require_once __DIR__ . '/../config/database.php';

class Barbeiro
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function listarAtivos()
    {
        $sql = "SELECT 
                    c.id,
                    c.nome,
                    c.email,
                    b.idade,
                    b.data_contratacao,
                    b.status,
                    GROUP_CONCAT(e.nome SEPARATOR ', ') AS especialidades
                FROM 
                    clientes c
                INNER JOIN 
                    barbeiros b ON c.id = b.cliente_id
                LEFT JOIN 
                    barbeiro_especialidade be ON b.cliente_id = be.barbeiro_id
                LEFT JOIN 
                    especialidades e ON be.especialidade_id = e.id
                WHERE
                    b.status = 'ativo'
                GROUP BY 
                    c.id, c.nome, c.email, b.idade, b.data_contratacao";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function verificarSeEhBarbeiro($cliente_id)
    {

        $stmt = $this->pdo->prepare("SELECT 
                                        * 
                                    FROM 
                                        barbeiros
                                    WHERE 
                                        cliente_id = ?");
        $stmt->execute([$cliente_id]);
        return $stmt->fetch();
    }

    public function criarBarbeiro($cliente_id, $idade, $data_contratacao)
    {
        $stmt = $this->pdo->prepare("INSERT INTO barbeiros (cliente_id, idade, data_contratacao, status) VALUES (?, ?, ?, 'ativo')");
        return $stmt->execute([$cliente_id, $idade, $data_contratacao]);
    }

    // Ativar um barbeiro inativo
    public function ativarBarbeiro($cliente_id)
    {
        $stmt = $this->pdo->prepare("UPDATE barbeiros SET status = 'ativo' WHERE cliente_id = ? AND status = 'inativo'");
        return $stmt->execute([$cliente_id]);
    }

    // Inativar um barbeiro ativo
    public function inativarBarbeiro($cliente_id)
    {
        $stmt = $this->pdo->prepare("UPDATE barbeiros SET status = 'inativo' WHERE cliente_id = ? AND status = 'ativo'");
        return $stmt->execute([$cliente_id]);
    }

    public function retornaBarbeiro($cliente_id)
    {
        $sql = "SELECT * FROM barbeiros b WHERE b.cliente_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$cliente_id]);
        return $stmt->fetch();
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM barbeiros LEFT JOIN clientes c ON c.id = barbeiros.cliente_id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarAtivosV2()
    {
        $sql = "SELECT * FROM barbeiros WHERE status = 'Ativo'";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTodosComEspecialidades()
    {
        $sql = "
            SELECT 
                c.id AS cliente_id,
                c.nome,
                b.idade,
                b.data_contratacao,
                e.nome AS especialidade
            FROM barbeiros b
            JOIN clientes c ON c.id = b.cliente_id
            LEFT JOIN barbeiro_especialidade be ON be.barbeiro_id = b.cliente_id
            LEFT JOIN especialidades e ON e.id = be.especialidade_id
            ORDER BY c.nome
        ";

        $stmt = $this->pdo->query($sql);
        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Agrupa por barbeiro
        $barbeiros = [];
        foreach ($dados as $linha) {
            $id = $linha['cliente_id'];
            if (!isset($barbeiros[$id])) {
                $barbeiros[$id] = [
                    'id' => $id,
                    'nome' => $linha['nome'],
                    'idade' => $linha['idade'],
                    'data_contratacao' => $linha['data_contratacao'],
                    'especialidades' => []
                ];
            }

            if ($linha['especialidade']) {
                $barbeiros[$id]['especialidades'][] = $linha['especialidade'];
            }
        }

        return array_values($barbeiros);
    }
}
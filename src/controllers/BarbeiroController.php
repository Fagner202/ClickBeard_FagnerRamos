<?php
$pdo = require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Barbeiro.php';

class BarbeiroController
{
    private $barbeiroModel;

    public function __construct($pdo)
    {
        $pdo = require __DIR__ . '/../config/database.php';
        $this->barbeiroModel = new Barbeiro($pdo);
    }

    function index()
    {
        // dd('Na controller listarBarbeiros');
        $barbeiros = $this->barbeiroModel->listarAtivos();
        
        renderView('barbeiros/index', [
            'title' => "Barbeiros - ClickBeard",
            'barbeiros' => $barbeiros
        ], false);
    }

    function deletarBarbeiro($id)
    {
        global $pdo;

        if (!$id) {
            http_response_code(400);
            echo 'ID inválido';
            exit;
        }

        try {
            $pdo->beginTransaction();

            // Deleta primeiro as especialidades (não tem CASCADE)
            $stmt = $pdo->prepare("DELETE FROM barbeiro_especialidade WHERE barbeiro_id = ?");
            $stmt->execute([$id]);

            // Deleta o cliente (o CASCADE vai deletar automaticamente o barbeiro)
            $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
            $stmt->execute([$id]);

            $pdo->commit();
            header('Location: /barbeiros');
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "Erro ao deletar: " . $e->getMessage();
        }
    }

    function criarBarbeiroComCliente()
    {
        global $pdo;
        session_start();

        $cliente_id = $_POST['cliente_id'] ?? null;
        $idade = $_POST['idade'] ?? null;
        $data_contratacao = $_POST['data_contratacao'] ?? null;

        if (!$cliente_id || !$idade || !$data_contratacao) {
            echo "Dados incompletos.";
            return;
        }

        // dd($cliente_id);

        // Verificar se já é barbeiro
        $stmt = $pdo->prepare("SELECT * FROM barbeiros WHERE cliente_id = ?");
        $stmt->execute([$cliente_id]);
        if ($stmt->fetch()) {
            echo "Você já é um barbeiro!";
            return;
        }

        $stmt = $pdo->prepare("INSERT INTO barbeiros (cliente_id, idade, data_contratacao) VALUES (?, ?, ?)");
        $stmt->execute([$cliente_id, $idade, $data_contratacao]);

        header('Location: /barbeiros');
        exit;
    }
}

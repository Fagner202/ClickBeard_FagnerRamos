<?php
require_once __DIR__ . '/../config/database.php';

function listarBarbeiros()
{
    global $pdo;

    $sql = "SELECT 
                c.id,
                c.nome,
                c.email,
                b.idade,
                b.data_contratacao,
                GROUP_CONCAT(e.nome SEPARATOR ', ') AS especialidades
            FROM 
                clientes c
            INNER JOIN 
                barbeiros b ON c.id = b.cliente_id
            LEFT JOIN 
                barbeiro_especialidade be ON b.cliente_id = be.barbeiro_id
            LEFT JOIN 
                especialidades e ON be.especialidade_id = e.id
            GROUP BY 
                c.id, c.nome, c.email, b.idade, b.data_contratacao";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $barbeiros = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

        $stmt = $pdo->prepare("DELETE FROM barbeiro_especialidade WHERE barbeiro_id = ?");
        $stmt->execute([$id]);

        $stmt = $pdo->prepare("DELETE FROM barbeiros WHERE id = ?");
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

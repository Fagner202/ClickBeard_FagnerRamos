<?php
require_once __DIR__ . '/../config/database.php';

function listarBarbeiros()
{
    global $pdo;

    $sql = "SELECT b.id, b.nome, b.idade, b.data_contratacao, GROUP_CONCAT(e.nome SEPARATOR ', ') AS especialidades
            FROM barbeiros b
            LEFT JOIN barbeiro_especialidade be ON b.id = be.barbeiro_id
            LEFT JOIN especialidades e ON be.especialidade_id = e.id
            GROUP BY b.id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $barbeiros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $title = "Barbeiros - ClickBeard";
    ob_start();
    require __DIR__ . '/../views/barbeiros/index.php';
    $content = ob_get_clean();
    require __DIR__ . '/../views/layout.php';
}

function criarBarbeiro()
{
    global $pdo;

    $nome = $_POST['nome'] ?? '';
    $idade = $_POST['idade'] ?? '';
    $data = $_POST['data_contratacao'] ?? '';
    $especialidades = $_POST['especialidades'] ?? [];

    if (empty($nome) || empty($idade) || empty($data)) {
        http_response_code(400);
        echo 'Dados obrigatórios não enviados.';
        exit;
    }

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO barbeiros (nome, idade, data_contratacao) VALUES (?, ?, ?)");
        $stmt->execute([$nome, $idade, $data]);
        $barbeiroId = $pdo->lastInsertId();

        $stmtEsp = $pdo->prepare("INSERT INTO barbeiro_especialidade (barbeiro_id, especialidade_id) VALUES (?, ?)");
        foreach ($especialidades as $espId) {
            $stmtEsp->execute([$barbeiroId, $espId]);
        }

        $pdo->commit();
        header('Location: /barbeiros');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Erro: " . $e->getMessage();
    }
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

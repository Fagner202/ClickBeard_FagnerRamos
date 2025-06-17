<?php

require_once __DIR__ . '/../models/BarbeiroEspecialidade.php';
require_once __DIR__ . '/../config/database.php';

class AjaxController
{
    private $barbeiroEspecialidadeModel;

    public function __construct($pdo)
    {
        $pdo = require __DIR__ . '/../config/database.php';
        $this->barbeiroEspecialidadeModel = new BarbeiroEspecialidade($pdo);
    }

    public function vincularEspecialidade()
    {
        // Lê os dados JSON enviados pelo JavaScript
        $dados = json_decode(file_get_contents('php://input'), true);

        $barbeiro_id = $dados['barbeiro_id'] ?? null;
        $especialidade_id = $dados['especialidade_id'] ?? null;

        if (!$barbeiro_id || !$especialidade_id) {
            http_response_code(400);
            echo json_encode(['erro' => 'Dados inválidos']);
            exit;
        }

        $sucesso = $this->barbeiroEspecialidadeModel->vincular($barbeiro_id, $especialidade_id);

        echo json_encode([
            'sucesso' => $sucesso,
            'mensagem' => $sucesso ? 'Vínculo criado com sucesso.' : 'Erro ao criar vínculo.'
        ]);
    }

    public function desvincularEspecialidade()
    {
        $dados = json_decode(file_get_contents('php://input'), true);

        $barbeiro_id = $dados['barbeiro_id'] ?? null;
        $especialidade_id = $dados['especialidade_id'] ?? null;

        if (!$barbeiro_id || !$especialidade_id) {
            http_response_code(400);
            echo json_encode(['erro' => 'Dados inválidos']);
            exit;
        }

        $sucesso = $this->barbeiroEspecialidadeModel->desvincular($barbeiro_id, $especialidade_id);

        echo json_encode([
            'sucesso' => $sucesso,
            'mensagem' => $sucesso ? 'Vínculo removido com sucesso.' : 'Erro ao remover vínculo.'
        ]);
    }

    public function atualizarValor()
    {
        session_start();
        $barbeiro_id = $_SESSION['usuario']['id'] ?? null;

        $dados = json_decode(file_get_contents('php://input'), true);
        $especialidade_id = $dados['especialidade_id'] ?? null;
        $novo_valor = $dados['novo_valor'] ?? null;

        if (!$barbeiro_id || !$especialidade_id || !$novo_valor) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos.']);
            return;
        }

        try {
            $stmt = $this->pdo->prepare("UPDATE barbeiro_especialidade SET valor = ? WHERE barbeiro_id = ? AND especialidade_id = ?");
            $stmt->execute([$novo_valor, $barbeiro_id, $especialidade_id]);

            echo json_encode(['sucesso' => true, 'mensagem' => 'Valor atualizado com sucesso.']);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro: ' . $e->getMessage()]);
        }
    }
}

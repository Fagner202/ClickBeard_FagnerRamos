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
}

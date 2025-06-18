<?php

require_once __DIR__ . '/../models/BarbeiroEspecialidade.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/auth.php';

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
        // dd($dados);

        $barbeiro_id = $dados['barbeiro_id'] ?? null;
        $especialidade_id = $dados['especialidade_id'] ?? null;
        $valor = $dados['valor'] ?? null;

        if (!$barbeiro_id || !$especialidade_id || !$valor) {
            http_response_code(400);
            echo json_encode(['erro' => 'Dados inválidos']);
            exit;
        }

        $sucesso = $this->barbeiroEspecialidadeModel->vincular($barbeiro_id, $especialidade_id, $valor);

        echo json_encode([
            'sucesso' => $sucesso,
            'mensagem' => $sucesso ? 'Vínculo criado com sucesso.' : 'Erro ao criar vínculo.'
        ]);
    }


    public function desvincularEspecialidade()
    {
        $dados = json_decode(file_get_contents('php://input'), true);

        // dd($dados);

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
        $pdo = require __DIR__ . '/../config/database.php';
        $usuario = autenticarUsuario();
        $barbeiro_id = $usuario['id'];

        $dados = json_decode(file_get_contents('php://input'), true);
        $especialidade_id = $dados['especialidade_id'] ?? null;
        $novo_valor = $dados['novo_valor'] ?? null;

        if (!$barbeiro_id || !$especialidade_id || !$novo_valor) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos.']);
            return;
        }
        $novo_valor = str_replace(',', '.', $novo_valor);

        try {
            $stmt = $pdo->prepare("UPDATE barbeiro_especialidade SET valor = ? WHERE barbeiro_id = ? AND especialidade_id = ?");
            $stmt->execute([$novo_valor, $barbeiro_id, $especialidade_id]);

            echo json_encode(['sucesso' => true, 'mensagem' => 'Valor atualizado com sucesso.']);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro: ' . $e->getMessage()]);
        }
    }

    public function buscarEspecialidadesPorBarbeiro($dados = [])
    {
        // Verifica se veio via query string (rota 1)
        if (isset($_GET['barbeiro_id'])) {
            $barbeiro_id = $_GET['barbeiro_id'];
        }
        // Ou via parâmetro da rota (rota 2)
        elseif (!empty($dados) && is_array($dados)) {
            $barbeiro_id = $dados[0];
        } else {
            http_response_code(400);
            echo json_encode(['erro' => 'Barbeiro não informado']);
            exit;
        }

        $especialidades = $this->barbeiroEspecialidadeModel->getEspecialidadesComValor($barbeiro_id);
        echo json_encode($especialidades);
    }


    public function criarAgendamento()
    {
        require_once __DIR__ . '/../models/Agendamento.php';

        $dados = json_decode(file_get_contents('php://input'), true);
        $usuario = autenticarUsuario();
        // dd($usuario);

        $cliente_id = $usuario['id'] ?? null;
        $barbeiro_id = $dados['barbeiro_id'] ?? null;
        $especialidade_id = $dados['especialidade_id'] ?? null;
        $data_hora = $dados['data_hora'] ?? null;

        if (!$cliente_id || !$barbeiro_id || !$especialidade_id || !$data_hora) {
            http_response_code(400);
            echo json_encode(['erro' => 'Dados incompletos.']);
            return;
        }

        $model = new Agendamento(require __DIR__ . '/../config/database.php');
        $sucesso = $model->criar($cliente_id, $barbeiro_id, $especialidade_id, $data_hora);

        echo json_encode([
            'sucesso' => $sucesso,
            'mensagem' => $sucesso ? 'Agendamento realizado com sucesso!' : 'Erro ao criar agendamento.'
        ]);
    }

    public function buscarAgendamentosUsuario()
    {
        require_once __DIR__ . '/../models/Agendamento.php';
        $usuario = autenticarUsuario();
        
        $model = new Agendamento(require __DIR__ . '/../config/database.php');
        $agendamentos = $model->buscarPorClienteId($usuario['id']);
        
        header('Content-Type: application/json');
        echo json_encode($agendamentos);
    }

    public function cancelarAgendamento()
    {
        require_once __DIR__ . '/../models/Agendamento.php';
        $dados = json_decode(file_get_contents('php://input'), true);
        $usuario = autenticarUsuario();
        
        $agendamentoId = $dados['agendamento_id'] ?? null;
        
        if (!$agendamentoId) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID do agendamento não fornecido.']);
            return;
        }
        
        $model = new Agendamento(require __DIR__ . '/../config/database.php');
        $sucesso = $model->cancelar($agendamentoId, $usuario['id']);
        
        echo json_encode([
            'sucesso' => $sucesso,
            'mensagem' => $sucesso ? 'Agendamento cancelado com sucesso!' : 'Erro ao cancelar agendamento.'
        ]);
    }

    public function buscarAgendamento($agendamentoId)
    {
        $agendamentoId = $agendamentoId[0];
        // dd($agendamentoId);
        require_once __DIR__ . '/../models/Agendamento.php';
        $usuario = autenticarUsuario();
        
        $model = new Agendamento(require __DIR__ . '/../config/database.php');
        $agendamento = $model->buscarPorIdEUsuario($agendamentoId, $usuario['id']);
        
        if (!$agendamento) {
            http_response_code(404);
            echo json_encode(['erro' => 'Agendamento não encontrado ou não pertence ao usuário.']);
            return;
        }
        
        header('Content-Type: application/json');
        echo json_encode($agendamento);
    }

    public function listarBarbeirosDisponiveis()
    {
        require_once __DIR__ . '/../models/Barbeiro.php';
        
        $model = new Barbeiro(require __DIR__ . '/../config/database.php');
        $barbeiros = $model->listarAtivosV2();
        // dd($barbeiros);
        
        header('Content-Type: application/json');
        echo json_encode($barbeiros);
    }

    public function atualizarAgendamento()
    {
        require_once __DIR__ . '/../models/Agendamento.php';
        $dados = json_decode(file_get_contents('php://input'), true);
        $usuario = autenticarUsuario();

        
        $agendamentoId = $dados['agendamento_id'] ?? null;
        $dataHora = $dados['data_hora'] ?? null;
        $barbeiroId = $dados['barbeiro_id'] ?? null;
        $especialidadeId = $dados['especialidade_id'] ?? null;
        
        if (!$agendamentoId || !$dataHora || !$barbeiroId || !$especialidadeId) {
            http_response_code(400);
            echo json_encode(['erro' => 'Dados incompletos.']);
            return;
        }
        
        $model = new Agendamento(require __DIR__ . '/../config/database.php');
        
        // Verifica se o agendamento pertence ao usuário
        if (!$model->verificarPropriedade($agendamentoId, $usuario['id'])) {
            http_response_code(403);
            echo json_encode(['erro' => 'Você não tem permissão para editar este agendamento.']);
            return;
        }
        
        $sucesso = $model->atualizar($agendamentoId, $dataHora, $barbeiroId, $especialidadeId);
        
        echo json_encode([
            'sucesso' => $sucesso,
            'mensagem' => $sucesso ? 'Agendamento atualizado com sucesso!' : 'Erro ao atualizar agendamento.'
        ]);
    }

    public function buscarAgendamentosPorBarbeiro($dados)
    {
        require_once __DIR__ . '/../models/Agendamento.php';
        $barbeiro_id = $dados['barbeiro_id'] ?? null;

        $model = new Agendamento(require __DIR__ . '/../config/database.php');

        if (!$barbeiro_id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID do barbeiro não informado']);
            exit;
        }

        $agendamentos = $model->buscarPorBarbeiro($barbeiro_id);

        echo json_encode($agendamentos);
    }

    public function finalizarAgendamento()
    {
        require_once __DIR__ . '/../models/Agendamento.php';
        $input = json_decode(file_get_contents('php://input'), true);
        $agendamento_id = $input['agendamento_id'] ?? null;

        $model = new Agendamento(require __DIR__ . '/../config/database.php');

        if (!$agendamento_id) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => 'ID do agendamento não informado.']);
            return;
        }

        $sucesso = $model->finalizar($agendamento_id);

        echo json_encode([
            'sucesso' => $sucesso,
            'mensagem' => $sucesso ? 'Agendamento finalizado.' : 'Erro ao finalizar.'
        ]);
    }

}

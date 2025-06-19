<?php

require_once __DIR__ . '/../models/BarbeiroEspecialidade.php';
require_once __DIR__ . '/../models/Especialidade.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/auth.php';

class AjaxController
{
    private $barbeiroEspecialidadeModel;
    private $especialidadeModel;

    public function __construct($pdo)
    {
        $pdo = require __DIR__ . '/../config/database.php';
        $this->especialidadeModel = new Especialidade($pdo);
        $this->barbeiroEspecialidadeModel = new BarbeiroEspecialidade($pdo);
    }

    /**
     * Vincula uma especialidade a um barbeiro com um valor específico.
     * Espera receber JSON com barbeiro_id, especialidade_id e valor.
     * Retorna JSON com sucesso ou erro.
     */
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

    /**
     * Remove o vínculo entre um barbeiro e uma especialidade.
     * Espera receber JSON com barbeiro_id e especialidade_id.
     * Retorna JSON com sucesso ou erro.
     */
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

    /**
     * Atualiza o valor de uma especialidade vinculada ao barbeiro autenticado.
     * Espera receber JSON com especialidade_id e novo_valor.
     * Retorna JSON com sucesso ou erro.
     */
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

    /**
     * Busca as especialidades vinculadas a um barbeiro.
     * Pode receber o barbeiro_id via query string ou parâmetro.
     * Retorna JSON com as especialidades.
     *
     * @param array $dados Parâmetros da rota (opcional)
     */
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

    /**
     * Cria um novo agendamento para o usuário autenticado.
     * Espera receber JSON com barbeiro_id, especialidade_id e data_hora.
     * Retorna JSON com sucesso ou erro.
     */
    public function criarAgendamento()
    {
        require_once __DIR__ . '/../models/Agendamento.php';

        $dados = json_decode(file_get_contents('php://input'), true);
        $usuario = autenticarUsuario();

        $cliente_id = $usuario['id'] ?? null;
        $barbeiro_id = $dados['barbeiro_id'] ?? null;
        $especialidade_id = $dados['especialidade_id'] ?? null;
        $data_hora = $dados['data_hora'] ?? null;

        if (!$cliente_id || !$barbeiro_id || !$especialidade_id || !$data_hora) {
            http_response_code(400);
            echo json_encode(['erro' => 'Dados incompletos.']);
            return;
        }

        // Validação do horário de funcionamento (08:00 às 18:00)
        $hora = date('H:i', strtotime($data_hora));
        if ($hora < '08:00' || $hora >= '18:00') {
            http_response_code(400);
            echo json_encode(['erro' => 'Horário fora do funcionamento da barbearia (08h às 18h).']);
            return;
        }

        dd('Horario correto');

        $model = new Agendamento(require __DIR__ . '/../config/database.php');
        $sucesso = $model->criar($cliente_id, $barbeiro_id, $especialidade_id, $data_hora);

        echo json_encode([
            'sucesso' => $sucesso,
            'mensagem' => $sucesso ? 'Agendamento realizado com sucesso!' : 'Erro ao criar agendamento.'
        ]);
    }


    /**
     * Busca todos os agendamentos do usuário autenticado.
     * Retorna JSON com a lista de agendamentos.
     */
    public function buscarAgendamentosUsuario()
    {
        require_once __DIR__ . '/../models/Agendamento.php';
        $usuario = autenticarUsuario();
        
        $model = new Agendamento(require __DIR__ . '/../config/database.php');
        $agendamentos = $model->buscarPorClienteId($usuario['id']);
        
        header('Content-Type: application/json');
        echo json_encode($agendamentos);
    }

    /**
     * Cancela um agendamento do usuário autenticado.
     * Espera receber JSON com agendamento_id.
     * Retorna JSON com sucesso ou erro.
     */
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

    /**
     * Busca um agendamento específico do usuário autenticado.
     * Retorna JSON com os dados do agendamento ou erro.
     *
     * @param array $agendamentoId Parâmetro da rota (id do agendamento)
     */
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

    /**
     * Lista todos os barbeiros ativos disponíveis.
     * Retorna JSON com a lista de barbeiros.
     */
    public function listarBarbeirosDisponiveis()
    {
        require_once __DIR__ . '/../models/Barbeiro.php';
        
        $model = new Barbeiro(require __DIR__ . '/../config/database.php');
        $barbeiros = $model->listarAtivosV2();
        // dd($barbeiros);
        
        header('Content-Type: application/json');
        echo json_encode($barbeiros);
    }

    /**
     * Atualiza um agendamento do usuário autenticado.
     * Espera receber JSON com agendamento_id, data_hora, barbeiro_id e especialidade_id.
     * Retorna JSON com sucesso ou erro.
     */
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

    /**
     * Busca todos os agendamentos de um barbeiro específico.
     * Espera receber barbeiro_id no array de dados.
     * Retorna JSON com a lista de agendamentos.
     *
     * @param array $dados Parâmetros com barbeiro_id
     */
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

    /**
     * Finaliza um agendamento.
     * Espera receber JSON com agendamento_id.
     * Retorna JSON com sucesso ou erro.
     */
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

    /**
     * Lista todas as especialidades cadastradas.
     * Retorna JSON com a lista de especialidades.
     */
    public function listarEspecialidades()
    {
        $especialidades = $this->especialidadeModel->getAllEspecialidade();
        echo json_encode($especialidades);
    }

    /**
     * Cria uma nova especialidade.
     * Espera receber JSON com nome.
     * Retorna JSON com sucesso ou erro.
     */
    public function criarEspecialidade()
    {
        $dados = json_decode(file_get_contents('php://input'), true);
        $nome = trim($dados['nome'] ?? '');

        if (!$nome) {
            http_response_code(400);
            echo json_encode(['erro' => 'Nome é obrigatório']);
            return;
        }

        $this->especialidadeModel->create($nome);
        echo json_encode(['sucesso' => true]);
    }

    /**
     * Edita uma especialidade existente.
     * Espera receber JSON com id e nome.
     * Retorna JSON com sucesso ou erro.
     */
    public function editarEspecialidade()
    {
        $dados = json_decode(file_get_contents('php://input'), true);
        $id = $dados['id'] ?? null;
        $nome = trim($dados['nome'] ?? '');

        if (!$id || !$nome) {
            http_response_code(400);
            echo json_encode(['erro' => 'Dados inválidos']);
            return;
        }

        $this->especialidadeModel->update($id, $nome);
        echo json_encode(['sucesso' => true]);
    }

    /**
     * Exclui uma especialidade.
     * Espera receber JSON com id.
     * Retorna JSON com sucesso ou erro.
     */
    public function excluirEspecialidade()
    {
        $dados = json_decode(file_get_contents('php://input'), true);
        $id = $dados['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID não informado']);
            return;
        }

        $this->especialidadeModel->delete($id);
        echo json_encode(['sucesso' => true]);
    }

    /**
     * Cria um novo barbeiro.
     * Espera receber JSON com cliente_id, idade e data_contratacao.
     * Retorna JSON com sucesso ou erro.
     */
    public function criarBarbeiro() {
        $dados = json_decode(file_get_contents('php://input'), true);

        $cliente_id = $dados['cliente_id'] ?? null;
        $idade = $dados['idade'] ?? null;
        $data = $dados['data_contratacao'] ?? null;

        if (!$cliente_id || !$idade || !$data) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos']);
            return;
        }

        $pdo = require __DIR__ . '/../config/database.php';

        // Prepara a query com placeholders (evita SQL Injection)
        $stmt = $pdo->prepare("INSERT INTO barbeiros (cliente_id, idade, data_contratacao) VALUES (?, ?, ?)");

        // Executa a query com os valores
        $sucesso = $stmt->execute([$cliente_id, $idade, $data]);

        // Verifica se deu certo
        if ($sucesso) {
            echo json_encode(["Barbeiro cadastrado com sucesso!" => true]);
            return;
        } else {
            echo json_encode(["Erro ao cadastrar barbeiro" . implode (", ", $stmt->errorInfo()) => true]);
        }
    }

    /**
     * Edita os dados de um barbeiro existente.
     * Espera receber JSON com cliente_id, idade e data_contratacao.
     * Retorna JSON com sucesso ou erro.
     */
    public function editarBarbeiro() {
        $dados = json_decode(file_get_contents('php://input'), true);

        $cliente_id = $dados['cliente_id'] ?? null;
        $idade = $dados['idade'] ?? null;
        $data = $dados['data_contratacao'] ?? null;

        if (!$cliente_id || !$idade || !$data) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos']);
            return;
        }

        $pdo = require __DIR__ . '/../config/database.php';

        $stmt = $pdo->prepare("UPDATE barbeiros SET idade = ?, data_contratacao = ? WHERE cliente_id = ?");
        $sucesso = $stmt->execute([$idade, $data, $cliente_id]);

        echo json_encode(['sucesso' => $sucesso]);
    }

    /**
     * Exclui um barbeiro.
     * Espera receber JSON com cliente_id.
     * Retorna JSON com sucesso ou erro.
     */
    public function excluirBarbeiro() {
        $dados = json_decode(file_get_contents('php://input'), true);
        $cliente_id = $dados['cliente_id'] ?? null;

        if (!$cliente_id) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => 'ID inválido']);
            return;
        }

        $pdo = require __DIR__ . '/../config/database.php';
        $stmt = $pdo->prepare("DELETE FROM barbeiros WHERE cliente_id = ?");
        $sucesso = $stmt->execute([$cliente_id]);

        echo json_encode(['sucesso' => $sucesso]);
    }



}

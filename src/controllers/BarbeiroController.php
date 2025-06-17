<?php
$pdo = require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Barbeiro.php';
require_once __DIR__ . '/../models/Especialidade.php';
require_once __DIR__ . '/../models/BarbeiroEspecialidade.php';

class BarbeiroController
{
    private $barbeiroModel;
    private $especialidadeModel;
    private $barbeiroEspecialidadeModel;

    public function __construct($pdo)
    {
        $pdo = require __DIR__ . '/../config/database.php';
        $this->barbeiroModel              = new Barbeiro($pdo);
        $this->especialidadeModel         = new Especialidade($pdo);
        $this->barbeiroEspecialidadeModel = new BarbeiroEspecialidade($pdo);
    }

    function index()
    {
        $usuario = autenticarUsuario();
        $barbeiro = $this->barbeiroModel->retornaBarbeiro($usuario['id']);
        $especialidades = $this->especialidadeModel->getAllEspecialidade();

        $especialidadesVinculadas = [];
        $valores = [];

        if ($barbeiro && $barbeiro['status'] === 'ativo') {
            $especialidadesVinculadas = $this->barbeiroEspecialidadeModel->getEspecialidadesVinculadas($barbeiro['cliente_id']);
            $valores = $especialidadesVinculadas; // Já vem no formato [id => valor]
        }

        renderView('clientes/index', [
            'title' => "Barbeiros - ClickBeard",
            'barbeiro' => $barbeiro,
            'especialidades' => $especialidades,
            'especialidadesVinculadas' => array_keys($especialidadesVinculadas), // Apenas os IDs
            'valores' => $valores // Associativo com valores
        ], false);
    }


    public function create()
    {
        session_start();

        $cliente_id = $_POST['cliente_id'] ?? null;
        $idade = $_POST['idade'] ?? null;
        $data_contratacao = $_POST['data_contratacao'] ?? null;

        if (!$cliente_id || !$idade || !$data_contratacao) {
            $_SESSION['erro'] = "Dados incompletos.";
            header('Location: /usuario');
            exit;
        }

        $barbeiroInativo = $this->barbeiroModel->verificarSeEhBarbeiro($cliente_id);

        if ($barbeiroInativo) {
            // Caso 3: Já foi barbeiro, reativar
            if ($this->barbeiroModel->ativarBarbeiro($cliente_id)) {
                $_SESSION['sucesso'] = "Seu perfil de barbeiro foi reativado!";
            } else {
                $_SESSION['erro'] = "Erro ao reativar perfil de barbeiro.";
            }
            header('Location: /usuario');
            exit;
        }

        // Caso 1: Nunca foi barbeiro
        if ($this->barbeiroModel->criarBarbeiro($cliente_id, $idade, $data_contratacao)) {
            $_SESSION['sucesso'] = "Barbeiro cadastrado com sucesso!";
        } else {
            $_SESSION['erro'] = "Erro ao cadastrar barbeiro.";
        }

        header('Location: /usuario');
        exit;
    }

    public function inativar()
    {
        session_start();
        $cliente_id = $_POST['cliente_id'] ?? null;

        if (!$cliente_id) {
            $_SESSION['erro'] = "Cliente inválido.";
            header('Location: /usuario');
            exit;
        }

        if ($this->barbeiroModel->inativarBarbeiro($cliente_id)) {
            $_SESSION['sucesso'] = "Você não é mais um barbeiro.";
        } else {
            $_SESSION['erro'] = "Erro ao inativar barbeiro.";
        }

        header('Location: /usuario');
        exit;
    }
}

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
        $usuario = autenticarUsuario();
        // dd($usuario);
        // dd('Na controller listarBarbeiros');
        $barbeiro = $this->barbeiroModel->retornaBarbeiro($usuario['id']);
        // dd($barbeiro);
        
        renderView('barbeiros/index', [
            'title' => "Barbeiros - ClickBeard",
            'barbeiro' => $barbeiro
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
            header('Location: /barbeiros');
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
            header('Location: /barbeiros');
            exit;
        }

        // Caso 1: Nunca foi barbeiro
        if ($this->barbeiroModel->criarBarbeiro($cliente_id, $idade, $data_contratacao)) {
            $_SESSION['sucesso'] = "Barbeiro cadastrado com sucesso!";
        } else {
            $_SESSION['erro'] = "Erro ao cadastrar barbeiro.";
        }

        header('Location: /barbeiros');
        exit;
    }

    public function inativar()
    {
        session_start();
        $cliente_id = $_POST['cliente_id'] ?? null;

        if (!$cliente_id) {
            $_SESSION['erro'] = "Cliente inválido.";
            header('Location: /barbeiros');
            exit;
        }

        if ($this->barbeiroModel->inativarBarbeiro($cliente_id)) {
            $_SESSION['sucesso'] = "Você não é mais um barbeiro.";
        } else {
            $_SESSION['erro'] = "Erro ao inativar barbeiro.";
        }

        header('Location: /barbeiros');
        exit;
    }
}

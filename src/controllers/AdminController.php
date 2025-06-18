<?php

$pdo = require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Barbeiro.php';
require_once __DIR__ . '/../models/Especialidade.php';
require_once __DIR__ . '/../models/BarbeiroEspecialidade.php';
require_once __DIR__ . '/../middleware/auth.php';

class AdminController {
    private $barbeiroModel;
    private $especialidadeModel;
    private $barbeiroEspecialidadeModel;

    public function __construct()
    {
        $pdo = require __DIR__ . '/../config/database.php';
        $this->barbeiroModel              = new Barbeiro($pdo);
        $this->especialidadeModel         = new Especialidade($pdo);
        $this->barbeiroEspecialidadeModel = new BarbeiroEspecialidade($pdo);
    }

    public function index()
    {
        $_SESSION = autenticarUsuario();
        // dd($_SESSION);
        // Só admins podem acessar
        session_start();
        if (!isset($_SESSION) || $_SESSION['tipo'] !== 'admin') {
            http_response_code(403);
            echo 'Acesso negado';
            exit;
        }

        dd('Na Controller de Login');

        $barbeiros = $this->barbeiroModel->listarTodosComEspecialidades();
        renderView('administrador/index', ['barbeiros' => $barbeiros]);
    }
}

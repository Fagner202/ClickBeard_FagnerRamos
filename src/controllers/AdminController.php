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

    /**
     * Exibe a dashboard administrativa com lista de barbeiros e clientes.
     * 
     * ### Descrição:
     * - Verifica se o usuário autenticado é um administrador.
     * - Se não for, retorna HTTP 403 (Acesso Negado).
     * - Busca todos os barbeiros (com suas especialidades) e clientes cadastrados.
     * - Renderiza a view `administrador/index` com os dados.
     * 
     * ### Requisitos:
     * - O usuário deve estar logado e ter perfil 'admin'.
     * - As tabelas `barbeiros` e `clientes` devem existir no banco de dados.
     * 
     * @return void
     * 
     * @throws PDOException Se houver erro ao consultar o banco de dados.
     * @throws Exception Se a sessão do usuário for inválida.
     * 
     * @uses Barbeiro::listarTodosComEspecialidades() Para obter a lista de barbeiros.
     * @uses renderView() Para renderizar a view com os dados.
     * 
     * @security HTTP 403 se o usuário não for admin.
     */
    public function index()
    {
        $_SESSION = autenticarUsuario();
        session_start();
        
        if (!isset($_SESSION) || $_SESSION['tipo'] !== 'admin') {
            http_response_code(403);
            echo 'Acesso negado';
            exit;
        }

        // Busca os barbeiros (já existente)
        $barbeiros = $this->barbeiroModel->listarTodosComEspecialidades();
        
        // **Consulta direta dos clientes (usando PDO)**
        $pdo = require __DIR__ . '/../config/database.php';
        $stmt = $pdo->query("SELECT * FROM clientes");
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // dd($clientes);

        // Passa os dados para a view
        renderView('administrador/index', [
            'barbeiros' => $barbeiros,
            'clientes' => $clientes // Adiciona os clientes aqui
        ], false);
    }
}

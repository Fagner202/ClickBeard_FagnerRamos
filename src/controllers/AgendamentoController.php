<?php

$pdo = require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Barbeiro.php';
require_once __DIR__ . '/../models/Especialidade.php';
require_once __DIR__ . '/../models/BarbeiroEspecialidade.php';
require_once __DIR__ . '/../middleware/auth.php';


class AgendamentoController {

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

    /**
     * Exibe a página de agendamento com a lista de barbeiros disponíveis.
     * 
     * ### Descrição:
     * - Obtém todos os barbeiros cadastrados no sistema.
     * - Renderiza a view 'agendamento/index' com os dados necessários.
     * 
     * ### Dados enviados para a view:
     * - `title` (string): Título da página ("Agendamentos").
     * - `barbeiros` (array): Lista de barbeiros retornados pelo model.
     * 
     * @return void
     * 
     * @uses BarbeiroModel::getAll() Para obter a lista completa de barbeiros.
     * @uses renderView() Para renderizar a view com os dados.
     * 
     * @throws \PDOException Se houver erro ao consultar o banco de dados.
     */
    public function index() {
        $barbeiros = $this->barbeiroModel->getAll();
        // dd($barbeiros);

        renderView('agendamento/index', [
            'title' => "Agendamentos",
            'barbeiros' => $barbeiros
        ], false);
    }
}
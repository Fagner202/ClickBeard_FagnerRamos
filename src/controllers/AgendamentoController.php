<?php

require_once __DIR__ . '/../config/database.php';

class AgendamentoController {

    public function __construct($pdo)
    {
        $pdo = require __DIR__ . '/../config/database.php';
    }

    public function index() {
        dd('na Controller de agendamento');
    }
}
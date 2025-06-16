<?php
session_start();
require_once __DIR__ . '/../utils/utils.php';
// require_once __DIR__ . '/../config.php';
$usuario = autenticarUsuario();
$title = 'Agendamentos - ClickBeard';
$cssPage = 'agendamentos'; // Para carregar o CSS específico

ob_start();
?>

<div class="row">
    <div class="col-12">
        <h1 class="mb-4">Meus Agendamentos</h1>
        
        <!-- Filtros -->
        <div class="filtros-container mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Data</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Barbeiro</label>
                    <select class="form-select">
                        <option>Todos</option>
                        <option>João Silva</option>
                        <option>Carlos Oliveira</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select">
                        <option>Todos</option>
                        <option>Confirmado</option>
                        <option>Pendente</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100">Filtrar</button>
                </div>
            </div>
        </div>

        <!-- Lista de Agendamentos -->
        <div class="card-agendamento">
            <div class="card-header-agendamento d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">João Silva</h5>
                    <small>Corte de Tesoura</small>
                </div>
                <span class="badge-estado badge-confirmado">Confirmado</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><i class="bi bi-calendar me-2"></i> 20 de Junho, 2025</p>
                        <p class="mb-1"><i class="bi bi-clock me-2"></i> 10:00 - 10:30</p>
                    </div>
                    <div class="col-md-6 d-flex align-items-center justify-content-end">
                        <button class="btn btn-cancelar me-2">
                            <i class="bi bi-x-circle me-1"></i> Cancelar
                        </button>
                        <button class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i> Editar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-agendamento">
            <div class="card-header-agendamento d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Carlos Oliveira</h5>
                    <small>Barba Completa</small>
                </div>
                <span class="badge-estado badge-pendente">Pendente</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><i class="bi bi-calendar me-2"></i> 22 de Junho, 2025</p>
                        <p class="mb-1"><i class="bi bi-clock me-2"></i> 14:00 - 14:30</p>
                    </div>
                    <div class="col-md-6 d-flex align-items-center justify-content-end">
                        <button class="btn btn-cancelar me-2">
                            <i class="bi bi-x-circle me-1"></i> Cancelar
                        </button>
                        <button class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i> Editar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Novo Agendamento -->
        <div class="d-grid mt-4">
            <a href="#" class="btn btn-primary py-3">
                <i class="bi bi-plus-circle me-2"></i> Novo Agendamento
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layouts/auth_layout.php';
?>
<?php
session_start();
require_once __DIR__ . '/../utils/utils.php';
$usuario = autenticarUsuario();

ob_start();
?>

<div class="container mt-5">
  <div class="text-center mb-5">
    <h1 class="display-5 fw-bold text-primary">💈 Bem-vindo ao ClickBeard</h1>
    <p class="lead">Sistema de agendamento de barbearia simples e eficiente.</p>

    <?php if ($usuario): ?>
      <p class="text-muted">Olá, <strong><?= htmlspecialchars($usuario['nome']) ?></strong>! Explore as funcionalidades abaixo.</p>
    <?php else: ?>
      <p class="text-muted">Faça login ou cadastre-se para começar.</p>
    <?php endif; ?>
  </div>

  <div class="row g-4">

    <!-- Card Cliente -->
    <div class="col-md-4">
      <div class="card shadow-sm h-100 border-primary">
        <div class="card-header bg-primary text-white fw-bold">
          <i class="bi bi-person"></i> Cliente
        </div>
        <div class="card-body">
          <p>Clientes podem:</p>
          <ul>
            <li>Cadastrar-se no sistema</li>
            <li>Agendar atendimentos com barbeiros</li>
            <li>Escolher especialidades disponíveis</li>
            <li>Cancelar agendamentos com no mínimo 2h de antecedência</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Card Barbeiro -->
    <div class="col-md-4">
      <div class="card shadow-sm h-100 border-success">
        <div class="card-header bg-success text-white fw-bold">
          <i class="bi bi-scissors"></i> Barbeiro
        </div>
        <div class="card-body">
          <p>Barbeiros podem:</p>
          <ul>
            <li>Vincular-se a especialidades cadastradas pelo administrador</li>
            <li>Definir seus próprios valores por especialidade</li>
            <li>Visualizar seus agendamentos e atender clientes</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Card Administrador -->
    <div class="col-md-4">
      <div class="card shadow-sm h-100 border-danger">
        <div class="card-header bg-danger text-white fw-bold">
          <i class="bi bi-diamond"></i> Administrador
        </div>
        <div class="card-body">
          <p>Administradores podem:</p>
          <ul>
            <li>Criar, editar e excluir barbeiros</li>
            <li>Gerenciar especialidades disponíveis</li>
            <li>Visualizar todos os agendamentos do sistema</li>
          </ul>
        </div>
      </div>
    </div>

  </div>

  <div class="text-center mt-5">
    <a href="/agendamento" class="btn btn-outline-primary btn-lg rounded-pill">
      <i class="bi bi-calendar-check"></i> Fazer um agendamento
    </a>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layouts/auth_layout.php';
?>

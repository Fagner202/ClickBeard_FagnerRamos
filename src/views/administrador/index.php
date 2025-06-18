<h2>Lista de Barbeiros</h2>

<?php foreach ($barbeiros as $barbeiro): ?>
    <div>
        <h4><?= htmlspecialchars($barbeiro['nome']) ?></h4>
        <p>Idade: <?= htmlspecialchars($barbeiro['idade']) ?></p>
        <p>Data de Contratação: <?= htmlspecialchars($barbeiro['data_contratacao']) ?></p>
        <p>Especialidades: <?= !empty($barbeiro['especialidades']) 
            ? implode(', ', array_map('htmlspecialchars', $barbeiro['especialidades'])) 
            : 'Nenhuma' ?>
        </p>
        <hr>
    </div>
<?php endforeach; ?>

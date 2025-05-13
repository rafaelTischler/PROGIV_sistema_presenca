<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
verificaLogin();

require_once 'includes/header.php';
?>

<h2>Dashboard</h2>

<?php if (isAdmin()): ?>
    <div class="alert alert-info">
        <h3>Área do Administrador</h3>
        <ul>
            <li><a href="admin/reunioes/listar.php">Gerenciar Reuniões</a></li>
            <li><a href="admin/declaracoes/emitir.php">Emitir Declarações</a></li>
        </ul>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <h3>Área do Servidor</h3>
        <ul>
            <li><a href="servidor/presenca/registrar.php">Registrar Presença</a></li>
            <li><a href="servidor/minhas-presencas/index.php">Minhas Presenças</a></li>
        </ul>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
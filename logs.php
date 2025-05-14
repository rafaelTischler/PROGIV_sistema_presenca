<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/funcoes.php';

verificaLogin(); 

if (!isAdmin()) {
    redirect('index.php'); 
}

$stmt = $conn->prepare("SELECT id, usuario_id, acao, modulo, data_log FROM logs ORDER BY data_log DESC");
$stmt->execute();
$logs = $stmt->get_result();

require_once 'includes/header.php';  
?>

<h2>Logs de Ações</h2>

<?php if ($logs->num_rows > 0): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuário ID</th>
                <th>Ação</th>
                <th>Módulo</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($log = $logs->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($log['id']) ?></td>
                    <td><?= htmlspecialchars($log['usuario_id']) ?></td>
                    <td><?= htmlspecialchars($log['acao']) ?></td>
                    <td><?= htmlspecialchars($log['modulo']) ?></td>
                    <td><?= formatarData($log['data_log'], 'd/m/Y H:i') ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <form action="index.php" method="get">
        <button type="submit" class="btn btn-secondary mb-3">Voltar</button>
    </form>


<?php else: ?>
    <p>Não há logs registrados.</p>
<?php endif; ?>

<?php require_once 'includes/footer.php'; 
?>
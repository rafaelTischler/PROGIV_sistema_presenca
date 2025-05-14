<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
verificaLogin();

$usuario_id = $_SESSION['usuario_id'];

$stmt = $conn->prepare("
    SELECT p.data_presenca, r.titulo, r.data_reuniao, r.local_reuniao
    FROM presencas p
    JOIN reunioes r ON p.reuniao_id = r.id
    WHERE p.usuario_id = ?
    ORDER BY r.data_reuniao DESC
");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

require_once '../../includes/header.php';
?>

<div class="container mt-4">
    <h2>Minhas Presenças</h2>

    <?php if ($result->num_rows === 0): ?>
        <div class="alert alert-info mt-3">Você ainda não registrou presença em nenhuma reunião.</div>
    <?php else: ?>
        <table class="table table-bordered mt-3">
            <thead class="table-light">
                <tr>
                    <th>Título da Reunião</th>
                    <th>Data da Reunião</th>
                    <th>Local</th>
                    <th>Data da Presença</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['titulo']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['data_reuniao'])) ?></td>
                        <td><?= htmlspecialchars($row['local_reuniao']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['data_presenca'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="../../index.php" class="btn btn-secondary mt-3">Voltar ao Início</a>
</div>

<?php require_once '../../includes/footer.php'; ?>

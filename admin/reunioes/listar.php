<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
verificaLogin();
if (!isAdmin()) redirect('../servidor/presenca/registrar.php');

$reunioes = $conn->query("
    SELECT r.*, u.nome as criador 
    FROM reunioes r
    JOIN usuarios u ON r.criado_por = u.id
    ORDER BY r.data_reuniao DESC
");

require_once '../../includes/header.php';
?>

<h2>Lista de Reuniões</h2>
<a href="criar.php" class="btn">Nova Reunião</a>

<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
        <tr style="background: #f5f5f5;">
            <th style="padding: 10px; border: 1px solid #ddd;">Título</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Data</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Local</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Criador</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($reuniao = $reunioes->fetch_assoc()): ?>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd;"><?= $reuniao['titulo'] ?></td>
            <td style="padding: 10px; border: 1px solid #ddd;"><?= date('d/m/Y H:i', strtotime($reuniao['data_reuniao'])) ?></td>
            <td style="padding: 10px; border: 1px solid #ddd;"><?= $reuniao['local_reuniao'] ?></td>
            <td style="padding: 10px; border: 1px solid #ddd;"><?= $reuniao['criador'] ?></td>
            <td style="padding: 10px; border: 1px solid #ddd;">
                <a href="visualizar.php?id=<?= $reuniao['id'] ?>">Visualizar</a> |
                <a href="editar.php?id=<?= $reuniao['id'] ?>">Editar</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php require_once '../../includes/footer.php'; ?>
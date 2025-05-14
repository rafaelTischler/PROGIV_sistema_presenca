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

if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    $stmt = $conn->prepare("DELETE FROM reunioes WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['sucesso'] = "Reunião excluída com sucesso.";
    } else {
        $_SESSION['erro'] = "Erro ao excluir reunião: " . $conn->error;
    }


    header("Location: listar.php");
    exit;
}

require_once '../../includes/header.php';
?>
<h2>Lista de Reuniões</h2>

<?php if (isset($_SESSION['sucesso'])): ?>
    <div class="alert alert-success"><?= $_SESSION['sucesso'];
                                        unset($_SESSION['sucesso']); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['erro'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['erro'];
                                    unset($_SESSION['erro']); ?></div>
<?php endif; ?>

<a href="criar.php" class="btn btn-primary mb-3">Nova Reunião</a>

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
                <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($reuniao['titulo']) ?></td>
                <td style="padding: 10px; border: 1px solid #ddd;"><?= date('d/m/Y H:i', strtotime($reuniao['data_reuniao'])) ?></td>
                <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($reuniao['local_reuniao']) ?></td>
                <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($reuniao['criador']) ?></td>
                <td style="padding: 10px; border: 1px solid #ddd;">
                    <a href="visualizar.php?id=<?= $reuniao['id'] ?>">Visualizar</a> |
                    <a href="editar.php?id=<?= $reuniao['id'] ?>">Editar</a> |
                    <a href="listar.php?excluir=<?= $reuniao['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir esta reunião?');" style="color:red;">Excluir</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<form action="../../index.php" method="get">
    <button type="submit" class="btn btn-secondary mt-3">Voltar</button>
</form>


<?php require_once '../../includes/footer.php'; ?>
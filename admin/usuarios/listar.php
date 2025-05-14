<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

verificaLogin();

if (!isAdmin()) {
    $_SESSION['erro'] = "Acesso negado!";
    redirect('../../login.php');
}

// Exclusão de usuário
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);

    // Impede excluir a si mesmo
    if ($id == $_SESSION['usuario_id']) {
        $_SESSION['erro'] = "Você não pode excluir sua própria conta.";
    } else {
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['sucesso'] = "Usuário excluído com sucesso.";
        } else {
            $_SESSION['erro'] = "Erro ao excluir usuário: " . $conn->error;
        }
    }

    redirect('admin/usuarios/listar.php');
}

$result = $conn->query("SELECT * FROM usuarios ORDER BY nome ASC");

require_once '../../includes/header.php';
?>

<h2>Gerenciar Usuários</h2>

<?php if (isset($_SESSION['erro'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['erro'];
                                    unset($_SESSION['erro']); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['sucesso'])): ?>
    <div class="alert alert-success"><?= $_SESSION['sucesso'];
                                        unset($_SESSION['sucesso']); ?></div>
<?php endif; ?>

<a href="../cadastrar_usuario.php" class="btn btn-success mb-3">Novo Usuário</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Matrícula</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Cargo</th>
            <th>Administrador?</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($usuario = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $usuario['id'] ?></td>
                <td><?= htmlspecialchars($usuario['matricula_siape']) ?></td>
                <td><?= htmlspecialchars($usuario['nome']) ?></td>
                <td><?= htmlspecialchars($usuario['email']) ?></td>
                <td><?= htmlspecialchars($usuario['cargo']) ?></td>
                <td><?= $usuario['is_admin'] ? 'Sim' : 'Não' ?></td>
                <td>
                    <a href="editar.php?id=<?= $usuario['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                    <?php if ($usuario['id'] != $_SESSION['usuario_id']): ?>
                        <a href="listar.php?excluir=<?= $usuario['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<form action="../../index.php" method="get">
    <button type="submit" class="btn btn-secondary mb-3">Voltar</button>
</form>


<?php require_once '../../includes/footer.php'; ?>
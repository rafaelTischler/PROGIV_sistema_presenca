<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
verificaLogin();
if (!isAdmin()) {
    $_SESSION['erro'] = "Acesso negado!";
    redirect('../servidor/presenca/registrar.php');
}

$usuarios = $conn->query("SELECT * FROM usuarios ORDER BY nome");

require_once '../../includes/header.php';
?>

<h2>Lista de Usuários</h2>
<a href="cadastrar_usuario.php" class="btn btn-primary mb-3">Novo Usuário</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Matrícula</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Cargo</th>
            <th>Tipo</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php while($usuario = $usuarios->fetch_assoc()): ?>
        <tr>
            <td><?= $usuario['matricula_siape'] ?></td>
            <td><?= $usuario['nome'] ?></td>
            <td><?= $usuario['email'] ?></td>
            <td><?= $usuario['cargo'] ?></td>
            <td><?= $usuario['is_admin'] ? 'Admin' : 'Usuário' ?></td>
            <td>
                <a href="editar_usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                <?php if ($usuario['id'] != $_SESSION['usuario_id']): ?>
                <a href="excluir_usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?')">Excluir</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php require_once '../../includes/footer.php'; ?>
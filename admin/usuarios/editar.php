<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
verificaLogin();
if (!isAdmin()) redirect('../servidor/presenca/registrar.php');

if (!isset($_GET['id'])) {
    $_SESSION['erro'] = "ID do usuário não informado.";
    header("Location: listar.php");
    exit;
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['erro'] = "Usuário não encontrado.";
    header("Location: listar.php");
    exit;
}
$usuario = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricula = trim($_POST['matricula']);
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $cargo = trim($_POST['cargo']);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    $novaSenha = trim($_POST['senha']);

    if (!empty($novaSenha)) {
        $stmt = $conn->prepare("UPDATE usuarios SET matricula_siape = ?, nome = ?, email = ?, senha = ?, cargo = ?, is_admin = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $matricula, $nome, $email, $novaSenha, $cargo, $is_admin, $id);
    } else {
        $stmt = $conn->prepare("UPDATE usuarios SET matricula_siape = ?, nome = ?, email = ?, cargo = ?, is_admin = ? WHERE id = ?");
        $stmt->bind_param("ssssii", $matricula, $nome, $email, $cargo, $is_admin, $id);
    }

    if ($stmt->execute()) {
        $_SESSION['sucesso'] = "Usuário atualizado com sucesso.";
        header("Location: listar.php");
        exit;
    } else {
        $_SESSION['erro'] = "Erro ao atualizar usuário: " . $conn->error;
    }
}

require_once '../../includes/header.php';
?>

<div class="container mt-4">
    <h2>Editar Usuário</h2>

    <?php if (isset($_SESSION['erro'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['erro']; unset($_SESSION['erro']); ?></div>
    <?php endif; ?>

    <form method="POST" class="border p-4 rounded bg-light">
        <div class="mb-3">
            <label class="form-label">Matrícula SIAPE</label>
            <input type="text" name="matricula" class="form-control" value="<?= htmlspecialchars($usuario['matricula_siape']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">E-mail</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Cargo</label>
            <input type="text" name="cargo" class="form-control" value="<?= htmlspecialchars($usuario['cargo']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nova Senha <small class="text-muted">(deixe em branco para manter a atual)</small></label>
            <input type="password" name="senha" class="form-control">
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="is_admin" id="is_admin" <?= $usuario['is_admin'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_admin">É administrador?</label>
        </div>

        <button type="submit" class="btn btn-success">Salvar Alterações</button>
        <a href="listar.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>

<?php
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricula = trim($_POST['matricula']);
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $cargo = trim($_POST['cargo']);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    if (empty($matricula)) {
        $_SESSION['erro'] = "Matrícula é obrigatória!";
    } else {
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE matricula_siape = ?");
        $stmt->bind_param("s", $matricula);
        $stmt->execute();

        if ($stmt->get_result()->num_rows > 0) {
            $_SESSION['erro'] = "Matrícula já cadastrada!";
        } else {
            $stmt = $conn->prepare("INSERT INTO usuarios 
                                  (matricula_siape, nome, email, senha, cargo, is_admin) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssi", $matricula, $nome, $email, $senha, $cargo, $is_admin);

            if ($stmt->execute()) {
                $_SESSION['sucesso'] = "Usuário cadastrado com sucesso!";
                redirect('login.php');
            } else {
                $_SESSION['erro'] = "Erro ao cadastrar usuário: " . $conn->error;
            }
        }
    }
}

?>

<?php if (isset($_SESSION['erro'])): ?>
    <div class="alert alert-danger">
        <?= $_SESSION['erro'];
        unset($_SESSION['erro']); ?>
    </div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Cadastrar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <h2 class="mb-4">Cadastrar Novo Usuário</h2>

        <?php if (isset($_SESSION['erro'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['erro'];
                unset($_SESSION['erro']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['sucesso'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['sucesso'];
                unset($_SESSION['sucesso']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="border p-4 rounded shadow-sm bg-light">
            <div class="mb-3">
                <label for="matricula" class="form-label">Matrícula SIAPE</label>
                <input type="text" name="matricula" id="matricula" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="nome" class="form-label">Nome Completo</label>
                <input type="text" name="nome" id="nome" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" name="senha" id="senha" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="cargo" class="form-label">Cargo</label>
                <input type="text" name="cargo" id="cargo" class="form-control" required>
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="is_admin" id="is_admin">
                <label class="form-check-label" for="is_admin">É administrador?</label>
            </div>

            <button type="submit" class="btn btn-primary">Cadastrar</button>
            <a href="listar_usuarios.php" class="btn btn-secondary ms-2">Voltar</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
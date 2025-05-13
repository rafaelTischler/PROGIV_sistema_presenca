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

<h2>Cadastrar Novo Usuário</h2>

<?php if (isset($_SESSION['erro'])): ?>
    <div class="alert alert-danger">
        <?= $_SESSION['erro'];
        unset($_SESSION['erro']); ?>
    </div>
<?php endif; ?>

<form method="POST">
    <div class="form-group">
        <label>Matrícula SIAPE:</label>
        <input type="text" name="matricula" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Nome Completo:</label>
        <input type="text" name="nome" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Email:</label>
        <input type="email" name="email" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Senha (texto puro):</label>
        <input type="password" name="senha" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Cargo:</label>
        <input type="text" name="cargo" class="form-control" required>
    </div>

    <div class="form-check mb-3">
        <input type="checkbox" name="is_admin" class="form-check-input" id="is_admin">
        <label class="form-check-label" for="is_admin">É administrador?</label>
    </div>

    <button type="submit" class="btn btn-primary">Cadastrar</button>
    <a href="listar_usuarios.php" class="btn btn-secondary">Voltar</a>
</form>
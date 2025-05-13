<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["entrar"])) {
        $matricula = $_POST['matricula'] ?? '';
        $senha = $_POST['senha'] ?? '';

        $stmt = $conn->prepare("SELECT id, nome, matricula_siape, senha, cargo, is_admin, ativo 
                           FROM usuarios 
                           WHERE matricula_siape = ?");
        $stmt->bind_param("s", $matricula);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();

            if (!$usuario['ativo']) {
                $erro = "Conta desativada!";
            } elseif ($senha === $usuario['senha']) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_matricula'] = $usuario['matricula_siape'];
                $_SESSION['usuario_cargo'] = $usuario['cargo'];
                $_SESSION['is_admin'] = $usuario['is_admin'];

                $conn->query("INSERT INTO logs (usuario_id, acao, modulo) 
                         VALUES ({$usuario['id']}, 'login', 'sistema')");

                redirect('index.php');
            } else {
                $erro = "Senha incorreta!";
            }
        } else {
            $erro = "Matrícula não encontrada!";
        }
    }
    if (isset($_POST["cadastrar"])) {
        redirect('admin/cadastrar_usuario.php');
    }
}

require_once 'includes/header.php';
?>

<h2>Login</h2>
<?php if (isset($erro)): ?>
    <div class="alert alert-danger"><?= $erro ?></div>
<?php endif; ?>

<form method="POST">
    <div class="form-group">
        <label>Matrícula SIAPE:</label>
        <input type="text" name="matricula" class="form-control">
    </div>
    <div class="form-group">
        <label>Senha:</label>
        <input type="password" name="senha" class="form-control">
    </div>
    <button type="submit" name="entrar" class="btn">Entrar</button>
    <button type="submit" name="cadastrar" class="btn">Não tenho uma conta</button>
</form>

<?php require_once 'includes/footer.php'; ?>
<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (isset($_POST['codigo'])) {
    $codigo = trim($_POST['codigo']);

    // Verifica se é um token válido
    $stmt = $conn->prepare("SELECT id FROM reunioes WHERE qrcode_token = ?");
    $stmt->bind_param("s", $codigo);
    $stmt->execute();

    if ($stmt->get_result()->num_rows === 1) {
        redirect("servidor/presenca/registrar.php?token=" . urlencode($codigo));
    } else {
        $_SESSION['erro'] = "Código inválido ou expirado.";
    }
}

?>

<h2>Registrar Presença com Código</h2>

<?php if (isset($_SESSION['erro'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['erro'];
                                    unset($_SESSION['erro']); ?></div>
<?php endif; ?>

<form method="POST">
    <div class="form-group">
        <label>Digite o código da reunião:</label>
        <input type="text" name="codigo" class="form-control" required autofocus>
    </div>

    <button type="submit" class="btn btn-primary">Registrar Presença</button>
</form>
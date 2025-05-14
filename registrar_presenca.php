<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
verificaLogin();

$mensagem_erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reuniao_id = intval($_POST['id']);

    // Verifica se a reunião existe
    $stmt = $conn->prepare("SELECT id FROM reunioes WHERE id = ?");
    $stmt->bind_param("i", $reuniao_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        // Redireciona para o registrar.php
        header("Location: servidor/presenca/registrar.php?id=" . $reuniao_id);
        exit;
    } else {
        $mensagem_erro = "Reunião com o código informado não foi encontrada.";
    }
}

require_once 'includes/header.php';
?>

<h2>Registrar Presença Manualmente</h2>

<?php if ($mensagem_erro): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($mensagem_erro) ?>
    </div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label for="id" class="form-label">Código da Reunião (ID):</label>
        <input type="number" class="form-control" id="id" name="id" required>
    </div>
    <button type="submit" class="btn btn-primary">Registrar Presença</button>
    <a href="index.php" class="btn btn-secondary">Voltar ao Início</a>
</form>

<?php require_once 'includes/footer.php'; ?>

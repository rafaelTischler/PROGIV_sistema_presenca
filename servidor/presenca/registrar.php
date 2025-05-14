<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
verificaLogin();

/** Função para validar se a reunião existe */
function buscarReuniao($conn, $reuniao_id) {
    $stmt = $conn->prepare("SELECT id, titulo FROM reunioes WHERE id = ?");
    $stmt->bind_param("i", $reuniao_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/** Função para registrar presença, retorna status */
function processarPresenca($conn, $usuario_id, $reuniao_id) {
    $stmt = $conn->prepare("SELECT id FROM presencas WHERE usuario_id = ? AND reuniao_id = ?");
    $stmt->bind_param("ii", $usuario_id, $reuniao_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        return 'ja_registrado';
    }

    $stmt = $conn->prepare("INSERT INTO presencas (usuario_id, reuniao_id, data_presenca) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $usuario_id, $reuniao_id);
    return $stmt->execute() ? 'registrado' : 'erro';
}

// Verificação do parâmetro
if (!isset($_GET['id'])) {
    $_SESSION['erro'] = "Reunião não especificada.";
    header('Location: ../../index.php');
    exit;
}

$reuniao_id = intval($_GET['id']);
$usuario_id = $_SESSION['usuario_id'];

// Busca reunião
$reuniao = buscarReuniao($conn, $reuniao_id);
if (!$reuniao) {
    $_SESSION['erro'] = "Reunião não encontrada.";
    header('Location: ../../index.php');
    exit;
}

// Processa presença
$status = processarPresenca($conn, $usuario_id, $reuniao_id);

require_once '../../includes/header.php';
?>

<div class="container mt-5">
    <?php if ($status === 'ja_registrado'): ?>
        <div class="alert alert-warning text-center">
            <h4 class="alert-heading">Presença já registrada.</h4>
            <p>Você já marcou presença nesta reunião anteriormente.</p>
        </div>
    <?php elseif ($status === 'registrado'): ?>
        <div class="alert alert-success text-center">
            <h4 class="alert-heading">Presença registrada com sucesso!</h4>
            <p>Obrigado por confirmar sua participação na reunião.</p>
        </div>
    <?php else: ?>
        <div class="alert alert-danger text-center">
            <h4 class="alert-heading">Erro ao registrar presença.</h4>
            <p>Tente novamente mais tarde.</p>
        </div>
    <?php endif; ?>

    <div class="text-center">
        <a href="../../index.php" class="btn btn-primary">Voltar ao Início</a>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

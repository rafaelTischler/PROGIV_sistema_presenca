<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
verificaLogin();

// Se veio via QR Code (token)
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Busca a reunião pelo token
    $stmt = $conn->prepare("SELECT id, titulo, data_reuniao FROM reunioes WHERE qrcode_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $reuniao = $result->fetch_assoc();
        $reuniao_id = $reuniao['id'];
        $usuario_id = $_SESSION['usuario_id'];
        
        // Verifica se já registrou presença
        $stmt = $conn->prepare("SELECT id FROM presencas WHERE usuario_id = ? AND reuniao_id = ?");
        $stmt->bind_param("ii", $usuario_id, $reuniao_id);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows === 0) {
            // Registra a presença
            $stmt = $conn->prepare("INSERT INTO presencas (usuario_id, reuniao_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $usuario_id, $reuniao_id);
            
            if ($stmt->execute()) {
                registrarLog('registrar_presenca', 'servidor/presenca', [
                    'reuniao_id' => $reuniao_id,
                    'usuario_id' => $usuario_id
                ]);
                
                $_SESSION['sucesso'] = "Presença registrada com sucesso na reunião: " . $reuniao['titulo'];
            } else {
                $_SESSION['erro'] = "Erro ao registrar presença: " . $conn->error;
            }
        } else {
            $_SESSION['info'] = "Você já registrou presença nesta reunião.";
        }
    } else {
        $_SESSION['erro'] = "Token de reunião inválido ou expirado.";
    }
    
    redirect('servidor/minhas-presencas/');
}

require_once '../../includes/header.php';
?>

<h2>Registrar Presença</h2>

<div class="alert alert-info">
    <p>Para registrar sua presença, escaneie o QR Code disponível no local da reunião.</p>
    <p>Ou <a href="qrcode.php">clique aqui</a> para digitar o código manualmente.</p>
</div>

<?php require_once '../../includes/footer.php'; ?>
<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
verificaLogin();
if (!isAdmin()) redirect('../servidor/presenca/registrar.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $data_reuniao = $_POST['data_reuniao'] ?? '';
    $local_reuniao = $_POST['local_reuniao'] ?? '';

    $stmt = $conn->prepare("INSERT INTO reunioes (titulo, descricao, data_reuniao, local_reuniao, criado_por) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $titulo, $descricao, $data_reuniao, $local_reuniao, $_SESSION['usuario_id']);

    if ($stmt->execute()) {
        $reuniao_id = $stmt->insert_id; // Esta linha deve vir ANTES de usar $reuniao_id

        // 1. Definir o caminho completo do arquivo QR Code
        $qrcode_filename = 'qrcode_' . $reuniao_id . '.png';
        $qrcode_file_path = QRCODE_DIR . $qrcode_filename;

        // 2. Criar a URL completa para registro de presença
        $url_presenca = BASE_URL . 'registrar_presenca.php?id=' . $reuniao_id;

        // 3. Verificar/Criar o diretório se não existir
        if (!file_exists(QRCODE_DIR)) {
            mkdir(QRCODE_DIR, 0777, true);
        }

        // 4. Gerar o QR Code com tratamento de erros
        try {
            QRcode::png(
                $url_presenca,
                $qrcode_file_path,
                QR_ECLEVEL_L,  // Nível de correção de erro
                10,            // Tamanho de cada módulo QR
                2              // Margem em módulos
            );

            // 5. Atualizar a reunião com o nome do arquivo QR Code
            $update_stmt = $conn->prepare("UPDATE reunioes SET qrcode_file = ? WHERE id = ?");
            $update_stmt->bind_param("si", $qrcode_filename, $reuniao_id);
            $update_stmt->execute();

            $_SESSION['sucesso'] = "Reunião criada com sucesso! QR Code gerado.";
            redirect('admin/reunioes/visualizar.php?id=' . $reuniao_id);
        } catch (Exception $e) {
            error_log("Erro ao gerar QR Code: " . $e->getMessage());
            $_SESSION['erro'] = "Reunião criada, mas houve um problema ao gerar o QR Code.";
            redirect('admin/reunioes/visualizar.php?id=' . $reuniao_id);
        }
    } else {
        $_SESSION['erro'] = "Erro ao criar reunião: " . $conn->error;
    }
}

require_once '../../includes/header.php';
?>

<h2>Criar Nova Reunião</h2>

<?php if (isset($_SESSION['erro'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['erro'];
                                    unset($_SESSION['erro']); ?></div>
<?php endif; ?>

<form method="POST">
    <div class="form-group">
        <label>Título:</label>
        <input type="text" name="titulo" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Descrição:</label>
        <textarea name="descricao" class="form-control" rows="3"></textarea>
    </div>

    <div class="form-group">
        <label>Data e Hora:</label>
        <input type="datetime-local" name="data_reuniao" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Local:</label>
        <input type="text" name="local_reuniao" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">Criar Reunião</button>
</form>

<form action="../../index.php" method="get">
    <button type="submit" class="btn btn-secondary mb-3">Voltar</button>
</form>


<?php require_once '../../includes/footer.php'; ?>
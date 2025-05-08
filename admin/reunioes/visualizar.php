<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
verificaLogin();
if (!isAdmin()) redirect('../servidor/presenca/registrar.php');

if (!isset($_GET['id'])) {
    $_SESSION['erro'] = "Reunião não especificada.";
    redirect('listar.php');
}

$reuniao_id = $_GET['id'];
$reuniao = $conn->query("
    SELECT r.*, u.nome as criador 
    FROM reunioes r
    JOIN usuarios u ON r.criado_por = u.id
    WHERE r.id = $reuniao_id
")->fetch_assoc();

if (!$reuniao) {
    $_SESSION['erro'] = "Reunião não encontrada.";
    redirect('listar.php');
}

// Contar presenças
$total_presencas = $conn->query("
    SELECT COUNT(*) as total 
    FROM presencas 
    WHERE reuniao_id = $reuniao_id
")->fetch_assoc()['total'];

// URL completa para registro de presença
$url_presenca = BASE_URL . 'registrar_presenca.php?id=' . $reuniao_id;

require_once '../../includes/header.php';
?>

<h2><?= $reuniao['titulo'] ?></h2>

<div style="display: flex; gap: 20px;">
    <div style="flex: 2;">
        <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($reuniao['data_reuniao'])) ?></p>
        <p><strong>Local:</strong> <?= $reuniao['local_reuniao'] ?></p>
        <p><strong>Criador:</strong> <?= $reuniao['criador'] ?></p>
        <p><strong>Total de Presenças:</strong> <?= $total_presencas ?></p>

        <h3>Descrição:</h3>
        <p><?= nl2br($reuniao['descricao']) ?></p>

        <a href="editar.php?id=<?= $reuniao['id'] ?>" class="btn">Editar</a>
    </div>

    <div style="text-align: center;">
        <h3>QR Code para Presença</h3>
        <?php if (file_exists('../../assets/qrcodes/qrcode_' . $reuniao['id'] . '.png')): ?>
            <img src="<?= BASE_URL ?>assets/qrcodes/qrcode_<?= $reuniao['id'] ?>.png" alt="QR Code" width="300px">
        <?php else: ?>
            <div style="border: 1px dashed #ccc; padding: 20px; margin: 10px 0;">
                <p>QR Code não gerado</p>
            </div>
        <?php endif; ?>
        
        <p>ID da Reunião: <strong><?= $reuniao['id'] ?></strong></p>
        <p>Link direto: 
            <small>
                <a href="<?= $url_presenca ?>" target="_blank">
                    <?= $url_presenca ?>
                </a>
            </small>
        </p>
        <p style="color: #666; font-size: 0.8em;">
            Acesse este link de outro dispositivo na mesma rede
        </p>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
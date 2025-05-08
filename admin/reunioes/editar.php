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
$reuniao = $conn->query("SELECT * FROM reunioes WHERE id = $reuniao_id")->fetch_assoc();

if (!$reuniao) {
    $_SESSION['erro'] = "Reunião não encontrada.";
    redirect('listar.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $data_reuniao = $_POST['data_reuniao'] ?? '';
    $local_reuniao = $_POST['local_reuniao'] ?? '';
    
    $stmt = $conn->prepare("UPDATE reunioes SET titulo = ?, descricao = ?, data_reuniao = ?, local_reuniao = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $titulo, $descricao, $data_reuniao, $local_reuniao, $reuniao_id);
    
    if ($stmt->execute()) {
        // Log
        $conn->query("INSERT INTO logs (usuario_id, acao, modulo) VALUES ({$_SESSION['usuario_id']}, 'editar_reuniao', 'admin/reunioes')");
        
        $_SESSION['sucesso'] = "Reunião atualizada com sucesso!";
        redirect('admin/reunioes/visualizar.php?id=' . $reuniao_id);
    } else {
        $_SESSION['erro'] = "Erro ao atualizar reunião: " . $conn->error;
    }
}

require_once '../../includes/header.php';
?>

<h2>Editar Reunião</h2>

<?php if (isset($_SESSION['erro'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['erro']; unset($_SESSION['erro']); ?></div>
<?php endif; ?>

<form method="POST">
    <div class="form-group">
        <label>Título:</label>
        <input type="text" name="titulo" class="form-control" value="<?= $reuniao['titulo'] ?>" required>
    </div>
    
    <div class="form-group">
        <label>Descrição:</label>
        <textarea name="descricao" class="form-control" rows="3"><?= $reuniao['descricao'] ?></textarea>
    </div>
    
    <div class="form-group">
        <label>Data e Hora:</label>
        <input type="datetime-local" name="data_reuniao" class="form-control" 
               value="<?= date('Y-m-d\TH:i', strtotime($reuniao['data_reuniao'])) ?>" required>
    </div>
    
    <div class="form-group">
        <label>Local:</label>
        <input type="text" name="local_reuniao" class="form-control" value="<?= $reuniao['local_reuniao'] ?>">
    </div>
    
    <button type="submit" class="btn">Atualizar</button>
    <a href="visualizar.php?id=<?= $reuniao['id'] ?>" class="btn">Cancelar</a>
</form>

<?php require_once '../../includes/footer.php'; ?>
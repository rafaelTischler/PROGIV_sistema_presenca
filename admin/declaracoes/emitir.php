<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/funcoes.php';
verificaLogin();

if (!isAdmin()) redirect('../servidor/presenca/registrar.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_POST['usuario_id'] ?? '';
    $data_inicio = $_POST['data_inicio'] ?? '';
    $data_fim = $_POST['data_fim'] ?? '';

    $stmt = $conn->prepare("SELECT nome, matricula_siape, cargo FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $servidor = $stmt->get_result()->fetch_assoc();
    $stmt = $conn->prepare("SELECT r.titulo, r.data_reuniao 
                           FROM reunioes r
                           JOIN presencas p ON r.id = p.reuniao_id
                           WHERE p.usuario_id = ? 
                           AND r.data_reuniao BETWEEN ? AND ? 
                           ORDER BY r.data_reuniao");
    $stmt->bind_param("iss", $usuario_id, $data_inicio, $data_fim);
    $stmt->execute();
    $reunioes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    if (count($reunioes) > 0) {
        $dados = [
            'servidor_nome' => $servidor['nome'],
            'servidor_matricula' => $servidor['matricula_siape'],
            'servidor_cargo' => $servidor['cargo'],
            'data_inicio' => formatarData($data_inicio, 'd/m/Y'),
            'data_fim' => formatarData($data_fim, 'd/m/Y'),
            'reunioes' => $reunioes
        ];
        $stmt = $conn->prepare("INSERT INTO declaracoes (usuario_id, emitido_por, conteudo, data_inicio, data_fim) VALUES (?, ?, ?, ?, ?)");
        $conteudo = "Declaração de participação em " . count($reunioes) . " reuniões";
        $stmt->bind_param("iisss", $usuario_id, $_SESSION['usuario_id'], $conteudo, $data_inicio, $data_fim);
        $stmt->execute();

        registrarLog('emitir_declaracao', 'admin/declaracoes', [
            'usuario_id' => $usuario_id,
            'data_inicio' => $data_inicio,
            'data_fim' => $data_fim
        ]);

        echo gerarDeclaracaoHTML($dados);
        exit();
    } else {
        $_SESSION['erro'] = "Nenhuma reunião encontrada no período selecionado.";
    }
}

$servidores = $conn->query("SELECT id, nome, matricula_siape FROM usuarios ORDER BY nome");

require_once '../../includes/header.php';
?>

<h2>Emitir Declaração de Participação</h2>

<form method="POST">
    <div class="form-group">
        <label>Servidor:</label>
        <select name="usuario_id" class="form-control" required>
            <option value="">Selecione um servidor</option>
            <?php while ($servidor = $servidores->fetch_assoc()): ?>
                <option value="<?= $servidor['id'] ?>">
                    <?= $servidor['nome'] ?> (<?= $servidor['matricula_siape'] ?>)
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Período - Data Início:</label>
        <input type="date" name="data_inicio" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Período - Data Fim:</label>
        <input type="date" name="data_fim" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Emitir Declaração</button>
</form>

<form action="../../index.php" method="get">
    <button type="submit" class="btn btn-secondary mb-3">Voltar</button>
</form>

<?php require_once '../../includes/footer.php'; ?>
<?php
require_once 'config.php';

function formatarData($data, $formato = 'd/m/Y H:i')
{
    $date = new DateTime($data);
    return $date->format($formato);
}

function gerarQRCode($token, $reuniao_id)
{
    // Gera a URL completa com o IP do notebook
    $url_presenca = BASE_URL . 'registrar_presenca.php?id=' . $reuniao_id . '&token=' . $token;

    $filename = QRCODE_DIR . 'qrcode_' . $reuniao_id . '.png';

    // Gera o QR Code com a URL completa
    QRcode::png(
        $url_presenca,
        $filename,
        QR_ECLEVEL_L,  // Nível de correção de erro
        10,            // Tamanho (10px por módulo)
        0              // Margem
    );

    return basename($filename);
}

function gerarDeclaracaoHTML($dados)
{
    ob_start(); // Inicia o buffer de saída
?>
    <div style="max-width: 800px; margin: 0 auto; font-family: Arial, sans-serif;">
        <h2 style="text-align: center;">DECLARAÇÃO DE PARTICIPAÇÃO EM REUNIÕES</h2>
        <p>
            Declaramos para os devidos fins que <strong><?= htmlspecialchars($dados['servidor_nome']) ?></strong>,
            matrícula <strong><?= htmlspecialchars($dados['servidor_matricula']) ?></strong>,
            cargo <strong><?= htmlspecialchars($dados['servidor_cargo']) ?></strong>,
            participou das seguintes reuniões no período de
            <strong><?= htmlspecialchars($dados['data_inicio']) ?></strong> a
            <strong><?= htmlspecialchars($dados['data_fim']) ?></strong>:
        </p>

        <h4>Reuniões Participadas:</h4>
        <ul>
            <?php foreach ($dados['reunioes'] as $reuniao): ?>
                <li>
                    <?= htmlspecialchars($reuniao['titulo']) ?> - <?= formatarData($reuniao['data_reuniao']) ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <p style="text-align: right;">
            São Vicente do Sul, <?= date('d/m/Y') ?>
        </p>

        <div style="text-align: right; margin-top: 60px;">
            <p>___________________________________________</p>
            <p>Assinatura do Responsável</p>
        </div>
    </div>
    <form action="../../index.php" method="get">
        <button type="submit" class="btn btn-secondary mt-3">Voltar</button>
    </form>

<?php
    return ob_get_clean(); // Retorna o conteúdo do buffer como string HTML

}

// Função auxiliar para verificar se o servidor está acessível
function verificarAcessibilidade()
{
    $url = BASE_URL . 'check.php';
    $headers = @get_headers($url);
    return $headers && strpos($headers[0], '200');
}
?>
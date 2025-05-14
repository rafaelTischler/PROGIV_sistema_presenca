<?php
require_once 'config.php';

function formatarData($data, $formato = 'd/m/Y H:i')
{
    $date = new DateTime($data);
    return $date->format($formato);
}

function gerarQRCode($token, $reuniao_id)
{

    $url_presenca = BASE_URL . 'registrar_presenca.php?id=' . $reuniao_id . '&token=' . $token;

    $filename = QRCODE_DIR . 'qrcode_' . $reuniao_id . '.png';

    QRcode::png(
        $url_presenca,
        $filename,
        QR_ECLEVEL_L,  
        10,        
        0     
    );

    return basename($filename);
}

function gerarDeclaracaoHTML($dados)
{
    ob_start(); 
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
    return ob_get_clean(); 

}

function verificarAcessibilidade()
{
    $url = BASE_URL . 'check.php';
    $headers = @get_headers($url);
    return $headers && strpos($headers[0], '200');
}
?>
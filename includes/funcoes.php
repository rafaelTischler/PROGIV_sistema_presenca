<?php
require_once 'config.php';

function formatarData($data, $formato = 'd/m/Y H:i') {
    $date = new DateTime($data);
    return $date->format($formato);
}

function gerarQRCode($token, $reuniao_id) {
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

function gerarDeclaracaoPDF($dados) {
    require_once __DIR__ . '/../lib/fpdf/fpdf.php';
    
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    
    // Cabeçalho
    $pdf->Cell(0, 10, utf8_decode('DECLARAÇÃO DE PARTICIPAÇÃO EM REUNIÕES'), 0, 1, 'C');
    $pdf->Ln(10);
    
    // Corpo do texto
    $pdf->SetFont('Arial', '', 12);
    $texto = utf8_decode("Declaramos para os devidos fins que {$dados['servidor_nome']}, matrícula {$dados['servidor_matricula']}, cargo {$dados['servidor_cargo']}, participou das seguintes reuniões no período de {$dados['data_inicio']} a {$dados['data_fim']}:");
    $pdf->MultiCell(0, 10, $texto);
    $pdf->Ln(10);
    
    // Lista de reuniões
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, utf8_decode('Reuniões Participadas:'), 0, 1);
    $pdf->SetFont('Arial', '', 12);
    
    foreach ($dados['reunioes'] as $reuniao) {
        $pdf->Cell(0, 10, utf8_decode("- {$reuniao['titulo']} em " . formatarData($reuniao['data_reuniao'])), 0, 1);
    }
    
    // Rodapé
    $pdf->Ln(15);
    $pdf->Cell(0, 10, utf8_decode("São Vicente do Sul, " . date('d/m/Y')), 0, 1, 'R');
    $pdf->Ln(20);
    $pdf->Cell(0, 10, "___________________________________________", 0, 1, 'R');
    $pdf->Cell(0, 10, utf8_decode("Assinatura do Responsável"), 0, 1, 'R');
    
    return $pdf->Output('S', 'declaracao.pdf');
}

// Função auxiliar para verificar se o servidor está acessível
function verificarAcessibilidade() {
    $url = BASE_URL . 'check.php';
    $headers = @get_headers($url);
    return $headers && strpos($headers[0], '200');
}
?>
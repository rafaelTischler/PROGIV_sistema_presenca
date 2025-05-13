<?php

require('../fpdf/fpdf.php');

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(40,10,'Listagem de emprestimos');
$cont =60;

    $pdf->ln();
    $texto="teste de pdf";
    $pdf->Cell(40,10,$texto);

$pdf->Output();
?>
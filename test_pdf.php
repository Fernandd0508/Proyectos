<?php
require_once __DIR__ . '/tcpdf/tcpdf.php';

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, '¡TCPDF funciona correctamente!', 0, 1, 'C');
$pdf->Output('test.pdf', 'I');

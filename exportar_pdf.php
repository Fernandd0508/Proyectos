<?php
session_start();
include 'db.php';
require_once('tcpdf/tcpdf.php');

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Obtener registros
$res = $conexion->query("SELECT * FROM registros ORDER BY fecha_ingreso DESC");

// Crear documento
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetTitle('Listado de Reparaciones');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);

// Título
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Listado de Reparaciones con Fotos y Detalles', 0, 1, 'C');
$pdf->Ln(3);

// Encabezado
$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(10, 8, 'ID', 1, 0, 'C', 1);
$pdf->Cell(35, 8, 'Cliente', 1, 0, 'C', 1);
$pdf->Cell(30, 8, 'Equipo', 1, 0, 'C', 1);
$pdf->Cell(25, 8, 'Costo (S/)', 1, 0, 'C', 1);
$pdf->Cell(25, 8, 'Tiempo', 1, 0, 'C', 1);
$pdf->Cell(30, 8, 'Fecha', 1, 0, 'C', 1);
$pdf->Cell(35, 8, 'Foto Entrada', 1, 0, 'C', 1);
$pdf->Cell(35, 8, 'Foto Salida', 1, 1, 'C', 1);

$pdf->SetFont('helvetica', '', 9);
$imgWidth = 33;
$imgHeight = 25;

while ($r = $res->fetch_assoc()) {
    // Primera fila: datos + imágenes
    $pdf->Cell(10, 30, $r['id'], 1, 0, 'C');
    $pdf->Cell(35, 30, $r['nombre_cliente'], 1, 0);
    $pdf->Cell(30, 30, $r['tipo_equipo'], 1, 0);
    $pdf->Cell(25, 30, number_format($r['costo_estimado'], 2), 1, 0, 'C');
    $pdf->Cell(25, 30, $r['tiempo_estimado'], 1, 0, 'C');
    $pdf->Cell(30, 30, $r['fecha_ingreso'], 1, 0, 'C');

    // Foto entrada
    $fotoEntrada = $r['foto_entrada'];
    if (file_exists($fotoEntrada)) {
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->Cell(35, 30, '', 1, 0);
        $pdf->Image($fotoEntrada, $x + 1, $y + 2, $imgWidth, $imgHeight);
    } else {
        $pdf->Cell(35, 30, 'No disponible', 1, 0, 'C');
    }

    // Foto salida
    $fotoSalida = $r['foto_salida'];
    if ($fotoSalida && file_exists($fotoSalida)) {
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->Cell(35, 30, '', 1, 1);
        $pdf->Image($fotoSalida, $x + 1, $y + 2, $imgWidth, $imgHeight);
    } else {
        $pdf->Cell(35, 30, 'Pendiente', 1, 1, 'C');
    }

    // Segunda fila: Descripción y Observaciones
    $descripcion = $r['descripcion'];
    $observaciones = $r['observaciones_finales'] ?: 'N/A';

    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(25, 8, 'Problema:', 0, 0);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->MultiCell(240, 8, $descripcion, 0, 1);

    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(25, 8, 'Observaciones:', 0, 0);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->MultiCell(240, 8, $observaciones, 0, 1);

    $pdf->Ln(5); // espacio entre registros
}

$pdf->Output('reparaciones_con_detalles.pdf', 'D');

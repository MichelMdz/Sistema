<?php
require_once '../../conexion.php';
require_once 'fpdf/fpdf.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

$pdf = new FPDF('P', 'mm', array(80, 100));
$pdf->AddPage();
$pdf->SetMargins(5, 0, 0);
$pdf->SetTitle("Ventas");
$pdf->SetFont('Arial', 'B', 10);
$id = $_GET['v'];
$idcliente = $_GET['cl'];
$config = mysqli_query($conexion, "SELECT * FROM configuracion");
$datos = mysqli_fetch_assoc($config);
$clientes = mysqli_query($conexion, "SELECT * FROM cliente WHERE idcliente = $idcliente");
$datosC = mysqli_fetch_assoc($clientes);
$ventas = mysqli_query($conexion, "SELECT d.*, p.codproducto, p.descripcion FROM detalle_venta d INNER JOIN producto p ON d.id_producto = p.codproducto WHERE d.id_venta = $id");

$nombre_usuario = isset($_GET['user']) ? $_GET['user'] : '';

$pdf->Cell(60, 1, utf8_decode($datos['nombre']), 0, 1, 'C');
$pdf->image("../../assets/img/logomedi.jpg", 52, 2, 18, 18, 'JPG');
$pdf->SetFont('Arial', 'B', 3);
$pdf->Ln(2);
$pdf->Cell(8, 2, utf8_decode("Teléfono: "), 0, 0, 'L');
$pdf->SetFont('Arial', '', 4);
$pdf->Cell(8, 2, $datos['telefono'], 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 3);
$pdf->Cell(8, 2, utf8_decode("Dirección: "), 0, 0, 'L');
$pdf->SetFont('Arial', '', 3);
$pdf->Cell(8, 2, utf8_decode($datos['direccion']), 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 3);
$pdf->Cell(8, 2, "Correo: ", 0, 0, 'L');
$pdf->SetFont('Arial', '', 3);
$pdf->Cell(8, 2, utf8_decode($datos['email']), 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 3);
$pdf->Cell(8, 2, "Atendio: ", 0, 0, 'L');
$pdf->SetFont('Arial', '', 3);
session_start();
$id_user = $_SESSION['idUser'];
$user_query = "SELECT nombre FROM usuario WHERE idusuario = '$id_user'";
$user_result = mysqli_query($conexion, $user_query);
$user_data = mysqli_fetch_assoc($user_result);
$nombre_usuario_atendio = $user_data['nombre'];
$pdf->Cell(8, 2, utf8_decode($nombre_usuario_atendio), 0, 1, 'L');
$pdf->Ln();

$generator = new BarcodeGeneratorPNG();
$barcode = $generator->getBarcode($id, $generator::TYPE_CODE_128);
file_put_contents('barcode.png', $barcode);
$pdf->Image('barcode.png', 9, 6, 10, 4, 'PNG');
$pdf->SetFont('Arial', 'B', 3);
$pdf->SetXY(9, 11);
$pdf->Cell(10, 0, "Venta #".$id, 0, 1, 'C');

$pdf->Ln(11);
$pdf->SetFont('Arial', 'B', 3);
$pdf->SetFillColor(0, 0, 0);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(70, 2, "Datos del cliente", 1, 1, 'C', 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(21, 2, utf8_decode('Nombre'), 0, 0, 'L');
$pdf->Cell(11, 2, utf8_decode('Teléfono'), 0, 0, 'L');
$pdf->Cell(20, 2, utf8_decode('Dirección'), 0, 1, 'L');
$pdf->SetFont('Arial','', 3);
$pdf->Cell(21, 2, utf8_decode($datosC['nombre']), 0, 0, 'L');
$pdf->Cell(11, 2, utf8_decode($datosC['telefono']), 0, 0, 'L');
$pdf->Cell(20, 2, utf8_decode($datosC['direccion']), 0, 1, 'L');

$pdf->Ln(1);
$pdf->SetFont('Arial', 'B', 3);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(70, 2, "Detalle de Producto", 1, 1, 'C', 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(30, 2, utf8_decode('Descripción'), 0, 0, 'L');
$pdf->Cell(10, 2, 'Cant.', 0, 0, 'L');
$pdf->Cell(15, 2, 'Precio', 0, 0, 'L');
$pdf->Cell(15, 2, 'Sub Total.', 0, 1, 'L');
$pdf->SetFont('Arial', '', 3);
$total = 0.00;
$desc = 0.00;
while ($row = mysqli_fetch_assoc($ventas)) {
    $pdf->Cell(30, 2, $row['descripcion'], 0, 0, 'L');
    $pdf->Cell(10, 2, $row['cantidad'], 0, 0, 'L');
    $pdf->Cell(15, 2, $row['precio'], 0, 0, 'L');
    $sub_total = $row['total'];
    $total = $total + $sub_total;
    $desc = $desc + $row['descuento'];
    $pdf->Cell(15, 2, number_format($sub_total, 2, '.', ','), 0, 1, 'L');
}

$pdf->Ln();
$pdf->SetFont('Arial', 'B', 5);
$pdf->Cell(71, 1, 'Total a Pagar', 0, 1, 'R');
$pdf->SetFont('Arial', '', 6);
$pdf->Cell(71, 4, number_format($total, 2, '.', ','), 0, 1, 'R');


$pdf->Output("ventas.pdf", "I");
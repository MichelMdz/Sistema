<?php
require '../../vendor/autoload.php';
require_once '../../conexion.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$id_cliente = $_GET['cl'];
$id_venta = $_GET['v'];
$query = mysqli_query($conexion, "SELECT v.*, c.nombre as cliente_nombre, c.direccion, u.usuario as usuario_nombre FROM ventas v INNER JOIN cliente c ON v.id_cliente = c.idcliente INNER JOIN usuario u ON v.id_usuario = u.idusuario WHERE v.id = $id_venta");

$venta = mysqli_fetch_assoc($query);

$query_detalles = mysqli_query($conexion, "SELECT d.*, p.descripcion FROM detalle_venta d INNER JOIN producto p ON d.id_producto = p.codproducto WHERE d.id_venta = $id_venta");

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'Cliente');
$sheet->setCellValue('B1', $venta['cliente_nombre']);
$sheet->setCellValue('A2', 'Dirección');
$sheet->setCellValue('B2', $venta['direccion']);
$sheet->setCellValue('A3', 'Usuario');
$sheet->setCellValue('B3', $venta['usuario_nombre']);

$sheet->setCellValue('A5', 'Producto');
$sheet->setCellValue('B5', 'Cantidad');
$sheet->setCellValue('C5', 'Precio');
$sheet->setCellValue('D5', 'Total');

$row_number = 6;
while ($detalle = mysqli_fetch_assoc($query_detalles)) {
    $sheet->setCellValue('A' . $row_number, $detalle['descripcion']);
    $sheet->setCellValue('B' . $row_number, $detalle['cantidad']);
    $sheet->setCellValue('C' . $row_number, $detalle['precio']);
    $sheet->setCellValue('D' . $row_number, $detalle['total']);
    $row_number++;
}

$writer = new Xlsx($spreadsheet);
$filename = 'venta_' . $id_venta . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
$writer->save('php://output');
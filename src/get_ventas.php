<?php
require_once "../conexion.php";

if (isset($_POST['fecha']) && isset($_POST['id_user'])) {
    $fecha = $_POST['fecha'];
    $id_user = $_POST['id_user'];

    $ventas_query = "SELECT SUM(total) AS total_ventas FROM ventas WHERE id_usuario = '$id_user' AND DATE(fecha) = '$fecha'";
    $ventas_result = mysqli_query($conexion, $ventas_query);
    $ventas_data = mysqli_fetch_assoc($ventas_result);
    $total_ventas = $ventas_data['total_ventas'];

    echo $total_ventas ? $total_ventas : 0;
}
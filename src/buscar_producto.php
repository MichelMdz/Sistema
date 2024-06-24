<?php
require("../conexion.php");
$codigo = $_GET['codigo'];
$sql = mysqli_query($conexion, "SELECT * FROM productos WHERE codigo = '$codigo'");
if ($producto = mysqli_fetch_assoc($sql)) {
    echo json_encode(['success' => true, 'id' => $producto['id'], 'descripcion' => $producto['descripcion'], 'precio' => $producto['precio']]);
} else {
    echo json_encode(['success' => false]);
}
<?php
session_start();
require_once "../conexion.php";
$id_user = $_SESSION['idUser'];
$user_query = "SELECT nombre FROM usuario WHERE idusuario = '$id_user'";
$user_result = mysqli_query($conexion, $user_query);
$user_data = mysqli_fetch_assoc($user_result);
$nombre_usuario = $user_data['nombre'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : '';
    $caja_registradora = isset($_POST['caja_registradora']) ? $_POST['caja_registradora'] : '';
    $dinero_en_caja = isset($_POST['dinero_en_caja']) ? $_POST['dinero_en_caja'] : '';
    $contado = isset($_POST['contado']) ? $_POST['contado'] : '';
    if (!empty($dinero_en_caja) && !empty($contado)) {
        $diferencia = $dinero_en_caja - $contado;
        $retirado = $contado;
    } else {
        $diferencia = 0;
        $retirado = 0;
    }
    $query = "INSERT INTO corte_caja (fecha, caja_registradora, dinero_en_caja, contado, diferencia, retirado, id_usuario) VALUES ('$fecha', '$caja_registradora', '$dinero_en_caja', '$contado', '$diferencia', '$retirado', '$id_user')";
    mysqli_query($conexion, $query);
    header('Location: corte_caja.php?success=1');
    exit(); // Asegúrate de que el script se detenga aquí.
}
include_once "includes/header.php";
?>

<div class="card">
    <div class="card-header bg-primary text-white text-center" style="font-size: 20px;">
        Corte de Caja - Usuario: <?php echo $nombre_usuario; ?>
    </div>
    <div class="card-body">
        <form method="POST" action="corte_caja.php">
            <div class="row">
                <!-- Primera Columna -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="fecha">Fecha</label>
                        <input type="date" name="fecha" id="fecha" class="form-control" required onchange="updateDineroEnCaja()">
                    </div>
                    <div class="form-group">
                        <label for="caja_registradora">Caja Registradora</label>
                        <select name="caja_registradora" id="caja_registradora" class="form-control" required>
                            <option value="">Selecciona una caja</option>
                            <option value="Caja 1">Caja 1</option>
                            <option value="Caja 2">Caja 2</option>
                            <option value="Caja 3">Caja 3</option>
                            <option value="Caja 4">Caja 4</option>
                            <option value="Caja 5">Caja 5</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="dinero_en_caja">Dinero en Caja</label>
                        <input type="number" step="0.01" name="dinero_en_caja" id="dinero_en_caja" class="form-control" required readonly>
                    </div>
                </div>
                
                <!-- Segunda Columna -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="contado">Contado</label>
                        <input type="number" step="0.01" name="contado" id="contado" class="form-control" required onchange="updateDiferencia()">
                    </div>
                    <div class="form-group">
                        <label for="diferencia">Diferencia</label>
                        <input type="number" step="0.01" name="diferencia" id="diferencia" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="retirado">Retirado</label>
                        <input type="number" step="0.01" name="retirado" id="retirado" class="form-control" readonly>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center mt-3">
                    <button type="submit" class="btn btn-success">Registrar corte</button>
                </div>  
            </div>
        </form>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-primary text-white text-center" style="font-size: 20px;">Registros de Corte de Caja</div>
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-striped table-bordered" id="tbl">
            <thead class="thead-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Caja Registradora</th>
                    <th>Dinero en Caja</th>
                    <th>Contado</th>
                    <th>Cambio</th>
                    <th>Retirado</th>
                    <th>Nombre de Usuario</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $registros_query = "SELECT c.*, u.nombre AS nombre_usuario FROM corte_caja c INNER JOIN usuario u ON c.id_usuario = u.idusuario ORDER BY c.fecha DESC";
                $registros_result = mysqli_query($conexion, $registros_query);
                while ($registro = mysqli_fetch_assoc($registros_result)) {
                ?>
                    <tr>
                        <td><?php echo $registro['fecha']; ?></td>
                        <td><?php echo isset($registro['caja_registradora']) ? $registro['caja_registradora'] : ''; ?></td>
                        <td><?php echo isset($registro['dinero_en_caja']) ? $registro['dinero_en_caja'] : ''; ?></td>
                        <td><?php echo $registro['contado']; ?></td>
                        <td><?php echo $registro['diferencia']; ?></td>
                        <td><?php echo $registro['retirado']; ?></td>
                        <td><?php echo $registro['nombre_usuario']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function updateDineroEnCaja() {
    var fecha = document.getElementById('fecha').value;
    var id_user = <?php echo $id_user; ?>;
    if (fecha) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'get_ventas.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                document.getElementById('dinero_en_caja').value = xhr.responseText;
            }
        };
        xhr.send('fecha=' + fecha + '&id_user=' + id_user);
    }
}

function updateDiferencia() {
    var dinero_en_caja = parseFloat(document.getElementById('dinero_en_caja').value);
    var contado = parseFloat(document.getElementById('contado').value);
    var diferencia = dinero_en_caja - contado;
    var retirado = contado;
    document.getElementById('diferencia').value = diferencia.toFixed(2);
    document.getElementById('retirado').value = retirado.toFixed(2);
}

document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success')) {
        Swal.fire({
            position: "top-end",
            icon: "success",
            title: "Registrado exitosamente",
            showConfirmButton: false,
            timer: 2000
        });
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});
</script>

<?php include_once "includes/footer.php"; ?>

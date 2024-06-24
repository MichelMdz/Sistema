<?php
session_start();
require_once "../conexion.php";
$id = $_GET['id'];
$sqlpermisos = mysqli_query($conexion, "SELECT * FROM permisos");
$usuarios = mysqli_query($conexion, "SELECT * FROM usuario WHERE idusuario = $id");
$resultUsuario = mysqli_num_rows($usuarios);
if (empty($resultUsuario)) {
    header("Location: usuarios.php");
    exit();
}
$consulta = mysqli_query($conexion, "SELECT * FROM detalle_permisos WHERE id_usuario = $id");
$datos = array();
foreach ($consulta as $asignado) {
    $datos[$asignado['id_permiso']] = true;
}
if (isset($_POST['permisos'])) {
    $id_user = $_GET['id'];
    $permisos = $_POST['permisos'];
    mysqli_query($conexion, "DELETE FROM detalle_permisos WHERE id_usuario = $id_user");
    if (!empty($permisos)) {
        foreach ($permisos as $permiso) {
            mysqli_query($conexion, "INSERT INTO detalle_permisos(id_usuario, id_permiso) VALUES ($id_user, $permiso)");
        }
    }
    $_SESSION['alert'] = '<div id="alerta" class="alert alert-success alert-dismissible fade show text-center" role="alert">
                <strong>Permisos Asignados</strong>
            </div>';
    $_SESSION['redirect'] = 'usuarios.php';
    header("Location: rol.php?id=$id");
    exit();
}
$alert = '';
if (isset($_SESSION['alert'])) {
    $alert = $_SESSION['alert'];
    unset($_SESSION['alert']); 
}
include_once "includes/header.php";?>

<div class="card">
            <div class="card-header bg-primary text-white text-center" style="font-size: 20px;"> Permisos</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card-body">
                        <form method="post" action="">
                    <?php echo $alert; ?>
                    <?php while ($row = mysqli_fetch_assoc($sqlpermisos)) { ?>
                        <div class="form-check form-check-inline m-4">
                            <label for="permiso_<?php echo $row['id']; ?>" class="p-2 text-uppercase"><?php echo $row['nombre']; ?></label>
                            <input id="permiso_<?php echo $row['id']; ?>" type="checkbox" name="permisos[]" value="<?php echo $row['id']; ?>" <?php if (isset($datos[$row['id']])) { echo "checked"; } ?>>
                        </div>
                    <?php } ?>
                    <br>
                    <div class="d-flex justify-content-center mt-2">
                        <button class="btn btn-success" type="submit">Modificar</button>
                        <button class="btn btn-success " type="button" onclick="window.location.href='usuarios.php'">Regresar</button>
                    </div>
                </form>
                        </div>
                    </div>
                </div>
            </div>
</div>
<?php include_once "includes/footer.php"; ?>
<script>
    setTimeout(function() {
        $('#alerta').fadeOut('slow', function() {
            <?php
            if (isset($_SESSION['redirect'])) {
                echo 'window.location.href = "' . $_SESSION['redirect'] . '";';
                unset($_SESSION['redirect']);
            }
            ?>
        });
    }, 3000);
</script>

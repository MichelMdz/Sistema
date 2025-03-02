<?php
session_start();
require_once "../conexion.php";
$id_user = $_SESSION['idUser'];
$permiso = "configuracion";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user!= 1) {
    header('Location: permisos.php');
}

if ($_POST) {
    $alert = '';
    if (empty(trim($_POST['nombre'])) || empty(trim($_POST['telefono'])) || empty(trim($_POST['email'])) || empty(trim($_POST['direccion']))) {
        $alert = '<div class="alert alert-error alert-dismissible fade show" role="alert">
                        Todos los campos son requeridos
                    </div>';
    } else {
        $nombre = $_POST['nombre'];
        $telefono = $_POST['telefono'];
        $email = $_POST['email'];
        $direccion = $_POST['direccion'];
        $id = $_POST['id'];
        $update = mysqli_query($conexion, "UPDATE configuracion SET nombre = '$nombre', telefono = '$telefono', email = '$email', direccion = '$direccion' WHERE id = $id");
        if ($update) {
            $alert = '<div class="alert alert-success alert-dismissible fade show text-center" role="alert">
    Datos Actualizados
</div>';
        }
    }
    $query = mysqli_query($conexion, "SELECT * FROM configuracion");
    $data = mysqli_fetch_assoc($query);
} else {
    $query = mysqli_query($conexion, "SELECT * FROM configuracion");
    $data = mysqli_fetch_assoc($query);
}

include_once "includes/header.php";
?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
        <div class="card-header bg-primary text-white text-center" style="font-size: 20px;">Datos de la Empresa</div>
            <div class="card-body">
                <?php echo isset($alert) ? $alert : ''; ?>
                <form action="" method="post" class="p-3">
                    <div class="form-group">
                        <label>Nombre:</label>
                        <input type="hidden" name="id" value="<?php echo $data['id'] ?>">
                        <input type="text" name="nombre" class="form-control" value="<?php echo $data['nombre']; ?>" id="txtNombre" placeholder="Nombre de la Empresa" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Teléfono:</label>
                        <input type="number" name="telefono" class="form-control" value="<?php echo $data['telefono']; ?>" id="txtTelEmpresa" placeholder="Teléfono de la Empresa" required>
                    </div>
                    <div class="form-group">
                        <label>Correo Electrónico:</label>
                        <input type="email" name="email" class="form-control" value="<?php echo $data['email']; ?>" id="txtEmailEmpresa" placeholder="Correo de la Empresa" required>
                    </div>
                    <div class="form-group">
                        <label>Dirección:</label>
                        <input type="text" name="direccion" class="form-control" value="<?php echo $data['direccion']; ?>" id="txtDirEmpresa" placeholder="Dirreción de la Empresa" required>
                    </div>
                    <div style="text-align: center;">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Modificar Datos</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include_once "includes/footer.php"; ?>
<script>
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 3000);
</script>
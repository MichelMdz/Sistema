<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$permiso = "laboratorios";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header('Location: permisos.php');
}
if (!empty($_POST)) {
    $alert = "";
    if (empty($_POST['laboratorio']) || empty($_POST['direccion'])) {
        $alert = '<div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                        Todo los campos son obligatorios
                    </div>';
    } else {
        $id = $_POST['id'];
        $laboratorio = $_POST['laboratorio'];
        $direccion = $_POST['direccion'];
        $result = 0;
        if (empty($id)) {
            $query = mysqli_query($conexion, "SELECT * FROM laboratorios WHERE laboratorio = '$laboratorio'");
            $result = mysqli_fetch_array($query);
            if ($result > 0) {
                $alert = '<div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                        El Laboratorio ya existe
                    </div>';
            } else {
                $query_insert = mysqli_query($conexion, "INSERT INTO laboratorios(laboratorio, direccion) values ('$laboratorio', '$direccion')");
                if ($query_insert) {
                    $alert = '<div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                        Laboratorio registrado
                    </div>';
                } else {
                    $alert = '<div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                        Error al registrar
                    </div>';
                }
            }
        } else {
            $sql_update = mysqli_query($conexion, "UPDATE laboratorios SET laboratorio = '$laboratorio', direccion = '$direccion' WHERE id = $id");
            if ($sql_update) {
                $alert = '<div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                        Laboratorio Modificado
                    </div>';
            } else {
                $alert = '<div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                        Error al modificar
                    </div>';
            }
        }
    }
    mysqli_close($conexion);
}
include_once "includes/header.php";
?>
<div class="card">
    <div class="card-header bg-primary text-white text-center" style="font-size: 20px;"> Formulario de Laboratorios</div>
    <div class="card-body">
        <?php echo (isset($alert)) ? $alert : ''; ?>
        <form action="" method="post" autocomplete="off" id="formulario">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="laboratorio" class="text-dark font-weight-bold">laboratorio</label>
                        <input type="text" placeholder="Ingrese laboratorio" name="laboratorio" id="laboratorio" class="form-control">
                        <input type="hidden" name="id" id="id">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="direccion" class="text-dark font-weight-bold">Dirección</label>
                        <input type="text" placeholder="Ingrese Dirección" name="direccion" id="direccion" class="form-control">
                    </div>
                </div>
                <div class="col-md-4 mt-4">
                    <input type="submit" value="Registrar" class="btn btn-primary" id="btnAccion">
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-primary text-white text-center" style="font-size: 20px;">Laboratorios Registrados</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="tbl">
                <thead class="thead-dark">
                    <tr>
                        <th>#ID</th>
                        <th>laboratorio</th>
                        <th>Dirección</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include "../conexion.php";

                    $query = mysqli_query($conexion, "SELECT * FROM laboratorios");
                    $result = mysqli_num_rows($query);
                    if ($result > 0) {
                        while ($data = mysqli_fetch_assoc($query)) { ?>
                            <tr>
                                <td><?php echo $data['id']; ?></td>
                                <td><?php echo $data['laboratorio']; ?></td>
                                <td><?php echo $data['direccion']; ?></td>
                                <td style="width: 200px;">
                                    <a href="#" onclick="editarLab(<?php echo $data['id']; ?>)" class="btn btn-primary"><i class='fas fa-edit'></i></a>
                                    <form action="eliminar_lab.php?id=<?php echo $data['id']; ?>" method="post" class="confirmar d-inline">
                                        <button class="btn btn-danger" type="submit"><i class='fas fa-trash-alt'></i> </button>
                                    </form>
                                </td>
                            </tr>
                    <?php }
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>
<script>
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 3000);
</script>
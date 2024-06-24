<?php
session_start();
$permiso = 'usuarios';
$id_user = $_SESSION['idUser'];
include "../conexion.php";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header('Location: permisos.php');
}
if (!empty($_POST)) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['correo'];
    $user = $_POST['usuario'];
    $alert = "";
    if (empty($nombre) || empty($email) || empty($user)) {
        $alert = '<div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                    Todo los campos son obligatorios
                </div>';
    } else {
        if (empty($id)) {
            $clave = $_POST['clave'];
            if (empty($clave)) {
                $alert = '<div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                    La contraseña es requerida
                </div>';
            } else {
                $clave = md5($_POST['clave']);
                $query = mysqli_query($conexion, "SELECT * FROM usuario where correo = '$email'");
                $result = mysqli_fetch_array($query);
                if ($result > 0) {
                    $alert = '<div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                    El correo ya existe
                </div>';
                } else {
                    $query_insert = mysqli_query($conexion, "INSERT INTO usuario(nombre,correo,usuario,clave) values ('$nombre', '$email', '$user', '$clave')");
                    if ($query_insert) {
                        $alert = '<div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                    Usuario Registrado
                </div>';
                    } else {
                        $alert = '<div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                    Error al registrar
                </div>';
                    }
                }
            }
        } else {
            $sql_update = mysqli_query($conexion, "UPDATE usuario SET nombre = '$nombre', correo = '$email' , usuario = '$user' WHERE idusuario = $id");
            if ($sql_update) {
                $alert = '<div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                    Usuario Modificado
                    
                        
                    </button>
                </div>';
            } else {
                $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Error al modificar
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
            }
        }
    }
}
include "includes/header.php";
?>
<div class="card">
<div class="card-header bg-primary text-white text-center" style="font-size: 20px;">Formulario de nuevos Usuarios</div>
    <div class="card-body">
        <form action="" method="post" autocomplete="off" id="formulario">
            <?php echo isset($alert) ? $alert : ''; ?>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" placeholder="Ingrese Nombre" name="nombre" id="nombre">
                        <input type="hidden" id="id" name="id">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="correo">Correo</label>
                        <input type="email" class="form-control" placeholder="Ingrese Correo Electrónico" name="correo" id="correo">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="usuario">Usuario</label>
                        <input type="text" class="form-control" placeholder="Ingrese Usuario" name="usuario" id="usuario">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="clave">Contraseña</label>
                        <input type="password" class="form-control" placeholder="Ingrese Contraseña" name="clave" id="clave">
                    </div>
                </div>
            </div>
            <div style="text-align: center;">
            <input type="submit" value="Registrar" class="btn btn-primary" id="btnAccion">
            </div>
        </form>
    </div>
</div>
<div class="card mt-4">
    <div class="card-header bg-primary text-white text-center" style="font-size: 20px;">Usuarios Registrados</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="tbl">
                <thead class="thead-dark">
                    <tr>
                        <th>#ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Usuario</th>
                        <th style="text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include "../conexion.php";
                    $query = mysqli_query($conexion, "SELECT * FROM usuario");
                    $result = mysqli_num_rows($query);
                    if ($result > 0) {
                        while ($data = mysqli_fetch_assoc($query)) {
                    ?>
                            <tr>
                                <td><?php echo $data['idusuario']; ?></td>
                                <td><?php echo $data['nombre']; ?></td>
                                <td><?php echo $data['correo']; ?></td>
                                <td><?php echo $data['usuario']; ?></td>
                                <td>
                                    <a href="rol.php?id=<?php echo $data['idusuario']; ?>" class="btn btn-warning"><i class='fas fa-key'></i></a>
                                    <a href="#" onclick="editarUsuario(<?php echo $data['idusuario']; ?>)" class="btn btn-success"><i class='fas fa-edit'></i></a>
                                    <form action="eliminar_usuario.php?id=<?php echo $data['idusuario']; ?>" method="post" class="confirmar d-inline">
                                        <button class="btn btn-danger" type="submit"><i class='fas fa-trash-alt'></i> </button>
                                    </form>
                                </td>
                            </tr>
                    <?php
                        }
                    }
                    ?>
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
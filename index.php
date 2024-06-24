<?php
session_start();
if (!empty($_SESSION['active'])) {
    header('location: src/');
} else {
    if (!empty($_POST)) {
        $alert = '';
        if (empty($_POST['usuario']) || empty($_POST['clave'])) {
            $alert = '<div id="alert" class="alert alert-warning alert-dismissible fade show" role="alert">
                        Ingrese usuario y contraseña
                    </div>';
        } else {
            require_once "conexion.php";
            $user = mysqli_real_escape_string($conexion, $_POST['usuario']);
            $clave = md5(mysqli_real_escape_string($conexion, $_POST['clave']));
            $query = mysqli_query($conexion, "SELECT * FROM usuario WHERE usuario = '$user' AND clave = '$clave'");
            mysqli_close($conexion);
            $resultado = mysqli_num_rows($query);
            if ($resultado > 0) {
                $dato = mysqli_fetch_array($query);
                $_SESSION['active'] = true;
                $_SESSION['idUser'] = $dato['idusuario'];
                $_SESSION['nombre'] = $dato['nombre'];
                $_SESSION['user'] = $dato['usuario'];
                header('Location: src/');
            } else {
                $alert = '<div id="alert" class="alert alert-danger alert-dismissible fade show" role="alert">
                        Contraseña incorrecta
                    </div>';
                session_destroy();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="assets/css/material-dashboard.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" href="assets/img/favicon.ico" />
</head>
<body class="bg">
    <div class="col-md-4 mx-auto">
        <div class="card">
            <div class="card-header card-header-info text-center">
                <img class="img-thumbnail" src="assets/img/logomedi.jpg" width="200"/>
                <h4 class="card-title">Iniciar Sesión</h4>
            </div>
            <div class="card-body">
                <?php echo isset($alert) ? $alert : ''; ?>
                <form action="" method="post" class="p-3">
                    <div class="form-group">
                        <input type="text" class="form-control form-control-lg text-center" id="exampleInputEmail1" placeholder="Usuario" name="usuario">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control form-control-lg text-center" id="exampleInputPassword1" placeholder="Contraseña" name="clave">
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-block btn-info font-weight-medium auth-form-btn" type="submit">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/js/material-dashboard.js"></script>
    <script>
        setTimeout(function() {
            document.getElementById('alert').remove();
        }, 3000);
    </script>
</body>
</html>
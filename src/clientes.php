<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$permiso = "clientes";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty($existe) && $id_user != 1) {
    header('Location: permisos.php');
    exit;
}
if (!empty($_POST)) {
    $alert = "";
    if (empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['direccion'])) {
        $alert = '<div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                    Todos los campos son obligatorios.
                  </div>';
    } else {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        $pdf = $_FILES['pdf'];
        $pdf_actual = $_POST['pdf_actual'];
        $pdf_name = $pdf['name'];
        $pdf_tmp_name = $pdf['tmp_name'];
        $pdf_error = $pdf['error'];
        $pdf_new_name = $pdf_actual; 

        if ($pdf_error === 0) {
            $pdf_new_name = uniqid('', true) . "-" . $pdf_name;
            $pdf_destination = 'uploads/' . $pdf_new_name;
            move_uploaded_file($pdf_tmp_name, $pdf_destination);
        }
        if (empty($id)) {
            $query = mysqli_query($conexion, "SELECT * FROM cliente WHERE nombre = '$nombre'");
            $result = mysqli_fetch_array($query);
            if ($result > 0) {
                $alert = '<div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                            El cliente ya existe.
                          </div>';
            } else {
                $query_insert = mysqli_query($conexion, "INSERT INTO cliente(nombre, telefono, direccion, pdf) VALUES ('$nombre', '$telefono', '$direccion', '$pdf_new_name')");
                if ($query_insert) {
                    $alert = '<div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                                Cliente registrado.
                              </div>';
                } else {
                    $alert = '<div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                                Error al registrar.
                              </div>';
                }
            }
        } else {
            $sql_update = mysqli_query($conexion, "UPDATE cliente SET nombre = '$nombre', telefono = '$telefono', direccion = '$direccion', pdf = '$pdf_new_name' WHERE idcliente = $id");
            if ($sql_update) {
                $alert = '<div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                            Cliente modificado.
                          </div>';
            } else {
                $alert = '<div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                            Error al modificar.
                          </div>';
            }
        }
    }
}

include_once "includes/header.php";
?>

<div class="card">
    <div class="card-header bg-primary text-white text-center" style="font-size: 20px;">Formulario de Clientes</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <?php echo (isset($alert)) ? $alert : ''; ?>
                <form action="" method="post" enctype="multipart/form-data" autocomplete="off" id="formulario">
                    <div class="row">
                        <!-- Campos existentes -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nombre" class="text-dark font-weight-bold">Nombre</label>
                                <input type="text" placeholder="Ingrese Nombre" name="nombre" id="nombre" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="telefono" class="text-dark font-weight-bold">Teléfono</label>
                                <input type="number" placeholder="Ingrese Teléfono" name="telefono" id="telefono" class="form-control">
                                <input type="hidden" name="id" id="id">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="direccion" class="text-dark font-weight-bold">Dirección</label>
                                <input type="text" placeholder="Ingrese Dirección" name="direccion" id="direccion" class="form-control">
                            </div>
                        </div>
                        <!-- Campo para el archivo PDF -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="pdf" class="text-dark font-weight-bold">Archivo PDF</label>
                                <input type="file" name="pdf" id="pdf" class="form-control" onchange="mostrarNombreArchivo()">
                                <small id="pdfHelp" class="form-text text-muted">Seleccione un archivo PDF.</small>
                            </div>
                        </div>
                        <!-- Campo oculto para el archivo PDF actual -->
                        <input type="hidden" name="pdf_actual" id="pdf_actual">

                        <div class="col-md-12 text-center mt-3">
                            <input type="submit" value="Registrar" class="btn btn-primary" id="btnAccion">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-primary text-white text-center" style="font-size: 20px;">Clientes Registrados</div>
    <div class="table-responsive">
        <table class="table table-striped table-bordered" id="tbl">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>PDF</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = mysqli_query($conexion, "SELECT * FROM cliente");
                $result = mysqli_num_rows($query);
                if ($result > 0) {
                    while ($data = mysqli_fetch_assoc($query)) { ?>
                        <tr id="cliente_<?php echo $data['idcliente']; ?>">
                            <td><?php echo $data['idcliente']; ?></td>
                            <td><?php echo $data['nombre']; ?></td>
                            <td><?php echo $data['telefono']; ?></td>
                            <td><?php echo $data['direccion']; ?></td>
                            <td>
                                <?php if (!empty($data['pdf'])) { ?>
                                    <a href="uploads/<?php echo $data['pdf']; ?>" target="_blank">Ver PDF</a>
                                <?php } else { ?>
                                    No hay PDF
                                <?php } ?>
                            </td>
                            <td>
                                <a href="#" onclick="editarCliente(<?php echo $data['idcliente']; ?>, '<?php echo $data['nombre']; ?>', '<?php echo $data['telefono']; ?>', '<?php echo $data['direccion']; ?>', '<?php echo $data['pdf']; ?>')" class="btn btn-primary">
                                    <i class='fas fa-edit'></i>
                                </a>
                                <form action="eliminar_cliente.php?id=<?php echo $data['idcliente']; ?>" method="post" class="confirmar d-inline">
                                    <button class="btn btn-danger" type="submit">
                                        <i class='fas fa-trash-alt'></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                <?php }
                }
                mysqli_close($conexion); 
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>

<script>
    function mostrarNombreArchivo() {
        const inputFile = document.getElementById('pdf');
        const fileName = inputFile.files[0]?.name;
        const pdfHelp = document.getElementById('pdfHelp');
        if (fileName) {
            pdfHelp.textContent = fileName;
        } else {
            pdfHelp.textContent = 'Seleccione un archivo PDF.';
        }
    }
    function editarCliente(idCliente, nombre, telefono, direccion, pdf) {
        document.getElementById('id').value = idCliente;
        document.getElementById('nombre').value = nombre;
        document.getElementById('telefono').value = telefono;
        document.getElementById('direccion').value = direccion;
        document.getElementById('pdf_actual').value = pdf;
        document.getElementById('pdfHelp').textContent = pdf ? pdf : 'Seleccione un archivo PDF.';
        document.getElementById('btnAccion').value = 'Actualizar';
    }
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 3000);
</script>
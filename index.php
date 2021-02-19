<?php

if (file_exists("archivo.txt")) {
    $aClientes = json_decode(file_get_contents("archivo.txt"), true);
} else {
    $aClientes = array();
}

if ($_POST) {

    $dni = $_POST["txtDNI"];
    $nombre = $_POST["txtNombre"];
    $telefono = $_POST["txtTelefono"];
    $correo = $_POST["txtCorreo"];
    $nombreImagen = "";

    if ($_FILES["archivo"]["error"] === UPLOAD_ERR_OK) {
        $nombreAleatorio = date("Ymdhmsi");
        $archivoTemp = $_FILES["archivo"]["tmp_name"];
        $nombreArchivo = $_FILES["archivo"]["name"];
        $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $nombreImagen = $nombreAleatorio . "." . $extension;
        move_uploaded_file($archivoTemp, "files/$nombreImagen");
    }

    if (isset($_GET["id"]) && ($_GET["id"] >= 0)) {
        if (isset($_POST["btnGuardar"])) {
            $nombreImagenAnt = $aClientes[$_GET["id"]]["imagen"];
            if (($nombreImagen != "") && ($nombreImagenAnt != $nombreImagen)) {
                unlink("files/" . $nombreImagenAnt);
            } else {
                $nombreImagen = $aClientes[$_GET["id"]]["imagen"];
            }

            $aClientes[$_GET["id"]] = array(
                "dni" => $dni,
                "nombre" => $nombre,
                "telefono" => $telefono,
                "correo" => $correo,
                "imagen" => $nombreImagen
            );
        } else if (isset($_POST["btnEliminar"])) {
            if (isset($_GET["do"]) && isset($_GET["do"]) == "eliminar") {
                if (file_exists("files/" . $aClientes[$_GET["id"]]["imagen"])) {
                    unlink("files/" . $aClientes[$_GET["id"]]["imagen"]);
                }
                print_r($_GET["id"]);
                unset($aClientes[$_GET["id"]]);
            }
        }
    } else {
        //Cliente nuevo
        $aClientes[] = array(
            "dni" => $dni,
            "nombre" => $nombre,
            "telefono" => $telefono,
            "correo" => $correo,
            "imagen" => $nombreImagen
        );
    }

    file_put_contents("archivo.txt", json_encode($aClientes));
    header("Location: index.php");
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABM de clientes</title>
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="css/fontawesome/css/fontawesome.min.css">
</head>

<body>
    <header class= "titulo">
        <div class="container titulo">
            <div class="col-12">
                <h1 class="center">Registro de clientes</h1>
            </div>
        </div>
    </header>
    <main class="container mt-4">
        <div class="row">
            <div class="col-sm-5 col-12">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="txtDNI" class="d-block">DNI:</label>
                        <input type="tel" class="form-control" name="txtDNI" id="txtDNI" required value="<?php echo isset($_GET["id"]) ? $aClientes[$_GET["id"]]["dni"] : ""; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="txtNombre" class="d-block">Nombre:</label>
                        <input type="text" class="form-control" name="txtNombre" id="txtNombre" required value="<?php echo isset($_GET["id"]) ? $aClientes[$_GET["id"]]["nombre"] : ""; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="txtTelefono" class="d-block">Teléfono:</label>
                        <input type="tel" class="form-control" name="txtTelefono" id="txtTelefono" required value="<?php echo isset($_GET["id"]) ? $aClientes[$_GET["id"]]["telefono"] : ""; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="txtCorreo" class="d-block">Correo:</label>
                        <input type="email" class="form-control" name="txtCorreo" id="txtCorreo" required value="<?php echo isset($_GET["id"]) ? $aClientes[$_GET["id"]]["correo"] : ""; ?>">
                    </div>
                    <div class="mb-3">
                        Archivo adjunto:<input type="file" class="form-control" name="archivo" id="archivo" class="d-block">
                        <button type="submit" class="btn btn-primary mt-3" id="btnGuardar" name="btnGuardar">Guardar</button>
                        <button type="submit" class="btn btn-danger mt-3 ml-3" id="btnEliminar" name="btnEliminar">Eliminar</button>
                    </div>
                </form>
            </div>
            <div class="col-sm-5 ml-auto col-12">
                <!--acá tiene que estar la tabla con todos los clientes-->
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>DNI</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($aClientes)) {
                            foreach ($aClientes as $index => $cliente) {
                                echo "<tr>";
                                echo "<td><img src=files/" . $cliente["imagen"] . " class='img-thumbnail'></td>";
                                echo "<td>" . $cliente["dni"] . "</td>";
                                echo "<td>" . strtoupper($cliente["nombre"]) . "</td>";
                                echo "<td><a href=mailto:" . $cliente["correo"] . ">" . $cliente["correo"] . "</a></td>";
                                echo "<td class=text-center>";
                                echo "<a href='index.php?id=" . $index . "'><i class='fas fa-edit rounded-circle p-2'></i></a>";
                                echo "<a href='index.php?id=" . $index . "&do=eliminar'><i class='fas fa-trash-alt rounded-circle p-2 m-1'></i></a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
                <a href="index.php" class="btn btn-success rounded-circle"><i class="fas fa-plus"></i></a>
            </div>
        </div>
    </main>
</body>

</html>
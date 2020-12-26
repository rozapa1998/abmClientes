<?php

    if(file_exists("clientes.txt")){
        //leer el archivo, el contenido es un json
        $strJson = file_get_contents("clientes.txt");
        //guardar en el array de clientes ese json decodificado
        $aClientes = json_decode($strJson, true);
    } else {
        $aClientes = array();
    }

    $id = isset($_GET["id"]) ? $_GET["id"] : '';

    if(isset($_GET["id"]) && $_GET["id"] >= 0 && isset($_GET["do"]) && $_GET["do"] == "eliminar"){
        //eliminar la posicion deseada en el array, invertigar unset
        //pasar el array a json
        //actualizar el archivo con este nuevo json
        if(file_exists("imagenes/" . $aClientes[$id]["imagen"])){
            unlink("imagenes/" . $aClientes[$id]["imagen"]);
        }
        unset($aClientes[$id]);
        $strJson = json_encode($aClientes);
        file_put_contents("clientes.txt", $strJson);
    }

    if($_POST){
        $dni = $_POST["txtDNI"];
        $nombre = $_POST["txtNombre"];
        $telefono = $_POST["txtTelefono"];
        $correo = $_POST["txtCorreo"];
        $nombreImagen = "";

        if($_FILES["archivo"]["error"] === UPLOAD_ERR_OK){
            $nombreAleatorio = date("Ymdhmsi");
            $archivo_tmp = $_FILES["archivo"]["tmp_name"];
            $nombreArchivo = $_FILES["archivo"]["name"];
            $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
            $nombreImagen = $nombreAleatorio . "." . $extension;
            move_uploaded_file($archivo_tmp, "imagenes/$nombreImagen");
        }
        
        if($nombreImagen != "" && isset($aClientes[$id]["imagen"]) && $aClientes[$id]["imagen"] != ""){
            //si se sube una imagen y hau una imagen previa entonces eliminarla,
            unlink("imagenes/" . $aClientes[$id]["imagen"]);
        }
        if($nombreImagen == ""){
            //si la persona no sube ninguna imagen, conservar la imagen que tenia previamente
            $nombreImagen = $aClientes[$id]["imagen"];
        }
       

        if(isset($_GET["id"]) && $_GET["id"] >= 0){
            //actualizacion
            $aClientes[$_GET["id"]] = array(
                "dni" => $dni,
                "nombre" => $nombre,
                "telefono" => $telefono,
                "correo" => $correo,
                "imagen" => $nombreImagen);
            header("Location: abm-clientes.php");    
        } else {
            //es nuevo
            $aClientes[] = array("dni" => $dni,
                             "nombre" => $nombre,
                             "telefono" => $telefono,
                             "correo" => $correo,
                             "imagen" => $nombreImagen);
        }

        //convertir array en json
        $strJson = json_encode($aClientes);
        //Guardar el json en un archivo archivo.txt
        file_put_contents("clientes.txt", $strJson); 
    }

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de clientes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" href="css/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="css/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="container">
        <div class="row mt-3">
            <div class="col-12 text-center">
                <h1><a href="abm-clientes.php" class="text-center">Registro de clientes</a></h1>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col 12 col-sm-6">
                <div class="form">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div>
                            <label for="txtDNI">DNI:</label><br>
                            <input type="number" name="txtDNI" id="txtDNI" required style="width:100%" class="border" 
                                    value="<?php echo isset($_GET["id"]) && !isset($_GET["do"]) ? $aClientes[$_GET["id"]]["dni"] : "" ?>">
                        </div>
                        <div>
                            <label for="txtNombre">Nombre:</label><br>
                            <input type="text" name="txtNombre" id="txtNombre" required style="width:100%" class="border"
                                    value="<?php echo isset($_GET["id"]) && !isset($_GET["do"]) ? $aClientes[$_GET["id"]]["nombre"] : "" ?>">
                        </div>
                        <div>
                            <label for="txtTelefono">Telefono:</label><br>
                            <input type="tel" name="txtTelefono" id="txtTelefono" required style="width:100%" class="border"
                                    value="<?php echo isset($_GET["id"]) && !isset($_GET["do"]) ? $aClientes[$_GET["id"]]["telefono"] : "" ?>">
                        </div>
                        <div>
                            <label for="txtCorreo">Correo:</label><br>
                            <input type="email" name="txtCorreo" id="txtCorreo" required style="width:100%" class="border"
                                    value="<?php echo isset($_GET["id"]) && !isset($_GET["do"]) ? $aClientes[$_GET["id"]]["correo"] : "" ?>">
                        </div>
                        <div>
                            <label for="archivo">Archivo adjunto:</label><br>
                            <input type="file" name="archivo" id="archivo" style="width:100%" class="border bg-white">
                        </div>
                        <div>
                            <button type="submit" class="btn mt-3">Guardar</button>    
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-12 col-sm-6 mt-3 mt-sm-0">
                <table class="table table-hover border">
                    <tr>
                        <th>Imagen</th>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Telefono</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                    <?php
                        foreach($aClientes as $key => $cliente){ ?>
                            <tr>
                                <td class="text-center"><img style="max-width: 100%;height:50px;" class="img-fluid img-thumbnail border shadow" src="imagenes/<?php echo $cliente["imagen"]?>"></td>
                                <td><?php echo $cliente["dni"] ?></td>
                                <td><?php echo mb_strtoupper($cliente["nombre"],"UTF-8") ?></td>
                                <td><?php echo $cliente["telefono"] ?></td>
                                <td><?php echo $cliente["correo"] ?></td>
                                <td class="text-center"><a href="abm-clientes.php?id=<?php echo $key ?>"><i class="fas fa-edit" alt="Modificar" title="Modificar"></i></a>
                                    <a href="abm-clientes.php?id=<?php echo $key ?>&do=eliminar"><i class="fas fa-trash-alt" alt="Eliminar" title="Eliminar"></i></a>
                                </td>
                            </tr>
                    <?php } ?> 
                </table>
            </div>
        </div>
    </div>
</body>
</html>
<?php

//Abrir el archivo.txt si existe
if (file_exists("archivo.txt")) {
    //Leer archivo
    $jsonClientes = file_get_contents("archivo.txt");
    //Convertir el json en array
    $aClientes = json_decode($jsonClientes, true);


} else {
    $aClientes = array();
}


$id = isset($_GET["id"])? $_GET["id"] : "";

if(isset($_GET["accion"]) && $_GET["accion"]=="eliminar"){
    //Elimina la imagen fisicamente
    $imgBorrar = "archivos/" .  $aClientes[$id]["imagen"];
    if(file_exists($imgBorrar)){
        unlink($imgBorrar);
    }

    //Eliminamos el cliente del array
    unset($aClientes[$id]);

    //Actualizo el archivo con el nuevo array de clientes modificado
    file_put_contents("archivo.txt", json_encode($aClientes));

    header("location: index.php");
}


if ($_POST) {
    $dni = $_POST["txtDni"];
    $nombre = $_POST["txtNombre"];
    $telefono = $_POST["txtTelefono"];
    $correo = $_POST["txtCorreo"];

    $nuevoNombre = "";
    if($_FILES["archivo"]["error"] === UPLOAD_ERR_OK){
        $nombreAleatorio = date("Ymdhmsi");
        $archivo_tmp = $_FILES["archivo"]["tmp_name"];
        $nombreArchivo = $_FILES["archivo"]["name"];
        $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $nuevoNombre = "$nombreAleatorio.$extension";
        move_uploaded_file($archivo_tmp,"archivos/" .$nuevoNombre);
    }
    

    if($id >= 0){
        if ($_FILES["archivo"]["error"] !== UPLOAD_ERR_OK) {
            $nuevoNombre = $aClientes[$id]["imagen"];
        } else {
            //Elimina la imagen anterior si existe
            if (file_exists("archivos/".$aClientes[$id]["imagen"])) {
                unlink("archivos/".$aClientes[$id]["imagen"]);
            }
        }
        //Actulizo el cliente marianela
        $aClientes[$id]= array("dni" => $dni,
                        "nombre" => $nombre,
                        "telefono" => $telefono,
                        "correo" => $correo,
                        "imagen" =>$nuevoNombre);
    } else {
        //Es un nuevo cliente marianela
        $aClientes[]= array("dni" => $dni,
                            "nombre" => $nombre,
                            "telefono" => $telefono,
                            "correo" => $correo,
                            "imagen" =>$nuevoNombre);
    }
    
              

    //Codificar el array en json
    $jsonCliente = json_encode($aClientes);

    //Guardar el json en un archivo.txt
    file_put_contents("archivo.txt", $jsonCliente);

    
}

?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css"
        integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
    <link rel="stylesheet" href="estilos.css">
    <title>Document</title>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-center m-5">
                <h1>Registro de Clientes</h1>
            </div>

        </div>

        <div class="row">
            <div class="col-6 mt-4">
                <form action="" method="POST" name="" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="txtDni">DNI</label>
                            <input type="text" name="txtDni" id="txtDni" class="form-control shadow" required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["dni"] : ""?>">
                            
                        </div>
                        <div class="mb-3">
                            <label for="txtNombre">Nombre</label>
                            <input type="text" name="txtNombre" id="txtNombre" class="form-control shadow " required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["nombre"] : ""?>">
                        </div>
                        <div class="mb-3">
                            <label for="txtDni">Teléfono</label>
                            <input type="tel" name="txtTelefono" id="txtTelefono" class="form-control shadow" required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["telefono"] : ""?>">
                        </div>
                        <div class="mb-3">
                            <label for="txtDni">Correo</label>
                            <input type="email" name="txtCorreo" id="txtCorreo" class="form-control shadow" required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["correo"] : ""?>">
                        </div>

                        <div class="mb-3 ">
                            <label for="txtCorreo">Archivo adjunto:</label><br>
                            <input type="file" id="archivo" name="archivo" class="form-control-file "
                                accept=".jpg, .jpeg, .png">
                            <small class="d-block">Archivos admitidos: .jpg, .jpeg, .png</small>
                        </div>
                    </div>
                    <div class="row"> 

                        <div class="mb-4">
                            <button type="submit" id="btnGuardar" name="btnGuardar" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>

                </form>

            </div>
            <div class="col-sm-6 col-12 mt-4">
                <table class="table table-hover border shadow">
                    <tr>
                        <th>Imagen</th>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                    
                    <?php foreach($aClientes as $key => $cliente):?>
                        <tr>
                            <td><img src="archivos/<?php echo $cliente['imagen']; ?>" class="img-thumbnail" ></td>
                            <td><?php echo $cliente["dni"];?></td>
                            <td><?php echo $cliente["nombre"];?></td>
                            <td><?php echo $cliente["correo"];?></td>
                            <td style="width: 110px;">
                                <a href="?id=<?php echo $key;?>"><i class="fas fa-edit"></i></a>
                                <a href="?id=<?php echo $key; ?>&accion=eliminar"><i class="fas fa-trash-alt"></i></a>

                            </td>
                        </tr>

                    <?php endforeach; ?>
                    
                </table>
                <div>
                    <a href="index.php"><i class="fas fa-plus"></i></a>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
<?php

ob_start(); // Utilizo la salida del buffer para evitar problemas con los header ();

include ("../plantillas/header.php");
include ("../utilidades/seguridad.php");
include ("../utilidades/validaciones.php");

soloRegistrados();

?>

<div class="container">
    <div class="card rounded my-5 principal-bg neumorphism border-none">
        <div class="card-header">
            <h2>Editar perfil</h2>
        </div>
        <div class="card-body">
            <p>Modifique los datos que desee cambiar.</p>
            <form name="fUsuarioEditar" method="post" enctype="multipart/form-data">

                <?php
                    $errores = false;
                ?> 

                <div class="row">
                    <!-- Campo Nombre -->
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" maxlength="30" value="<?=$_SESSION['nombre']?>">
                        </div>
                        <?php if (isset($_POST["enviar"])) { $errores |= validarCampoVacio($_POST["nombre"], "Nombre"); } ?>
                    </div>
                    <!-- Campo Apellido1 -->
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="apellido1" class="form-label">Primer apellido:</label>
                            <input type="text" class="form-control" id="apellido1" name="apellido1" maxlength="30" value="<?=$_SESSION['apellido1']?>">
                        </div>
                        <?php if (isset($_POST["enviar"])) { $errores |= validarCampoVacio($_POST["apellido1"], "Primer apellido"); } ?>
                    </div>
                    <!-- Campo Apellido2 -->
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="apellido2" class="form-label">Segundo apellido:</label>
                            <input type="text" class="form-control" id="apellido2" name="apellido2" maxlength="30" value="<?=$_SESSION['apellido2']?>">
                        </div>
                        <?php if (isset($_POST["enviar"])) { $errores |= validarCampoVacio($_POST["apellido2"], "Segundo apellido"); } ?>
                    </div>
                </div>

                <div class="row">
                    <!-- Campo Contraseña -->
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="contrasena" class="form-label">Contraseña:</label>
                            <input type="password" class="form-control" id="contrasena" name="contrasena" maxlength="30" value="<?=$_SESSION['contrasena']?>">
                        </div>
                        <?php if (isset($_POST["enviar"])) { $errores |= validarContrasena($_POST["contrasena"]); }?>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-6 d-flex flex-column align-items-center">
                        <!-- Campo Foto Perfil -->
                        <div class="mb-3 text-center">
                            <label for="imagen" class="form-label">Foto de perfil:</label> <br>
                            <img id="preview" src="<?=$_SESSION['img_perfil']?>" alt="Foto de perfil" class="img-thumbnail rounded-circle mb-2" style="width: 150px; height: 150px;">
                            <input type="file" name="imagen" id="imagen" class="form-control" onchange="previewImage(event)">
                            <?php if (isset($_POST["enviar"]) && !empty($_FILES["imagen"]["tmp_name"])) { $errores |= validarImagen($_FILES["imagen"]); } ?>
                        </div>
                        <div class="my-3 text-center">
                            <a class="btn btn-secondary" href="perfil.php" style="text-decoration:none; color:white">Cancelar</a>
                            <button type="submit" class="btn btn-primary ms-2" name="enviar">Editar perfil</button> 
                        </div>
                    </div>
                </div>            

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script utilizado para previsualizar la imagen de perfil subida por el usuario -->
<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>

<?php

    if (isset($_POST["enviar"])) {
        if (!$errores) {

            $nombre = $_POST["nombre"];
            $apellido1 = $_POST["apellido1"];
            $apellido2 = $_POST["apellido2"];
            $contrasena = $_POST["contrasena"];

            if ($contrasena == $_SESSION["contrasena"]) {
                $contrasenaEnc = $_SESSION["contrasena"];
            }
            else {
                $contrasenaEnc = password_hash($contrasena, PASSWORD_DEFAULT);
            }
            
            if (!empty($_FILES["imagen"]["tmp_name"])) {
                    
                $imagenTmp = $_FILES["imagen"]["tmp_name"];
                $imagenName = $_FILES["imagen"]["name"]; 

                $extensionArch = pathinfo($imagenName, PATHINFO_EXTENSION);
                $nuevoNombre = $_SESSION["id"] . "." . $extensionArch;
                $destino = "../imagenes/perfil/" . $nuevoNombre;

                if (file_exists($_SESSION["img_perfil"])) {
                    if ($_SESSION["img_perfil"] != "../imagenes/logos/person.jpg") {
                        unlink($_SESSION["img_perfil"]);
                    }
                }

                if (move_uploaded_file($imagenTmp, $destino)) {
                    $imagenFinal = $destino;
                }
                else {
                    echo "<p style='color:red'>Error al subir la imagen. Se ha establecido una imagen por defecto.</p>";
                    $imagenFinal = "../imagenes/logos/person.jpg";
                }
            }
            else {
                $imagenFinal = $_SESSION["img_perfil"];
            }
            

            $stmt = $con->prepare(
                "UPDATE usuario SET nombre = :nombre, apellido1 = :apellido1, apellido2 = :apellido2, contrasena = :contrasena, img_perfil = :img_perfil WHERE id = :id"
            );

            $fila = $stmt->execute(
                [
                    ":nombre" => $nombre,
                    ":apellido1" => $apellido1,
                    ":apellido2" => $apellido2,
                    ":contrasena" => $contrasenaEnc,
                    ":img_perfil" => $imagenFinal,
                    ":id" => $_SESSION["id"]
                ]
            );

            if ($fila == 1) {

                $_SESSION["nombre"] = $nombre;
                $_SESSION["apellido1"] = $apellido1;
                $_SESSION["apellido2"] = $apellido2;
                $_SESSION["contrasena"] = $contrasenaEnc;
                $_SESSION["img_perfil"] = $imagenFinal;

                header("Location: perfil.php?mensaje=cuenta_modificada");
            }
            
        }
    }
    
    include ("../plantillas/footer.php");

    ob_end_flush();

?>
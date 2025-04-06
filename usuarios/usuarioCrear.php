<?php

ob_start(); // Utilizo la salida del buffer para evitar problemas con los header ();

include ("../plantillas/header.php");
include ("../utilidades/validaciones.php");

?>

<div class="container">
    <div class="card rounded my-5 principal-bg neumorphism border-none">
        <div class="card-header">
            <h2>Creación de una cuenta</h2>
        </div>
        <div class="card-body">
            <p>Rellene los siguientes campos con los datos que se le piden a continuación:</p>
            <form name="fUsuarioCrear" method="post" enctype="multipart/form-data">

                <?php
                    $errores = false;
                    $estado = 1;
                ?> 

                <div class="row">
                    <!-- Campo Nombre -->
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" maxlength="30" <?php campoEnviado("nombre")?>>
                        </div>
                        <?php if (isset($_POST["enviar"])) { $errores |= validarCampoVacio($_POST["nombre"], "Nombre"); } ?>
                    </div>
                    <!-- Campo Apellido1 -->
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="apellido1" class="form-label">Primer apellido:</label>
                            <input type="text" class="form-control" id="apellido1" name="apellido1" maxlength="30" <?php campoEnviado("apellido1")?>>
                        </div>
                        <?php if (isset($_POST["enviar"])) { $errores |= validarCampoVacio($_POST["apellido1"], "Primer apellido"); } ?>
                    </div>
                    <!-- Campo Apellido2 -->
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="apellido2" class="form-label">Segundo apellido:</label>
                            <input type="text" class="form-control" id="apellido2" name="apellido2" maxlength="30" <?php campoEnviado("apellido2")?>>
                        </div>
                        <?php if (isset($_POST["enviar"])) { $errores |= validarCampoVacio($_POST["apellido2"], "Segundo apellido"); } ?>
                    </div>
                </div>

                <div class="row">
                    <!-- Campo Email -->
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico:</label>
                            <input type="text" class="form-control" id="email" name="email" maxlength="50" <?php campoEnviado("email")?>>
                        </div>
                        <?php if (isset($_POST["enviar"])) { $errores |= validarEmail($_POST["email"]); } ?>
                        <?php if (isset($_POST["enviar"])) { $errores |= validarEmailExistente($_POST["email"], $con); }?>
                    </div>
                    <!-- Campo Contraseña -->
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="contrasena" class="form-label">Contraseña:</label>
                            <input type="password" class="form-control" id="contrasena" name="contrasena" maxlength="30">
                        </div>
                        <?php if (isset($_POST["enviar"])) { $errores |= validarContrasena($_POST["contrasena"]); }?>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-6 d-flex flex-column align-items-center">
                        <!-- Campo Foto Perfil -->
                        <div class="mb-3 text-center">
                            <label for="imagen" class="form-label">Foto de perfil:</label> <br>
                            <img id="preview" src="/imagenes/logos/person.jpg" alt="Foto de perfil" class="img-thumbnail rounded-circle mb-2" style="width: 150px; height: 150px;">
                            <input type="file" name="imagen" id="imagen" class="form-control" onchange="previewImage(event)">
                            <?php if (isset($_POST["enviar"]) && !empty($_FILES["imagen"]["tmp_name"])) { $errores |= validarImagen($_FILES["imagen"]); } ?>
                        </div>
                        <div class="my-3 text-center">
                            <a class="btn btn-secondary" href="login.php" style="text-decoration:none; color:white">Cancelar</a>
                            <button type="submit" class="btn btn-primary ms-2" name="enviar">Crear cuenta</button>
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

            echo "$estado";

            $nombre = $_POST["nombre"];
            $apellido1 = $_POST["apellido1"];
            $apellido2 = $_POST["apellido2"];
            $email = $_POST["email"];
            $contrasena = $_POST["contrasena"];
            
            $contrasenaEnc = password_hash($contrasena, PASSWORD_DEFAULT);

            $stmt = $con->prepare("SELECT estado FROM usuario WHERE email = :email"); 
            $stmt->execute([":email" => $email]); 
            $estado = ($stmt->fetchColumn() == 2) ? 2 : 1;

            if ($estado == 2) {

                $stmt = $con->prepare(
                    "UPDATE usuario SET nombre = :nombre, apellido1 = :apellido1, apellido2 = :apellido2, contrasena = :contrasena, estado = :estado WHERE email = :email"
                );

                $fila = $stmt->execute(
                    [
                        ":nombre" => $nombre,
                        ":apellido1" => $apellido1,
                        ":apellido2" => $apellido2,
                        ":contrasena" => $contrasenaEnc,
                        ":estado" => 1,
                        ":email" => $email
                    ]
                );
            }
            else {

                $stmt = $con->prepare(
                    "INSERT INTO usuario (nombre, apellido1, apellido2, contrasena, email) VALUES (:nombre, :apellido1, :apellido2, :contrasena, :email)"
                );

                $fila = $stmt->execute(
                    [
                        ":nombre" => $nombre,
                        ":apellido1" => $apellido1,
                        ":apellido2" => $apellido2,
                        ":contrasena" => $contrasenaEnc,
                        ":email" => $email
                    ]
                );
            }

            $stmt = $con->prepare("SELECT id FROM usuario WHERE email = :email");
            $stmt->execute([":email" => $email]); 

            if ($fila = $stmt->fetch()) {

                $idUsuario = $fila["id"];

                if (!empty($_FILES["imagen"]["tmp_name"])) {
                    
                    $imagenTmp = $_FILES["imagen"]["tmp_name"];
                    $imagenName = $_FILES["imagen"]["name"]; 

                    $extensionArch = pathinfo($imagenName, PATHINFO_EXTENSION);
                    $nuevoNombre = $idUsuario . "." . $extensionArch;
                    $destino = "../imagenes/perfil/" . $nuevoNombre;

                    if (move_uploaded_file($imagenTmp, $destino)) {
                        $imagenFinal = $destino;
                    }
                    else {
                        echo "<p style='color:red'>Error al subir la imagen. Se ha establecido una imagen por defecto.</p>";
                        $imagenFinal = "../imagenes/logos/person.jpg";
                    }
                }
                else {
                    $imagenFinal = "../imagenes/logos/person.jpg";
                }

                $stmt = $con->prepare(
                    "UPDATE usuario SET img_perfil = :destino WHERE id = :id"
                );
                $fila = $stmt->execute(
                    [
                        ":destino" => $imagenFinal,
                        ":id" => $idUsuario
                    ]
                );

                if ($fila == 1) {header("Location: login.php?mensaje=cuenta_creada");}

            }
        }
    }
    
include ("../plantillas/footer.php");

ob_end_flush();

?>
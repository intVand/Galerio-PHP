<?php

ob_start(); // Utilizo la salida del buffer para evitar problemas con los header ();

include ("../plantillas/header.php");
include ("../utilidades/validaciones.php");
include ("../utilidades/phpmailerConfig.php");
include ("../utilidades/mensajes.php");

?>

<div class="container">
    <div class="card rounded my-5 principal-bg neumorphism border-none">
        <div class="card-header">
            <h2>Restablecimiento de contraseña</h2>
        </div>
        <div class="card-body">
            <p>Introduzca a continuación el correo electrónico, recibirá un email en dicho correo con los pasos para restablecer su contraseña.</p>
            <form name="fRestablecer" method="post" onsubmit="mostrarSpinner()">

                <?php
                    $errores = false;
                ?> 

                <div class="row">
                    <!-- Campo Email -->
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico:</label>
                            <input type="text" class="form-control" id="email" name="email" maxlength="50">
                        </div>
                        <?php if (isset($_POST["enviar"])) { $errores |= validarEmailNoExistente($_POST["email"], $con); }?>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-6 d-flex flex-column align-items-center">
                        <div class="my-3 text-center">
                            <a class="btn btn-secondary" href="../usuarios/login.php" style="text-decoration:none; color:white">Cancelar</a>
                            <button type="submit" class="btn btn-primary ms-2" name="enviar">Restablecer contraseña</button>
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

    <div id="spinner" class="text-center" style="display: none;"> 
        <div class="spinner-border text-secondary" role="status" style="width: 3rem; height: 3rem;">
        </div> 
    </div>

</div>

<?php

    if (isset($_POST["enviar"])) {
        if (!$errores) {

            $email = $_POST["email"];
            $token = bin2hex(random_bytes(50));

            // Añadir el token a la tabla usuario
            $stmt = $con->prepare(
                "UPDATE usuario SET token = :token WHERE email = :email"
            );

            $stmt->execute(
                [
                    ":token" => $token,
                    ":email" => $email
                ]
            );

            // Obtener el id del usuario (lo usaré para enviarlo con el correo electrónico)
            $stmt = $con->prepare(
                "SELECT id FROM usuario WHERE email LIKE :email"
            );

            $stmt->execute([":email" => $email]);

            if ($dato = $stmt->fetch()) {
                $id = $dato[0];
            }

            // Enviar el correo electrónico
            $mail = getMailer();
            $mensaje = 
            "
                <h2>Restablecimiento de contraseña</h2>
                <p>Hola! Parece que has solicitado un restablecimiento de tu contraseña de Galerio, si es así, sigue leyendo, de lo contrario haz caso omiso al mensaje, gracias!</p>   
                <p>Haz clic en el siguiente enlace para restablecer tu contraseña: <a href='http://localhost:3000/restablecimiento/restablecerContrasena.php?token=" . $token . "&id=" . $id . "'>Restablecer contraseña</a></p>
            ";

            try { 
                $mail->addAddress($email); 
                $mail->isHTML(true); 
                $mail->Subject = "Restablecimiento de credenciales"; 
                $mail->Body = $mensaje;
                $mail->send();
                
                echo "<div class='container my-5'>";
                echo "  <div class='row justify-content-center'>";
                echo "      <div class='col-md-8'>";
                                mostrarMensaje("correo_exito"); 
                echo "      </div>";
                echo "  </div>";
                echo "</div>";
                
            } catch (Exception $e) {
                echo "<div class='container my-5'>";
                echo "  <div class='row justify-content-center'>";
                echo "      <div class='col-md-8'>";
                                mostrarMensaje("correo_error");
                echo "      </div>";
                echo "  </div>";
                echo "</div>"; 
            }    
            echo "<script>document.getElementById('spinner').style.display = 'none';</script>";
        }     
    }    

include ("../plantillas/footer.php");

ob_end_flush();

?>

<script> 
    function mostrarSpinner() { 
        document.getElementById("spinner").style.display = "block"; 
    } 
</script>
<?php

ob_start(); // Utilizo la salida del buffer para evitar problemas con los header ();

include ("../plantillas/header.php");
include ("../utilidades/validaciones.php");

$token = isset($_GET["token"]) ? $_GET["token"] : (isset($_POST["token"]) ? $_POST["token"] : " ");
$id = isset($_GET["id"]) ? $_GET["id"] : (isset($_POST["id"]) ? $_POST["id"] : " ");

$stmt = $con->prepare(
    "SELECT token FROM usuario WHERE id = :id"
);

$stmt->execute([":id" => $id]);

if ($dato = $stmt->fetch()) {
    $tokenReal = $dato[0];
}

if ($token != $tokenReal) {
    header ("Location: ../usuarios/login.php?mensaje=restablecer_error");
}

?>

<div class="container">
    <div class="card bg-light shadow rounded my-5">
        <div class="card-header">
            <h2>Restablecimiento de contraseña</h2>
        </div>
        <div class="card-body">
            <p>Introduzca a continuación la nueva contraseña:</p>
            <form name="fRestablecerContrasena" method="post">

                <input type="hidden" name="token" value="<?=$token?>">
                <input type="hidden" name="id" value="<?=$id?>">

                <?php
                    $errores = false;
                ?> 

                <div class="row">
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
</div>

<?php

    if (isset($_POST["enviar"])) {
        if (!$errores) {

            $contrasena = $_POST["contrasena"];
            $contrasenaEnc = password_hash($contrasena, PASSWORD_DEFAULT);

            $stmt = $con->prepare(
                "UPDATE usuario SET contrasena = :contrasena, token = ' ' WHERE id = :id"
            );

            $fila = $stmt->execute(
                [
                    ":contrasena" => $contrasenaEnc,
                    ":id" => $id
                ]
            );

            if ($fila == 1) {
                header("Location: ../usuarios/login.php?mensaje=restablecer_contraseña");
            }
        }
    }        

include ("../plantillas/footer.php");

ob_end_flush();

?>
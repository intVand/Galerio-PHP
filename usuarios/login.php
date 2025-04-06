<?php

ob_start(); // Utilizo la salida del buffer para evitar problemas con los header ();

include ("../plantillas/header.php");
include ("../utilidades/mensajes.php");

// Token CSRF utilizado para la seguridad de los inicios de sesión
if(empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); 
}

$errVacio = false;
$errExistente = false;

if(isset($_POST["iniciarS"])) {
    if(empty($_POST["correoIni"]) || empty($_POST["contrasenaIni"]) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errVacio = true;
    } else {
        $stmt = $con->prepare('SELECT email, contrasena, estado FROM usuario WHERE email LIKE :email');
        $stmt->execute([':email' => $_POST['correoIni']]);

        if ($datos = $stmt->fetch()) {
            $emailReal = $datos[0];
            $contrasenaEnc = $datos[1];
            $estado = $datos[2];

            if (password_verify($_POST["contrasenaIni"], $contrasenaEnc)) {
                $errExistente = false;
            } else {
                $errExistente = true;
            }

            if ($estado == 2) {
                $errExistente = true;
            }
        } else {
            $errExistente = true;
        }
    }

    if (!$errExistente && !$errVacio) {
        $correoIni = $_POST["correoIni"];

        $consultaR = $con->prepare('SELECT nombre, rol, id, apellido1, apellido2, img_perfil, contrasena FROM usuario WHERE email LIKE :email');
        $consultaR->execute([':email' => $correoIni]);
        if ($dato = $consultaR->fetch()) {
            $nombre = $dato[0];
            $rol = $dato[1];
            $id = $dato[2];
            $apellido1 = $dato[3];
            $apellido2 = $dato[4];
            $img_perfil = $dato[5];
            $contrasena = $dato[6];
        }

        $_SESSION["nombre"] = $nombre;
        $_SESSION["apellido1"] = $apellido1;
        $_SESSION["apellido2"] = $apellido2;
        $_SESSION["id"] = $id;
        $_SESSION["rol"] = $rol;
        $_SESSION["email"] = $emailReal;
        $_SESSION["img_perfil"] = $img_perfil;
        $_SESSION["contrasena"] = $contrasena;

        header("Location: ../index.php");
    }
}

?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <?php
            if (isset($_GET["mensaje"])) {
                mostrarMensaje($_GET["mensaje"]);
            }
            ?>
        </div>
    </div>
</div>

<div class="container d-flex justify-content-center align-items-center my-2 py-5" style="opacity: 1;">
    <div class="card p-3 mb-5 rounded neumorphism principal-bg" style="border: none;">
        <div class="card-body principal-bg">
            <h5 class="card-title text-center">Iniciar sesión / Registrarse</h5>
            <form name="fInicio" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico:</label>
                    <input type="email" class="form-control" id="email" aria-describedby="emailHelp" name="correoIni" maxlength="50">
                </div>
                <div class="mb-3">
                    <label for="contrasena" class="form-label">Contraseña:</label>
                    <input type="password" class="form-control" id="contrasena" name="contrasenaIni" maxlength="30">
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary me-md-2" name="iniciarS">Iniciar sesión</button>
                    <a href="usuarioCrear.php" class="btn btn-secondary">Registrarse</a>
                </div>
                <div class="mt-3 mb-3">
                    <p>¿Problemas para iniciar sesión?</p>
                    <a href="../restablecimiento/restablecerFormulario.php" class="card-link">Restablecer contraseña</a>
                </div>
                <?php
                if ($errVacio) {
                    mostrarMensaje("campos_vacios");
                }
                if ($errExistente) {
                    mostrarMensaje("usuario_existe");
                }
                ?>
            </form>
        </div>
    </div>
</div>

<?php

include ("../plantillas/footer.php");

ob_end_flush();

?>
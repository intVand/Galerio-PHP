<?php

ob_start(); // Utilizo la salida del buffer para evitar problemas con los header ();

include ("../plantillas/header.php");
include ("../utilidades/phpmailerConfig.php");
include ("../utilidades/seguridad.php");

soloRegistrados();
soloAdmin();

if (isset($_GET["id"]) && $_GET["id"] != "") {
    $id = $_GET["id"];
}
else {
    header("Location: usuarioGestionar.php?mensaje=correo_error");
}

$stmt = $con->prepare(
    "SELECT nombre, apellido1, apellido2, email FROM usuario WHERE id = :id"
);

$stmt->execute([":id" => $id]);

    if ($dato = $stmt->fetch()) {
        $nombre = $dato[0];
        $apellido1 = $dato[1];
        $apellido2 = $dato[2];
        $email = $dato[3];
    }

?>

<div class="container"> 
    <div class="card rounded my-5 principal-bg neumorphism border-none"> 
        <div class="card-header"> 
            <h2>Confirmación de Baneo</h2> 
        </div> 
        <div class="card-body"> 
            <p>¿Estás seguro de que quieres banear al usuario <strong><?=$nombre . " " . $apellido1 . " " . $apellido2?></strong>? Si lo haces, se le enviará un correo electrónico notificándolo.</p> 
            <form method="post" onsubmit="mostrarSpinner()"> 
                <a href="usuarioGestionar.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" name="enviar" class="btn btn-danger">Banear usuario</button> 
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

    $stmt = $con->prepare(
        "UPDATE usuario SET estado = 2 WHERE id = :id"
    );

    $stmt->execute([":id" => $id]);

    // Enviar el correo electrónico
    $mail = getMailer();
    $mensaje = 
    "
        <h2>Cuenta baneada</h2>
        <p>¡Hola! Parece ser que has incumplido los términos de nuestro sitio web, y por ello su cuenta ha sido reportada por uno de nuestros administradores.</p>   
        <p>Para recuperar su cuenta tendrá que registrarse de nuevo en el sitio web, utilizando su misma dirección de correo electrónico.</p>
        <p>No se preocupe, sus imágenes y likes seguirán estando asociados a su cuenta.</p>
    ";

    try { 
        $mail->addAddress($email); 
        $mail->isHTML(true); 
        $mail->Subject = "Cuenta baneada"; 
        $mail->Body = $mensaje;
        $mail->send();

        header("Location: usuarioGestionar.php?mensaje=correo_exito");

    } catch (Exception $e) {
        header("Location: usuarioGestionar.php?mensaje=correo_error");
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
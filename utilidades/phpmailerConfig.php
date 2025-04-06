<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require $_SERVER["DOCUMENT_ROOT"]."/PHPMailer/src/Exception.php";
require $_SERVER["DOCUMENT_ROOT"]."/PHPMailer/src/PHPMailer.php";
require $_SERVER["DOCUMENT_ROOT"]."/PHPMailer/src/SMTP.php";

// Función que contiene la configuración común de PHP Mailer
function getMailer() {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = "[TU_SERVIDOR_SMTP]"; // Servidor SMTP al que conectarse (ej: smtp.gmail.com)
    $mail->SMTPAuth = true;
    $mail->Username = "[TU_CORREO_ELECTRONICO]"; // Email emisor
    $mail->Password = "[TU_CONTRASENA_CORREO]"; // Contraseña de la aplicación o del correo electrónico
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = "[PUERTO_SMTP]"; // Puerto TCP al que conectarse (ej: 587 para TLS, 465 para SSL)
    $mail->setFrom("[TU_CORREO_ELECTRONICO]", "[TU_NOMBRE_REMITENTE]"); // Establece la dirección del remitente y el nombre que aparecerá
    return $mail;
}

?>
<?php

// Verifica si la sesión ya se ha iniciado antes (ya que también se inicia en header.php).
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cierra la sesión y devuelve al usuario a index.php.
if (isset($_GET["accion"]) && $_GET["accion"] == "cerrar_sesion") {
    session_unset();
    session_destroy();

    if (isset($_GET["mensaje"]) && $_GET["mensaje"] == "cuenta_eliminada") {
        header("Location: ../usuarios/login.php?mensaje=cuenta_eliminada");
    }
    else {
        header("Location: ../index.php");
    }
}

// Si el usuario no está registrado, se le devuelve a index.php.
function soloRegistrados() {
    if (!isset($_SESSION["id"])) {
        header("Location: /index.php");
    }
}

// Si el usuario no es administrador, se le devuelve a index.php.
function soloAdmin() {
    if ($_SESSION["rol"] == "usuario") {
        header("Location: /index.php");
    }
}

?>
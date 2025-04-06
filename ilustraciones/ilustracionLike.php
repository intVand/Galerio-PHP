<?php

    session_start();
    include($_SERVER["DOCUMENT_ROOT"]."/utilidades/conectarDB.php");

    if (isset($_GET["accion"]) && isset($_GET["idImagen"]) && isset($_GET["idUsuario"])) {

        $accion = $_GET["accion"];
        $idImagen = $_GET["idImagen"];
        $idUsuario = $_GET["idUsuario"];

        if ($accion == "añadir") {
            
            $stmt = $con->prepare(
                "INSERT INTO likes (id_usuario, id_imagen) VALUES (:idUsuario, :idImagen)"
            );

            $stmt->execute(
                [
                    "idUsuario" => $idUsuario, 
                    "idImagen" => $idImagen
                ]
            );

        } elseif ($accion == "quitar") {
            
            $stmt = $con->prepare(
                "DELETE FROM likes WHERE id_usuario = :idUsuario AND id_imagen = :idImagen"
            );

            $stmt->execute(
                [
                    "idUsuario" => $idUsuario, 
                    "idImagen" => $idImagen
                ]
            );

        }

        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit;
    }
    else {
        header("Location: {$_SERVER['HTTP_REFERER']}");
    }

?>

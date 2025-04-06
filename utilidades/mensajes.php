<?php

    function mostrarMensaje($tipo) {
        $mensajes = [
            "cuenta_creada" => ["mensaje" => "Cuenta creada con éxito.", "tipo" => "success", "icono" => "check-circle-fill"],
            "cuenta_eliminada" => ["mensaje" => "Cuenta eliminada con éxito.", "tipo" => "success", "icono" => "check-circle-fill"],
            "cuenta_modificada" => ["mensaje" => "Su perfil ha sido editado con éxito.", "tipo" => "success", "icono" => "check-circle-fill"],
            "campos_vacios" => ["mensaje" => "Correo electrónico / Contraseña no pueden estar en blanco.", "tipo" => "danger", "icono" => "exclamation-triangle-fill"],
            "usuario_existe" => ["mensaje" => "Correo electrónico / Contraseña incorrectos, o la cuenta no existe.", "tipo" => "danger", "icono" => "exclamation-triangle-fill"],
            "correo_exito" => ["mensaje" => "Se ha enviado el correo electrónico con éxito.", "tipo" => "success", "icono" => "check-circle-fill"],
            "correo_error" => ["mensaje" => "Ha ocurrido un error durante el envío, vuelva a intentarlo.", "tipo" => "danger", "icono" => "exclamation-triangle-fill"],
            "restablecer_error" => ["mensaje" => "Error al restablecer la contraseña, vuelva a intentarlo de nuevo.", "tipo" => "danger", "icono" => "exclamation-triangle-fill"],
            "restablecer_contraseña" => ["mensaje" => "Contraseña restablecida con éxito.", "tipo" => "success", "icono" => "check-circle-fill"],
            "usuario_inexistente" => ["mensaje" => "El usuario buscado no existe en la web.", "tipo" => "danger", "icono" => "exclamation-triangle-fill"],
            "categoria_creada" => ["mensaje" => "Categoría creada con éxito.", "tipo" => "success", "icono" => "check-circle-fill"],
            "categoria_eliminada" => ["mensaje" => "Categoría eliminada con éxito.", "tipo" => "success", "icono" => "check-circle-fill"],
            "categoria_modificada" => ["mensaje" => "Categoría modificada con éxito.", "tipo" => "success", "icono" => "check-circle-fill"],
            "categoria_inexistente" => ["mensaje" => "No existen categorías para realizar cambios.", "tipo" => "danger", "icono" => "exclamation-triangle-fill"],
            "ilustracion_creada" => ["mensaje" => "Ilustración creada con éxito.", "tipo" => "success", "icono" => "check-circle-fill"],
            "ilustracion_eliminada" => ["mensaje" => "Ilustración eliminada con éxito.", "tipo" => "success", "icono" => "check-circle-fill"],
            "ilustracion_modificada" => ["mensaje" => "Ilustración modificada con éxito.", "tipo" => "success", "icono" => "check-circle-fill"],
            "ilustracion_inexistente" => ["mensaje" => "No existen ilustraciones con el nombre buscado.", "tipo" => "danger", "icono" => "exclamation-triangle-fill"],
        ];

        if (isset($mensajes[$tipo])) {
            echo "<div class=\"alert alert-" . $mensajes[$tipo]["tipo"] . " d-flex align-items-center\" role=\"alert\">";
            echo    "<i class=\"bi bi-" . $mensajes[$tipo]["icono"] . "\"></i>";
            echo    "<div class='ms-1'>";
            echo      $mensajes[$tipo]["mensaje"];
            echo    "</div>";
            echo "</div>";
        }
    }

?>
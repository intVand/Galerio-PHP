<?php

ob_start(); // Utilizo la salida del buffer para evitar problemas con los header ();

include ("../plantillas/header.php");
include ("../utilidades/seguridad.php");
include ("../utilidades/validaciones.php");

soloRegistrados();

if (isset($_POST["enviar"])) {
    $idImagen = $_POST["idImagen"];
}
else {
    $idImagen = $_GET["idImagen"];
}

$stmt = $con->prepare(
    "SELECT nombre, url, id_usuario FROM imagen WHERE id = :id"
);

$stmt->execute(["id" => $idImagen]);

if ($fila = $stmt->fetch()) {
    $nombre = $fila[0]; 
    $url = $fila[1];
    $idUsuario = $fila[2];
}

if ($idUsuario != $_SESSION["id"] && $_SESSION["rol"] != "admin") {
    header("Location: /index.php");
}

?>

<div class="container">
    <div class="card rounded my-5 principal-bg neumorphism border-none">
        <div class="card-header">
            <h2>Eliminar imagen</h2>
        </div>
        <div class="card-body">
            <p>¿Desea eliminar la siguiente imagen?</p>
            <form name="fIlustracionEliminar" method="post">
                <input type="hidden" name="idImagen" value=<?=$idImagen?>>

                <?php
                    $errores = false;
                ?> 

                <div class="row justify-content-center mt-5">
                    <div class="col-md-6 d-flex flex-column align-items-center">
                        <!-- Campo ilustración -->
                        <div class="mb-3 text-center">
                            <h4><?=$nombre?></h4>
                            <div class="image-container rounded" style="width: 100%; height: 350px; display: flex; justify-content: center; align-items: center; overflow: hidden;">    
                                <img id="preview" src="<?=$url?>" alt="Ilustración a eliminar" class="img-fluid rounded" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                </div>    

                <!-- Checkbox de confirmación -->
                <div class="row justify-content-center mt-5">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="eliminar" name="eliminar">
                                <label class="form-check-label" for="eliminar">Confirmo que deseo eliminar la imagen</label>
                            </div>
                        </div>
                        <?php if (isset($_POST["enviar"]) && !isset($_POST["eliminar"])) { $errores |= validarCheckbox("eliminar"); } ?>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-6 d-flex flex-column align-items-center">
                        <div class="my-3 text-center">
                            <a class="btn btn-secondary" href="/index.php" style="text-decoration:none; color:white">Cancelar</a>
                            <button type="submit" class="btn btn-danger ms-2" name="enviar">Eliminar imagen</button>
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

            $stmt = $con->prepare(
                "DELETE FROM likes WHERE id_imagen = :idImagen"
            );

            $stmt->execute([":idImagen" => $idImagen ]);

            if (file_exists($url)) {
                if ($url != "../imagenes/logos/image-fill.png") {
                    unlink($url);
                }
            }
            
            $stmt = $con->prepare(
                "DELETE FROM imagen WHERE id = :id"
            );

            $stmt->execute([":id" => $idImagen ]);

            header("Location: ../usuarios/perfil.php?mensaje=ilustracion_eliminada&idUsuario=$idUsuario");

        }
    }
    
include ("../plantillas/footer.php");

ob_end_flush();

?>
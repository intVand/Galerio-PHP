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
    "SELECT nombre, descripcion, url, id_categoria, id_usuario FROM imagen WHERE id = :id"
);

$stmt->execute(["id" => $idImagen]);

if ($fila = $stmt->fetch()) {
    $nombre = $fila[0];
    $descripcion = $fila[1];
    $url = $fila[2];
    $idCategoria = $fila[3];
    $idUsuario = $fila[4];
}

if ($idUsuario != $_SESSION["id"] && $_SESSION["rol"] != "admin") {
    header("Location: /index.php");
}

?>

<div class="container">
    <div class="card rounded my-5 principal-bg neumorphism border-none">
        <div class="card-header">
            <h2>Editar imagen</h2>
        </div>
        <div class="card-body">
            <p>A continuación se le presenta un formulario para que pueda editar los datos sobre la imagen que desea.</p>
            <form name="fIlustracionEditar" method="post" enctype="multipart/form-data">
                <input type="hidden" name="idImagen" value=<?=$idImagen?>>

                <?php
                    $errores = false;
                ?> 

                <div class="row justify-content-center mt-5">
                    <div class="col-md-6 d-flex flex-column align-items-center">
                        <!-- Campo ilustración -->
                        <div class="mb-3 text-center">
                            <h4><label for="imagen" class="form-label">Ilustración a subir:</label></h4>
                            <div class="image-container rounded" style="width: 100%; height: 350px; display: flex; justify-content: center; align-items: center; overflow: hidden;">    
                                <img id="preview" src="<?=$url?>" alt="Ilustración subida" class="img-fluid rounded" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                            </div>
                            <input type="file" name="imagen" id="imagen" class="form-control mt-4" onchange="previewImage(event)">
                            <?php if (isset($_POST["enviar"]) && !empty($_FILES["imagen"]["tmp_name"])) { $errores |= validarImagen($_FILES["imagen"]); } ?>
                        </div>
                    </div>
                </div>    

                <div class="row justify-content-center mt-5">
                    <!-- Campo Nombre -->
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" maxlength="30" value="<?=$nombre?>">
                        </div>
                        <?php if (isset($_POST["enviar"])) { $errores |= validarCampoVacio($_POST["nombre"], "Nombre"); } ?>
                    </div>
                    <!-- Campo Categoría -->
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="categoria" class="form-label">Categoría:</label>
                            <select name="categoria" class="form-select" id="categoria">
                                <option value=0>Sin categoría</option>

                                <?php
                                    $stmt = $con->prepare(
                                        "SELECT nombre, id FROM categoria"        
                                    );

                                    $stmt->execute();

                                    if ($stmt->rowCount() > 0) {
                                        while ($fila = $stmt->fetch()) {
                                            $selected = $fila[1] == $idCategoria ? "selected" : "";
                                            echo "<option value={$fila[1]} $selected>{$fila[0]}</option>";
                                        }
                                    }
                                ?>

                            </select>    
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <!-- Campo Descripción -->
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción:</label> 
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" maxlength="200"><?=$descripcion?></textarea>
                        </div>
                        <?php if (isset($_POST["enviar"])) { $errores |= validarCampoVacio($_POST["descripcion"], "Descripción"); } ?>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-6 d-flex flex-column align-items-center">
                        <div class="my-3 text-center">
                            <a class="btn btn-secondary" href="/index.php" style="text-decoration:none; color:white">Cancelar</a>
                            <button type="submit" class="btn btn-primary ms-2" name="enviar">Editar imagen</button>
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

            $nombre = $_POST["nombre"];
            $descripcion = $_POST["descripcion"];
            
            if ($_POST["categoria"] == 0) {
                $idCategoria = null;
            }
            else {
                $idCategoria = $_POST["categoria"];
            }
            
            if (!empty($_FILES["imagen"]["tmp_name"])) {
                    
                $imagenTmp = $_FILES["imagen"]["tmp_name"];
                $imagenName = $_FILES["imagen"]["name"]; 

                $extensionArch = pathinfo($imagenName, PATHINFO_EXTENSION);
                $nuevoNombre = $idImagen . "." . $extensionArch;
                $destino = "../imagenes/ilustraciones/" . $nuevoNombre;

                if (file_exists($url)) {
                    unlink($url);
                }

                if (move_uploaded_file($imagenTmp, $destino)) {
                    $imagenFinal = $destino;
                }
                else {
                    echo "<p style='color:red'>Error al subir la imagen. Se ha establecido una imagen por defecto.</p>";
                    $imagenFinal = $url;
                }
            }
            else {
                $imagenFinal = $url;
            }

            $stmt = $con->prepare(
                "UPDATE imagen SET nombre = :nombre, descripcion = :descripcion, id_categoria = :id_categoria, url = :url WHERE id = :id"
            );

            $fila = $stmt->execute(
                [
                    ":nombre" => $nombre,
                    ":descripcion" => $descripcion,
                    ":id_categoria" => $idCategoria,
                    ":url" => $imagenFinal,
                    ":id" => $idImagen
                ]
            );

            if ($fila == 1) {header("Location: ilustracionVer.php?mensaje=ilustracion_modificada&idImagen=$idImagen");}

        }
    }
    
include ("../plantillas/footer.php");

ob_end_flush();

?>
<?php

ob_start(); // Utilizo la salida del buffer para evitar problemas con los header ();

include ("../plantillas/header.php");
include ("../utilidades/seguridad.php");
include ("../utilidades/mensajes.php");
include ("../utilidades/validaciones.php");

soloRegistrados();
soloAdmin();

?>

<div class="container">

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

    <div class="card rounded my-5 principal-bg neumorphism border-none">
        <div class="card-header">
            <h2>Creación de categorías</h2>
        </div>
        <div class="card-body">
            <p>Rellene los siguientes campos con los datos que se le piden a continuación:</p>
            <form name="fCategoriaCrear" method="post">

                <?php
                    $errores = false;
                ?> 

                <!-- Campo categoría -->
                <div class="row">
                    <div class="col-md-7">
                        <div class="mb-3">
                            <div class="form-group mb-3 col-md-6">
                                <label for="categoria">Nombre de la categoría:</label>
                                <input type="text" class="form-control mt-2" id="categoria" name="categoria" maxlength="30">
                            </div>
                        </div>
                        <?php if (isset($_POST["enviar"])) { $errores |= validarCategoria($_POST["categoria"], $con); } ?>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-6 d-flex flex-column align-items-center">
                        <div class="my-3 text-center">
                            <a class="btn btn-secondary" href="../usuarios/administrar.php" style="text-decoration:none; color:white">Volver</a>
                            <button type="submit" class="btn btn-primary ms-2" name="enviar">Crear categoría</button>
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

            $nombreCat = $_POST["categoria"];

            $stmt = $con->prepare(
                "INSERT INTO categoria (nombre) VALUES (:nombre)"
            );

            $fila = $stmt->execute([":nombre" => $nombreCat]);

            if ($fila == 1) {
                header ("Location: categoriaCrear.php?mensaje=categoria_creada");
            }
        }
    }                

include ("../plantillas/footer.php");

ob_end_flush();

?>
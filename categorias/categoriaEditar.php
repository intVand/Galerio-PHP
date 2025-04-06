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
            <h2>Edición de categorías</h2>
        </div>
        <div class="card-body">
            <p>Seleccione a continuación la categoría a modificar y escriba el nuevo nombre para la misma:</p>
            <form name="fCategoriaEditar" method="post">

                <?php

                    $errores = false;

                    $stmt = $con->prepare(
                        "SELECT nombre, id FROM categoria"
                    );

                    $stmt->execute();

                    if ($stmt->rowCount() > 0) {

                        // Campo categorías ya creadas
                        echo "<div class='row'>";
                        echo "  <div class='col-md-7'>";
                        echo "      <div class='mb-3'>";
                        echo "          <div class='form-group mb-3 col-md-6'>";
                        echo "              <label for='categoriaEd'>Seleccione la categoría a modificar:</label>";
                        echo "              <select name='categoriaEd' class='form-select mt-2' id='categoriaEd'>";

                        while ($fila = $stmt->fetch()) {
                            echo "<option value={$fila[1]}>{$fila[0]}</option>";
                        }

                        echo "              </select>";
                        echo "          </div>";
                        echo "      </div>";
                        echo "  </div>";
                        echo "</div>";

                        // Campo categoría a modificar
                        echo "<div class='row'>";
                        echo "  <div class='col-md-7'>";
                        echo "      <div class='mb-3'>";
                        echo "          <div class='form-group mb-3 col-md-6'>";
                        echo "              <label for='categoria'>Nuevo nombre de la categoría:</label>";
                        echo "              <input type='text' class='form-control mt-2' id='categoria' name='categoria' maxlength='30'>";
                        echo "          </div>";
                        echo "      </div>";
                                    if (isset($_POST["enviar"])) { $errores |= validarCategoria($_POST["categoria"], $con); }
                        echo "  </div>";
                        echo "</div>";

                    }
                    else {
                        $errores = true;
                        mostrarMensaje("categoria_inexistente");
                    }

                ?> 

                <div class="row justify-content-center">
                    <div class="col-md-6 d-flex flex-column align-items-center">
                        <div class="my-3 text-center">
                            <a class="btn btn-secondary" href="../usuarios/administrar.php" style="text-decoration:none; color:white">Volver</a>
                            <button type="submit" class="btn btn-primary ms-2" name="enviar">Editar categoría</button>
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

            $nombreCat = $_POST["categoriaEd"];
            $nuevoNombreCat = $_POST["categoria"];

            $stmt = $con->prepare(
                "UPDATE categoria SET nombre = :nombre WHERE id = :id"
            );

            $fila = $stmt->execute(
                [
                    ":nombre" => $nuevoNombreCat,
                    ":id" => $nombreCat
                ]
            );

            if ($fila == 1) {
                header ("Location: categoriaEditar.php?mensaje=categoria_modificada");
            }
        }
    }                

include ("../plantillas/footer.php");

ob_end_flush();

?>
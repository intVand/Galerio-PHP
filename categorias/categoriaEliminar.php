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
            <h2>Eliminación de categorías</h2>
        </div>
        <div class="card-body">
            <p>Seleccione a continuación la categoría a eliminar y escriba la frase correspondiente:</p>
            <form name="fCategoriaEliminar" method="post">

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
                        echo "              <label for='categoriaEd'>Seleccione la categoría a eliminar:</label>";
                        echo "              <select name='categoriaEd' class='form-select mt-2' id='categoriaEd'>";

                        while ($fila = $stmt->fetch()) {
                            echo "<option value={$fila[1]}>{$fila[0]}</option>";
                        }

                        echo "              </select>";
                        echo "          </div>";
                        echo "      </div>";
                        echo "  </div>";
                        echo "</div>";

                        // Campo categoría a eliminar
                        echo "<div class='row'>";
                        echo "  <div class='col-md-7'>";
                        echo "      <div class='mb-3'>";
                        echo "          <div class='form-group mb-3 col-md-6'>";
                        echo "              <label for='confirmacion'>Escriba la siguiente frase: <b>eLiMiNaR</b></label>";
                        echo "              <input type='text' class='form-control mt-2' id='confirmacion' name='confirmacion'>";
                        echo "          </div>";
                        echo "      </div>";
                                    if (isset($_POST["enviar"])) { $errores |= validarConfirmacion($_POST["confirmacion"]); } 
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
                            <button type="submit" class="btn btn-danger ms-2" name="enviar">Eliminar categoría</button>
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

            $categoria = $_POST["categoriaEd"];


            // Elimino la categoría de todas las imágenes asociadas
            $stmt = $con->prepare(
                "UPDATE imagen SET id_categoria = null WHERE id_categoria = :id_categoria"
            );

            $stmt->execute([":id_categoria" => $categoria]);


            // Elimino la categoría
            $stmt = $con->prepare(
                "DELETE FROM categoria WHERE id = :id"
            );

            $fila = $stmt->execute([":id" => $categoria]);
               
            if ($fila == 1) {
                header ("Location: categoriaEliminar.php?mensaje=categoria_eliminada");
            }
        }
    }                

include ("../plantillas/footer.php");

ob_end_flush();

?>
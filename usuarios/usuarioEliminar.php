<?php

ob_start(); // Utilizo la salida del buffer para evitar problemas con los header ();

include ("../plantillas/header.php");
include ("../utilidades/seguridad.php");
include ("../utilidades/validaciones.php");

soloRegistrados();

?>

<div class="container">
    <div class="card rounded my-5 principal-bg neumorphism border-none">
        <div class="card-header">
            <h2>Eliminación de cuenta</h2>
        </div>
        <div class="card-body">
            <h4>¡Información importante!</h4>
            <p>La eliminación de su cuenta supone la perdida de toda información relacionada a la misma, es importante que tenga en cuenta, y que marque los siguientes puntos para proceder con la eliminación de la cuenta:</p>
            <form name="fUsuarioEliminar" method="post">

                <?php
                    $errores = false;
                ?> 

                <!-- Checkbox de confirmación -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="perfil" name="perfil">
                                <label class="form-check-label" for="perfil">Confirmo que deseo eliminar mi perfil</label>
                            </div>
                        </div>
                        <?php if (isset($_POST["enviar"]) && !isset($_POST["perfil"])) { $errores |= validarCheckbox("perfil"); } ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="imagenes" name="imagenes">
                                <label class="form-check-label" for="imagenes">Confirmo que deseo eliminar todas mis imágenes</label>
                            </div>
                        </div>
                        <?php if (isset($_POST["enviar"]) && !isset($_POST["imagenes"])) { $errores |= validarCheckbox("imagenes"); } ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="likes" name="likes">
                                <label class="form-check-label" for="likes">Confirmo que deseo eliminar todos mis likes</label>
                            </div>
                        </div>
                        <?php if (isset($_POST["enviar"]) && !isset($_POST["likes"])) { $errores |= validarCheckbox("likes"); } ?>
                    </div>
                </div>

                <!-- Frase de confirmación -->
                <div class="row mt-2">
                    <div class="col-md-7">
                        <div class="mb-3">
                            <div class="form-group mb-3 col-md-6">
                                <label for="confirmacion" class="mb-2">Escriba la siguiente frase: <b>eLiMiNaR</b></label>
                                <input type="text" class="form-control" id="confirmacion" name="confirmacion">
                            </div>
                        </div>
                        <?php if (isset($_POST["enviar"])) { $errores |= validarConfirmacion($_POST["confirmacion"]); } ?>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-6 d-flex flex-column align-items-center">
                        <div class="my-3 text-center">
                            <a class="btn btn-secondary" href="perfil.php" style="text-decoration:none; color:white">Cancelar</a>
                            <button type="submit" class="btn btn-danger ms-2" name="enviar">Eliminar cuenta</button>
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

            $id = $_SESSION["id"];

            // Eliminar los likes de las imagenes del usuario
            $stmt = $con->prepare(
                "DELETE FROM likes WHERE id_imagen IN (SELECT id FROM imagen WHERE id_usuario = :id)"
            );

            $stmt->execute([":id" => $id ]);

            // Eliminar los likes dados por el usuario
            $stmt = $con->prepare(
                "DELETE FROM likes WHERE id_usuario = :id"
            );

            $stmt->execute([":id" => $id ]);

            // Eliminar todas las imagenes subidas por el usuario
            $stmt = $con->prepare (
                "SELECT url FROM imagen WHERE id_usuario = :id"
            );

            $stmt->execute([":id" => $id ]);

            $imagenes = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($imagenes as $urlImagen) {
                if (file_exists($urlImagen)) {
                    if ($urlImagen != "../imagenes/logos/image-fill.png") {
                        unlink($urlImagen);
                    }
                }
            }

            $stmt = $con->prepare(
                "DELETE FROM imagen WHERE id_usuario = :id"
            );

            $stmt->execute([":id" => $id ]);

            // Eliminar los datos del usuario
            if (file_exists($_SESSION["img_perfil"])) {
                if ($_SESSION["img_perfil"] != "../imagenes/logos/person.jpg") {
                    unlink($_SESSION["img_perfil"]);
                }
            }

            $stmt = $con->prepare(
                "DELETE FROM usuario WHERE id = :id"
            );

            $stmt->execute([":id" => $id ]);

            header("Location: ../utilidades/seguridad.php?mensaje=cuenta_eliminada&accion=cerrar_sesion");
            
        }
    }
    
    include ("../plantillas/footer.php");

    ob_end_flush();

?>



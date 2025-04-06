<?php

include ("plantillas/header.php");
include ("ilustraciones/ilustracion.php");
include ("ilustraciones/ilustracionTarjeta.php");


$pagina = 1;
$resultadosPP = 4;

// Ilustraciones con más Likes
$imagenesGustadas = obtenerIlustraciones($con, $pagina, $resultadosPP, "likes", "DESC");

// Ilustraciones más recientes
$imagenesRecientes = obtenerIlustraciones($con, $pagina, $resultadosPP, "fecha", "DESC");

// Ilustraciones del artista más valorado

$stmt = $con->prepare(
    "SELECT img.id_usuario, usr.nombre
    FROM imagen img
    JOIN usuario usr ON img.id_usuario = usr.id
    WHERE img.id = (SELECT id_imagen FROM likes GROUP BY id_imagen ORDER BY COUNT(*) DESC LIMIT 1)"
);

$stmt->execute();

$artistaValorado = $stmt->fetch(PDO::FETCH_ASSOC);
$artistaValoradoId = $artistaValorado["id_usuario"];
$artistaValoradoNombre = $artistaValorado["nombre"];

$ImagenesArtista = obtenerIlustraciones($con, $pagina, $resultadosPP, "likes", "DESC", "id_usuario", $artistaValoradoId);

?> 

<div class="container">

    <div class="row justify-content-center mt-5 mb-1 rounded shadow p-5 gradient-bg text-center">     
        <div class="col-md-5">
            <div class="mb-3">
                <h1>¡Bienvenid@ a Galerio!</h1>
                <p>Observa, sube o valora ilustraciones.</p>
            </div>
        </div>
    </div>

    <div class="row justify-content-left mt-5 mb-4">     
        <div class="col-md-4 rounded principal-bg neumorphism border-none p-4">
            <div class="mb-3">
                <h3>Ilustraciones más gustadas</h3>
                <p>Estas son las 4 ilustraciones más valoradas de Galerio.</p>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4">
        <?php
            foreach ($imagenesGustadas as $imagen) {
                echo "<div class='col d-flex justify-content-center mt-3 mb-5'>";
                generarTarjetaIlustracion($con, $imagen);
                echo "</div>";
            }
        ?>
    </div>

    <div class="row justify-content-left mt-5 mb-4">     
        <div class="col-md-4 rounded principal-bg neumorphism border-none p-4">
            <div class="mb-3">
                <h3>Ilustraciones más recientes</h3>
                <p>Estas son las 4 ilustraciones más recientes que se han subido a Galerio.</p>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4">
        <?php
            foreach ($imagenesRecientes as $imagen) {
                echo "<div class='col d-flex justify-content-center mt-3 mb-5'>";
                generarTarjetaIlustracion($con, $imagen);
                echo "</div>";
            }
        ?>
    </div>

    <div class="row justify-content-left mt-5 mb-4">     
        <div class="col-md-4 rounded principal-bg neumorphism border-none p-4">
            <div class="mb-3">
                <h3>Artista más valorado</h3>
                <p><?=$artistaValoradoNombre?> es el usuario con mejor valoración de Galerio, se mostrarán las 4 ilustraciones con más valoración del usuario.</p>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 mb-4">
        <?php
            foreach ($ImagenesArtista as $imagen) {
                echo "<div class='col d-flex justify-content-center mt-3 mb-5'>";
                generarTarjetaIlustracion($con, $imagen);
                echo "</div>";
            }
        ?>
    </div>
    
</div>

<?php

include ("plantillas/footer.php");

?>
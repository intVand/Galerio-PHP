<?php

include ("../plantillas/header.php");
include ("../utilidades/mensajes.php");
include ("../ilustraciones/ilustracion.php");
include ("../ilustraciones/ilustracionTarjeta.php");

if (isset($_GET["idUsuario"])) {
    $idUsuario = $_GET["idUsuario"];
}
else {
    $idUsuario = $_SESSION["id"];
}

$stmt = $con->prepare(
    "SELECT nombre, apellido1, apellido2, img_perfil, email FROM usuario WHERE id = :id"
);

$stmt->execute([":id" => $idUsuario]);

if ($dato = $stmt->fetch()) {
    $nombre = $dato[0];
    $apellido1 = $dato[1];
    $apellido2 = $dato[2];
    $img_perfil = $dato[3];
    $email = $dato[4];
}

$paginaL = 1;
$resultadosPPL = 4;

// Ilustraciones con más Likes
$ImagenesArtista = obtenerIlustraciones($con, $paginaL, $resultadosPPL, "likes", "DESC", "id_usuario", $idUsuario);

// Todas las ilustraciones del usuario
// Paginación
if (isset($_GET["pagina"])) {
    $pagina = (int)$_GET["pagina"];
}
else {
    $pagina = 1;
}

// Limita el número de resultados por página
$resultadosPP = 12;

// Ordenación
$orden = "fecha";

if (isset($_GET["asc"])) {
    $tipoOrden = "ASC";
}
else if (isset($_GET["desc"])) {
    $tipoOrden = "DESC";
}
else if (isset($_GET["tipoOrden"])) {
    $tipoOrden = $_GET["tipoOrden"];
}
else {
    $tipoOrden = "DESC";
}

// Obtener el número total de ilustraciones
$stmt = $con->prepare("SELECT COUNT(*) AS total FROM imagen WHERE id_usuario = :idUsuario");
$stmt->execute([":idUsuario" => $idUsuario]);
$fila = $stmt->fetch(PDO::FETCH_ASSOC);
$totalIlu = $fila["total"];

// Calcula el número total de páginas
$totalPag = ceil($totalIlu / $resultadosPP);

$todasLasImagenes = obtenerIlustraciones($con, $pagina, $resultadosPP, $orden, $tipoOrden, "id_usuario", $idUsuario);

?>

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

<div class="container">

    <div class="row justify-content-left">         
        <div class="card text-center w-100">
            <div class="card-body">
                <img src="<?= $img_perfil ?>" class="rounded-circle mb-3" alt="Imagen de perfil" width="150px" height="150px">
                <h5 class="card-title"><?= $nombre . " " . $apellido1 . " " . $apellido2?></h5>
                <p class="text-secondary small"> <i class="bi bi-envelope-fill"></i> <?=$email?></p>
                <div class="my-3">

                    <?php
                    if (isset($_SESSION["id"]) && $idUsuario == $_SESSION["id"]) {
                        echo "<a href='usuarioEditar.php' class='btn btn-outline-primary me-1'><i class='bi bi-pencil-fill me-2'></i>Editar Perfil</a>";
                        echo "<a href='usuarioEliminar.php' class='btn btn-outline-danger ms-1'><i class='bi bi-trash-fill me-2'></i>Eliminar Cuenta</a>";
                    }
                    ?>
                    
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-left mt-5 mb-4">     
        <div class="col-md-5 rounded principal-bg neumorphism border-none p-4">
            <div class="mb-3">
                <h3>Ilustraciones con más likes de <?=$nombre?></h3>
                <p>Estas son las 4 ilustraciones más valoradas de <?=$nombre?>.</p>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4">
        <?php
            foreach ($ImagenesArtista as $imagen) {
                echo "<div class='col d-flex justify-content-center mt-3 mb-5'>";
                generarTarjetaIlustracion($con, $imagen);
                echo "</div>";
            }
        ?>
    </div>

    <div class="row justify-content-left mt-5 mb-4">     
        <div class="col-md-5 rounded principal-bg neumorphism border-none p-4">
            <div class="mb-3">
                <h3>Todas las ilustraciones de <?=$nombre?></h3>
                <p>Estas son todas las ilustraciones de <?=$nombre?>.</p>
            
                <a href="perfil.php?asc=asc&idUsuario=<?=$idUsuario?>"class="me-2"><button class="btn btn-outline-secondary btn-sm">Más antiguas</button></a>
                <a href="perfil.php?desc=desc&idUsuario=<?=$idUsuario?>"><button class="btn btn-outline-secondary btn-sm">Más recientes</button></a>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4">
        <?php
            foreach ($todasLasImagenes as $imagen) {
                echo "<div class='col d-flex justify-content-center mt-3 mb-5'>";
                generarTarjetaIlustracion($con, $imagen);
                echo "</div>";
            }
        ?>
    </div>

    <div class="row justify-content-center my-5">     
        <div class="col-md-4">
            <div class="mb-3">
                <ul class="pagination pagination-md justify-content-center">

                <?php
                    for ($i = 1; $i <= $totalPag; $i++) {
                        if ($i == $pagina) {
                            echo '<li class="page-item active" aria-current="page"><span class="page-link">' . $i . '</span></li>';
                        } else {
                            echo '<li class="page-item"><a class="page-link" href="perfil.php?pagina=' . $i . '&tipoOrden=' . $tipoOrden . '&idUsuario=' . $idUsuario . '">' . $i . '</a></li>';
                        }
                    }
                ?>

                </ul>
            </div>
        </div>
    </div>

</div>

<?php

include ("../plantillas/footer.php");

?>
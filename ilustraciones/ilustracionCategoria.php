<?php

include ("../plantillas/header.php");
include ("ilustracion.php");
include ("ilustracionTarjeta.php");


if (isset($_GET["id"])) {
    $idCategoria = $_GET["id"];

    $stmt = $con->prepare(
        "SELECT nombre FROM categoria WHERE id = :id"
    );

    $stmt->execute([":id" => $idCategoria]);

    if ($fila = $stmt->fetch()) {
        $nombreCategoria = $fila[0];
    }
}

// Paginación
if (isset($_GET["pagina"])) {
    $pagina = (int)$_GET["pagina"];
}
else {
    $pagina = 1;
}

// Limita el número de resultados por página
$resultadosPP = 16;

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
$stmt = $con->prepare("SELECT COUNT(*) AS total FROM imagen WHERE id_categoria = :idCategoria");
$stmt->execute([":idCategoria" => $idCategoria]);
$fila = $stmt->fetch(PDO::FETCH_ASSOC);
$totalIlu = $fila["total"];

// Calcula el número total de páginas
$totalPag = ceil($totalIlu / $resultadosPP);

$idCategoria = (int)$idCategoria;
$idCategoriaFiltro = "%,$idCategoria,%";
$todasLasImagenes = obtenerIlustraciones($con, $pagina, $resultadosPP, $orden, $tipoOrden, "CONCAT(',', imagen.id_categoria, ',')", $idCategoriaFiltro);

?> 

<div class='container'>

    <div class="row justify-content-left mt-5 mb-1">     
        <div class="col-md-4 rounded principal-bg neumorphism border-none p-4">
            <div class="mb-3">
                <h3><?=$nombreCategoria?></h3>
                <p>Se muestran todas las imágenes disponibles en la web, que coincidan con la categoría <?=$nombreCategoria?>.</p>

                <a href="ilustracionCategoria.php?asc=asc&id=<?=$idCategoria?>"class="me-2"><button class="btn btn-outline-secondary btn-sm">Más antiguas</button></a>
                <a href="ilustracionCategoria.php?desc=desc&id=<?=$idCategoria?>"><button class="btn btn-outline-secondary btn-sm">Más recientes</button></a>
            </div>
        </div>
    </div>

    <div class='row row-cols-1 row-cols-md-3 row-cols-lg-4'>
    <?php   
        foreach ($todasLasImagenes as $imagen) {
            echo "<div class='col d-flex justify-content-center mt-5'>";
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
                            echo '<li class="page-item"><a class="page-link" href="ilustracionCategoria.php?pagina=' . $i . '&tipoOrden=' . $tipoOrden . '&id=' . $idCategoria . '">' . $i . '</a></li>';
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
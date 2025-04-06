<?php

include ("../plantillas/header.php");
include ("ilustracion.php");
include ("ilustracionTarjeta.php");
include ("../utilidades/mensajes.php");


$encontrado = false;

if (isset($_GET["ilustracionBus"])) {
    $nombreIlustracion = $_GET["ilustracionBus"];
}

$stmt = $con->prepare(
    "SELECT id FROM imagen WHERE nombre LIKE :nombre"
);

if ($nombreIlustracion != "" && $nombreIlustracion != " ") {

    $referenciaIlustracion = "%" . $nombreIlustracion . "%";
    $stmt->bindParam(":nombre", $referenciaIlustracion, PDO::PARAM_STR);

    $stmt->execute();

    if ($stmt->fetch() > 0) {
        $encontrado = true;
    }
    else {
        echo "<div class='container my-5'>";
        echo "  <div class='row justify-content-center'>";
        echo "      <div class='col-md-8'>";
                        mostrarMensaje("ilustracion_inexistente");
        echo "      </div>";
        echo "  </div>";
        echo "</div>";
    }
}
else {
    echo "<div class='container my-5'>";
    echo "  <div class='row justify-content-center'>";
    echo "      <div class='col-md-8'>";
                    mostrarMensaje("ilustracion_inexistente");
    echo "      </div>";
    echo "  </div>";
    echo "</div>";
}

if ($encontrado) {

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
$stmt = $con->prepare("SELECT COUNT(*) AS total FROM imagen WHERE nombre LIKE '%' :nombre '%'");
$stmt->execute([":nombre" => $nombreIlustracion]);
$fila = $stmt->fetch(PDO::FETCH_ASSOC);
$totalIlu = $fila["total"];

// Calcula el número total de páginas
$totalPag = ceil($totalIlu / $resultadosPP);

$todasLasImagenes = obtenerIlustraciones($con, $pagina, $resultadosPP, $orden, $tipoOrden, "imagen.nombre", $nombreIlustracion);

?> 

<div class='container'>

    <div class="row justify-content-left mt-5 mb-1">     
        <div class="col-md-4 rounded principal-bg neumorphism border-none p-4">
            <div class="mb-3">
                <h3>Búsqueda de imágenes</h3>
                <p>Se muestran todas las imágenes disponibles en la web, que coincidan con la búsqueda realizada.</p>

                <a href="ilustracionBuscar.php?asc=asc&ilustracionBus=<?=$nombreIlustracion?>"class="me-2"><button class="btn btn-outline-secondary btn-sm">Más antiguas</button></a>
                <a href="ilustracionBuscar.php?desc=desc&ilustracionBus=<?=$nombreIlustracion?>"><button class="btn btn-outline-secondary btn-sm">Más recientes</button></a>
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
                            echo '<li class="page-item"><a class="page-link" href="ilustracionBuscar.php?pagina=' . $i . '&tipoOrden=' . $tipoOrden . '&ilustracionBus=' . $nombreIlustracion . '">' . $i . '</a></li>';
                        }
                    }
                ?>

                </ul>
            </div>
        </div>
    </div>

</div>

<?php

}

include ("../plantillas/footer.php");

?>
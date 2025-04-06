<?php

include ("../plantillas/header.php");
include ("../utilidades/seguridad.php");
include ("../utilidades/mensajes.php");
include ("usuario.php");

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
            <h2>Visualizador de usuarios</h2>
        </div>
        <div class="card-body">
            <div class="container-fluid my-3">
                <div class="row justify-content-end">
                    <div class="col-md-3">
                        <form class="d-flex" method="get" action="usuarioBuscar.php">
                            <input class="form-control me-2" type="search" placeholder="Buscar usuario" aria-label="Search" name="usuario">
                            <button class="btn btn-outline-dark" type="submit">Buscar</button>
                        </form>
                    </div>
                </div>
            </div>

            <table class="table table-striped text-center">
                        
                <tr>
                    <th class="align-middle"><b>ID</b></th>
                    <th class="align-middle"><b>Nombre</b></th>
                    <th class="align-middle"><b>Apellidos</b></th>
                    <th class="align-middle"><b>Email</b></th>
                    <th class="align-middle"><b>Rol</b></th>
                    <th class="align-middle"><b>Estado</b></th>
                    <th class="align-middle"><b>Ver perfil</b></th>
                    <th class="align-middle"><b>Eliminar</b></th>
                </tr>    

<?php

    // Paginación
    if (isset($_GET["pagina"])) {
        $pagina = (int)$_GET["pagina"];
    }
    else {
        $pagina = 1;
    }

    // Limita el número de resultados por página
    $resultadosPP = 3;

    // Ordenación
    $orden = "Nombre";

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
        $tipoOrden = "ASC";
    }


    $usuariosObt = obtenerUsuarios($con, $pagina, $resultadosPP, $orden, $tipoOrden);

    foreach ($usuariosObt as $usuario) {
        echo "<tr>";
        echo "<td>{$usuario->getID()}</td>";
        echo "<td>{$usuario->getNombre()}</td>";
        echo "<td>{$usuario->getApellido1()} {$usuario->getApellido2()}</td>";
        echo "<td>{$usuario->getEmail()}</td>";
        echo "<td>{$usuario->getRol()}</td>";
        
        if ($usuario->getEstado() == 1) {
            echo "<td>Activo</td>";
        }
        else {
            echo "<td>Borrado</td>";
        }

        echo "<td><a href='perfil.php?idUsuario={$usuario->getID()}'><i class='bi bi-person-circle text-success fs-5'></i></a></td>";

        if ($usuario->getEstado() == 1 && $usuario->getRol() == "usuario") {
            echo "<td><a href='usuarioBanear.php?id={$usuario->getID()}'><i class='bi bi-trash-fill text-danger fs-5'></i></a></td>";
        }
        else {
            echo "<td>X</td>";
        }
        
        echo "</tr>";
    }
?>

    </table>

<?php

    // Obtener el número total de usuarios
    $stmt = $con->prepare("SELECT COUNT(*) AS total FROM usuario");
    $stmt->execute();
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalUsu = $fila["total"];

    // Calcula el número total de páginas
    $totalPag = ceil($totalUsu / $resultadosPP);

    echo '<div class="d-flex justify-content-center align-items-center">';
    echo '<nav aria-label="paginacion"><ul class="pagination pagination-md">';

    for ($i = 1; $i <= $totalPag; $i++) {
        if ($i == $pagina) {
            echo '<li class="page-item active" aria-current="page"><span class="page-link">' . $i . '</span></li>';
        } else {
            echo '<li class="page-item"><a class="page-link" href="usuarioGestionar.php?pagina=' . $i . '&tipoOrden=' . $tipoOrden . '">' . $i . '</a></li>';
        }
    }

    echo "</ul></nav>";

    echo "<br><br>";

    echo "</div>";
    echo '<div class="d-flex justify-content-center align-items-center">';

    echo '<a href="usuarioGestionar.php?asc=asc' . '" style="margin: 5px"><button class="btn btn-outline-secondary btn-sm">Orden ascendente</button></a>';
    echo '<a href="usuarioGestionar.php?desc=desc' . '" style="margin: 5px"><button class="btn btn-outline-secondary btn-sm">Orden descendente</button></a>';

    echo "<br><br>";

    echo "</div>";

?>

            <a href="administrar.php" class="btn btn-secondary">Volver</a>
        </div>        
    </div>
</div>

<?php

include ("../plantillas/footer.php");

?>
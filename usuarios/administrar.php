<?php

include ("../plantillas/header.php");
include ("../utilidades/seguridad.php");
include ("../utilidades/mensajes.php");

soloRegistrados();
soloAdmin();

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

<div class="container d-flex justify-content-center align-items-center my-5">
    <div class="card text-center w-100 principal-bg neumorphism border-none">
        <div class="card-body">
            <h2>Panel de administrador</h2>
            <br>
            <h4>Gestión de usuarios</h4>
            <a class="btn btn-primary" href="usuarioGestionar.php">Gestionar usuarios</a>
            <br><br>
            <h4>Gestión de categorías</h4>
            <a class="btn btn-primary" href="../categorias/categoriaCrear.php">Crear categoría</a>
            <a class="btn btn-warning" href="../categorias/categoriaEditar.php">Editar categoría</a>
            <a class="btn btn-danger" href="../categorias/categoriaEliminar.php">Eliminar categoría</a>
        </div>
    </div>
</div>

<?php

include ("../plantillas/footer.php");

?>
<?php

    include($_SERVER["DOCUMENT_ROOT"]."/utilidades/conectarDB.php");

    session_start();

    $stmt = $con->prepare(
        "SELECT id, nombre FROM categoria"
    );
    
    $stmt->execute();
    
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="/estilos.css">
        <link rel="icon" type="image/jpg" href="../imagenes/logos/galerio_logo.ico"/>
        <title>Galerio</title>
    </head>
    
    <body class="d-flex flex-column min-vh-100 dots-bg">

        <header class="sticky-top">
            <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow" id="nav-principal">
                <div class="container-fluid px-5">

                    <a class="navbar-brand" href="/index.php">
                        <img src='/imagenes/logos/galerio_logo.png' width='64' height='64' alt='Galerio'>
                    </a>

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">

                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="/index.php">Inicio</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/ilustraciones/ilustracionTodas.php">Imágenes</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false"> Categorías </a> 
                                <div class="dropdown-menu dropdown-menu-scrollable" aria-labelledby="navbarDropdownMenuLink"> 
                                    <?php foreach ($categorias as $categoria): ?> 
                                        <a class="dropdown-item" href="/ilustraciones/ilustracionCategoria.php?id=<?= $categoria['id'] ?>"><?= $categoria['nombre'] ?></a> 
                                    <?php endforeach; ?> 
                                </div>
                            </li>
                        </ul>

                        <div class="mx-lg-5 mb-3 mb-lg-0">
                            <?php

                                if (isset($_SESSION["id"])) {
                                    echo "<div class='dropdown'>";
                                    echo "<button class='btn btn-outline-secondary dropdown-toggle' type='button' id='profileDropdown' data-bs-toggle='dropdown' aria-expanded='false'>";
                                    echo "<img src='" . $_SESSION["img_perfil"] . "' width='30' height='30' class='rounded-circle me-2' alt='Foto de perfil'>";
                                    echo "<span class='me-2'>¡Hola " . $_SESSION["nombre"] . "!</span>";
                                    echo "</button>";
                                    echo "<ul class='dropdown-menu' aria-labelledby='profileDropdown'>";
                                    echo "<li><a class='dropdown-item' href='/usuarios/perfil.php'>Ver perfil</a></li>";

                                    if ($_SESSION["rol"] == "admin") {
                                        echo "<li><a class='dropdown-item' href='/usuarios/administrar.php'>Administrar</a></li>";
                                    }  

                                    echo "<li><a class='dropdown-item' href='/ilustraciones/ilustracionCrear.php'>Subir imagen</a></li>";
                                    echo "<li><a class='dropdown-item' href='/ilustraciones/ilustracionGuardada.php'>Likes</a></li>";
                                    echo "<li><hr class='dropdown-divider'></li>";
                                    echo "<li><a class='dropdown-item text-danger fw-bold' href='../utilidades/seguridad.php?accion=cerrar_sesion'>Cerrar sesión</a></li>";
                                    echo "</ul>";
                                    echo "</div>";
                                } else {
                                    echo "<a href='/usuarios/login.php' class='btn btn-outline-dark me-2'>Iniciar sesión / Registrarse</a>";
                                }
       
                            ?>
                        </div>

                        <form class="d-flex" action="/ilustraciones/ilustracionBuscar.php" method="get">
                            <input class="form-control me-2" type="search" placeholder="Buscar imágenes" aria-label="Search" name="ilustracionBus">
                            <button class="btn btn-outline-secondary" type="submit" name="buscarIlus">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                        
                    </div>

                </div>
            </nav>
        </header>

  
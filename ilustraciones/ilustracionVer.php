<?php

ob_start(); // Utilizo la salida del buffer para evitar problemas con los header ();

include ("../plantillas/header.php");
include ("../utilidades/mensajes.php");

if (isset($_GET["idImagen"]) && $_GET["idImagen"] != "") {
    $idImagen = $_GET["idImagen"];
}
else {
    header("Location: /index.php");
}

$stmt = $con->prepare(
    "SELECT img.nombre, img.descripcion, img.url, img.id_categoria, cat.nombre, img.fecha, usu.nombre, usu.apellido1, usu.apellido2, usu.img_perfil, usu.id,(SELECT COUNT(*) FROM likes WHERE likes.id_imagen = img.id)
     FROM imagen img 
     LEFT JOIN categoria cat ON cat.id = img.id_categoria 
     JOIN usuario usu ON usu.id = img.id_usuario 
     WHERE img.id = :id"
);

$stmt->execute([":id" => $idImagen]);

if ($fila = $stmt->fetch()) {
    $nombre = $fila[0];
    $descripcion = $fila[1];
    $url = $fila[2];
    $idCategoria = $fila[3];
    $nombreCategoria = $fila[4];
    $fecha = $fila[5];
    $nombreUsuario = $fila[6];
    $apellido1 = $fila[7];
    $apellido2 = $fila[8];
    $imgPerfil = $fila[9];
    $idUsuario = $fila[10];
    $likes = $fila[11];
}

$fechaObj = new DateTime($fecha);
$fechaForm = $fechaObj->format("d-m-Y");

if ($nombreCategoria == "") {
    $nombreCategoria = "Sin categoría";
}

if (isset($_SESSION["id"])) {
    $usuarioActual = $_SESSION["id"];
    $stmt = $con->prepare("SELECT COUNT(*) FROM likes WHERE id_usuario = :idUsuario AND id_imagen = :idImagen"); 
    $stmt->execute(['idUsuario' => $usuarioActual, 'idImagen' => $idImagen]); 
    $tieneLike= $stmt->fetchColumn() > 0;
}
else {
    $usuarioActual = null;
    $tieneLike = false;
}


if ($tieneLike) {
    $accion = "quitar";
    $tipoLike = "btn-danger";
    $tipoCorazon = "bi-heart-fill";
}
else {
    $accion = "añadir";
    $tipoLike = "btn-outline-danger";
    $tipoCorazon = "bi-heart";
}

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
        <div class="card-body">
            <p class="text-center fw-bold"><?=$nombre?></p>

            <div class="row justify-content-center mt-2">
                <div class="col-md-6 d-flex flex-column align-items-center">
                    <div class="mb-3 text-center">
                        <img src="<?=$url?>" alt="<?=$nombre?>" class="img-fluid rounded" style="max-width: 100%; max-height: 400px; object-fit: contain;">

                        <a href="/usuarios/perfil.php?idUsuario=<?=$idUsuario?>" target="_blank" style="text-decoration: none; color: inherit;">
                            <div class="d-flex align-items-left mt-3">
                                <img src="<?=$imgPerfil?>" class="rounded-circle" width="30" height="30" alt="<?=$nombreUsuario?>">
                                <span class='ms-2'><?=$nombreUsuario . " " . $apellido1 . " " . $apellido2?></span>
                            </div>
                        </a>    
                    </div>
                </div>
            </div>  
            
            <div class="row mt-3">
                <div class="col-md-5 d-flex flex-column align-items-center">
                    <div class="mb-3">
                        <p>Categoria: <?=$nombreCategoria?></p>
                    </div>
                </div>
                <div class="col-md-2 d-flex flex-column align-items-center">
                    <div class="mb-3">
                        <p><?=$fechaForm?></p>
                    </div>
                </div>
                <div class="col-md-5 d-flex flex-column align-items-center">
                    <div class="mb-3">
                        
                        <?php
                            if ($usuarioActual) { 
                                echo "<a href='/ilustraciones/ilustracionLike.php?accion=$accion&idImagen=$idImagen&idUsuario=$usuarioActual' class='btn $tipoLike btn-sm'><i class='bi $tipoCorazon'></i></a> $likes Likes";
                            } 
                            else { 
                                echo "<button class='btn btn-outline-danger btn-sm' disabled><i class='bi bi-heart'></i></button> $likes Likes"; 
                            }
                        ?>

                    </div>
                </div>
            </div>

            <div class="row justify-content-center mt-2">
                <div class="col-md-6 d-flex flex-column align-items-center">
                    <div class="mb-3">
                        <p class="text-break"><?=$descripcion?></p>
                    </div>
                </div>
            </div> 
        </div>
    </div>

</div>    

<?php
    
include ("../plantillas/footer.php");

ob_end_flush();

?>
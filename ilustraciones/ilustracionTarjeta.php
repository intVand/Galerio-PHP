<?php

    // Función que se encarga de generar las tarjetas para mostrar las imágenes
    function generarTarjetaIlustracion($con, $ilustracion) {

    $id = $ilustracion->getID();
    $nombre = $ilustracion->getNombre();
    $descripcion = $ilustracion->getDescripcion();
    $url = $ilustracion->getURL();
    $likes = $ilustracion->getLikes();
    $fecha = $ilustracion->getFecha();


    if (isset($_SESSION["id"])) {
        $usuarioActual = $_SESSION["id"];
        $stmt = $con->prepare("SELECT COUNT(*) FROM likes WHERE id_usuario = :idUsuario AND id_imagen = :idImagen"); 
        $stmt->execute(['idUsuario' => $usuarioActual, 'idImagen' => $id]); 
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


    if (isset($_SESSION["rol"]) && $_SESSION["rol"] == "admin") {
        $esAdmin = true;
    }
    else {
        $esAdmin = false;
    }


    $stmt = $con->prepare(
        "SELECT imagen.id_usuario, usuario.img_perfil, usuario.nombre, usuario.apellido1, usuario.apellido2 FROM imagen JOIN usuario ON imagen.id_usuario = usuario.id WHERE imagen.id = :idImagen"
    );

    $stmt->execute(["idImagen" => $id]);

    if ($fila = $stmt->fetch()) {
        $idUsuario = $fila[0];
        $imgPerfil = $fila[1];
        $nombreUsuario = $fila[2];
        $apellido1 = $fila[3];
        $apellido2 = $fila[4];
    }

    $nombreCompleto = $nombreUsuario . " " . substr($apellido1, 0, 1) . ". " . substr($apellido2, 0, 1) . ".";

    if ($likes >= 1000000) {
        $likesFormato = round($likes / 1000000, 1) . "M";
    } elseif ($likes >= 1000) {
        $likesFormato = round($likes / 1000, 1) . "K";
    } else {
        $likesFormato = $likes;
    }

    $mostrarBotones = $esAdmin || ($usuarioActual == $idUsuario);

    echo "<div class='card' style='width: 18rem;'>"; 
    echo "  <div class='card-header text-center fw-bold text-truncate'>$nombre</div>"; 
    echo "      <div class='position-relative card-img-top-container'>"; 
    echo "          <a href='/ilustraciones/ilustracionVer.php?idImagen=$id'><img src='$url' class='card-img-top' alt='$nombre' style='height: 200px; object-fit: cover; border-radius: 0;'></a>";

    if ($mostrarBotones) { 
        echo "      <div class='position-absolute top-0 end-0 p-2'>"; 
        echo "          <a href='/ilustraciones/ilustracionEditar.php?idImagen=$id' class='btn btn-outline-warning btn-sm'><i class='bi bi-pencil'></i></a>"; 
        echo "          <a href='/ilustraciones/ilustracionEliminar.php?idImagen=$id' class='btn btn-outline-danger btn-sm'><i class='bi bi-trash'></i></a>"; 
        echo "      </div>"; 
    } 

    echo "      </div>"; 
    echo "      <div class='card-body'>"; 
    echo "          <div class='d-flex justify-content-between align-items-center'>";
    echo "              <a href='/usuarios/perfil.php?idUsuario=$idUsuario' target='_blank' style='text-decoration: none; color: inherit;'>"; 
    echo "                  <div class='d-flex align-items-center'>"; 
    echo "                      <img src='$imgPerfil' class='rounded-circle' width='30' height='30' alt='$nombreUsuario'>"; 
    echo "                      <span class='ms-2'>$nombreCompleto</span>"; 
    echo "                  </div>";
    echo "              </a>";
    echo "          <div>"; 

    if ($usuarioActual) { 
        echo " <a href='/ilustraciones/ilustracionLike.php?accion=$accion&idImagen=$id&idUsuario=$usuarioActual' class='btn $tipoLike btn-sm'><i class='bi $tipoCorazon'></i></a> $likesFormato";
    } 
    else { 
        echo " <button class='btn btn-outline-danger btn-sm' disabled data-bs-toggle='tooltip' data-bs-placement='top' title='Debe registrarse'><i class='bi bi-heart'></i></button> $likesFormato"; 
    }

    echo "      </div>"; 
    echo "  </div>"; 
    echo " </div>"; 
    echo "</div>";

    }

?>
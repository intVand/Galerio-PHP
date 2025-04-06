<?php

    // Se debe asignar un usuario y contraseña acordes a la configuración de la base de datos
    define ("USER","root"); 
    define ("PASSWORD",""); 
    
    try {

        $dsn = "mysql:host=localhost;dbname=galerio_db";

        $con = new PDO($dsn, USER, PASSWORD);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    } catch (PDOException $e){
        echo 'Error: '.$e->getMessage();
    }

?>
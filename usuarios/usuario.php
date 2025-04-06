<?php

    class Usuario {

        // Atributos privados de la clase para cumplir con el principio de encapsulamiento
        private $estado;
        private $id;
        private $nombre;
        private $apellido1;
        private $apellido2;
        private $email;
        private $rol;
        
    
        // Métodos getters para la obtención de cada uno de los atributos privados de la clase
        public function getEstado() {
            return $this->estado;
        }
        
        public function getID() {
            return $this->id;
        }
        
        public function getNombre() {
            return $this->nombre;
        }

        public function getApellido1() {
            return $this->apellido1;
        }

        public function getApellido2() {
            return $this->apellido2;
        }

        public function getEmail() {
            return $this->email;
        }

        public function getRol() {
            return $this->rol;
        }
        
    }

    // Función que nos devuelve un array de objetos usuarios
    function obtenerUsuarios($con, $pagina, $resultadosPP, $orden, $tipoOrden, $nombreUsuario = null) {

        $inicio = ($pagina-1) * $resultadosPP;
    
        $sql = "SELECT usuario.estado, usuario.id, usuario.nombre, usuario.apellido1, usuario.apellido2, usuario.email, usuario.rol
                FROM usuario
                ";
    
        if ($nombreUsuario != null) {
            $sql .= "WHERE usuario.nombre LIKE :nombreUsuario ";
        }
    
        $sql .= "ORDER BY usuario.$orden $tipoOrden 
                 LIMIT :inicio, :resultados";
    
        $consulta = $con->prepare($sql);
    
        if ($nombreUsuario != null) {
            $nombreUsuario = "%" . $nombreUsuario . "%";
            $consulta->bindParam(":nombreUsuario", $nombreUsuario, PDO::PARAM_STR);
        }
    
        $consulta->bindParam(':inicio', $inicio, PDO::PARAM_INT);
        $consulta->bindParam(':resultados', $resultadosPP, PDO::PARAM_INT);
    
        $consulta->execute();
    
        $consulta->setFetchMode(PDO::FETCH_CLASS, 'Usuario');
        $usuarios = [];
    
        while($fila = $consulta->fetch()) {
            $usuarios[] = $fila;
        }
        return $usuarios;
    }
    
?>
<?php

    class Ilustracion {

        // Atributos privados de la clase para cumplir con el principio de encapsulamiento
        private $id;
        private $nombre;
        private $descripcion;
        private $url;
        private $fecha;
        private $id_categoria;
        private $id_usuario;
        private $likes;

         // Métodos getters para la obtención de cada uno de los atributos privados de la clase
        public function getID() {
            return $this->id;
        }

        public function getNombre() {
            return $this->nombre;
        }

        public function getDescripcion() {
            return $this->descripcion;
        }

        public function getUrl() {
            return $this->url;
        }

        public function getFecha() {
            return $this->fecha;
        }

        public function getId_categoria() {
            return $this->id_categoria;
        }

        public function getId_usuario() {
            return $this->id_usuario;
        }

        public function getLikes() {
            return $this->likes;
        }

    }

    // Función que nos devuelve un array de objetos ilustracion
    function obtenerIlustraciones($con, $pagina, $resultadosPP, $orden, $tipoOrden, $filtro = null, $valorFiltro = null, $likesUsuario = null) {

        $inicio = ($pagina - 1) * $resultadosPP; 
        
        $sql = "SELECT imagen.*, (SELECT COUNT(*) FROM likes WHERE likes.id_imagen = imagen.id) AS likes FROM imagen"; 
        
        if ($filtro != null && $valorFiltro != null) { 
            $sql .= " WHERE $filtro LIKE :valorFiltro"; 
        } 
        
        if ($likesUsuario != null) { 
            $sql .= ($filtro != null ? " AND" : " WHERE") . " imagen.id IN (SELECT id_imagen FROM likes WHERE id_usuario = :likesUsuario)"; 
        } 
        
        $sql .= " ORDER BY $orden $tipoOrden LIMIT :inicio, :resultados"; 
        $consulta = $con->prepare($sql); 
        
        if ($filtro != null && $valorFiltro != null) { 
            $valorFiltro = "%" . $valorFiltro . "%"; 
            $consulta->bindParam(":valorFiltro", $valorFiltro, PDO::PARAM_STR); 
        } 
        
        if ($likesUsuario != null) { 
            $consulta->bindParam(':likesUsuario', $likesUsuario, PDO::PARAM_INT); 
        } 
        
        $consulta->bindParam(':inicio', $inicio, PDO::PARAM_INT); 
        $consulta->bindParam(':resultados', $resultadosPP, PDO::PARAM_INT); 
        $consulta->execute(); 
        $consulta->setFetchMode(PDO::FETCH_CLASS, 'Ilustracion'); 
        $ilustracion = []; 
        while ($fila = $consulta->fetch()) { 
            $ilustracion[] = $fila; 
        } 
        
        return $ilustracion;
    }

?>
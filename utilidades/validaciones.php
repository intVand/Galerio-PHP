<?php

    // Se encarga de validar que haya datos introducidos en aquellos campos obligatorios.
    function validarCampoVacio($campo, $nombreCampo) {
        if (empty($campo)) {
            echo "<p style='color:red'>El campo $nombreCampo no puede estar vacío</p> <br>";
            return true;
        }
        return false;
    }

    // Se encarga de validar que el formato del correo electronico sea correcto.
    function validarEmail($email) { 
        if (validarCampoVacio($email, "Correo electrónico") == false) {     
            if (!preg_match("/^[A-z0-9.-]+@[A-z0-9][A-z0-9-]*(.[A-z0-9-]+)*.([A-z]{2,6})/", $email)) {
                echo "<p style='color:red'>Formato del Correo electrónico incorrecto [*@*.*]</p>";
                return true;
            }
            return false;
        }
        else {
            return true;
        }
    }

    // Se encarga de validar que la contraseña introducida sea de al menos 8 caracteres.
    function validarContrasena($contrasena) {
        if (validarCampoVacio($contrasena, "contraseña") == false) {
            if (strlen($contrasena) < 8) {
                echo "<p style='color:red'>La contraseña debe tener al menos 8 caracteres, se recomienda el uso de letras, números y símbolos.</p>";
                return true;
            }
            return false;
        }
        return true;
    }

    // Se encarga de validar que el formato del DNI sea correcto.
    function validarDNI($dni) {        
        if (!preg_match("/^[0-9]{8}[A-Z]$/", $dni)) {
            echo "<p style='color:red'>Formato de DNI incorrecto [8 números + 1 letra mayúscula]</p>";
            return true;
        }
    
        $letras = "TRWAGMYFPDXBNJZSQVHLCKE";
        $numero = substr($dni, 0, 8);
        $letra = substr($dni, -1);
        $letraCorrecta = substr($letras, $numero % 23, 1);
    
        if ($letra != $letraCorrecta) {
            echo "<p style='color:red'>La letra del DNI no corresponde con los números</p>";
            return true;
        }
        return false;        
    }

    // Se encarga de validar que el correo electronico introducido no exista ya en la base de datos.
    function validarEmailExistente($email, $con) {
        $stmt = $con->prepare("SELECT nombre, estado FROM usuario WHERE email = :email");
        $stmt->execute([":email" => $email]);

        if ($datos = $stmt->fetch()) {
            if ($datos['estado'] != 2) {
                $estado = 1;
                echo "<p style='color:red'>El correo electrónico introducido ya existe en la web</p>";
                return true;
            } else {
                $estado = 2;
            }
        }
        return false;      
    }

    // Se encarga de validar que el correo electronico introducido si exista ya en la base de datos.
    function validarEmailNoExistente($email, $con) {
        if (validarEmail($email) == false) {
            $stmt = $con->prepare("SELECT nombre, estado FROM usuario WHERE email = :email");
            $stmt->execute([":email" => $email]);

            if ($datos = $stmt->fetch()) {
                if ($datos['estado'] == 2) {
                    echo "<p style='color:red'>El correo electrónico introducido no existe en la web</p>";
                    return true;
                } else {
                    return false;
                }
            }
            else {
                echo "<p style='color:red'>El correo electrónico introducido no existe en la web</p>";
                return true;
            }
        }
        else {
            return true;
        }    
    }

    // Se encarga de validar que el dni introducido no exista ya en la base de datos.
    function validarDNIExistente($dni, $con) {        
        $stmt = $con->prepare("SELECT nombre, estado FROM usuario WHERE dni = :dni");
        $stmt->execute([":dni" => $dni]);

        if ($datos = $stmt->fetch()) {
            if ($datos['Estado'] != 2) {
                echo "<p style='color:red'>El DNI introducido ya existe en la base de datos, escoja otro diferente</p>";
                return true;
            }
        }
        return false;        
    }

    // Se encarga de validar que las imagenes subidas tengan el peso y formato correcto.
    function validarImagen($imagen) {       
        if (!empty($imagen["tmp_name"])) {
            if ($imagen["size"] > 1024000) {
                echo "<p style='color:red'>La imagen que ha subido es demasiado pesada, no debe pesar más de 1 MB</p>";
                return true;
            } else {
                $imagenTmp = $imagen["tmp_name"];
                $comprobarImg = getimagesize($imagenTmp);
    
                if ($comprobarImg !== false) {
                    $extension = $comprobarImg[2];
    
                    $comprobarE = image_type_to_extension($extension, false);
    
                    $cTipos = [".jpg", ".jpeg", ".png", ".gif"];
                    $encontrado = false;
    
                    foreach ($cTipos as $tipo) {
                        if (strpos($tipo, $comprobarE) !== false) {
                            $encontrado = true;
                            break;
                        }
                    }
    
                    if (!$encontrado) {
                        echo "<p style='color:red'>Tipo de imagen no válida, solo se permiten: [jpg, jpeg, png o gif]</p>";
                        return true;
                    }
                } else {
                    echo "<p style='color:red'>El archivo subido no es una imagen válida</p>";
                    return true;
                }
            }
        }
        else {
            return false; 
        }
               
    }

    // Se encarga de reescribir el valor introducido por el usuario en un campo especifico, cuando se envia el formulario.
    function campoEnviado($campo) {
        if (isset($_POST["enviar"])) {
            if (isset($_POST[$campo])) {
                echo "value='{$_POST[$campo]}'";
            }
        }
    }

    // Se encarga de reescribir el valor introducido por el usuario en los campos de tipo TextArea, cuando se envia el formulario.
    function campoEnviadoTextArea($campo) {
        if (isset($_POST["enviar"])) {
            if (isset($_POST[$campo])) {
                echo "$_POST[$campo]";
            }
        }
    }

    // Se encarga de validar que se hayan marcado los checkbox.
    function validarCheckbox($nombreCampo) {
        echo "<p style='color:red'>El checkbox $nombreCampo no se ha marcado</p> <br>";
        return true;
    }

    // Se encarga de validar que el usuario introduzca la frase correcta de confirmación.
    function validarConfirmacion($campo) {
        if (validarCampoVacio($campo, "de confirmación") == false) {     
            if ($campo != "eLiMiNaR") {
                echo "<p style='color:red'>Debe escribir la frase de manera correcta.</p>";
                return true;
            }
            return false;
        }
        else {
            return true;
        }
    }

    // Se encarga de validar que la categoría no esté en blanco, ni exista en la base de datos.
    function validarCategoria($categoria, $con) {
        if (validarCampoVacio($categoria, "categoría") == false) {

            $stmt = $con->prepare("SELECT id FROM categoria WHERE LOWER(nombre) = LOWER(:nombre)");
            $stmt->execute([":nombre" => $categoria]);

            if ($datos = $stmt->fetch()) {
                echo "<p style='color:red'>La categoría introducida ya existe</p>";
                return true;
            }
            else {
                return false;
            }
        }
        return true;
    }

?>
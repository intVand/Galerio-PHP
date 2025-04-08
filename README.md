# Galerio - Galería de Arte Online

[![PHP Version](https://img.shields.io/badge/PHP-8.2.12-blue.svg)](https://www.php.net/)

## Descripción

Galerio es una galería de arte online desarrollada principalmente en **PHP 8.2.12**. El proyecto tiene como objetivo ofrecer una plataforma para exhibir y gestionar obras de arte. **El proyecto implementa un completo sistema CRUD que permite la creación, visualización, modificación y eliminación de usuarios, categorías e imágenes.**

Este proyecto utiliza las siguientes tecnologías:

* **Servidor Local:** XAMPP 8.2.12 (con Apache)
* **PHP:** 8.2.12
* **Base de Datos:** MariaDB (incluido en XAMPP)
* **Frontend:** HTML 5, CSS 3, Bootstrap 5
* **Correo Electrónico:** PHPMailer 6.9.3 (incorporado manualmente, sin Composer)
* **Interactividad:** JavaScript
* **Sistema CRUD:** Implementación de las operaciones básicas de Crear, Leer, Actualizar y Eliminar para la gestión del contenido.

Este proyecto fue creado como parte de un proyecto de ciclo y se comparte con fines demostrativos en mi currículum vitae.

## Implementación

A continuación, se detalla la configuración necesaria para ejecutar Galerio en tu entorno local utilizando XAMPP (puede usarse cualquier otro servidor local e incluso un hosting).

### Prerrequisitos

Asegúrate de tener instalado lo siguiente en tu sistema:

* **XAMPP 8.2.12:** Con los módulos de Apache y MySQL (MariaDB) en funcionamiento. Puedes descargarlo desde [https://www.apachefriends.org/download.html](https://www.apachefriends.org/download.html).

### Configuración de la Base de Datos

1.  **Plantilla de la Base de Datos:**
    * La plantilla de la base de datos de Galerio se encuentra en la carpeta `BBDD` del proyecto.
    * Utiliza phpMyAdmin (incluido en XAMPP) u otra herramienta de gestión de bases de datos para importar este archivo (`.sql`) a tu servidor MariaDB.

### Configuración de PHPMailer

1.  **Archivo de Configuración:**
    * La configuración para hacer funcionar PHPMailer se encuentra en el archivo: `utilidades/phpmailerConfig.php`.
    * Abre este archivo con un editor de texto y revisa las siguientes configuraciones:
        * **`$mail->isSMTP();`**: Indica que se utilizará SMTP para el envío de correos.
        * **`$mail->Host = "[TU_SERVIDOR_SMTP]";`**: Reemplaza `"[TU_SERVIDOR_SMTP]"` con la dirección del servidor SMTP de tu proveedor de correo electrónico (ej: `smtp.gmail.com`).
        * **`$mail->SMTPAuth = true;`**: Habilita la autenticación SMTP.
        * **`$mail->Username = "[TU_CORREO_ELECTRONICO]";`**: Reemplaza `"[TU_CORREO_ELECTRONICO]"` con la dirección de correo electrónico que utilizarás para enviar los correos.
        * **`$mail->Password = "[TU_CONTRASENA_CORREO]";`**: Reemplaza `"[TU_CONTRASENA_CORREO]"` con la contraseña de tu cuenta de correo electrónico o contraseña de aplicación.
        * **`$mail->SMTPSecure = "[TIPO_DE_SEGURIDAD_SMTP]";`**: Reemplaza `"[TIPO_DE_SEGURIDAD_SMTP]"` con el tipo de seguridad SMTP (`'tls'` o `'ssl'`).
        * **`$mail->Port = "[PUERTO_SMTP]";`**: Reemplaza `"[PUERTO_SMTP]"` con el puerto SMTP (`587` para TLS, `465` para SSL).
        * **`$mail->setFrom("[TU_CORREO_ELECTRONICO]", "[TU_NOMBRE_REMITENTE]");`**: Reemplaza el primer placeholder con tu dirección de correo y el segundo con el nombre que quieres que aparezca como remitente.

### Configuración de la Conexión a la Base de Datos

1.  **Archivo de Conexión:**
    * La configuración para la conexión a la base de datos se encuentra en el archivo: `utilidades/conectarDB.php`.
    * Abre este archivo con un editor de texto y revisa las siguientes configuraciones:
        * **`define ("USER", "root");`**: **Es fundamental que reemplaces `"root"` con el nombre de un usuario de base de datos que hayas creado específicamente para esta aplicación.**
        * **`define ("PASSWORD", "");`**: **Reemplaza `""` con la contraseña del usuario de base de datos que creaste.**
        * **`$dsn = "mysql:host=localhost;dbname=galerio_db";`**: Asegúrate de que `galerio_db` coincida con el nombre de la base de datos que creaste. Si tu servidor de base de datos no está en `localhost`, deberás modificar esta línea.

### Uso

Una vez completada la configuración, puedes acceder a la galería de arte Galerio a través de tu navegador web. Asegúrate de que tu servidor XAMPP esté en funcionamiento y accede a la URL correspondiente a la ubicación de tu proyecto (por ejemplo, `http://localhost/Galerio/` o la ruta que hayas configurado).

**Usuario Administrador Inicial:**

Recuerda que el usuario administrador inicial debe crearse directamente en la base de datos utilizando phpMyAdmin u otra herramienta de gestión de bases de datos. Inserta un nuevo registro en la tabla de usuarios con los campos necesarios (nombre de usuario, contraseña hasheada, rol de administrador, etc.).

También puedes crea un usuario desde la propia web de Galerio, usando el sistema CRUD, y posteriormente desde phpMyAdmin modificar únicamente el rol, sustituyendo "usuario" por "admin".

## Autor

intVand (Iván Bonmatí Baeza)

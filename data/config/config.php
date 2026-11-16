<?php
/* =====================================================================
   config/config.php  →  AJUSTES GENERALES DEL SISTEMA
   Si quieres cambiar algo básico (nombre, logo, pie de página,
   Bootstrap sin internet), se hace AQUÍ y nada más.
   ===================================================================== */

// Nombre que aparece en el encabezado y en la pestaña del navegador
define('NOMBRE_SISTEMA', 'Panadería "La Reforma" ');

// Ruta del logo (carpeta img/ que ya tienes en el proyecto)
define('LOGO', 'img/Logo_LaReforma.jpeg');

// ¿Mostrar el pie de página café abajo?  true = sí | false = no
define('MOSTRAR_PIE', true);

// ¿Cargar Bootstrap desde la carpeta assets/bootstrap/ (sin internet)?
//   false = se descarga de internet (CDN)   |   true = usa los archivos locales
// (las instrucciones están en assets/bootstrap/LEEME.txt)
define('BOOTSTRAP_LOCAL', false);

// Versión de las tablas de la base de datos (si la subes, se vuelve a revisar el esquema)
define('ESQUEMA_VERSION', 1);

// Zona horaria de Yucatán (para fechas y horas de las ventas)
date_default_timezone_set('America/Merida');

// ---- Rutas (se calculan solas, no las toques) ----
define('RUTA_RAIZ', str_replace('\\', '/', realpath(__DIR__ . '/..')));   // carpeta del proyecto en el disco
define('RUTA_INCLUDES', RUTA_RAIZ . '/includes');                          // carpeta de piezas del diseño

// BASE_URL = dirección web del proyecto, por ejemplo  /Proyecto_Formulario/
$docroot = str_replace('\\', '/', (string)realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
if ($docroot !== '' && strpos(RUTA_RAIZ, $docroot) === 0) {
    define('BASE_URL', rtrim(substr(RUTA_RAIZ, strlen($docroot)), '/') . '/');
} else {
    define('BASE_URL', '/' . basename(RUTA_RAIZ) . '/');
}

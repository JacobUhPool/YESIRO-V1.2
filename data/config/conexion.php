<?php
/* =====================================================================
   config/conexion.php  →  Enlace a la conexión principal en la raíz
   ===================================================================== */

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Requerimos el archivo conexion.php que está 2 carpetas atrás (en la raíz de YEFIRO)
require_once __DIR__ . '/../../conexion.php';

?>
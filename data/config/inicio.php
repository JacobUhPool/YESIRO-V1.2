<?php
/* =====================================================================
   config/inicio.php  →  CARGADOR GENERAL
   Cada página del sistema empieza con UNA sola línea:
        require_once __DIR__ . '/../../config/inicio.php';
   y con eso se cargan, en orden: ajustes, base de datos, tablas,
   permisos, funciones de ayuda y control de sesión.
   ===================================================================== */
require_once __DIR__ . '/config.php';              // 1) ajustes generales
require_once __DIR__ . '/conexion.php';            // 2) conexión a MySQL ($conn)
require_once __DIR__ . '/esquema.php';             // 3) crea las tablas si faltan
require_once __DIR__ . '/permisos.php';            // 4) rangos y permisos
require_once __DIR__ . '/sesion.php';              // 5) inicio de sesión
require_once RUTA_INCLUDES . '/funciones.php';     // 6) funciones de ayuda

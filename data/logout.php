<?php
/* logout.php → CERRAR SESIÓN: borra la sesión y regresa al login */
require_once __DIR__ . '/config/inicio.php';
if (logueado()) { bitacora('Cierre de sesión'); }
$_SESSION = [];          // borra los datos de la sesión
session_destroy();       // destruye la sesión
header('Location: ' . url('login.php?salio=1'));
exit;

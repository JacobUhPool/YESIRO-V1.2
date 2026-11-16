<?php
/* =====================================================================
   includes/encabezado.php  →  parte de ARRIBA del marco de cada página
   Dibuja <head> (aquí se cargan BOOTSTRAP y el CSS), el encabezado café
   y el menú lateral, y deja abierto el área de contenido.

   Cada página lo usa así:
        $titulo = "Clientes";
        include RUTA_INCLUDES . '/encabezado.php';
   (si no quieres título automático:  $sin_titulo = true; )
   ===================================================================== */
if (!isset($titulo)) { $titulo = "Panel principal"; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo) ?> - <?= e(NOMBRE_SISTEMA) ?></title>

    <!-- ===== BOOTSTRAP (diseño de formularios, tablas y botones) ===== -->
    <?= bootstrap_css() ?>

    <!-- ===== CSS PROPIO (colores y estructura del marco): assets/css/estilos.css ===== -->
    <link rel="stylesheet" href="<?= url('assets/css/estilos.css') ?>?v=<?= filemtime(RUTA_RAIZ . '/assets/css/estilos.css') ?>">
</head>
<body>

    <!-- ===== ENCABEZADO: logo, nombre del sistema y usuario ===== -->
    <header class="encabezado d-flex align-items-center px-3 py-2">
        <img src="<?= url(LOGO) ?>" alt="Logo" class="logo-encabezado">
        <span class="fs-5 fw-bold ms-3"><?= e(NOMBRE_SISTEMA) ?></span>

        <span class="ms-auto d-none d-md-inline">
            👤 <b><?= e($_SESSION['nombre']) ?></b> (<?= e(nombre_rol($_SESSION['rol'])) ?>)
        </span>

        <!-- Botón que abre/cierra el menú en celulares -->
        <button class="btn btn-outline-light ms-auto d-md-none" type="button"
                data-bs-toggle="collapse" data-bs-target="#menuLateral">☰ Menú</button>
    </header>

    <!-- ===== CUERPO: menú lateral + contenido ===== -->
    <div class="cuerpo">
        <?php include RUTA_INCLUDES . '/menu.php'; ?>

        <main class="contenido">
            <?php mostrar_flash(); /* mensajes de éxito / error */ ?>
            <?php if (empty($sin_titulo)): ?>
                <h1 class="h3 titulo-pagina"><?= e($titulo) ?></h1>
            <?php endif; ?>

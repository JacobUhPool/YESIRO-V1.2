<?php
/* =====================================================================
   panel.php  →  PANTALLA DE INICIO (después de iniciar sesión)
   ===================================================================== */
require_once __DIR__ . '/config/inicio.php';
requiere_login();                 // cualquier usuario con sesión puede entrar

$titulo = "Inicio";
$sin_titulo = true;               // esta página tiene su propio título grande
include RUTA_INCLUDES . '/encabezado.php';

// Color de la etiqueta según el rango
$colores = ['sistemas' => 'text-bg-dark', 'jefe' => 'text-bg-danger',
            'administrador' => 'text-bg-primary', 'cajero' => 'text-bg-secondary'];
?>
<div class="bienvenida">
    <h2>¡Bienvenido al Sistema!</h2>
    <p class="fs-3 mt-3 mb-1">Buen día, <b><?= e($_SESSION['nombre']) ?></b></p>
    <p class="fs-5">Tu nivel de acceso es:
        <span class="badge <?= $colores[$_SESSION['rol']] ?? 'text-bg-secondary' ?>">
            <?= e(nombre_rol($_SESSION['rol'])) ?>
        </span>
    </p>
    <img src="<?= url(LOGO) ?>" alt="Logo de la Panadería" class="logo-bienvenida">
    <p class="text-muted">Selecciona una opción del menú lateral para comenzar a trabajar.</p>
</div>
<?php include RUTA_INCLUDES . '/pie.php'; ?>

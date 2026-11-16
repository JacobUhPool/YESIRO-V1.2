<?php
/* =====================================================================
   includes/menu.php  →  MENÚ LATERAL
   Cada opción aparece solo si el rango del usuario tiene el permiso
   (los permisos se definen en config/permisos.php).

   PARA AGREGAR UNA OPCIÓN NUEVA: agrega una línea en $grupos:
        ['permiso', 'icono', 'Texto del botón', 'ruta/del/archivo.php']
   ===================================================================== */

$script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);   // página actual

$grupos = [
    'Caja y ventas' => [
        ['ventas.cobrar',    '💰', 'Cobrar (nueva venta)', 'modulos/ventas/cobrar.php'],
        ['ventas.historial', '🧾', tiene_permiso('ventas.todas') ? 'Historial de ventas' : 'Mis ventas', 'modulos/ventas/historial.php'],
        ['caja.operar',      '🏧', 'Caja y corte',         'modulos/caja/caja.php'],
        ['caja.todas',       '📚', 'Historial de cortes',  'modulos/caja/cortes.php'],
        ['clientes.ver',     '👤', 'Clientes',             'modulos/clientes/listar.php'],
    ],
    'Inventario' => [
        ['productos.gestionar', '📦', 'Productos e insumos', 'modulos/productos/listar.php'],
    ],
    'Compras' => [
        ['compras.gestionar',     '🛒', 'Compras',     'modulos/compras/listar.php'],
        ['proveedores.gestionar', '🚚', 'Proveedores', 'modulos/proveedores/listar.php'],
    ],
    'Administración' => [
        ['usuarios.gestionar', '👥', 'Usuarios', 'modulos/usuarios/listar.php'],
        ['bitacora.ver',       '📜', 'Bitácora', 'modulos/bitacora/listar.php'],
    ],
];

// ¿Esta opción es la página donde estoy?
function menu_activo($ruta, $script) {
    if (substr($script, -strlen($ruta)) === $ruta) return true;                 // mismo archivo
    // las páginas "listar" también se marcan cuando estoy en agregar/editar del mismo módulo
    return basename($ruta) === 'listar.php' && strpos($script, '/' . dirname($ruta) . '/') !== false;
}
?>
<aside id="menuLateral" class="menu-lateral collapse d-md-flex">

    <!-- Opciones principales (solo las que el rango puede usar) -->
    <nav class="menu-opciones">
        <?php foreach ($grupos as $nombreGrupo => $items):
            // dejar solo las opciones permitidas
            $permitidos = array_filter($items, function ($i) { return tiene_permiso($i[0]); });
            if (!$permitidos) continue;   // si no hay ninguna, no se muestra el grupo
        ?>
            <div class="titulo-grupo"><?= e($nombreGrupo) ?></div>
            <?php foreach ($permitidos as $i): ?>
                <a class="btn-menu <?= menu_activo($i[3], $script) ? 'activo' : '' ?>" href="<?= url($i[3]) ?>">
                    <?= $i[1] ?> <?= e($i[2]) ?>
                </a>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </nav>

    <!-- Sección de abajo: Inicio y Cerrar sesión -->
    <div class="menu-cuenta">
        <div class="titulo-grupo">Mi cuenta</div>
        <a class="btn-menu <?= substr($script, -9) === 'panel.php' ? 'activo' : '' ?>" href="<?= url('panel.php') ?>">🏠 Inicio</a>
        <a class="btn-menu salir" href="<?= url('logout.php') ?>">🚪 Cerrar sesión</a>
    </div>
</aside>

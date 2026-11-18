<?php
/* =====================================================================
   modulos/bitacora/listar.php  →  BITÁCORA DEL SISTEMA (solo rango Sistemas)
   Muestra quién hizo qué y cuándo (altas, cambios, bajas, ventas, cortes...).
   Se llena con la función bitacora() de includes/funciones.php
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('bitacora.ver');
requiere_pin_jefe();

$q = trim($_GET['q'] ?? '');
$like = '%' . $q . '%';

// SELECT: últimos 300 movimientos que coincidan con la búsqueda
$st = $conn->prepare("SELECT * FROM bitacora
                      WHERE CONCAT_WS(' ', usuario_nombre, accion, detalle) LIKE ?
                      ORDER BY id_bitacora DESC LIMIT 300");
$st->bind_param("s", $like);
$st->execute();
$filas = $st->get_result();

$titulo = "Bitácora del sistema";
include RUTA_INCLUDES . '/encabezado.php';
?>
<form method="get" class="d-flex gap-2 mb-3" style="max-width:420px">
    <label for="q_busqueda" class="visually-hidden">Buscar</label>
    <input type="text" class="form-control" name="q" value="<?= e($q) ?>" placeholder="Buscar usuario, acción o detalle...">
    <button class="btn btn-outline-secondary">Buscar</button>
</form>

<div class="table-responsive">
    <table class="table table-sm table-striped align-middle">
        <thead class="table-light"><tr><th>Fecha y hora</th><th>Usuario</th><th>Acción</th><th>Detalle</th></tr></thead>
        <tbody>
        <?php while ($f = $filas->fetch_assoc()): ?>
            <tr>
                <td class="text-nowrap"><?= e($f['fecha_hora']) ?></td>
                <td><?= e($f['usuario_nombre']) ?></td>
                <td><?= e($f['accion']) ?></td>
                <td><?= e($f['detalle']) ?></td>
            </tr>
        <?php endwhile; ?>
        <?php if ($filas->num_rows === 0): ?>
            <tr><td colspan="4" class="text-center text-muted py-4">Sin movimientos.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include RUTA_INCLUDES . '/pie.php'; ?>

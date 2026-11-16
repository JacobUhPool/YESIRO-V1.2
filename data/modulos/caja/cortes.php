<?php
/* =====================================================================
   modulos/caja/cortes.php  →  HISTORIAL DE CORTES DE CAJA (todos los cajeros)
   Solo Administrador, Jefe y Sistemas.
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('caja.todas');
requiere_pin_jefe();

// SELECT: los últimos 100 cortes con el nombre del cajero
$cortes = $conn->query("SELECT k.*, u.nombre AS cajero
                        FROM cortes_caja k JOIN usuarios u ON u.id_usuario = k.id_usuario
                        ORDER BY k.id_corte DESC LIMIT 100");

$titulo = "Historial de cortes";
include RUTA_INCLUDES . '/encabezado.php';
?>
<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr><th>#</th><th>Cajero</th><th>Apertura</th><th>Cierre</th><th>Fondo</th><th>Efectivo</th>
                <th>Tarjeta</th><th>Esperado</th><th>Contado</th><th>Diferencia</th><th>Estado</th></tr>
        </thead>
        <tbody>
        <?php while ($c = $cortes->fetch_assoc()): ?>
            <tr>
                <td><?= $c['id_corte'] ?></td>
                <td><?= e($c['cajero']) ?></td>
                <td><?= e($c['fecha_apertura']) ?></td>
                <td><?= e($c['fecha_cierre'] ?? '—') ?></td>
                <td><?= dinero($c['fondo_inicial']) ?></td>
                <td><?= dinero($c['total_efectivo']) ?></td>
                <td><?= dinero($c['total_tarjeta']) ?></td>
                <td><?= $c['estado'] === 'cerrada' ? dinero($c['efectivo_esperado']) : '—' ?></td>
                <td><?= $c['estado'] === 'cerrada' ? dinero($c['efectivo_contado']) : '—' ?></td>
                <td>
                    <?php if ($c['estado'] === 'cerrada'): ?>
                        <span class="badge <?= (float)$c['diferencia'] == 0 ? 'text-bg-success' : 'text-bg-danger' ?>"><?= dinero($c['diferencia']) ?></span>
                    <?php else: ?>—<?php endif; ?>
                </td>
                <td><span class="badge <?= $c['estado'] === 'abierta' ? 'text-bg-warning' : 'text-bg-secondary' ?>"><?= $c['estado'] === 'abierta' ? 'Abierta' : 'Cerrada' ?></span></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include RUTA_INCLUDES . '/pie.php'; ?>

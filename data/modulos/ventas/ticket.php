<?php
/* =====================================================================
   modulos/ventas/ticket.php  →  VER / IMPRIMIR un ticket de venta
   El cajero solo puede ver sus propios tickets.
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('ventas.historial');

$id = (int)($_GET['id'] ?? 0);

// SELECT del ticket con datos del cajero, cliente y estado de su caja
$st = $conn->prepare("SELECT t.*, u.nombre AS cajero, c.nombre AS cliente, k.estado AS estado_corte
                      FROM tickets t
                      JOIN usuarios u ON u.id_usuario = t.id_usuario
                      JOIN clientes c ON c.id_cliente = t.id_cliente
                      JOIN cortes_caja k ON k.id_corte = t.id_corte
                      WHERE t.id_ticket = ?");
$st->bind_param("i", $id);
$st->execute();
$t = $st->get_result()->fetch_assoc();

// No existe, o es de otro cajero y no tengo permiso de ver todas
if (!$t || (!tiene_permiso('ventas.todas') && (int)$t['id_usuario'] !== (int)$_SESSION['uid'])) {
    flash('peligro', 'Ticket no encontrado.');
    redirigir('modulos/ventas/historial.php');
}

// Panes del ticket
$st = $conn->prepare("SELECT d.*, p.nombre FROM ticket_detalle d JOIN productos p ON p.id_producto = d.id_producto WHERE d.id_ticket = ?");
$st->bind_param("i", $id);
$st->execute();
$lineas = $st->get_result();

$puede_cancelar = tiene_permiso('ventas.cancelar') && $t['estado'] === 'activo' && $t['estado_corte'] === 'abierta';

$titulo = "Ticket #" . $t['id_ticket'];
include RUTA_INCLUDES . '/encabezado.php';
?>
<div class="ticket card shadow-sm">
    <div class="card-body">
        <div class="text-center mb-2">
            <b><?= e(NOMBRE_SISTEMA) ?></b><br>
            Ticket #<?= $t['id_ticket'] ?><br>
            <?= e($t['fecha_hora']) ?>
            <?php if ($t['estado'] === 'cancelado'): ?><div class="text-danger fw-bold">*** CANCELADO ***</div><?php endif; ?>
        </div>
        <div class="small mb-2">Cajero: <?= e($t['cajero']) ?><br>Cliente: <?= e($t['cliente']) ?></div>
        <hr>
        <?php while ($l = $lineas->fetch_assoc()): ?>
            <div class="d-flex justify-content-between">
                <span><?= (float)$l['cantidad'] ?> x <?= e($l['nombre']) ?></span>
                <span><?= dinero($l['subtotal']) ?></span>
            </div>
        <?php endwhile; ?>
        <hr>
        <div class="d-flex justify-content-between fw-bold"><span>TOTAL</span><span><?= dinero($t['total']) ?></span></div>
        <div class="d-flex justify-content-between"><span>Pago (<?= e($t['metodo_pago']) ?>)</span><span><?= dinero($t['recibido']) ?></span></div>
        <div class="d-flex justify-content-between"><span>Cambio</span><span><?= dinero($t['cambio']) ?></span></div>
        <div class="text-center mt-3">¡Gracias por su compra!</div>
    </div>
</div>

<div class="text-center mt-3 no-imprimir">
    <button class="btn btn-cafe" onclick="window.print()">🖨 Imprimir</button>
    <a class="btn btn-outline-secondary" href="<?= url('modulos/ventas/cobrar.php') ?>">Nueva venta</a>
    <a class="btn btn-outline-secondary" href="<?= url('modulos/ventas/historial.php') ?>">Ver ventas</a>
    <?php if ($puede_cancelar): ?>
        <form method="post" action="<?= url('acciones/ventas/cancelar.php') ?>" class="d-inline"
              data-confirmar="¿Cancelar este ticket? El stock regresará al inventario.">
            <?= csrf_campo() ?>
            <input type="hidden" name="id" value="<?= $t['id_ticket'] ?>">
            <button class="btn btn-outline-danger">Cancelar ticket</button>
        </form>
    <?php endif; ?>
</div>
<?php include RUTA_INCLUDES . '/pie.php'; ?>

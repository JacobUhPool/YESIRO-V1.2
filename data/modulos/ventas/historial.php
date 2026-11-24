<?php
/* =====================================================================
   modulos/ventas/historial.php  →  HISTORIAL DE VENTAS (tickets) por día
   - Cajero: solo ve SUS ventas.
   - Administrador / Jefe / Sistemas: ven las de todos los cajeros.
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('ventas.historial');
requiere_pin_jefe();

$ve_todas = tiene_permiso('ventas.todas');

// Día a consultar (por defecto hoy)
$fecha = $_GET['fecha'] ?? date('Y-m-d');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) { $fecha = date('Y-m-d'); }

// SELECT de los tickets del día (con el cajero y el cliente)
$sql = "SELECT t.*, u.nombre AS cajero, c.nombre AS cliente
        FROM tickets t
        JOIN usuarios u ON u.id_usuario = t.id_usuario
        JOIN clientes c ON c.id_cliente = t.id_cliente
        WHERE DATE(t.fecha_hora) = ?";
if (!$ve_todas) { $sql .= " AND t.id_usuario = ?"; }
$sql .= " ORDER BY t.fecha_hora DESC";

$st = $conn->prepare($sql);
if ($ve_todas) { $st->bind_param("s", $fecha); }
else { $uid = (int)$_SESSION['uid']; $st->bind_param("si", $fecha, $uid); }
$st->execute();
$tickets = $st->get_result()->fetch_all(MYSQLI_ASSOC);

// Totales del día (sin contar los cancelados)
$total = 0; $efectivo = 0; $tarjeta = 0; $cuantos = 0;
foreach ($tickets as $t) {
    if ($t['estado'] !== 'activo') continue;
    $cuantos++;
    $total += $t['total'];
    if ($t['metodo_pago'] === 'Efectivo') { $efectivo += $t['total']; } else { $tarjeta += $t['total']; }
}

$titulo = $ve_todas ? "Historial de ventas" : "Mis ventas";
include RUTA_INCLUDES . '/encabezado.php';
?>
<form method="get" class="d-flex gap-2 mb-3" style="max-width:340px">
    <input type="date" class="form-control" name="fecha" value="<?= e($fecha) ?>">
    <button class="btn btn-outline-secondary">Ver</button>
</form>

<!-- Resumen del día -->
<div class="row g-3 mb-3">
    <div class="col-sm-6 col-lg-3"><div class="card shadow-sm"><div class="card-body">
        <div class="text-muted">Ventas del día</div><div class="fs-4 fw-bold"><?= $cuantos ?></div></div></div></div>
    <div class="col-sm-6 col-lg-3"><div class="card shadow-sm"><div class="card-body">
        <div class="text-muted">Total vendido</div><div class="fs-4 fw-bold" style="color:var(--cafe)"><?= dinero($total) ?></div></div></div></div>
    <div class="col-sm-6 col-lg-3"><div class="card shadow-sm"><div class="card-body">
        <div class="text-muted">En efectivo</div><div class="fs-4 fw-bold"><?= dinero($efectivo) ?></div></div></div></div>
    <div class="col-sm-6 col-lg-3"><div class="card shadow-sm"><div class="card-body">
        <div class="text-muted">En tarjeta</div><div class="fs-4 fw-bold"><?= dinero($tarjeta) ?></div></div></div></div>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr><th>Ticket</th><th>Hora</th><?php if ($ve_todas): ?><th>Cajero</th><?php endif; ?>
                <th>Cliente</th><th>Pago</th><th class="text-end">Total</th><th>Estado</th><th></th></tr>
        </thead>
        <tbody>
        <?php foreach ($tickets as $t): ?>
            <tr class="<?= $t['estado'] === 'cancelado' ? 'text-muted text-decoration-line-through' : '' ?>">
                <td>#<?= $t['id_ticket'] ?></td>
                <td><?= substr($t['fecha_hora'], 11, 5) ?></td>
                <?php if ($ve_todas): ?><td><?= e($t['cajero']) ?></td><?php endif; ?>
                <td><?= e($t['cliente']) ?></td>
                <td><?= e($t['metodo_pago']) ?></td>
                <td class="text-end"><?= dinero($t['total']) ?></td>
                <td><span class="badge <?= $t['estado'] === 'activo' ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= $t['estado'] === 'activo' ? 'Activo' : 'Cancelado' ?></span></td>
                <td><a class="btn btn-sm btn-outline-primary" href="<?= url('modulos/ventas/ticket.php?id=' . $t['id_ticket']) ?>">Ver</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$tickets): ?>
            <tr><td colspan="8" class="text-center text-muted py-4">No hay ventas en esta fecha.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include RUTA_INCLUDES . '/pie.php'; ?>

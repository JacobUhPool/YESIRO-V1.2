<?php
/* =====================================================================
   modulos/caja/caja.php  →  MI CAJA: abrir caja y hacer el CORTE
   - Sin caja abierta: pide el fondo inicial (dinero con el que empieza).
   - Con caja abierta: muestra lo vendido en el turno (efectivo y tarjeta) y
     permite cerrar la caja contando el efectivo.
   Guardan: acciones/caja/abrir.php  y  acciones/caja/cerrar.php
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('caja.operar');

$uid = (int)$_SESSION['uid'];

// ¿Tengo una caja abierta?
$st = $conn->prepare("SELECT * FROM cortes_caja WHERE id_usuario = ? AND estado = 'abierta' LIMIT 1");
$st->bind_param("i", $uid);
$st->execute();
$corte = $st->get_result()->fetch_assoc();

// Si está abierta: sumar lo vendido en este turno (sin tickets cancelados)
$efectivo = 0; $tarjeta = 0; $tickets = 0;
if ($corte) {
    $st = $conn->prepare("SELECT metodo_pago, COUNT(*) n, COALESCE(SUM(total),0) s
                          FROM tickets WHERE id_corte = ? AND estado = 'activo' GROUP BY metodo_pago");
    $st->bind_param("i", $corte['id_corte']);
    $st->execute();
    $r = $st->get_result();
    while ($f = $r->fetch_assoc()) {
        $tickets += (int)$f['n'];
        if ($f['metodo_pago'] === 'Efectivo') { $efectivo = (float)$f['s']; } else { $tarjeta = (float)$f['s']; }
    }
}

// Mis últimos 5 cortes cerrados
$st = $conn->prepare("SELECT * FROM cortes_caja WHERE id_usuario = ? AND estado = 'cerrada' ORDER BY id_corte DESC LIMIT 5");
$st->bind_param("i", $uid);
$st->execute();
$ultimos = $st->get_result();

$titulo = "Caja y corte";
include RUTA_INCLUDES . '/encabezado.php';
?>

<?php if (!$corte): ?>
    <!-- ================= CAJA CERRADA: ABRIR ================= -->
    <div class="card shadow-sm mb-4" style="max-width:480px">
        <div class="card-header fw-bold">🏧 Abrir caja</div>
        <div class="card-body">
            <p class="text-muted">Escribe con cuánto efectivo inicias tu turno (fondo para dar cambio).</p>
            <form method="post" action="<?= url('acciones/caja/abrir.php') ?>" class="needs-validation" novalidate>
                <?= csrf_campo() ?>
                <label for="fondo_inicial" class="form-label">Fondo inicial (MXN)</label>
                <input type="number" class="form-control mb-3" name="fondo_inicial" id="fondo_inicial"
                       min="0" step="0.01" value="500" required>
                <div class="invalid-feedback">Escribe el fondo inicial.</div>
                <button class="btn btn-cafe">Abrir caja</button>
            </form>
        </div>
    </div>

<?php else: ?>
    <!-- ================= CAJA ABIERTA: RESUMEN Y CORTE ================= -->
    <div class="alert alert-success">Caja abierta desde <b><?= e($corte['fecha_apertura']) ?></b>.</div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3"><div class="card shadow-sm"><div class="card-body">
            <div class="text-muted">Fondo inicial</div><div class="fs-4 fw-bold"><?= dinero($corte['fondo_inicial']) ?></div></div></div></div>
        <div class="col-6 col-lg-3"><div class="card shadow-sm"><div class="card-body">
            <div class="text-muted">Ventas (<?= $tickets ?> tickets)</div><div class="fs-4 fw-bold"><?= dinero($efectivo + $tarjeta) ?></div></div></div></div>
        <div class="col-6 col-lg-3"><div class="card shadow-sm"><div class="card-body">
            <div class="text-muted">Cobrado en efectivo</div><div class="fs-4 fw-bold"><?= dinero($efectivo) ?></div></div></div></div>
        <div class="col-6 col-lg-3"><div class="card shadow-sm"><div class="card-body">
            <div class="text-muted">Cobrado con tarjeta</div><div class="fs-4 fw-bold"><?= dinero($tarjeta) ?></div></div></div></div>
    </div>

    <div class="card shadow-sm mb-4" style="max-width:520px">
        <div class="card-header fw-bold">🔒 Cerrar caja (corte)</div>
        <div class="card-body">
            <p>Efectivo que <b>debe haber</b> en caja: <b><?= dinero($corte['fondo_inicial'] + $efectivo) ?></b>
               <small class="text-muted">(fondo + ventas en efectivo)</small></p>
            <form method="post" action="<?= url('acciones/caja/cerrar.php') ?>" class="needs-validation" novalidate
                  data-confirmar="¿Cerrar la caja? Ya no podrás cobrar hasta abrirla otra vez.">
                <?= csrf_campo() ?>
                <label for="efectivo_contado" class="form-label">Efectivo contado (MXN)</label>
                <input type="number" class="form-control mb-3" name="efectivo_contado" id="efectivo_contado"
                       min="0" step="0.01" required>
                <div class="invalid-feedback">Cuenta el efectivo y escríbelo.</div>
                <button class="btn btn-danger">Cerrar caja</button>
                <a class="btn btn-outline-secondary" href="<?= url('modulos/ventas/cobrar.php') ?>">Volver a cobrar</a>
            </form>
        </div>
    </div>
<?php endif; ?>

<!-- Mis últimos cortes -->
<h2 class="h5 mt-4">Mis últimos cortes</h2>
<div class="table-responsive">
    <table class="table table-sm align-middle">
        <thead class="table-light">
            <tr><th>#</th><th>Apertura</th><th>Cierre</th><th>Efectivo</th><th>Tarjeta</th><th>Esperado</th><th>Contado</th><th>Diferencia</th></tr>
        </thead>
        <tbody>
        <?php while ($c = $ultimos->fetch_assoc()): ?>
            <tr>
                <td><?= $c['id_corte'] ?></td><td><?= e($c['fecha_apertura']) ?></td><td><?= e($c['fecha_cierre']) ?></td>
                <td><?= dinero($c['total_efectivo']) ?></td><td><?= dinero($c['total_tarjeta']) ?></td>
                <td><?= dinero($c['efectivo_esperado']) ?></td><td><?= dinero($c['efectivo_contado']) ?></td>
                <td><span class="badge <?= (float)$c['diferencia'] == 0 ? 'text-bg-success' : 'text-bg-danger' ?>"><?= dinero($c['diferencia']) ?></span></td>
            </tr>
        <?php endwhile; ?>
        <?php if ($ultimos->num_rows === 0): ?>
            <tr><td colspan="8" class="text-center text-muted py-3">Aún no has hecho cortes.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include RUTA_INCLUDES . '/pie.php'; ?>

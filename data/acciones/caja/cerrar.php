<?php
/* =====================================================================
   acciones/caja/cerrar.php  →  UPDATE: CORTE DE CAJA
   Suma lo cobrado en efectivo y con tarjeta durante el turno, compara el
   efectivo esperado contra el contado y guarda la diferencia.
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('caja.operar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirigir('modulos/caja/caja.php'); }
verificar_csrf();

$uid = (int)$_SESSION['uid'];
$contado = (float)($_POST['efectivo_contado'] ?? 0);
if ($contado < 0) { flash('peligro', 'El efectivo contado no puede ser negativo.'); redirigir('modulos/caja/caja.php'); }

// Mi caja abierta
$st = $conn->prepare("SELECT id_corte, fondo_inicial FROM cortes_caja WHERE id_usuario = ? AND estado = 'abierta' LIMIT 1");
$st->bind_param("i", $uid);
$st->execute();
$corte = $st->get_result()->fetch_assoc();
if (!$corte) { flash('aviso', 'No tienes una caja abierta.'); redirigir('modulos/caja/caja.php'); }
$id_corte = (int)$corte['id_corte'];

// Totales del turno por forma de pago (sin tickets cancelados)
$efectivo = 0.0; $tarjeta = 0.0;
$st = $conn->prepare("SELECT metodo_pago, COALESCE(SUM(total),0) s FROM tickets
                      WHERE id_corte = ? AND estado = 'activo' GROUP BY metodo_pago");
$st->bind_param("i", $id_corte);
$st->execute();
$r = $st->get_result();
while ($f = $r->fetch_assoc()) {
    if ($f['metodo_pago'] === 'Efectivo') { $efectivo = (float)$f['s']; } else { $tarjeta = (float)$f['s']; }
}

$esperado = (float)$corte['fondo_inicial'] + $efectivo;   // lo que debería haber en la caja
$diferencia = $contado - $esperado;                        // + sobra, − falta

// UPDATE: cerrar la caja y guardar el corte
$ahora = date('Y-m-d H:i:s');
$st = $conn->prepare("UPDATE cortes_caja
                      SET fecha_cierre = ?, total_efectivo = ?, total_tarjeta = ?, efectivo_esperado = ?,
                          efectivo_contado = ?, diferencia = ?, estado = 'cerrada'
                      WHERE id_corte = ?");
$st->bind_param("sdddddi", $ahora, $efectivo, $tarjeta, $esperado, $contado, $diferencia, $id_corte);
$st->execute();

bitacora('Corte de caja', "Corte #$id_corte, diferencia " . dinero($diferencia));
if (abs($diferencia) < 0.005) {
    flash('exito', 'Caja cerrada. El corte cuadra perfecto.');
} else {
    flash('aviso', 'Caja cerrada. Diferencia de ' . dinero($diferencia) . ($diferencia > 0 ? ' (sobra dinero).' : ' (falta dinero).'));
}
redirigir('modulos/caja/caja.php');

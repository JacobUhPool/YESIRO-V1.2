<?php
/* =====================================================================
   acciones/compras/cancelar.php  →  UPDATE: CANCELAR un pedido pendiente
   Solo se pueden cancelar las compras que aún no se han recibido.
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('compras.gestionar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirigir('modulos/compras/listar.php'); }
verificar_csrf();

$id = (int)($_POST['id'] ?? 0);

// UPDATE solo si sigue pendiente
$st = $conn->prepare("UPDATE compras SET estado = 'cancelada' WHERE id_compra = ? AND estado = 'pendiente'");
$st->bind_param("i", $id);
$st->execute();

if ($st->affected_rows > 0) {
    bitacora('Cancelar compra', "Compra #$id");
    flash('exito', "Compra #$id cancelada.");
} else {
    flash('aviso', 'Solo se pueden cancelar compras pendientes.');
}
redirigir('modulos/compras/listar.php');

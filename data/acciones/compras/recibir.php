<?php
/* =====================================================================
   acciones/compras/recibir.php  →  UPDATE: RECIBIR la mercancía de un pedido
   Pasa la compra de "pendiente" a "recibida" y SUMA el stock al inventario.
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('compras.gestionar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirigir('modulos/compras/listar.php'); }
verificar_csrf();

$id = (int)($_POST['id'] ?? 0);

$conn->begin_transaction();
try {
    $st = $conn->prepare("SELECT estado FROM compras WHERE id_compra = ? FOR UPDATE");
    $st->bind_param("i", $id);
    $st->execute();
    $c = $st->get_result()->fetch_assoc();
    if (!$c || $c['estado'] !== 'pendiente') { throw new Exception("Esa compra no está pendiente."); }

    $up = $conn->prepare("UPDATE compras SET estado = 'recibida' WHERE id_compra = ?");
    $up->bind_param("i", $id);
    $up->execute();

    aplicar_recepcion_compra($conn, $id);   // suma el stock
    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    flash('peligro', $e->getMessage());
    redirigir('modulos/compras/listar.php');
}

bitacora('Recibir compra', "Compra #$id");
flash('exito', "Compra #$id recibida: el inventario ya se actualizó.");
redirigir('modulos/compras/listar.php');

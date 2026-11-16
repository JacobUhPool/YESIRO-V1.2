<?php
/* =====================================================================
   acciones/ventas/cancelar.php  →  CANCELAR un ticket (UPDATE)
   Marca el ticket como "cancelado" y REGRESA los panes al inventario.
   Solo se puede cancelar si la caja de ese ticket sigue abierta.
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('ventas.cancelar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirigir('modulos/ventas/historial.php'); }
verificar_csrf();

$id = (int)($_POST['id'] ?? 0);

$conn->begin_transaction();
try {
    // Buscar el ticket y su caja (bloqueado mientras se cancela)
    $st = $conn->prepare("SELECT t.estado, k.estado AS estado_corte
                          FROM tickets t JOIN cortes_caja k ON k.id_corte = t.id_corte
                          WHERE t.id_ticket = ? FOR UPDATE");
    $st->bind_param("i", $id);
    $st->execute();
    $t = $st->get_result()->fetch_assoc();

    if (!$t) { throw new Exception("Ticket no encontrado."); }
    if ($t['estado'] !== 'activo') { throw new Exception("Ese ticket ya está cancelado."); }
    if ($t['estado_corte'] !== 'abierta') { throw new Exception("No se puede cancelar: la caja de ese ticket ya hizo su corte."); }

    // UPDATE: marcar como cancelado
    $up = $conn->prepare("UPDATE tickets SET estado = 'cancelado' WHERE id_ticket = ?");
    $up->bind_param("i", $id);
    $up->execute();

    // UPDATE: regresar el stock de cada pan
    $st = $conn->prepare("SELECT id_producto, cantidad FROM ticket_detalle WHERE id_ticket = ?");
    $st->bind_param("i", $id);
    $st->execute();
    $lineas = $st->get_result()->fetch_all(MYSQLI_ASSOC);
    $dev = $conn->prepare("UPDATE productos SET stock = stock + ? WHERE id_producto = ?");
    foreach ($lineas as $l) {
        $c = (float)$l['cantidad']; $p = (int)$l['id_producto'];
        $dev->bind_param("di", $c, $p);
        $dev->execute();
    }
    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    flash('peligro', $e->getMessage());
    redirigir('modulos/ventas/historial.php');
}

bitacora('Cancelar ticket', "Ticket #$id");
flash('exito', "Ticket #$id cancelado y stock regresado al inventario.");
redirigir('modulos/ventas/historial.php');

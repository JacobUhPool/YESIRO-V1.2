<?php
/* =====================================================================
   acciones/ventas/registrar.php  →  INSERT de la VENTA (ticket) + descuento de stock
   Recibe el carrito desde modulos/ventas/cobrar.php. Todo ocurre dentro de
   una TRANSACCIÓN: o se guarda TODO (ticket + panes + descuento) o NADA.
   Los precios se leen de la base de datos (no del navegador).
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('ventas.cobrar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirigir('modulos/ventas/cobrar.php'); }
verificar_csrf();

$uid = (int)$_SESSION['uid'];

// PASO 1: la caja del cajero debe estar abierta
$st = $conn->prepare("SELECT id_corte FROM cortes_caja WHERE id_usuario = ? AND estado = 'abierta' LIMIT 1");
$st->bind_param("i", $uid);
$st->execute();
$corte = $st->get_result()->fetch_assoc();
if (!$corte) { flash('peligro', 'Tu caja está cerrada. Ábrela para cobrar.'); redirigir('modulos/caja/caja.php'); }
$id_corte = (int)$corte['id_corte'];

// PASO 2: leer el carrito (JSON) y juntar productos repetidos
$items = json_decode($_POST['carrito'] ?? '[]', true);
$lineas = [];
if (is_array($items)) {
    foreach ($items as $it) {
        $idp = (int)($it['id'] ?? 0);
        $cant = (int)($it['cantidad'] ?? 0);
        if ($idp > 0 && $cant > 0) { $lineas[$idp] = ($lineas[$idp] ?? 0) + $cant; }
    }
}
if (!$lineas) { flash('peligro', 'El carrito está vacío.'); redirigir('modulos/ventas/cobrar.php'); }
ksort($lineas);   // siempre en el mismo orden (evita bloqueos entre cajeros)

// PASO 3: forma de pago y cliente
$metodo = ($_POST['metodo_pago'] ?? '') === 'Tarjeta' ? 'Tarjeta' : 'Efectivo';
$id_cliente = (int)($_POST['id_cliente'] ?? 1);
$st = $conn->prepare("SELECT 1 FROM clientes WHERE id_cliente = ?");
$st->bind_param("i", $id_cliente);
$st->execute();
if ($st->get_result()->num_rows === 0) { $id_cliente = 1; }   // si no existe: Público en general

// PASO 4: TRANSACCIÓN
$conn->begin_transaction();
try {
    $detalle = [];
    $total = 0.0;

    // 4.1 Revisar cada producto: precio real y stock suficiente (FOR UPDATE bloquea la fila)
    $sel = $conn->prepare("SELECT nombre, precio, stock FROM productos
                           WHERE id_producto = ? AND tipo = 'terminado' AND activo = 1 FOR UPDATE");
    foreach ($lineas as $idp => $cant) {
        $sel->bind_param("i", $idp);
        $sel->execute();
        $p = $sel->get_result()->fetch_assoc();
        if (!$p) { throw new Exception("Un producto del carrito ya no está disponible."); }
        if ((float)$p['stock'] < $cant) {
            throw new Exception("Stock insuficiente de «{$p['nombre']}»: quedan " . (float)$p['stock'] . " y pediste $cant.");
        }
        $sub = (float)$p['precio'] * $cant;
        $total += $sub;
        $detalle[] = [$idp, $cant, (float)$p['precio'], $sub];
    }

    // 4.2 Efectivo: validar lo recibido y calcular el cambio
    if ($metodo === 'Efectivo') {
        $recibido = (float)($_POST['recibido'] ?? 0);
        if ($recibido < $total) { throw new Exception("El efectivo recibido no alcanza para pagar " . dinero($total) . "."); }
        $cambio = $recibido - $total;
    } else {
        $recibido = $total;   // tarjeta: se cobra exacto
        $cambio = 0.0;
    }

    // 4.3 INSERT del ticket
    $ahora = date('Y-m-d H:i:s');
    $ins = $conn->prepare("INSERT INTO tickets (id_corte, id_usuario, id_cliente, fecha_hora, metodo_pago, total, recibido, cambio)
                           VALUES (?,?,?,?,?,?,?,?)");
    $ins->bind_param("iiissddd", $id_corte, $uid, $id_cliente, $ahora, $metodo, $total, $recibido, $cambio);
    $ins->execute();
    $id_ticket = $conn->insert_id;

    // 4.4 INSERT de cada pan vendido + UPDATE (descontar stock)
    $insd = $conn->prepare("INSERT INTO ticket_detalle (id_ticket, id_producto, cantidad, precio_unitario, subtotal) VALUES (?,?,?,?,?)");
    $upd  = $conn->prepare("UPDATE productos SET stock = stock - ? WHERE id_producto = ?");
    foreach ($detalle as $d) {
        $cantd = (float)$d[1];
        $insd->bind_param("iiddd", $id_ticket, $d[0], $cantd, $d[2], $d[3]);
        $insd->execute();
        $upd->bind_param("di", $cantd, $d[0]);
        $upd->execute();
    }

    $conn->commit();    // todo salió bien: se confirma

} catch (Exception $e) {
    $conn->rollback();  // algo falló: se deshace TODO
    flash('peligro', $e->getMessage());
    redirigir('modulos/ventas/cobrar.php');
}

bitacora('Venta registrada', "Ticket #$id_ticket por " . dinero($total) . " ($metodo)");
flash('exito', 'Venta registrada. Cambio: ' . dinero($cambio));
redirigir('modulos/ventas/ticket.php?id=' . $id_ticket);

<?php
/* =====================================================================
   acciones/compras/registrar.php  →  INSERT de una COMPRA y sus renglones
   Si viene marcado "recibida", también suma el stock (aplicar_recepcion_compra,
   en includes/funciones.php). Todo dentro de una transacción.
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('compras.gestionar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirigir('modulos/compras/listar.php'); }
verificar_csrf();

$uid = (int)$_SESSION['uid'];
$id_proveedor = (int)($_POST['id_proveedor'] ?? 0);
$fecha = $_POST['fecha'] ?? date('Y-m-d');
$notas = trim($_POST['notas'] ?? '');
$notas = ($notas === '') ? null : $notas;
$recibida = !empty($_POST['recibida']);

// Renglones: id_producto[], cantidad[], costo[]
$ids = $_POST['id_producto'] ?? [];
$cants = $_POST['cantidad'] ?? [];
$costos = $_POST['costo'] ?? [];

$lineas = [];
$total = 0.0;
foreach ($ids as $i => $idp) {
    $idp = (int)$idp;
    $cant = (float)($cants[$i] ?? 0);
    $costo = (float)($costos[$i] ?? 0);
    if ($idp > 0 && $cant > 0 && $costo >= 0) {
        $lineas[] = [$idp, $cant, $costo];
        $total += $cant * $costo;
    }
}
if ($id_proveedor <= 0 || !$lineas || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    flash('peligro', 'Revisa el proveedor, la fecha y que haya al menos un insumo.');
    redirigir('modulos/compras/nueva.php');
}

$estado = $recibida ? 'recibida' : 'pendiente';

$conn->begin_transaction();
try {
    // INSERT de la compra
    $st = $conn->prepare("INSERT INTO compras (id_proveedor, id_usuario, fecha, total, estado, notas) VALUES (?,?,?,?,?,?)");
    $st->bind_param("iisdss", $id_proveedor, $uid, $fecha, $total, $estado, $notas);
    $st->execute();
    $id_compra = $conn->insert_id;

    // INSERT de cada renglón
    $ins = $conn->prepare("INSERT INTO compra_detalle (id_compra, id_producto, cantidad, costo_unitario) VALUES (?,?,?,?)");
    foreach ($lineas as $l) {
        $ins->bind_param("iidd", $id_compra, $l[0], $l[1], $l[2]);
        $ins->execute();
    }

    // Si ya llegó la mercancía: sumar al inventario
    if ($recibida) { aplicar_recepcion_compra($conn, $id_compra); }

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    flash('peligro', 'No se pudo guardar la compra: ' . $e->getMessage());
    redirigir('modulos/compras/nueva.php');
}

bitacora('Insertar compra', "Compra #$id_compra por " . dinero($total) . " ($estado)");
flash('exito', $recibida ? 'Compra guardada y stock actualizado.' : 'Compra guardada como pendiente.');
redirigir('modulos/compras/listar.php');

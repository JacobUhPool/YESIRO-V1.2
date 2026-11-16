<?php
/* =====================================================================
   acciones/productos/ajustar_stock.php  →  UPDATE del stock (sumar / restar)
   Se usa para corregir existencias a mano (merma, conteo físico, producción).
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('productos.gestionar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirigir('modulos/productos/listar.php'); }
verificar_csrf();

$id        = (int)($_POST['id'] ?? 0);
$cantidad  = (float)($_POST['cantidad'] ?? 0);
$operacion = $_POST['operacion'] ?? '';

if ($id <= 0 || $cantidad <= 0 || !in_array($operacion, ['sumar', 'restar'], true)) {
    flash('peligro', 'Datos no válidos para ajustar el stock.');
    redirigir('modulos/productos/listar.php');
}

// UPDATE: GREATEST(...,0) evita que el stock quede en negativo al restar
if ($operacion === 'sumar') {
    $st = $conn->prepare("UPDATE productos SET stock = stock + ? WHERE id_producto = ?");
} else {
    $st = $conn->prepare("UPDATE productos SET stock = GREATEST(stock - ?, 0) WHERE id_producto = ?");
}
$st->bind_param("di", $cantidad, $id);
$st->execute();

bitacora('Ajustar stock', "Producto #$id: $operacion $cantidad");
flash('exito', 'Stock actualizado.');
redirigir('modulos/productos/listar.php');

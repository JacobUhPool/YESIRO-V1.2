<?php
/* =====================================================================
   acciones/proveedores/eliminar.php  →  DELETE (ELIMINAR PROVEEDOR)
   Elimina un proveedor de la tabla «proveedores» (DELETE).
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('proveedores.gestionar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirigir('modulos/proveedores/listar.php'); }
verificar_csrf();

$id = (int)($_POST['id'] ?? 0);

// Buscar el nombre (para dejarlo en la bitácora)
$st = $conn->prepare("SELECT nombre FROM proveedores WHERE id_proveedor = ?");
$st->bind_param("i", $id);
$st->execute();
$fila = $st->get_result()->fetch_assoc();
if (!$fila) { flash('peligro', 'Registro no encontrado.'); redirigir('modulos/proveedores/listar.php'); }
$nombre = $fila['nombre'];

try {
    // DELETE: borra el registro con ese id
    $del = $conn->prepare("DELETE FROM proveedores WHERE id_proveedor = ?");
    $del->bind_param("i", $id);
    $del->execute();
} catch (mysqli_sql_exception $e) {
    // MySQL no deja borrar si el registro ya se usa en ventas o compras
    flash('peligro', 'No se puede eliminar: el proveedor ya tiene movimientos (ventas o compras) registrados.');
    redirigir('modulos/proveedores/listar.php');
}

bitacora('Eliminar proveedor', "Proveedor #$id: $nombre");
flash('exito', 'Proveedor eliminado.');
redirigir('modulos/proveedores/listar.php');

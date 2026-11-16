<?php
/* =====================================================================
   acciones/productos/eliminar.php  →  DELETE (ELIMINAR PRODUCTO)
   Elimina un producto de la tabla «productos» (DELETE).
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('productos.gestionar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirigir('modulos/productos/listar.php'); }
verificar_csrf();

$id = (int)($_POST['id'] ?? 0);

// Buscar el nombre (para dejarlo en la bitácora)
$st = $conn->prepare("SELECT nombre FROM productos WHERE id_producto = ?");
$st->bind_param("i", $id);
$st->execute();
$fila = $st->get_result()->fetch_assoc();
if (!$fila) { flash('peligro', 'Registro no encontrado.'); redirigir('modulos/productos/listar.php'); }
$nombre = $fila['nombre'];

try {
    // DELETE: borra el registro con ese id
    $del = $conn->prepare("DELETE FROM productos WHERE id_producto = ?");
    $del->bind_param("i", $id);
    $del->execute();
} catch (mysqli_sql_exception $e) {
    // Si ya tiene ventas o compras, MySQL no deja borrarlo: se DESACTIVA en su lugar
    $up = $conn->prepare("UPDATE productos SET activo = 0 WHERE id_producto = ?");
    $up->bind_param("i", $id);
    $up->execute();
    bitacora('Desactivar producto', "Producto #$id: $nombre");
    flash('aviso', 'El producto ya tiene movimientos, así que se DESACTIVÓ en lugar de eliminarse.');
    redirigir('modulos/productos/listar.php');
}

bitacora('Eliminar producto', "Producto #$id: $nombre");
flash('exito', 'Producto eliminado.');
redirigir('modulos/productos/listar.php');

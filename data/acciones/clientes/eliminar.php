<?php
/* =====================================================================
   acciones/clientes/eliminar.php  →  DELETE (ELIMINAR CLIENTE)
   Elimina un cliente de la tabla «clientes» (DELETE).
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('clientes.editar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirigir('modulos/clientes/listar.php'); }
verificar_csrf();

$id = (int)($_POST['id'] ?? 0);
// Este registro es del sistema (se usa en las ventas sin cliente) y NO se puede borrar
if ($id === 1) { flash('peligro', 'Este registro es del sistema y no se puede eliminar.'); redirigir('modulos/clientes/listar.php'); }

// Buscar el nombre (para dejarlo en la bitácora)
$st = $conn->prepare("SELECT nombre FROM clientes WHERE id_cliente = ?");
$st->bind_param("i", $id);
$st->execute();
$fila = $st->get_result()->fetch_assoc();
if (!$fila) { flash('peligro', 'Registro no encontrado.'); redirigir('modulos/clientes/listar.php'); }
$nombre = $fila['nombre'];

try {
    // DELETE: borra el registro con ese id
    $del = $conn->prepare("DELETE FROM clientes WHERE id_cliente = ?");
    $del->bind_param("i", $id);
    $del->execute();
} catch (mysqli_sql_exception $e) {
    // MySQL no deja borrar si el registro ya se usa en ventas o compras
    flash('peligro', 'No se puede eliminar: el cliente ya tiene movimientos (ventas o compras) registrados.');
    redirigir('modulos/clientes/listar.php');
}

bitacora('Eliminar cliente', "Cliente #$id: $nombre");
flash('exito', 'Cliente eliminado.');
redirigir('modulos/clientes/listar.php');

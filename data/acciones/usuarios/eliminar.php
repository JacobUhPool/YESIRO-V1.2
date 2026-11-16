<?php
/* =====================================================================
   acciones/usuarios/eliminar.php  →  DELETE de un usuario
   Si el usuario ya tiene ventas, cortes o compras, MySQL no deja borrarlo:
   en ese caso lo correcto es DESACTIVARLO.
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('usuarios.gestionar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirigir('modulos/usuarios/listar.php'); }
verificar_csrf();

$id = (int)($_POST['id'] ?? 0);
$u = buscar_usuario($conn, $id);
$volver = 'modulos/usuarios/listar.php';

if (!$u)                                 { flash('peligro', 'Usuario no encontrado.'); redirigir($volver); }
if ($id === (int)$_SESSION['uid'])       { flash('peligro', 'No puedes eliminar tu propia cuenta.'); redirigir($volver); }
if (!puede_gestionar_usuario($u['rol'])) { flash('peligro', 'No tienes permiso sobre esa cuenta.'); redirigir($volver); }
if ($u['rol'] === 'sistemas' && $u['activo'] && contar_activos_rol($conn, 'sistemas') <= 1) {
    flash('peligro', 'Debe quedar al menos una cuenta de Sistemas activa.'); redirigir($volver);
}

try {
    // DELETE
    $st = $conn->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $st->bind_param("i", $id);
    $st->execute();
} catch (mysqli_sql_exception $e) {
    flash('aviso', 'Ese usuario ya tiene ventas, cortes o compras registradas. Mejor desactívalo.');
    redirigir($volver);
}

bitacora('Eliminar usuario', $u['nombre'] . ' (' . $u['correo'] . ')');
flash('exito', 'Usuario «' . $u['nombre'] . '» eliminado.');
redirigir($volver);

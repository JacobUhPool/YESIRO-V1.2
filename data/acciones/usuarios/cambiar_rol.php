<?php
/* =====================================================================
   acciones/usuarios/cambiar_rol.php  →  UPDATE del RANGO de un usuario
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('usuarios.gestionar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirigir('modulos/usuarios/listar.php'); }
verificar_csrf();

$id = (int)($_POST['id'] ?? 0);
$rol = $_POST['rol'] ?? '';
$u = buscar_usuario($conn, $id);
$volver = 'modulos/usuarios/listar.php';

if (!$u)                                   { flash('peligro', 'Usuario no encontrado.'); redirigir($volver); }
if ($id === (int)$_SESSION['uid'])         { flash('peligro', 'No puedes cambiar tu propio rango.'); redirigir($volver); }
if (!puede_gestionar_usuario($u['rol']))   { flash('peligro', 'No tienes permiso sobre esa cuenta.'); redirigir($volver); }
if (!in_array($rol, roles_asignables(), true)) { flash('peligro', 'No puedes dar ese rango.'); redirigir($volver); }

// Nunca debe quedarse el sistema sin una cuenta "sistemas" activa
if ($u['rol'] === 'sistemas' && $u['activo'] && $rol !== 'sistemas' && contar_activos_rol($conn, 'sistemas') <= 1) {
    flash('peligro', 'Debe quedar al menos una cuenta de Sistemas activa.'); redirigir($volver);
}

// UPDATE
$st = $conn->prepare("UPDATE usuarios SET rol = ? WHERE id_usuario = ?");
$st->bind_param("si", $rol, $id);
$st->execute();

bitacora('Cambiar rango', $u['nombre'] . ": " . nombre_rol($u['rol']) . " → " . nombre_rol($rol));
flash('exito', 'Rango de «' . $u['nombre'] . '» cambiado a ' . nombre_rol($rol) . '.');
redirigir($volver);

<?php
/* =====================================================================
   acciones/usuarios/cambiar_estado.php  →  UPDATE: ACTIVAR / DESACTIVAR usuario
   Un usuario desactivado ya no puede entrar (si estaba dentro, se le cierra la sesión).
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('usuarios.gestionar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirigir('modulos/usuarios/listar.php'); }
verificar_csrf();

$id = (int)($_POST['id'] ?? 0);
$u = buscar_usuario($conn, $id);
$volver = 'modulos/usuarios/listar.php';

if (!$u)                                 { flash('peligro', 'Usuario no encontrado.'); redirigir($volver); }
if ($id === (int)$_SESSION['uid'])       { flash('peligro', 'No puedes desactivar tu propia cuenta.'); redirigir($volver); }
if (!puede_gestionar_usuario($u['rol'])) { flash('peligro', 'No tienes permiso sobre esa cuenta.'); redirigir($volver); }
if ($u['rol'] === 'sistemas' && $u['activo'] && contar_activos_rol($conn, 'sistemas') <= 1) {
    flash('peligro', 'Debe quedar al menos una cuenta de Sistemas activa.'); redirigir($volver);
}

// UPDATE: invierte el estado (1 → 0, 0 → 1)
$st = $conn->prepare("UPDATE usuarios SET activo = 1 - activo WHERE id_usuario = ?");
$st->bind_param("i", $id);
$st->execute();

bitacora($u['activo'] ? 'Desactivar usuario' : 'Activar usuario', $u['nombre']);
flash('exito', 'Estado de «' . $u['nombre'] . '» actualizado.');
redirigir($volver);

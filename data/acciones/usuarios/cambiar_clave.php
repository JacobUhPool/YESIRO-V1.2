<?php
/* =====================================================================
   acciones/usuarios/cambiar_clave.php  →  UPDATE de la CONTRASEÑA
   Puedes cambiar tu propia clave o la de quien tu rango administra.
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('usuarios.gestionar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirigir('modulos/usuarios/listar.php'); }
verificar_csrf();

$id = (int)($_POST['id'] ?? 0);
$clave = $_POST['clave'] ?? '';
$u = buscar_usuario($conn, $id);

if (!$u) { flash('peligro', 'Usuario no encontrado.'); redirigir('modulos/usuarios/listar.php'); }
if ($id !== (int)$_SESSION['uid'] && !puede_gestionar_usuario($u['rol'])) {
    flash('peligro', 'No tienes permiso sobre esa cuenta.'); redirigir('modulos/usuarios/listar.php');
}
if (strlen($clave) < 8) { flash('peligro', 'La nueva contraseña debe tener al menos 8 caracteres.'); redirigir('modulos/usuarios/listar.php'); }

// UPDATE: guardar la nueva clave cifrada
$hash = password_hash($clave, PASSWORD_DEFAULT);
$st = $conn->prepare("UPDATE usuarios SET clave_hash = ? WHERE id_usuario = ?");
$st->bind_param("si", $hash, $id);
$st->execute();

bitacora('Cambiar clave', "Usuario #$id: " . $u['nombre']);
flash('exito', 'Contraseña de «' . $u['nombre'] . '» actualizada.');
redirigir('modulos/usuarios/listar.php');

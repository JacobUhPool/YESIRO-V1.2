<?php
/* =====================================================================
   acciones/usuarios/crear.php  →  INSERT de un USUARIO NUEVO
   La contraseña se guarda CIFRADA (password_hash), nunca en texto legible.
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('usuarios.gestionar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirigir('modulos/usuarios/listar.php'); }
verificar_csrf();

$nombre = trim($_POST['nombre'] ?? '');
$correo = strtolower(trim($_POST['correo'] ?? ''));
$clave  = $_POST['clave'] ?? '';
$rol    = $_POST['rol'] ?? '';
$volver = 'modulos/usuarios/listar.php';

// Validaciones
if ($nombre === '')                               { flash('peligro', 'Escribe el nombre.'); redirigir($volver); }
if (!filter_var($correo, FILTER_VALIDATE_EMAIL))  { flash('peligro', 'El correo no es válido.'); redirigir($volver); }
if (strlen($clave) < 8)                           { flash('peligro', 'La contraseña debe tener al menos 8 caracteres.'); redirigir($volver); }
if (!in_array($rol, roles_asignables(), true))    { flash('peligro', 'No tienes permiso para dar ese rango.'); redirigir($volver); }

// ¿Ya existe ese correo?
$st = $conn->prepare("SELECT 1 FROM usuarios WHERE correo = ?");
$st->bind_param("s", $correo);
$st->execute();
if ($st->get_result()->num_rows > 0) { flash('peligro', 'Ya existe un usuario con ese correo.'); redirigir($volver); }

// INSERT
$hash = password_hash($clave, PASSWORD_DEFAULT);
$st = $conn->prepare("INSERT INTO usuarios (nombre, correo, clave_hash, rol) VALUES (?,?,?,?)");
$st->bind_param("ssss", $nombre, $correo, $hash, $rol);
$st->execute();

bitacora('Insertar usuario', "$nombre ($correo) como " . nombre_rol($rol));
flash('exito', "Usuario «$nombre» creado como " . nombre_rol($rol) . ".");
redirigir($volver);

<?php
/* =====================================================================
   acciones/caja/abrir.php  →  INSERT de la APERTURA DE CAJA
   Crea un registro en "cortes_caja" con el fondo inicial del cajero.
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('caja.operar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirigir('modulos/caja/caja.php'); }
verificar_csrf();

$uid = (int)$_SESSION['uid'];
$fondo = (float)($_POST['fondo_inicial'] ?? 0);
if ($fondo < 0) { flash('peligro', 'El fondo no puede ser negativo.'); redirigir('modulos/caja/caja.php'); }

// Un cajero no puede tener dos cajas abiertas a la vez
$st = $conn->prepare("SELECT 1 FROM cortes_caja WHERE id_usuario = ? AND estado = 'abierta'");
$st->bind_param("i", $uid);
$st->execute();
if ($st->get_result()->num_rows > 0) { flash('aviso', 'Ya tienes una caja abierta.'); redirigir('modulos/caja/caja.php'); }

// INSERT: abrir la caja
$ahora = date('Y-m-d H:i:s');
$st = $conn->prepare("INSERT INTO cortes_caja (id_usuario, fecha_apertura, fondo_inicial) VALUES (?,?,?)");
$st->bind_param("isd", $uid, $ahora, $fondo);
$st->execute();

bitacora('Abrir caja', "Fondo inicial: " . dinero($fondo));
flash('exito', 'Caja abierta. ¡Buen turno!');
redirigir('modulos/ventas/cobrar.php');

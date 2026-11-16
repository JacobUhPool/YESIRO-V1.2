<?php
/* =====================================================================
   acciones/proveedores/actualizar.php  →  UPDATE (EDITAR PROVEEDOR)
   Recibe el formulario de editar proveedor (POST) y ACTUALIZA ese registro
   en la tabla «proveedores». Al terminar regresa a la lista.
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('proveedores.gestionar');          // ¿este rango puede editar?

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirigir('modulos/proveedores/listar.php'); }
verificar_csrf();

$id = (int)($_POST['id'] ?? 0);              // cuál registro se está editando
if ($id <= 0) { flash('peligro', 'Registro no válido.'); redirigir('modulos/proveedores/listar.php'); }
$volver = 'modulos/proveedores/formulario.php?id=' . $id;   // a dónde regresar si hay error

// PASO 1: leer los datos del formulario
$v = [];
$v['nombre'] = trim($_POST['nombre'] ?? '');
$v['municipio'] = trim($_POST['municipio'] ?? '');
if ($v['municipio'] === '') { $v['municipio'] = null; }   // vacío = sin dato (NULL)
$v['telefono'] = trim($_POST['telefono'] ?? '');
if ($v['telefono'] === '') { $v['telefono'] = null; }   // vacío = sin dato (NULL)
$v['correo'] = trim($_POST['correo'] ?? '');
if ($v['correo'] === '') { $v['correo'] = null; }   // vacío = sin dato (NULL)
$v['direccion'] = trim($_POST['direccion'] ?? '');
if ($v['direccion'] === '') { $v['direccion'] = null; }   // vacío = sin dato (NULL)

// PASO 2: validar
if ($v['nombre'] === null || $v['nombre'] === '') { flash('peligro', 'El campo «Nombre del proveedor» es obligatorio.'); redirigir($volver); }
if ($v['correo'] !== null && !filter_var($v['correo'], FILTER_VALIDATE_EMAIL)) { flash('peligro', 'El correo no es válido.'); redirigir($volver); }

// PASO 3: guardar con UPDATE (solo el registro con ese id)
$st = $conn->prepare("UPDATE proveedores SET nombre = ?, municipio = ?, telefono = ?, correo = ?, direccion = ? WHERE id_proveedor = ?");
$st->bind_param("sssssi", $v['nombre'], $v['municipio'], $v['telefono'], $v['correo'], $v['direccion'], $id);
$st->execute();

// PASO 4: bitácora, mensaje y volver a la lista
bitacora('Actualizar proveedor', "Proveedor #$id: " . $v['nombre']);
flash('exito', 'Proveedor actualizado correctamente.');
redirigir('modulos/proveedores/listar.php');

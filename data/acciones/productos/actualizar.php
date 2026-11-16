<?php
/* =====================================================================
   acciones/productos/actualizar.php  →  UPDATE (EDITAR PRODUCTO)
   Recibe el formulario de editar producto (POST) y ACTUALIZA ese registro
   en la tabla «productos». Al terminar regresa a la lista.
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('productos.gestionar');          // ¿este rango puede editar?

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirigir('modulos/productos/listar.php'); }
verificar_csrf();

$id = (int)($_POST['id'] ?? 0);              // cuál registro se está editando
if ($id <= 0) { flash('peligro', 'Registro no válido.'); redirigir('modulos/productos/listar.php'); }
$volver = 'modulos/productos/formulario.php?id=' . $id;   // a dónde regresar si hay error

// PASO 1: leer los datos del formulario
$v = [];
$v['nombre'] = trim($_POST['nombre'] ?? '');
$v['id_categoria'] = (($_POST['id_categoria'] ?? '') === '') ? null : (int)$_POST['id_categoria'];
$v['tipo'] = trim($_POST['tipo'] ?? '');
$v['unidad'] = trim($_POST['unidad'] ?? '');
$v['precio'] = (float)($_POST['precio'] ?? 0);
$v['costo'] = (float)($_POST['costo'] ?? 0);
$v['stock'] = (float)($_POST['stock'] ?? 0);
$v['stock_minimo'] = (float)($_POST['stock_minimo'] ?? 0);
$v['activo'] = (int)($_POST['activo'] ?? 0);

// PASO 2: validar
if ($v['nombre'] === null || $v['nombre'] === '') { flash('peligro', 'El campo «Nombre del producto» es obligatorio.'); redirigir($volver); }
if ($v['tipo'] === null || $v['tipo'] === '') { flash('peligro', 'El campo «Tipo» es obligatorio.'); redirigir($volver); }
if ($v['unidad'] === null || $v['unidad'] === '') { flash('peligro', 'El campo «Unidad (pieza, kg, litro...)» es obligatorio.'); redirigir($volver); }
if ($v['precio'] < 0) { flash('peligro', '«Precio de venta (MXN)» no puede ser negativo.'); redirigir($volver); }
if ($v['costo'] < 0) { flash('peligro', '«Costo (MXN)» no puede ser negativo.'); redirigir($volver); }
if ($v['stock'] < 0) { flash('peligro', '«Stock actual» no puede ser negativo.'); redirigir($volver); }
if ($v['stock_minimo'] < 0) { flash('peligro', '«Stock mínimo (alerta)» no puede ser negativo.'); redirigir($volver); }

// PASO 3: guardar con UPDATE (solo el registro con ese id)
$st = $conn->prepare("UPDATE productos SET nombre = ?, id_categoria = ?, tipo = ?, unidad = ?, precio = ?, costo = ?, stock = ?, stock_minimo = ?, activo = ? WHERE id_producto = ?");
$st->bind_param("sissddddii", $v['nombre'], $v['id_categoria'], $v['tipo'], $v['unidad'], $v['precio'], $v['costo'], $v['stock'], $v['stock_minimo'], $v['activo'], $id);
$st->execute();

// PASO 4: bitácora, mensaje y volver a la lista
bitacora('Actualizar producto', "Producto #$id: " . $v['nombre']);
flash('exito', 'Producto actualizado correctamente.');
redirigir('modulos/productos/listar.php');

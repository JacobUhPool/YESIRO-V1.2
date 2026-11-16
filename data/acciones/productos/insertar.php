<?php
/* =====================================================================
   acciones/productos/insertar.php  →  INSERT (AGREGAR PRODUCTO)
   Recibe el formulario de agregar producto (POST), valida los datos y los
   GUARDA en la tabla «productos» de la base de datos. No muestra nada: al
   terminar regresa a la lista con un mensaje.
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('productos.gestionar');          // ¿este rango puede agregar?

// Solo se acepta si viene de un formulario (POST) con su token de seguridad
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirigir('modulos/productos/listar.php'); }
verificar_csrf();

$volver = 'modulos/productos/formulario.php';   // a dónde regresar si hay un error

// PASO 1: leer los datos que mandó el formulario
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

// PASO 3: guardar con INSERT (consulta preparada: los ? se llenan aparte → evita inyección SQL)
$st = $conn->prepare("INSERT INTO productos (nombre, id_categoria, tipo, unidad, precio, costo, stock, stock_minimo, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$st->bind_param("sissddddi", $v['nombre'], $v['id_categoria'], $v['tipo'], $v['unidad'], $v['precio'], $v['costo'], $v['stock'], $v['stock_minimo'], $v['activo']);
$st->execute();
$nuevo_id = $conn->insert_id;

// PASO 4: dejar registro en la bitácora, avisar y volver a la lista
bitacora('Insertar producto', "Producto #$nuevo_id: " . $v['nombre']);
flash('exito', 'Producto guardado correctamente.');
redirigir('modulos/productos/listar.php');

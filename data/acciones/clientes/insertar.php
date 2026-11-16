<?php
/* =====================================================================
   acciones/clientes/insertar.php  →  INSERT (AGREGAR CLIENTE)
   Recibe el formulario de agregar cliente (POST), valida los datos y los
   GUARDA en la tabla «clientes» de la base de datos. No muestra nada: al
   terminar regresa a la lista con un mensaje.
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('clientes.agregar');          // ¿este rango puede agregar?

// Solo se acepta si viene de un formulario (POST) con su token de seguridad
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirigir('modulos/clientes/listar.php'); }
verificar_csrf();

$volver = 'modulos/clientes/formulario.php';   // a dónde regresar si hay un error

// PASO 1: leer los datos que mandó el formulario
$v = [];
$v['nombre'] = trim($_POST['nombre'] ?? '');
$v['telefono'] = trim($_POST['telefono'] ?? '');
if ($v['telefono'] === '') { $v['telefono'] = null; }   // vacío = sin dato (NULL)
$v['correo'] = trim($_POST['correo'] ?? '');
if ($v['correo'] === '') { $v['correo'] = null; }   // vacío = sin dato (NULL)
$v['direccion'] = trim($_POST['direccion'] ?? '');
if ($v['direccion'] === '') { $v['direccion'] = null; }   // vacío = sin dato (NULL)

// PASO 2: validar
if ($v['nombre'] === null || $v['nombre'] === '') { flash('peligro', 'El campo «Nombre completo» es obligatorio.'); redirigir($volver); }
if ($v['correo'] !== null && !filter_var($v['correo'], FILTER_VALIDATE_EMAIL)) { flash('peligro', 'El correo no es válido.'); redirigir($volver); }

// PASO 3: guardar con INSERT (consulta preparada: los ? se llenan aparte → evita inyección SQL)
$st = $conn->prepare("INSERT INTO clientes (nombre, telefono, correo, direccion) VALUES (?, ?, ?, ?)");
$st->bind_param("ssss", $v['nombre'], $v['telefono'], $v['correo'], $v['direccion']);
$st->execute();
$nuevo_id = $conn->insert_id;

// PASO 4: dejar registro en la bitácora, avisar y volver a la lista
bitacora('Insertar cliente', "Cliente #$nuevo_id: " . $v['nombre']);
flash('exito', 'Cliente guardado correctamente.');
redirigir('modulos/clientes/listar.php');

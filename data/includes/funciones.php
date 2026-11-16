<?php
/* =====================================================================
   includes/funciones.php  →  FUNCIONES DE AYUDA (se usan en todo el sistema)
   ===================================================================== */

// url('modulos/clientes/listar.php')  →  /Proyecto_Formulario/modulos/clientes/listar.php
function url($ruta = '') {
    return BASE_URL . ltrim($ruta, '/');
}

// Manda al usuario a otra página y detiene el programa
function redirigir($ruta) {
    header('Location: ' . url($ruta));
    exit;
}

// e() = "escapar": evita que alguien meta código HTML/JS en un nombre (seguridad XSS)
function e($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

// dinero(25.5)  →  $25.50
function dinero($n) {
    return '$' . number_format((float)$n, 2);
}

// ---------------------------------------------------------------------
// MENSAJES (aparecen una sola vez arriba de la siguiente página)
//   flash('exito', 'Guardado');   tipos: exito | peligro | aviso | info
// ---------------------------------------------------------------------
function flash($tipo, $texto) {
    $_SESSION['flash'][] = ['tipo' => $tipo, 'texto' => $texto];
}
function mostrar_flash() {
    if (empty($_SESSION['flash'])) return;
    $mapa = ['exito' => 'success', 'peligro' => 'danger', 'aviso' => 'warning', 'info' => 'info'];
    foreach ($_SESSION['flash'] as $m) {
        $clase = $mapa[$m['tipo']] ?? 'info';
        echo '<div class="alert alert-' . $clase . ' alert-dismissible fade show" role="alert">'
           . e($m['texto'])
           . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
    unset($_SESSION['flash']);   // se muestran una sola vez
}

// ---------------------------------------------------------------------
// SEGURIDAD CSRF: evita que otra página envíe formularios en tu nombre
//   En el formulario se pone  csrf_campo()  y en la acción se llama  verificar_csrf();
// ---------------------------------------------------------------------
function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function csrf_campo() {
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}
function verificar_csrf() {
    if (!isset($_POST['csrf'], $_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
        flash('peligro', 'El formulario expiró. Inténtalo de nuevo.');
        redirigir('panel.php');
    }
}

// ---------------------------------------------------------------------
// BITÁCORA: deja registrado quién hizo qué (la ve el rango "Sistemas")
//   bitacora('Insertar cliente', 'Cliente #5: Ana');
// ---------------------------------------------------------------------
function bitacora($accion, $detalle = '') {
    global $conn;
    try {
        $uid = $_SESSION['uid'] ?? null;
        $nombre = $_SESSION['nombre'] ?? 'Sistema';
        $st = $conn->prepare("INSERT INTO bitacora (id_usuario, usuario_nombre, accion, detalle) VALUES (?,?,?,?)");
        $st->bind_param("isss", $uid, $nombre, $accion, $detalle);
        $st->execute();
    } catch (Throwable $e) {
        // la bitácora nunca debe detener el sistema
    }
}

// ---------------------------------------------------------------------
// BOOTSTRAP: aquí se cargan sus archivos (CDN de internet o carpeta local)
// ---------------------------------------------------------------------
function bootstrap_css() {
    if (BOOTSTRAP_LOCAL) {
        return '<link href="' . url('assets/bootstrap/bootstrap.min.css') . '" rel="stylesheet">';
    }
    return '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">';
}
function bootstrap_js() {
    if (BOOTSTRAP_LOCAL) {
        return '<script src="' . url('assets/bootstrap/bootstrap.bundle.min.js') . '"></script>';
    }
    return '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>';
}

// ---------------------------------------------------------------------
// COMPRAS: al RECIBIR una compra se suma el stock de cada producto
// y se actualiza su costo con el último precio pagado.
// ---------------------------------------------------------------------
function aplicar_recepcion_compra($conn, $id_compra) {
    $st = $conn->prepare("SELECT id_producto, cantidad, costo_unitario FROM compra_detalle WHERE id_compra = ?");
    $st->bind_param("i", $id_compra);
    $st->execute();
    $lineas = $st->get_result()->fetch_all(MYSQLI_ASSOC);

    $up = $conn->prepare("UPDATE productos SET stock = stock + ?, costo = ? WHERE id_producto = ?");
    foreach ($lineas as $l) {
        $cant = (float)$l['cantidad'];
        $costo = (float)$l['costo_unitario'];
        $idp = (int)$l['id_producto'];
        $up->bind_param("ddi", $cant, $costo, $idp);
        $up->execute();
    }
}

// ---------------------------------------------------------------------
// USUARIOS: cuenta cuántos usuarios ACTIVOS hay con un rango.
// Se usa para que nunca se quede el sistema sin una cuenta "sistemas".
// ---------------------------------------------------------------------
function contar_activos_rol($conn, $rol) {
    $st = $conn->prepare("SELECT COUNT(*) c FROM usuarios WHERE rol = ? AND activo = 1");
    $st->bind_param("s", $rol);
    $st->execute();
    return (int)$st->get_result()->fetch_assoc()['c'];
}

// Busca un usuario por su id (devuelve null si no existe)
function buscar_usuario($conn, $id) {
    $st = $conn->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
    $st->bind_param("i", $id);
    $st->execute();
    return $st->get_result()->fetch_assoc();
}

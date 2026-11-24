<?php
/* =====================================================================
   config/sesion.php  →  CONTROL DE ACCESO Y SEGURIDAD
   ===================================================================== */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function logueado() {
    return isset($_SESSION['uid']);
}

function iniciar_sesion($u) {
    session_regenerate_id(true);
    $_SESSION['uid']    = (int)$u['id_usuario'];
    $_SESSION['nombre'] = $u['nombre'];
    $_SESSION['rol']    = $u['rol'];
}

function requiere_login() {
    global $conn;
    if (!logueado()) { redirigir('login.php'); }

    // 🔥 SOLUCIÓN AL BOTÓN DE RETROCESO: Prohibir caché al navegador
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    $uid = (int)$_SESSION['uid'];
    $st = $conn->prepare("SELECT nombre, rol, activo FROM usuarios WHERE id_usuario = ?");
    $st->bind_param("i", $uid);
    $st->execute();
    $u = $st->get_result()->fetch_assoc();

    if (!$u || !$u['activo']) {
        $_SESSION = [];
        session_destroy();
        redirigir('login.php?cerrada=1');
    }
    $_SESSION['nombre'] = $u['nombre'];
    $_SESSION['rol']    = $u['rol'];
}

function requiere_permiso($permiso) {
    requiere_login();
    if (!tiene_permiso($permiso)) {
        http_response_code(403);
        $titulo = "Sin permiso";
        include RUTA_INCLUDES . '/encabezado.php';
        echo '<div class="alert alert-danger shadow-sm">⛔ Tu rango (' . e(nombre_rol($_SESSION['rol'])) . ') no tiene permiso para entrar aquí. Función restringida.</div>';
        echo '<a class="btn btn-cafe" href="' . url('panel.php') . '">Volver al inicio</a>';
        include RUTA_INCLUDES . '/pie.php';
        exit;
    }
}

//  NUEVA FUNCIÓN: PIN DE SEGURIDAD PARA EL JEFE
function requiere_pin_jefe() {
    if ($_SESSION['rol'] === 'jefe') {
        if (isset($_POST['pin_seguridad'])) {
            // Este es el PIN del jefe. Puedes cambiar el '1234' por el que quieras.
            if ($_POST['pin_seguridad'] === '1234') { 
                $_SESSION['pin_verificado'] = true;
            } else {
                flash('peligro', 'Código de verificación incorrecto.');
            }
        }

        // Si el jefe aún no pone su PIN, lo bloqueamos en esta pantalla
        if (empty($_SESSION['pin_verificado'])) {
            $titulo = "Bóveda de Reportes";
            $sin_titulo = true;
            include RUTA_INCLUDES . '/encabezado.php';
            ?>
            <div class="card shadow border-danger mx-auto mt-5" style="max-width: 400px;">
                <div class="card-body text-center p-4">
                    <h4 class="mb-3 text-danger">🔐 Acceso Confidencial</h4>
                    <p class="text-muted">Por seguridad, ingresa tu código de verificación para visualizar los reportes financieros.</p>
                    <form method="post">
                        <input type="password" name="pin_seguridad" class="form-control text-center mb-3 fs-3" placeholder="****" required>
                        <button class="btn btn-danger w-100">Verificar identidad</button>
                    </form>
                </div>
            </div>
            <?php
            include RUTA_INCLUDES . '/pie.php';
            exit;
        }
    }
}
?>
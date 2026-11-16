<?php
/* =====================================================================
   login.php  →  INICIO DE SESIÓN
   - Si aún no existe ningún usuario, pide crear la cuenta "Sistemas".
   - Si ya existen, muestra el formulario de acceso normal.
   ===================================================================== */
require_once __DIR__ . '/config/inicio.php';     //Carga la base de datos y herramientas.

// Si ya inició sesión, se manda al panel
if (logueado()) { redirigir('panel.php'); } // Protege el login. Si el usuario ya metió su contraseña antes y la sesión existe, lo manda directo al panel para que no vea el formulario de nuevo.

// ¿Existe al menos un usuario en la BD?
$hay_usuarios = (int)$conn->query("SELECT COUNT(*) c FROM usuarios")->fetch_assoc()['c'] > 0; // Cuenta cuántos usuarios hay en la base de datos. Si el resultado es cero, $hay_usuarios es falso. Esto sirve para saber si debe mostrar el login normal o la pantalla para crear al primer administrador.
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    // ---- Primera vez: crear la cuenta de Sistemas (acceso total) ----
    if ($accion === 'primer_admin' && !$hay_usuarios) {
        $nombre = trim($_POST['nombre']);
        $correo = strtolower(trim($_POST['correo']));
        $clave  = $_POST['clave'];

        if ($nombre === '') {
            $error = "Escribe un nombre.";
        } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $error = "El correo no es válido.";
        } elseif (strlen($clave) < 8) {
            $error = "La contraseña debe tener al menos 8 caracteres.";
        } elseif ($clave !== $_POST['clave2']) {
            $error = "Las contraseñas no coinciden.";
        } else {
            // La contraseña se guarda cifrada (hash), nunca en texto legible
            $hash = password_hash($clave, PASSWORD_DEFAULT);
            $st = $conn->prepare("INSERT INTO usuarios (nombre, correo, clave_hash, rol) VALUES (?,?,?,'sistemas')");
            $st->bind_param("sss", $nombre, $correo, $hash);
            $st->execute(); 
            iniciar_sesion(['id_usuario' => $conn->insert_id, 'nombre' => $nombre, 'rol' => 'sistemas']);
            bitacora('Cuenta inicial creada', $correo);
            redirigir('modulos/usuarios/listar.php');
        }

    // ---- Inicio de sesión normal ----
    } elseif ($accion === 'login') {
        $correo = strtolower(trim($_POST['correo']));
        $st = $conn->prepare("SELECT * FROM usuarios WHERE correo = ?");
        $st->bind_param("s", $correo);
        $st->execute();
        $u = $st->get_result()->fetch_assoc();

        // password_verify compara lo escrito contra el hash guardado
        if ($u && password_verify($_POST['clave'], $u['clave_hash'])) {
            if (!$u['activo']) {
                $error = "Tu cuenta está desactivada. Habla con el administrador.";
            } else {
                iniciar_sesion($u);
                bitacora('Inicio de sesión');
                redirigir('panel.php');
            }
        } else {
            usleep(400000);   // pequeña espera contra intentos repetidos
            $error = "Correo o contraseña incorrectos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión - <?= e(NOMBRE_SISTEMA) ?></title>
    <!-- Bootstrap y CSS propio -->
    <?= bootstrap_css() ?>
    <link rel="stylesheet" href="<?= url('assets/css/estilos.css') ?>?v=<?= filemtime(RUTA_RAIZ . '/assets/css/estilos.css') ?>">
</head>
<body style="background: var(--beige)"> <!-- fondo de inicio de sesion bienvenida -->

    <!-- Tarjeta centrada con el formulario -->
    <div class="container d-flex align-items-center justify-content-center flex-grow-1 py-4">
        <div class="card shadow" style="width: 100%; max-width: 440px;">

            <div class="card-header text-center text-white py-3" style="background: var(--cafe)">
                <img src="<?= url(LOGO) ?>" alt="Logo" width="90" class="rounded mb-2"><br>
                <span class="fs-5 fw-bold"><?= e(NOMBRE_SISTEMA) ?></span><br>
                <small><?= $hay_usuarios ? "Iniciar sesión" : "Configuración inicial" ?></small>
            </div>

            <div class="card-body p-4">
                <?php if ($error): ?>
                    <div class="alert alert-danger py-2">✖ <?= e($error) ?></div>
                <?php endif; ?>
                <?php if (isset($_GET['salio'])): ?>
                    <div class="alert alert-success py-2">Sesión cerrada correctamente.</div>
                <?php endif; ?>
                <?php if (isset($_GET['cerrada'])): ?>
                    <div class="alert alert-warning py-2">Tu cuenta fue desactivada o eliminada.</div>
                <?php endif; ?>

            <?php if ($hay_usuarios): ?>
                <!-- FORMULARIO DE ACCESO -->
                <form method="post" class="needs-validation" novalidate>
                    <input type="hidden" name="accion" value="login">

                    <label for="correo" class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control mb-3" name="correo" id="correo" required>

                    <label for="clave" class="form-label">Contraseña</label>
                    <input type="password" class="form-control mb-4" name="clave" id="clave" required>

                    <button type="submit" class="btn btn-cafe w-100">Entrar</button>
                </form>

            <?php else: ?>
                <!-- FORMULARIO PARA CREAR LA CUENTA SISTEMAS (solo la primera vez) -->
                <p>Aún no hay usuarios. Crea la cuenta de <b>Sistemas</b> (acceso total); desde ahí podrás crear a los demás.</p>
                <form method="post" class="needs-validation" novalidate>
                    <input type="hidden" name="accion" value="primer_admin">

                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control mb-3" name="nombre" id="nombre" required>

                    <label for="correo" class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control mb-3" name="correo" id="correo" required>

                    <label for="clave" class="form-label">Contraseña (mínimo 8 caracteres)</label>
                    <input type="password" class="form-control mb-3" name="clave" id="clave" minlength="8" required>

                    <label for="clave2" class="form-label">Repetir contraseña</label>
                    <input type="password" class="form-control mb-4" name="clave2" id="clave2" minlength="8" required>

                    <button type="submit" class="btn btn-cafe w-100">Crear cuenta</button>
                    <div class="text-center mt-3"><a href="#">¿Olvidaste tu contraseña?</a></div>
                </form>
            <?php endif; ?>
            </div>
        </div>
    </div>

    <?= bootstrap_js() ?>
    <script src="<?= url('assets/js/validacion.js') ?>"></script>
</body>
</html>

<?php
/* =====================================================================
   modulos/usuarios/listar.php  →  ADMINISTRACIÓN DE USUARIOS
   - Crear usuarios (acciones/usuarios/crear.php)
   - Cambiar clave, rango, activar/desactivar y eliminar (acciones/usuarios/*.php)
   Reglas:  Sistemas administra a todos | Jefe solo a Administrador y Cajero.
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('usuarios.gestionar');

$asignables = roles_asignables();          // rangos que puedo dar
$usuarios = $conn->query("SELECT * FROM usuarios ORDER BY FIELD(rol,'sistemas','jefe','administrador','cajero'), nombre");

$titulo = "Usuarios";
include RUTA_INCLUDES . '/encabezado.php';
?>

<!-- ================= CREAR USUARIO ================= -->
<div class="card shadow-sm mb-4">
    <div class="card-header fw-bold">Crear usuario nuevo</div>
    <div class="card-body">
        <form method="post" action="<?= url('acciones/usuarios/crear.php') ?>" class="needs-validation" novalidate>
            <?= csrf_campo() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="nombre" class="form-label">Nombre</label>
                    <label for="q_busqueda" class="visually-hidden">Buscar</label>
                    <input type="text" class="form-control" name="nombre" id="nombre" required>
                </div>
                <div class="col-md-6">
                    <label for="correo" class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control" name="correo" id="correo" required>
                </div>
                <div class="col-md-6">
                    <label for="clave" class="form-label">Contraseña (mínimo 8 caracteres)</label>
                    <input type="password" class="form-control" name="clave" id="clave" minlength="8" required>
                </div>
                <div class="col-md-6">
                    <label for="rol" class="form-label">Rango</label>
                    <select class="form-select" name="rol" id="rol" required>
                        <?php foreach ($asignables as $r): ?>
                            <option value="<?= $r ?>" <?= $r === 'cajero' ? 'selected' : '' ?>><?= e(nombre_rol($r)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-cafe mt-3">Crear usuario</button>
        </form>
    </div>
</div>

<!-- ================= LISTA ================= -->
<div class="table-responsive">
    <table class="table-hover align-middle">
        <thead class="table-light">
            <tr><th>Nombre</th><th>Correo</th><th>Rango</th><th>Estado</th><th>Acciones</th></tr>
        </thead>
        <tbody>
        <?php while ($f = $usuarios->fetch_assoc()):
            $soyYo = ((int)$f['id_usuario'] === (int)$_SESSION['uid']);
            $puedo = puede_gestionar_usuario($f['rol']);     // ¿puedo administrar a esta persona?
        ?>
            <tr class="<?= $f['activo'] ? '' : 'text-muted' ?>">
                <td><?= e($f['nombre']) ?> <?= $soyYo ? '<small class="text-muted">(tú)</small>' : '' ?></td>
                <td><?= e($f['correo']) ?></td>
                <td><?= e(nombre_rol($f['rol'])) ?></td>
                <td><span class="badge <?= $f['activo'] ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= $f['activo'] ? 'Activo' : 'Desactivado' ?></span></td>
                <td>
                <?php if ($puedo || $soyYo): ?>
                    <!-- Cambiar contraseña -->
                    <form method="post" action="<?= url('acciones/usuarios/cambiar_clave.php') ?>" class="d-inline-flex gap-1 mb-1">
                        <?= csrf_campo() ?><input type="hidden" name="id" value="<?= $f['id_usuario'] ?>">
                        <input type="password" class="form-control form-control-sm" style="width:130px" name="clave" placeholder="Nueva clave" minlength="8" required>
                        <button class="btn btn-sm btn-outline-primary">Cambiar clave</button>
                    </form>
                <?php endif; ?>
                <?php if ($puedo && !$soyYo): ?>
                    <!-- Cambiar rango -->
                    <form method="post" action="<?= url('acciones/usuarios/cambiar_rol.php') ?>" class="d-inline-flex gap-1 mb-1">
                        <?= csrf_campo() ?><input type="hidden" name="id" value="<?= $f['id_usuario'] ?>">
                        <select name="rol" class="form-select form-select-sm" style="width:140px">
                            <?php foreach ($asignables as $r): ?>
                                <option value="<?= $r ?>" <?= $r === $f['rol'] ? 'selected' : '' ?>><?= e(nombre_rol($r)) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-sm btn-outline-secondary">Cambiar rango</button>
                    </form>
                    <!-- Activar / desactivar y eliminar -->
                    <form method="post" action="<?= url('acciones/usuarios/cambiar_estado.php') ?>" class="d-inline mb-1">
                        <?= csrf_campo() ?><input type="hidden" name="id" value="<?= $f['id_usuario'] ?>">
                        <button class="btn btn-sm btn-warning"><?= $f['activo'] ? 'Desactivar' : 'Activar' ?></button>
                    </form>
                    <form method="post" action="<?= url('acciones/usuarios/eliminar.php') ?>" class="d-inline mb-1"
                          data-confirmar="¿Eliminar este usuario?">
                        <?= csrf_campo() ?><input type="hidden" name="id" value="<?= $f['id_usuario'] ?>">
                        <button class="btn btn-sm btn-danger">🗑</button>
                    </form>
                <?php elseif (!$puedo && !$soyYo): ?>
                    <small class="text-muted">Sin permiso sobre este rango</small>
                <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include RUTA_INCLUDES . '/pie.php'; ?>

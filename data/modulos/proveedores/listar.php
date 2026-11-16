<?php
/* =====================================================================
   modulos/proveedores/listar.php  →  LISTA DE PROVEEDORES
   Muestra la tabla de proveedores con buscador. Los botones Agregar, Editar y
   Eliminar apuntan a formulario.php y a la carpeta acciones/proveedores/.
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('proveedores.gestionar');                          // ¿puede ver esta sección?
$puede_agregar = tiene_permiso('proveedores.gestionar');            // ¿ve el botón Agregar?
$puede_editar  = tiene_permiso('proveedores.gestionar');            // ¿ve Editar y Eliminar?

// Buscador: texto que escribió el usuario (?q=...)
$q = trim($_GET['q'] ?? '');
$like = '%' . $q . '%';

// SELECT: trae los registros que coincidan con la búsqueda
$st = $conn->prepare("SELECT * FROM proveedores WHERE CONCAT_WS(' ', nombre, municipio, telefono, correo) LIKE ? ORDER BY nombre");
$st->bind_param("s", $like);
$st->execute();
$filas = $st->get_result();

$titulo = 'Proveedores';
include RUTA_INCLUDES . '/encabezado.php';
?>
<div class="d-flex flex-wrap gap-2 mb-3">
    <form method="get" class="d-flex gap-2">
        <input type="text" class="form-control" name="q" value="<?= e($q) ?>" placeholder="Buscar...">
        <button class="btn btn-outline-secondary">Buscar</button>
    </form>
    <?php if ($puede_agregar): ?>
        <a class="btn btn-cafe ms-auto" href="<?= url('modulos/proveedores/formulario.php') ?>">➕ Agregar proveedor</a>
    <?php endif; ?>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr><th>Nombre</th><th>Municipio</th><th>Teléfono</th><th>Correo</th><?php if ($puede_editar): ?><th>Acciones</th><?php endif; ?></tr>
        </thead>
        <tbody>
        <?php while ($f = $filas->fetch_assoc()): ?>
            <tr><td><?= e($f['nombre']) ?></td><td><?= e($f['municipio']) ?></td><td><?= e($f['telefono']) ?></td><td><?= e($f['correo']) ?></td>
                <?php if ($puede_editar): ?>
                <td>
                    <!-- EDITAR: abre el formulario con los datos cargados -->
                    <a class="btn btn-sm btn-outline-primary" href="<?= url('modulos/proveedores/formulario.php?id=' . $f['id_proveedor']) ?>">✏️ Editar</a>
                    <?php if (true): ?>
                    <!-- ELIMINAR: se envía a acciones/proveedores/eliminar.php (pide confirmación) -->
                    <form method="post" action="<?= url('acciones/proveedores/eliminar.php') ?>" class="d-inline"
                          data-confirmar="¿Eliminar este proveedor?">
                        <?= csrf_campo() ?>
                        <input type="hidden" name="id" value="<?= $f['id_proveedor'] ?>">
                        <button class="btn btn-sm btn-outline-danger">🗑 Eliminar</button>
                    </form>
                    <?php endif; ?>
                </td>
                <?php endif; ?>
            </tr>
        <?php endwhile; ?>
        <?php if ($filas->num_rows === 0): ?>
            <tr><td colspan="5" class="text-center text-muted py-4">No hay registros.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include RUTA_INCLUDES . '/pie.php'; ?>

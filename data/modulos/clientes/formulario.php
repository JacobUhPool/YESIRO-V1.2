<?php
/* =====================================================================
   modulos/clientes/formulario.php  →  FORMULARIO PARA AGREGAR O EDITAR CLIENTE
   - Sin ?id=...  → modo AGREGAR (campos vacíos) → envía a acciones/clientes/insertar.php
   - Con ?id=5    → modo EDITAR (campos llenos)  → envía a acciones/clientes/actualizar.php
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';

$id = (int)($_GET['id'] ?? 0);
$editando = $id > 0;
requiere_permiso($editando ? 'clientes.editar' : 'clientes.agregar');

// Valores iniciales: vacíos (agregar) o los del registro (editar)
$f = [
    'nombre' => '',
    'telefono' => '',
    'correo' => '',
    'direccion' => '',
];
if ($editando) {
    $st = $conn->prepare("SELECT * FROM clientes WHERE id_cliente = ?");
    $st->bind_param("i", $id);
    $st->execute();
    $f = $st->get_result()->fetch_assoc();
    if (!$f) { flash('peligro', 'No se encontró el registro.'); redirigir('modulos/clientes/listar.php'); }
}

$titulo = $editando ? 'Editar cliente' : 'Agregar cliente';
include RUTA_INCLUDES . '/encabezado.php';
?>
<div class="card shadow-sm">
    <div class="card-body">
        <!-- El formulario se envía a insertar.php o actualizar.php según el modo -->
        <form method="post" action="<?= url('acciones/clientes/' . ($editando ? 'actualizar.php' : 'insertar.php')) ?>"
              class="needs-validation" novalidate>
            <?= csrf_campo() ?>
            <?php if ($editando): ?><input type="hidden" name="id" value="<?= $id ?>"><?php endif; ?>

            <div class="row g-3">
<div class="col-md-6">
<label for="nombre" class="form-label">Nombre completo</label>
<input type="text" class="form-control" name="nombre" id="nombre" value="<?= e($f['nombre']) ?>" maxlength="100" required>
<div class="invalid-feedback">Este campo es obligatorio.</div>
</div>
<div class="col-md-3">
<label for="telefono" class="form-label">Teléfono</label>
<input type="tel" class="form-control" name="telefono" id="telefono" value="<?= e($f['telefono']) ?>" maxlength="20">

</div>
<div class="col-md-3">
<label for="correo" class="form-label">Correo electrónico</label>
<input type="email" class="form-control" name="correo" id="correo" value="<?= e($f['correo']) ?>" maxlength="100">

</div>
<div class="col-md-12">
<label for="direccion" class="form-label">Dirección</label>
<textarea class="form-control" name="direccion" id="direccion" rows="2"><?= e($f['direccion']) ?></textarea>

</div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-cafe">Guardar</button>
                <a href="<?= url('modulos/clientes/listar.php') ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php include RUTA_INCLUDES . '/pie.php'; ?>

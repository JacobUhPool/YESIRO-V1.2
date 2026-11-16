<?php
/* =====================================================================
   modulos/proveedores/formulario.php  →  FORMULARIO PARA AGREGAR O EDITAR PROVEEDOR
   - Sin ?id=...  → modo AGREGAR (campos vacíos) → envía a acciones/proveedores/insertar.php
   - Con ?id=5    → modo EDITAR (campos llenos)  → envía a acciones/proveedores/actualizar.php
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';

$id = (int)($_GET['id'] ?? 0);
$editando = $id > 0;
requiere_permiso($editando ? 'proveedores.gestionar' : 'proveedores.gestionar');

// Valores iniciales: vacíos (agregar) o los del registro (editar)
$f = [
    'nombre' => '',
    'municipio' => '',
    'telefono' => '',
    'correo' => '',
    'direccion' => '',
];
if ($editando) {
    $st = $conn->prepare("SELECT * FROM proveedores WHERE id_proveedor = ?");
    $st->bind_param("i", $id);
    $st->execute();
    $f = $st->get_result()->fetch_assoc();
    if (!$f) { flash('peligro', 'No se encontró el registro.'); redirigir('modulos/proveedores/listar.php'); }
}

$titulo = $editando ? 'Editar proveedor' : 'Agregar proveedor';
include RUTA_INCLUDES . '/encabezado.php';
?>
<div class="card shadow-sm">
    <div class="card-body">
        <!-- El formulario se envía a insertar.php o actualizar.php según el modo -->
        <form method="post" action="<?= url('acciones/proveedores/' . ($editando ? 'actualizar.php' : 'insertar.php')) ?>"
              class="needs-validation" novalidate>
            <?= csrf_campo() ?>
            <?php if ($editando): ?><input type="hidden" name="id" value="<?= $id ?>"><?php endif; ?>

            <div class="row g-3">
<div class="col-md-6">
<label for="nombre" class="form-label">Nombre del proveedor</label>
<input type="text" class="form-control" name="nombre" id="nombre" value="<?= e($f['nombre']) ?>" maxlength="100" required>
<div class="invalid-feedback">Este campo es obligatorio.</div>
</div>
<div class="col-md-6">
<label for="municipio" class="form-label">Municipio</label>
<select class="form-select" name="municipio" id="municipio">
    <option value="">Selecciona...</option>
    <option value="Mérida" <?= (string)$f['municipio'] === 'Mérida' ? 'selected' : '' ?>>Mérida</option>
    <option value="Valladolid" <?= (string)$f['municipio'] === 'Valladolid' ? 'selected' : '' ?>>Valladolid</option>
    <option value="Tizimín" <?= (string)$f['municipio'] === 'Tizimín' ? 'selected' : '' ?>>Tizimín</option>
    <option value="Izamal" <?= (string)$f['municipio'] === 'Izamal' ? 'selected' : '' ?>>Izamal</option>
    <option value="Espita" <?= (string)$f['municipio'] === 'Espita' ? 'selected' : '' ?>>Espita</option>
    <option value="Kantunilkín" <?= (string)$f['municipio'] === 'Kantunilkín' ? 'selected' : '' ?>>Kantunilkín</option>
    <option value="Playa del Carmen" <?= (string)$f['municipio'] === 'Playa del Carmen' ? 'selected' : '' ?>>Playa del Carmen</option>
    <option value="Sucilá" <?= (string)$f['municipio'] === 'Sucilá' ? 'selected' : '' ?>>Sucilá</option>
    <option value="Panabá" <?= (string)$f['municipio'] === 'Panabá' ? 'selected' : '' ?>>Panabá</option>
    <option value="Calotmul" <?= (string)$f['municipio'] === 'Calotmul' ? 'selected' : '' ?>>Calotmul</option>
    <option value="Kikil" <?= (string)$f['municipio'] === 'Kikil' ? 'selected' : '' ?>>Kikil</option>
    <option value="Otro" <?= (string)$f['municipio'] === 'Otro' ? 'selected' : '' ?>>Otro</option>
</select>

</div>
<div class="col-md-6">
<label for="telefono" class="form-label">Teléfono</label>
<input type="tel" class="form-control" name="telefono" id="telefono" value="<?= e($f['telefono']) ?>" maxlength="20">

</div>
<div class="col-md-6">
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
                <a href="<?= url('modulos/proveedores/listar.php') ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php include RUTA_INCLUDES . '/pie.php'; ?>

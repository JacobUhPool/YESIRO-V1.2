<?php
/* =====================================================================
   modulos/productos/formulario.php  →  FORMULARIO PARA AGREGAR O EDITAR PRODUCTO
   - Sin ?id=...  → modo AGREGAR (campos vacíos) → envía a acciones/productos/insertar.php
   - Con ?id=5    → modo EDITAR (campos llenos)  → envía a acciones/productos/actualizar.php
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';

$id = (int)($_GET['id'] ?? 0);
$editando = $id > 0;
requiere_permiso($editando ? 'productos.gestionar' : 'productos.gestionar');

// Valores iniciales: vacíos (agregar) o los del registro (editar)
$f = [
    'nombre' => '',
    'id_categoria' => '',
    'tipo' => 'terminado',
    'unidad' => 'pieza',
    'precio' => '0',
    'costo' => '0',
    'stock' => '0',
    'stock_minimo' => '5',
    'activo' => '1',
];
if ($editando) {
    $st = $conn->prepare("SELECT * FROM productos WHERE id_producto = ?");
    $st->bind_param("i", $id);
    $st->execute();
    $f = $st->get_result()->fetch_assoc();
    if (!$f) { flash('peligro', 'No se encontró el registro.'); redirigir('modulos/productos/listar.php'); }
}
// Opciones del campo «Sección» (salen de la base de datos)
$opc_id_categoria = $conn->query("SELECT id_categoria, nombre FROM categorias ORDER BY nombre");

$titulo = $editando ? 'Editar producto' : 'Agregar producto';
include RUTA_INCLUDES . '/encabezado.php';
?>
<div class="card shadow-sm">
    <div class="card-body">
        <!-- El formulario se envía a insertar.php o actualizar.php según el modo -->
        <form method="post" action="<?= url('acciones/productos/' . ($editando ? 'actualizar.php' : 'insertar.php')) ?>"
              class="needs-validation" novalidate>
            <?= csrf_campo() ?>
            <?php if ($editando): ?><input type="hidden" name="id" value="<?= $id ?>"><?php endif; ?>

            <div class="row g-3">
<div class="col-md-6">
<label for="nombre" class="form-label">Nombre del producto</label>
<input type="text" class="form-control" name="nombre" id="nombre" value="<?= e($f['nombre']) ?>" maxlength="100" required>
<div class="invalid-feedback">Este campo es obligatorio.</div>
</div>
<div class="col-md-6">
<label for="id_categoria" class="form-label">Sección</label>
<select class="form-select" name="id_categoria" id="id_categoria">
    <option value="">Sin sección</option>
    <?php while ($o = $opc_id_categoria->fetch_assoc()): ?>
        <option value="<?= $o['id_categoria'] ?>" <?= (string)$f['id_categoria'] === (string)$o['id_categoria'] ? 'selected' : '' ?>><?= e($o['nombre']) ?></option>
    <?php endwhile; ?>
</select>

</div>
<div class="col-md-3">
<label for="tipo" class="form-label">Tipo</label>
<select class="form-select" name="tipo" id="tipo" required>
    <option value="terminado" <?= (string)$f['tipo'] === 'terminado' ? 'selected' : '' ?>>Producto terminado (se vende)</option>
    <option value="insumo" <?= (string)$f['tipo'] === 'insumo' ? 'selected' : '' ?>>Insumo (materia prima)</option>
</select>
<div class="invalid-feedback">Este campo es obligatorio.</div>
</div>
<div class="col-md-3">
<label for="unidad" class="form-label">Unidad (pieza, kg, litro...)</label>
<input type="text" class="form-control" name="unidad" id="unidad" value="<?= e($f['unidad']) ?>" maxlength="20" required>
<div class="invalid-feedback">Este campo es obligatorio.</div>
</div>
<div class="col-md-3">
<label for="precio" class="form-label">Precio de venta (MXN)</label>
<input type="number" class="form-control" name="precio" id="precio" step="0.01" min="0" value="<?= e($f['precio']) ?>">

</div>
<div class="col-md-3">
<label for="costo" class="form-label">Costo (MXN)</label>
<input type="number" class="form-control" name="costo" id="costo" step="0.01" min="0" value="<?= e($f['costo']) ?>">

</div>
<div class="col-md-3">
<label for="stock" class="form-label">Stock actual</label>
<input type="number" class="form-control" name="stock" id="stock" step="0.01" min="0" value="<?= e($f['stock']) ?>">

</div>
<div class="col-md-3">
<label for="stock_minimo" class="form-label">Stock mínimo (alerta)</label>
<input type="number" class="form-control" name="stock_minimo" id="stock_minimo" step="0.01" min="0" value="<?= e($f['stock_minimo']) ?>">

</div>
<div class="col-md-3">
<label for="activo" class="form-label">Estado</label>
<select class="form-select" name="activo" id="activo">
    <option value="1" <?= (string)$f['activo'] === '1' ? 'selected' : '' ?>>Activo</option>
    <option value="0" <?= (string)$f['activo'] === '0' ? 'selected' : '' ?>>Inactivo</option>
</select>

</div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-cafe">Guardar</button>
                <a href="<?= url('modulos/productos/listar.php') ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php include RUTA_INCLUDES . '/pie.php'; ?>

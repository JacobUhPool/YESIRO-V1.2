<?php
/* =====================================================================
   modulos/compras/nueva.php  →  FORMULARIO DE NUEVA COMPRA A PROVEEDOR
   Eliges proveedor y los insumos con cantidad y costo.
   - Si marcas "ya recibí la mercancía", el stock se suma de inmediato.
   - Si no, queda "pendiente" y se recibe después desde la lista.
   Guarda: acciones/compras/registrar.php     JS: assets/js/compra.js
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('compras.gestionar');

$proveedores = $conn->query("SELECT id_proveedor, nombre, municipio FROM proveedores ORDER BY nombre");
// Insumos activos (los productos que se compran); se pueden comprar también terminados
$productos = $conn->query("SELECT id_producto, nombre, unidad, costo, tipo FROM productos WHERE activo = 1 ORDER BY tipo ASC, nombre");
$lista = $productos->fetch_all(MYSQLI_ASSOC);

$scripts = ['assets/js/compra.js'];
$titulo = "Nueva compra";
include RUTA_INCLUDES . '/encabezado.php';
?>
<div class="card shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= url('acciones/compras/registrar.php') ?>" class="needs-validation" novalidate>
            <?= csrf_campo() ?>
            <div class="row g-3 mb-3">
                <div class="col-md-5">
                    <label for="id_proveedor" class="form-label">Proveedor</label>
                    <select name="id_proveedor" id="id_proveedor" class="form-select" required>
                        <option value="">Selecciona...</option>
                        <?php while ($p = $proveedores->fetch_assoc()): ?>
                            <option value="<?= $p['id_proveedor'] ?>"><?= e($p['nombre']) ?> (<?= e($p['municipio']) ?>)</option>
                        <?php endwhile; ?>
                    </select>
                    <div class="invalid-feedback">Selecciona un proveedor.</div>
                </div>
                <div class="col-md-3">
                    <label for="fecha" class="form-label">Fecha</label>
                    <input type="date" name="fecha" id="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="notas" class="form-label">Notas (opcional)</label>
                    <input type="text" name="notas" id="notas" class="form-control" maxlength="200">
                </div>
            </div>

            <!-- Filas de insumos -->
            <table class="table table-sm align-middle">
                <thead><tr><th>Producto / insumo</th><th style="width:140px">Cantidad</th><th style="width:160px">Costo unitario</th><th style="width:60px"></th></tr></thead>
                <tbody id="filas-compra"></tbody>
            </table>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="agregar-fila">➕ Agregar otro insumo</button>

            <div class="d-flex justify-content-end align-items-center gap-3 mt-3">
                <span class="fs-5">Total:</span><span class="total-grande" id="total-compra">$0.00</span>
            </div>

            <div class="form-check mt-3">
                <input class="form-check-input" type="checkbox" name="recibida" id="recibida" value="1" checked>
                <label class="form-check-label" for="recibida">Ya recibí la mercancía (sumar al inventario ahora)</label>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-cafe">Guardar compra</button>
                <a href="<?= url('modulos/compras/listar.php') ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<!-- Plantilla de una fila (JavaScript la copia cada vez que agregas un insumo) -->
<template id="fila-plantilla">
    <tr>
        <td>
            <select name="id_producto[]" class="form-select form-select-sm producto" required>
                <option value="">Selecciona...</option>
                <?php foreach ($lista as $p): ?>
                    <option value="<?= $p['id_producto'] ?>" data-costo="<?= e($p['costo']) ?>">
                        <?= e($p['nombre']) ?> (<?= e($p['unidad']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td><input type="number" name="cantidad[]" class="form-control form-control-sm cant" min="0.01" step="0.01" required></td>
        <td><input type="number" name="costo[]" class="form-control form-control-sm costo" min="0" step="0.01" required></td>
        <td><button type="button" class="btn btn-sm btn-outline-danger quitar">✕</button></td>
    </tr>
</template>
<?php include RUTA_INCLUDES . '/pie.php'; ?>

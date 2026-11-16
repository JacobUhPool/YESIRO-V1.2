<?php
/* =====================================================================
   modulos/ventas/cobrar.php  →  PANTALLA DE COBRO (caja registradora)
   Flujo del cajero:  1) tener la caja abierta  2) elegir panes
   3) elegir cliente y forma de pago  4) cobrar.
   El JavaScript está en assets/js/cobrar.js y el guardado en
   acciones/ventas/registrar.php
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('ventas.cobrar');

// ¿Este usuario tiene su caja abierta? (no se puede cobrar sin abrir caja)
$uid = (int)$_SESSION['uid'];
$st = $conn->prepare("SELECT id_corte, fecha_apertura FROM cortes_caja WHERE id_usuario = ? AND estado = 'abierta' LIMIT 1");
$st->bind_param("i", $uid);
$st->execute();
$corte = $st->get_result()->fetch_assoc();

$titulo = "Cobrar (nueva venta)";

if (!$corte) {
    include RUTA_INCLUDES . '/encabezado.php';
    echo '<div class="alert alert-warning">Primero debes <b>abrir tu caja</b> para poder cobrar.</div>';
    echo '<a class="btn btn-cafe" href="' . url('modulos/caja/caja.php') . '">🏧 Abrir caja</a>';
    include RUTA_INCLUDES . '/pie.php';
    exit;
}

// Panes y bebidas que se pueden vender: terminados, activos y con existencias
$res = $conn->query("SELECT p.id_producto AS id, p.nombre, p.precio, p.stock,
                            COALESCE(c.nombre, 'Sin sección') AS categoria
                     FROM productos p LEFT JOIN categorias c ON c.id_categoria = p.id_categoria
                     WHERE p.tipo = 'terminado' AND p.activo = 1 AND p.stock > 0
                     ORDER BY c.nombre, p.nombre");
$productos = [];
while ($f = $res->fetch_assoc()) {
    $f['id'] = (int)$f['id'];
    $f['precio'] = (float)$f['precio'];
    $f['stock'] = (float)$f['stock'];
    $productos[] = $f;
}
$secciones = array_values(array_unique(array_column($productos, 'categoria')));   // pestañas

// Clientes (el 1 es "Público en general")
$clientes = $conn->query("SELECT id_cliente, nombre FROM clientes ORDER BY id_cliente = 1 DESC, nombre");

$scripts = ['assets/js/cobrar.js'];   // JS propio de esta página
include RUTA_INCLUDES . '/encabezado.php';
?>
<!-- Los productos pasan de PHP a JavaScript en formato JSON -->
<script>window.PRODUCTOS = <?= json_encode($productos, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;</script>

<div class="row g-3">

    <!-- ========== IZQUIERDA: productos ========== -->
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <input type="text" id="buscar" class="form-control mb-3" placeholder="🔍 Buscar pan o bebida...">

                <!-- Secciones (Pan dulce, Pan salado, ...) -->
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <button type="button" class="btn btn-sm btn-outline-secondary active" data-seccion="todas">Todas</button>
                    <?php foreach ($secciones as $s): ?>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-seccion="<?= e($s) ?>"><?= e($s) ?></button>
                    <?php endforeach; ?>
                </div>

                <div class="row g-2 lista-productos" id="lista-productos"></div>
            </div>
        </div>
    </div>

    <!-- ========== DERECHA: carrito y cobro ========== -->
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">🧾 Ticket actual</div>
            <div class="card-body">
                <form method="post" action="<?= url('acciones/ventas/registrar.php') ?>" id="form-cobro">
                    <?= csrf_campo() ?>
                    <input type="hidden" name="carrito" id="campo-carrito" value="[]">

                    <table class="table table-sm align-middle">
                        <thead><tr><th>Producto</th><th>Cant.</th><th class="text-end">Importe</th><th></th></tr></thead>
                        <tbody id="carrito-filas"></tbody>
                    </table>
                    <p id="carrito-vacio" class="text-muted">Aún no hay productos. Toca un pan para agregarlo.</p>

                    <div class="d-flex justify-content-between align-items-center border-top pt-2 mb-3">
                        <span class="fs-5">Total</span>
                        <span class="total-grande" id="total">$0.00</span>
                    </div>

                    <!-- Cliente -->
                    <label for="id_cliente" class="form-label">Cliente</label>
                    <select name="id_cliente" id="id_cliente" class="form-select mb-3">
                        <?php while ($c = $clientes->fetch_assoc()): ?>
                            <option value="<?= $c['id_cliente'] ?>"><?= e($c['nombre']) ?></option>
                        <?php endwhile; ?>
                    </select>

                    <!-- Forma de pago -->
                    <label class="form-label d-block">Forma de pago</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="metodo_pago" id="pago_efectivo" value="Efectivo" checked>
                        <label class="form-check-label" for="pago_efectivo">Efectivo</label>
                    </div>
                    <div class="form-check form-check-inline mb-3">
                        <input class="form-check-input" type="radio" name="metodo_pago" id="pago_tarjeta" value="Tarjeta">
                        <label class="form-check-label" for="pago_tarjeta">Tarjeta</label>
                    </div>

                    <!-- Solo para efectivo: recibido y cambio -->
                    <div id="bloque-efectivo" class="mb-3">
                        <div class="input-group mb-2">
                            <span class="input-group-text">Recibido $</span>
                            <input type="number" class="form-control" name="recibido" id="recibido" min="0" step="0.01">
                            <button type="button" class="btn btn-outline-secondary" id="btn-exacto">Exacto</button>
                        </div>
                        <div>Cambio: <b id="cambio">—</b></div>
                    </div>

                    <button type="submit" class="btn btn-cafe w-100 btn-lg">Cobrar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include RUTA_INCLUDES . '/pie.php'; ?>

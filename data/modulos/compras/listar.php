<?php
/* =====================================================================
   modulos/compras/listar.php  →  LISTA DE COMPRAS (pedidos a proveedores)
   Estados:  pendiente → (Recibir) → recibida  |  pendiente → (Cancelar) → cancelada
   Botones → acciones/compras/recibir.php y cancelar.php
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('compras.gestionar');

// SELECT: compras con proveedor, quién la registró y un resumen de lo que incluye
$compras = $conn->query("SELECT c.*, p.nombre AS proveedor, u.nombre AS usuario,
        (SELECT GROUP_CONCAT(CONCAT(pr.nombre, ' x', cd.cantidad+0) SEPARATOR ', ')
           FROM compra_detalle cd JOIN productos pr ON pr.id_producto = cd.id_producto
          WHERE cd.id_compra = c.id_compra) AS articulos
    FROM compras c
    JOIN proveedores p ON p.id_proveedor = c.id_proveedor
    JOIN usuarios u ON u.id_usuario = c.id_usuario
    ORDER BY c.id_compra DESC LIMIT 100");

$colores = ['pendiente' => 'text-bg-warning', 'recibida' => 'text-bg-success', 'cancelada' => 'text-bg-secondary'];

$titulo = "Compras";
include RUTA_INCLUDES . '/encabezado.php';
?>
<a class="btn btn-cafe mb-3" href="<?= url('modulos/compras/nueva.php') ?>">➕ Nueva compra</a>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr><th>#</th><th>Fecha</th><th>Proveedor</th><th>Artículos</th><th class="text-end">Total</th><th>Estado</th><th>Acciones</th></tr>
        </thead>
        <tbody>
        <?php while ($c = $compras->fetch_assoc()): ?>
            <tr>
                <td><?= $c['id_compra'] ?></td>
                <td><?= e($c['fecha']) ?></td>
                <td><?= e($c['proveedor']) ?><div class="small text-muted">Registró: <?= e($c['usuario']) ?></div></td>
                <td class="small"><?= e($c['articulos']) ?></td>
                <td class="text-end"><?= dinero($c['total']) ?></td>
                <td><span class="badge <?= $colores[$c['estado']] ?>"><?= ucfirst($c['estado']) ?></span></td>
                <td>
                    <?php if ($c['estado'] === 'pendiente'): ?>
                        <!-- RECIBIR: suma el stock al inventario -->
                        <form method="post" action="<?= url('acciones/compras/recibir.php') ?>" class="d-inline"
                              data-confirmar="¿Ya llegó la mercancía? Se sumará al inventario.">
                            <?= csrf_campo() ?><input type="hidden" name="id" value="<?= $c['id_compra'] ?>">
                            <button class="btn btn-sm btn-success">Recibir</button>
                        </form>
                        <!-- CANCELAR -->
                        <form method="post" action="<?= url('acciones/compras/cancelar.php') ?>" class="d-inline"
                              data-confirmar="¿Cancelar este pedido?">
                            <?= csrf_campo() ?><input type="hidden" name="id" value="<?= $c['id_compra'] ?>">
                            <button class="btn btn-sm btn-outline-danger">Cancelar</button>
                        </form>
                    <?php else: ?>—<?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include RUTA_INCLUDES . '/pie.php'; ?>

<?php
/* =====================================================================
   modulos/productos/listar.php  →  LISTA DE PRODUCTOS E INSUMOS
   - Pestañas: Todos | Terminados (se venden) | Insumos (materia prima)
   - Buscador, alerta de stock bajo y ajuste rápido de stock (+ / −)
   - Botones Agregar / Editar / Eliminar → formulario.php y acciones/productos/
   ===================================================================== */
require_once __DIR__ . '/../../config/inicio.php';
requiere_permiso('productos.gestionar');

// Filtros que vienen en la URL (?tipo=...&q=...)
$tipo = $_GET['tipo'] ?? 'todos';
if (!in_array($tipo, ['todos', 'terminado', 'insumo'], true)) { $tipo = 'todos'; }
$q = trim($_GET['q'] ?? '');
$like = '%' . $q . '%';

// SELECT: productos con el nombre de su sección (JOIN con categorias)
$sql = "SELECT p.*, c.nombre AS seccion
        FROM productos p LEFT JOIN categorias c ON c.id_categoria = p.id_categoria
        WHERE p.nombre LIKE ?";
if ($tipo !== 'todos') { $sql .= " AND p.tipo = ?"; }
$sql .= " ORDER BY p.tipo, c.nombre, p.nombre";

$st = $conn->prepare($sql);
if ($tipo !== 'todos') { $st->bind_param("ss", $like, $tipo); }
else                   { $st->bind_param("s", $like); }
$st->execute();
$filas = $st->get_result();

$titulo = "Productos e insumos";
include RUTA_INCLUDES . '/encabezado.php';
?>

<!-- Pestañas por tipo -->
<ul class="nav nav-pills mb-3">
    <?php foreach (['todos' => 'Todos', 'terminado' => 'Terminados (venta)', 'insumo' => 'Insumos (materia prima)'] as $k => $txt): ?>
        <li class="nav-item">
            <a class="nav-link <?= $tipo === $k ? 'active' : '' ?>"
               href="?tipo=<?= $k ?>&q=<?= urlencode($q) ?>"><?= $txt ?></a>
        </li>
    <?php endforeach; ?>
</ul>

<div class="d-flex flex-wrap gap-2 mb-3">
    <form method="get" class="d-flex gap-2">
        <input type="hidden" name="tipo" value="<?= e($tipo) ?>">
        <input type="text" class="form-control" name="q" value="<?= e($q) ?>" placeholder="Buscar producto...">
        <button class="btn btn-outline-secondary">Buscar</button>
    </form>
    <a class="btn btn-cafe ms-auto" href="<?= url('modulos/productos/formulario.php') ?>">➕ Agregar producto</a>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Producto</th><th>Sección</th><th>Tipo</th><th>Precio</th><th>Costo</th>
                <th>Stock</th><th>Ajustar stock</th><th>Estado</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($f = $filas->fetch_assoc()):
            $bajo = (float)$f['stock'] <= (float)$f['stock_minimo'];   // ¿stock bajo?
        ?>
            <tr class="<?= $f['activo'] ? '' : 'text-muted' ?>">
                <td><?= e($f['nombre']) ?></td>
                <td><?= e($f['seccion'] ?? '—') ?></td>
                <td><?= $f['tipo'] === 'terminado' ? 'Terminado' : 'Insumo' ?></td>
                <td><?= $f['tipo'] === 'terminado' ? dinero($f['precio']) : '—' ?></td>
                <td><?= dinero($f['costo']) ?></td>
                <td>
                    <span class="badge <?= $bajo ? 'text-bg-danger' : 'text-bg-info' ?>">
                        <?= rtrim(rtrim(number_format($f['stock'], 2, '.', ''), '0'), '.') ?> <?= e($f['unidad']) ?>
                    </span>
                    <?php if ($bajo): ?><small class="text-danger d-block">Stock bajo</small><?php endif; ?>
                </td>
                <td>
                    <!-- AJUSTAR STOCK: suma o resta existencias (acciones/productos/ajustar_stock.php) -->
                    <form method="post" action="<?= url('acciones/productos/ajustar_stock.php') ?>" class="d-inline-flex gap-1">
                        <?= csrf_campo() ?>
                        <input type="hidden" name="id" value="<?= $f['id_producto'] ?>">
                        <input type="number" name="cantidad" class="form-control form-control-sm" style="width:80px"
                               min="0.01" step="0.01" placeholder="Cant." required>
                        <button name="operacion" value="sumar" class="btn btn-sm btn-success">+</button>
                        <button name="operacion" value="restar" class="btn btn-sm btn-warning">−</button>
                    </form>
                </td>
                <td><span class="badge <?= $f['activo'] ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= $f['activo'] ? 'Activo' : 'Inactivo' ?></span></td>
                <td>
                    <a class="btn btn-sm btn-outline-primary" href="<?= url('modulos/productos/formulario.php?id=' . $f['id_producto']) ?>">✏️</a>
                    <form method="post" action="<?= url('acciones/productos/eliminar.php') ?>" class="d-inline"
                          data-confirmar="¿Eliminar este producto? (si ya tiene ventas se desactivará)">
                        <?= csrf_campo() ?>
                        <input type="hidden" name="id" value="<?= $f['id_producto'] ?>">
                        <button class="btn btn-sm btn-outline-danger">🗑</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        <?php if ($filas->num_rows === 0): ?>
            <tr><td colspan="9" class="text-center text-muted py-4">No hay productos.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<p class="small text-muted">Etiqueta roja = el stock llegó al mínimo configurado en cada producto.</p>
<?php include RUTA_INCLUDES . '/pie.php'; ?>

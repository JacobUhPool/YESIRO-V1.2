<?php
/* =====================================================================
   config/esquema.php  →  CREA LAS TABLAS DE LA BASE DE DATOS SOLAS
   La primera vez que abres el sistema, este archivo crea en phpMyAdmin
   (base ventas_prueba) todas las tablas y mete datos de ejemplo
   (panes yucatecos, insumos, proveedores). No hay que crear nada a mano.
   Tablas que crea:
     usuarios, categorias, productos, clientes, proveedores,
     cortes_caja, tickets, ticket_detalle, compras, compra_detalle,
     bitacora, config_sistema
   (Las tablas viejas "inventario" y "ventas" no se tocan: ya no se usan.)
   ===================================================================== */

// Lee de la BD en qué versión está el esquema (0 = todavía no se ha creado)
function version_esquema($conn) {
    try {
        $r = $conn->query("SELECT valor FROM config_sistema WHERE clave = 'esquema'");
        $f = $r->fetch_assoc();
        return $f ? (int)$f['valor'] : 0;
    } catch (mysqli_sql_exception $e) {
        return 0;     // la tabla config_sistema aún no existe
    }
}

function instalar_esquema($conn) {
    $t = [];   // lista de instrucciones CREATE TABLE

    $t[] = <<<'SQL'
CREATE TABLE IF NOT EXISTS config_sistema (
    clave VARCHAR(50) PRIMARY KEY,
    valor VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
SQL;

    // USUARIOS: quién puede entrar al sistema y con qué rango
    $t[] = <<<'SQL'
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario     INT AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(100) NOT NULL,
    correo         VARCHAR(100) NOT NULL UNIQUE,
    clave_hash     VARCHAR(255) NOT NULL,
    rol            VARCHAR(20) NOT NULL DEFAULT 'cajero',
    activo         TINYINT(1) NOT NULL DEFAULT 1,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
SQL;

    // CATEGORÍAS: las secciones de la panadería (pan dulce, pan salado...)
    $t[] = <<<'SQL'
CREATE TABLE IF NOT EXISTS categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre       VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
SQL;

    // PRODUCTOS: panes terminados (se venden) e insumos (materia prima)
    $t[] = <<<'SQL'
CREATE TABLE IF NOT EXISTS productos (
    id_producto    INT AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(100) NOT NULL,
    id_categoria   INT NULL,
    tipo           ENUM('terminado','insumo') NOT NULL DEFAULT 'terminado',
    unidad         VARCHAR(20) NOT NULL DEFAULT 'pieza',
    precio         DECIMAL(10,2) NOT NULL DEFAULT 0,
    costo          DECIMAL(10,2) NOT NULL DEFAULT 0,
    stock          DECIMAL(10,2) NOT NULL DEFAULT 0,
    stock_minimo   DECIMAL(10,2) NOT NULL DEFAULT 5,
    activo         TINYINT(1) NOT NULL DEFAULT 1,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
SQL;

    $t[] = <<<'SQL'
CREATE TABLE IF NOT EXISTS clientes (
    id_cliente     INT AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(100) NOT NULL,
    telefono       VARCHAR(20) NULL,
    correo         VARCHAR(100) NULL,
    direccion      TEXT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
SQL;

    $t[] = <<<'SQL'
CREATE TABLE IF NOT EXISTS proveedores (
    id_proveedor   INT AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(100) NOT NULL,
    telefono       VARCHAR(20) NULL,
    correo         VARCHAR(100) NULL,
    municipio      VARCHAR(60) NULL,
    direccion      TEXT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
SQL;

    // CORTES DE CAJA: apertura y cierre de la caja de cada cajero
    $t[] = <<<'SQL'
CREATE TABLE IF NOT EXISTS cortes_caja (
    id_corte          INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario        INT NOT NULL,
    fecha_apertura    DATETIME NOT NULL,
    fondo_inicial     DECIMAL(10,2) NOT NULL DEFAULT 0,
    fecha_cierre      DATETIME NULL,
    total_efectivo    DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_tarjeta     DECIMAL(10,2) NOT NULL DEFAULT 0,
    efectivo_esperado DECIMAL(10,2) NOT NULL DEFAULT 0,
    efectivo_contado  DECIMAL(10,2) NULL,
    diferencia        DECIMAL(10,2) NULL,
    estado            ENUM('abierta','cerrada') NOT NULL DEFAULT 'abierta',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
SQL;

    // TICKETS: cada venta cobrada
    $t[] = <<<'SQL'
CREATE TABLE IF NOT EXISTS tickets (
    id_ticket   INT AUTO_INCREMENT PRIMARY KEY,
    id_corte    INT NOT NULL,
    id_usuario  INT NOT NULL,
    id_cliente  INT NOT NULL DEFAULT 1,
    fecha_hora  DATETIME NOT NULL,
    metodo_pago ENUM('Efectivo','Tarjeta') NOT NULL,
    total       DECIMAL(10,2) NOT NULL,
    recibido    DECIMAL(10,2) NOT NULL DEFAULT 0,
    cambio      DECIMAL(10,2) NOT NULL DEFAULT 0,
    estado      ENUM('activo','cancelado') NOT NULL DEFAULT 'activo',
    FOREIGN KEY (id_corte)   REFERENCES cortes_caja(id_corte),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
SQL;

    // TICKET_DETALLE: los panes que lleva cada ticket
    $t[] = <<<'SQL'
CREATE TABLE IF NOT EXISTS ticket_detalle (
    id_detalle      INT AUTO_INCREMENT PRIMARY KEY,
    id_ticket       INT NOT NULL,
    id_producto     INT NOT NULL,
    cantidad        DECIMAL(10,2) NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal        DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_ticket)   REFERENCES tickets(id_ticket),
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
SQL;

    // COMPRAS: pedidos a proveedores (pendiente → recibida → suma stock)
    $t[] = <<<'SQL'
CREATE TABLE IF NOT EXISTS compras (
    id_compra    INT AUTO_INCREMENT PRIMARY KEY,
    id_proveedor INT NOT NULL,
    id_usuario   INT NOT NULL,
    fecha        DATE NOT NULL,
    total        DECIMAL(10,2) NOT NULL DEFAULT 0,
    estado       ENUM('pendiente','recibida','cancelada') NOT NULL DEFAULT 'pendiente',
    notas        VARCHAR(200) NULL,
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id_proveedor),
    FOREIGN KEY (id_usuario)   REFERENCES usuarios(id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
SQL;

    $t[] = <<<'SQL'
CREATE TABLE IF NOT EXISTS compra_detalle (
    id_detalle     INT AUTO_INCREMENT PRIMARY KEY,
    id_compra      INT NOT NULL,
    id_producto    INT NOT NULL,
    cantidad       DECIMAL(10,2) NOT NULL,
    costo_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_compra)   REFERENCES compras(id_compra),
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
SQL;

    // BITÁCORA: registro de lo que hace cada usuario
    $t[] = <<<'SQL'
CREATE TABLE IF NOT EXISTS bitacora (
    id_bitacora    INT AUTO_INCREMENT PRIMARY KEY,
    fecha_hora     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_usuario     INT NULL,
    usuario_nombre VARCHAR(100) NOT NULL,
    accion         VARCHAR(80) NOT NULL,
    detalle        VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
SQL;

    foreach ($t as $sql) {
        $conn->query($sql);
    }

    // MIGRACIÓN: antes el rango era 'admin'/'cajero'; ahora hay 4 rangos.
    // Los que eran 'admin' pasan a 'sistemas' (acceso total).
    $conn->query("ALTER TABLE usuarios MODIFY rol VARCHAR(20) NOT NULL DEFAULT 'cajero'");
    $conn->query("UPDATE usuarios SET rol = 'sistemas' WHERE rol = 'admin'");

    sembrar_datos($conn);

    // Guardar la versión para no repetir todo en cada página
    $st = $conn->prepare("INSERT INTO config_sistema (clave, valor) VALUES ('esquema', ?)
                          ON DUPLICATE KEY UPDATE valor = VALUES(valor)");
    $v = (string)ESQUEMA_VERSION;
    $st->bind_param("s", $v);
    $st->execute();
}

// DATOS DE EJEMPLO (solo se insertan si las tablas están vacías)
function sembrar_datos($conn) {

    // ---- Secciones ----
    if ((int)$conn->query("SELECT COUNT(*) c FROM categorias")->fetch_assoc()['c'] === 0) {
        foreach (['Pan dulce', 'Pan salado', 'Especialidades yucatecas', 'Pan de temporada',
                  'Pasteles y postres', 'Bebidas', 'Insumos'] as $c) {
            $st = $conn->prepare("INSERT INTO categorias (nombre) VALUES (?)");
            $st->bind_param("s", $c);
            $st->execute();
        }
    }

    // ---- Cliente por defecto (id 1): las ventas sin cliente van aquí ----
    if ((int)$conn->query("SELECT COUNT(*) c FROM clientes")->fetch_assoc()['c'] === 0) {
        $conn->query("INSERT INTO clientes (id_cliente, nombre) VALUES (1, 'Público en general')");
    }

    // ---- Proveedores de ejemplo ----
    if ((int)$conn->query("SELECT COUNT(*) c FROM proveedores")->fetch_assoc()['c'] === 0) {
        $provs = [
            ['Harinera y Abarrotes Mérida', 'Mérida'],
            ['Lácteos de Valladolid',       'Valladolid'],
            ['Abarrotes Tizimín',           'Tizimín'],
            ['Granja Avícola de Izamal',    'Izamal'],
        ];
        foreach ($provs as $p) {
            $st = $conn->prepare("INSERT INTO proveedores (nombre, municipio) VALUES (?, ?)");
            $st->bind_param("ss", $p[0], $p[1]);
            $st->execute();
        }
    }

    // ---- Productos e insumos de ejemplo ----
    if ((int)$conn->query("SELECT COUNT(*) c FROM productos")->fetch_assoc()['c'] === 0) {

        // Mapa  nombre de sección → id
        $cat = [];
        $r = $conn->query("SELECT id_categoria, nombre FROM categorias");
        while ($f = $r->fetch_assoc()) { $cat[$f['nombre']] = (int)$f['id_categoria']; }

        // [nombre, sección, tipo, unidad, precio, costo, stock, stock mínimo]
        $items = [
            // Pan dulce
            ['Concha de vainilla',        'Pan dulce', 'terminado', 'pieza', 12, 5, 40, 10],
            ['Cuernito de mantequilla',   'Pan dulce', 'terminado', 'pieza', 10, 4.5, 35, 10],
            ['Oreja',                     'Pan dulce', 'terminado', 'pieza', 14, 6, 30, 8],
            ['Dona de chocolate',         'Pan dulce', 'terminado', 'pieza', 15, 7, 30, 8],
            ['Polvorón de naranja',       'Pan dulce', 'terminado', 'pieza', 10, 4, 40, 10],
            ['Pan de coco',               'Pan dulce', 'terminado', 'pieza', 13, 5.5, 30, 8],
            // Pan salado
            ['Bolillo',                   'Pan salado', 'terminado', 'pieza', 3, 1.2, 150, 30],
            ['Telera para torta',         'Pan salado', 'terminado', 'pieza', 5, 2, 80, 20],
            ['Pan de queso de bola',      'Pan salado', 'terminado', 'pieza', 18, 9, 25, 8],
            ['Pan de chaya y queso',      'Pan salado', 'terminado', 'pieza', 20, 9, 20, 8],
            ['Empanada de cazón',         'Pan salado', 'terminado', 'pieza', 25, 12, 20, 8],
            // Especialidades yucatecas
            ['Pan de elote (rebanada)',   'Especialidades yucatecas', 'terminado', 'pieza', 20, 8, 24, 6],
            ['Rosca de xtabentún',        'Especialidades yucatecas', 'terminado', 'pieza', 25, 11, 15, 5],
            ['Pan de plátano macho',      'Especialidades yucatecas', 'terminado', 'pieza', 22, 9, 15, 5],
            ['Mini marquesita',           'Especialidades yucatecas', 'terminado', 'pieza', 18, 7, 20, 5],
            // Temporada
            ['Pan de muerto (Hanal Pixán)', 'Pan de temporada', 'terminado', 'pieza', 30, 13, 20, 5],
            ['Rosca de Reyes (rebanada)',   'Pan de temporada', 'terminado', 'pieza', 35, 15, 20, 5],
            // Pasteles
            ['Pastel tres leches (rebanada)', 'Pasteles y postres', 'terminado', 'pieza', 38, 16, 16, 4],
            ['Flan napolitano (rebanada)',    'Pasteles y postres', 'terminado', 'pieza', 30, 12, 12, 4],
            // Bebidas
            ['Horchata (vaso)',               'Bebidas', 'terminado', 'pieza', 20, 6, 40, 10],
            ['Café de olla (vaso)',           'Bebidas', 'terminado', 'pieza', 18, 5, 40, 10],
            ['Agua de chaya con limón (vaso)', 'Bebidas', 'terminado', 'pieza', 20, 5, 30, 8],
            ['Atole de maíz nuevo (vaso)',    'Bebidas', 'terminado', 'pieza', 18, 5, 30, 8],
            // Insumos (materia prima)
            ['Harina de trigo',     'Insumos', 'insumo', 'kg',     0, 22, 80, 20],
            ['Azúcar',              'Insumos', 'insumo', 'kg',     0, 28, 50, 15],
            ['Manteca vegetal',     'Insumos', 'insumo', 'kg',     0, 55, 30, 8],
            ['Mantequilla',         'Insumos', 'insumo', 'kg',     0, 160, 15, 5],
            ['Levadura fresca',     'Insumos', 'insumo', 'kg',     0, 90, 5, 2],
            ['Huevo',               'Insumos', 'insumo', 'pieza',  0, 3.2, 300, 60],
            ['Leche',               'Insumos', 'insumo', 'litro',  0, 24, 40, 10],
            ['Queso de bola',       'Insumos', 'insumo', 'kg',     0, 280, 10, 3],
            ['Coco rallado',        'Insumos', 'insumo', 'kg',     0, 120, 6, 2],
            ['Elote tierno',        'Insumos', 'insumo', 'kg',     0, 30, 15, 5],
            ['Xtabentún',           'Insumos', 'insumo', 'litro',  0, 180, 3, 1],
            ['Chaya',               'Insumos', 'insumo', 'manojo', 0, 15, 10, 3],
            ['Plátano macho',       'Insumos', 'insumo', 'kg',     0, 25, 12, 4],
            ['Cazón deshebrado',    'Insumos', 'insumo', 'kg',     0, 180, 8, 2],
            ['Chocolate',           'Insumos', 'insumo', 'kg',     0, 150, 6, 2],
        ];
        $st = $conn->prepare("INSERT INTO productos
            (nombre, id_categoria, tipo, unidad, precio, costo, stock, stock_minimo)
            VALUES (?,?,?,?,?,?,?,?)");
        foreach ($items as $i) {
            $idcat = $cat[$i[1]] ?? null;
            $st->bind_param("sissdddd", $i[0], $idcat, $i[2], $i[3], $i[4], $i[5], $i[6], $i[7]);
            $st->execute();
        }
    }
}

// Si el esquema no está al día, se crea / actualiza (solo pasa la primera vez)
if (version_esquema($conn) < ESQUEMA_VERSION) {
    try {
        instalar_esquema($conn);
    } catch (Throwable $e) {
        die("Error al preparar la base de datos: " . htmlspecialchars($e->getMessage()));
    }
}

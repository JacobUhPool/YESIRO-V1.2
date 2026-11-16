-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 22-10-2026 a las 03:10:34
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `panaderia`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora`
--

CREATE TABLE `bitacora` (
  `id_bitacora` int(11) NOT NULL,
  `fecha_hora` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_usuario` int(11) DEFAULT NULL,
  `usuario_nombre` varchar(100) NOT NULL,
  `accion` varchar(80) NOT NULL,
  `detalle` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `bitacora`
--

INSERT INTO `bitacora` (`id_bitacora`, `fecha_hora`, `id_usuario`, `usuario_nombre`, `accion`, `detalle`) VALUES
(1, '2026-10-21 05:23:22', 2, 'Felix', 'Abrir caja', 'Fondo inicial: $500.00'),
(2, '2026-10-21 05:24:18', 2, 'Felix', 'Venta registrada', 'Ticket #1 por $30.00 (Efectivo)'),
(3, '2026-10-21 05:25:23', 2, 'Felix', 'Corte de caja', 'Corte #1, diferencia $-30.00'),
(4, '2026-10-21 05:25:45', 2, 'Felix', 'Cierre de sesión', ''),
(5, '2026-10-21 05:27:41', 1, 'yessef', 'Inicio de sesión', ''),
(6, '2026-10-21 05:32:02', 1, 'yessef', 'Cierre de sesión', ''),
(7, '2026-10-21 05:32:15', 2, 'Felix', 'Inicio de sesión', ''),
(8, '2026-10-21 05:45:19', 2, 'Felix', 'Abrir caja', 'Fondo inicial: $500.00'),
(9, '2026-10-21 05:47:11', 2, 'Felix', 'Venta registrada', 'Ticket #2 por $60.00 (Efectivo)'),
(10, '2026-10-21 05:49:10', 2, 'Felix', 'Corte de caja', 'Corte #2, diferencia $-60.00'),
(11, '2026-10-21 05:50:16', 2, 'Felix', 'Cierre de sesión', ''),
(12, '2026-10-21 05:50:26', 2, 'Felix', 'Inicio de sesión', ''),
(13, '2026-10-21 05:50:34', 2, 'Felix', 'Cierre de sesión', ''),
(14, '2026-10-21 05:50:44', 2, 'Felix', 'Inicio de sesión', ''),
(15, '2026-10-21 05:50:51', 2, 'Felix', 'Cierre de sesión', ''),
(16, '2026-10-21 05:50:57', 1, 'yessef', 'Inicio de sesión', ''),
(17, '2026-10-21 14:31:48', 1, 'yessef', 'Inicio de sesión', ''),
(18, '2026-10-21 14:36:23', 1, 'yessef', 'Cierre de sesión', ''),
(19, '2026-10-21 14:36:40', 2, 'Felix', 'Inicio de sesión', ''),
(20, '2026-10-21 14:37:41', 2, 'Felix', 'Abrir caja', 'Fondo inicial: $500.00'),
(21, '2026-10-21 14:38:30', 2, 'Felix', 'Cierre de sesión', ''),
(22, '2026-10-21 14:39:22', 1, 'yessef', 'Inicio de sesión', ''),
(23, '2026-10-21 15:44:09', 1, 'yessef', 'Inicio de sesión', ''),
(24, '2026-10-21 15:44:21', 1, 'yessef', 'Cierre de sesión', ''),
(25, '2026-10-21 23:14:16', 3, 'Jairo', 'Inicio de sesión', ''),
(26, '2026-10-21 23:17:18', 3, 'Jairo', 'Insertar proveedor', 'Proveedor #5: Insumos Enrique SA de CV'),
(27, '2026-10-21 23:23:12', 3, 'Jairo', 'Cambiar rango', 'yessef: Sistemas → Sistemas'),
(28, '2026-10-21 23:24:05', 3, 'Jairo', 'Insertar usuario', 'junior (junior@gmail.com) como Cajero'),
(29, '2026-10-21 23:24:28', 3, 'Jairo', 'Cierre de sesión', ''),
(30, '2026-10-21 23:24:46', 4, 'junior', 'Inicio de sesión', ''),
(31, '2026-10-21 23:26:43', 4, 'junior', 'Cierre de sesión', ''),
(32, '2026-10-21 23:27:32', 3, 'Jairo', 'Inicio de sesión', ''),
(33, '2026-10-21 23:28:27', 3, 'Jairo', 'Insertar usuario', 'Mario Castañeda (mario@gmail.com) como Jefe'),
(34, '2026-10-21 23:29:08', 3, 'Jairo', 'Insertar usuario', 'Enrique cesar (enrique@gmail.com) como Administrador'),
(35, '2026-10-21 23:31:33', 3, 'Jairo', 'Cierre de sesión', ''),
(36, '2026-10-21 23:31:42', 6, 'Enrique cesar', 'Inicio de sesión', ''),
(37, '2026-10-21 23:32:55', 6, 'Enrique cesar', 'Cierre de sesión', ''),
(38, '2026-10-21 23:33:06', 5, 'Mario Castañeda', 'Inicio de sesión', ''),
(39, '2026-10-21 23:47:25', 5, 'Mario Castañeda', 'Cierre de sesión', ''),
(40, '2026-10-21 23:48:49', 5, 'Mario Castañeda', 'Inicio de sesión', ''),
(41, '2026-10-21 23:56:40', 5, 'Mario Castañeda', 'Cierre de sesión', ''),
(42, '2026-10-21 23:56:46', 6, 'Enrique cesar', 'Inicio de sesión', ''),
(43, '2026-10-21 23:59:26', 6, 'Enrique cesar', 'Cierre de sesión', ''),
(44, '2026-10-21 23:59:32', 5, 'Mario Castañeda', 'Inicio de sesión', ''),
(45, '2026-10-21 23:59:35', 5, 'Mario Castañeda', 'Cierre de sesión', ''),
(46, '2026-10-21 23:59:50', 4, 'junior', 'Inicio de sesión', ''),
(47, '2026-10-22 00:02:24', 4, 'junior', 'Insertar cliente', 'Cliente #2: Fulanito Hernandez'),
(48, '2026-10-22 00:03:10', 4, 'junior', 'Abrir caja', 'Fondo inicial: $500.00'),
(49, '2026-10-22 00:06:06', 4, 'junior', 'Venta registrada', 'Ticket #3 por $48.00 (Efectivo)'),
(50, '2026-10-22 00:09:56', 4, 'junior', 'Cierre de sesión', ''),
(51, '2026-10-22 00:10:02', 6, 'Enrique cesar', 'Inicio de sesión', ''),
(52, '2026-10-22 00:18:50', 6, 'Enrique cesar', 'Cierre de sesión', ''),
(53, '2026-10-22 00:18:56', 3, 'Jairo', 'Inicio de sesión', ''),
(54, '2026-10-22 00:19:01', 3, 'Jairo', 'Cierre de sesión', ''),
(55, '2026-10-22 00:19:09', 5, 'Mario Castañeda', 'Inicio de sesión', ''),
(56, '2026-10-22 00:19:12', 5, 'Mario Castañeda', 'Cierre de sesión', ''),
(57, '2026-10-22 00:19:16', 4, 'junior', 'Inicio de sesión', ''),
(58, '2026-10-22 01:09:07', 4, 'junior', 'Inicio de sesión', ''),
(59, '2026-10-22 01:09:15', 4, 'junior', 'Cierre de sesión', ''),
(60, '2026-10-22 01:09:19', 6, 'Enrique cesar', 'Inicio de sesión', ''),
(61, '2026-10-22 01:09:42', 6, 'Enrique cesar', 'Cierre de sesión', ''),
(62, '2026-10-22 01:09:48', 5, 'Mario Castañeda', 'Inicio de sesión', ''),
(63, '2026-10-22 01:09:51', 5, 'Mario Castañeda', 'Cierre de sesión', ''),
(64, '2026-10-22 01:10:03', 6, 'Enrique cesar', 'Inicio de sesión', ''),
(65, '2026-10-22 01:10:10', 6, 'Enrique cesar', 'Cierre de sesión', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`) VALUES
(6, 'Bebidas'),
(3, 'Especialidades yucatecas'),
(7, 'Insumos'),
(4, 'Pan de temporada'),
(1, 'Pan dulce'),
(2, 'Pan salado'),
(5, 'Pasteles y postres');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `nombre`, `telefono`, `correo`, `direccion`, `fecha_registro`) VALUES
(1, 'Público en general', NULL, NULL, NULL, '2026-10-21 05:23:02'),
(2, 'Fulanito Hernandez', '9861015734', 'fulanito@gmail.com', 'c 67 x 90 #555', '2026-10-22 00:02:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id_compra` int(11) NOT NULL,
  `id_proveedor` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estado` enum('pendiente','recibida','cancelada') NOT NULL DEFAULT 'pendiente',
  `notas` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra_detalle`
--

CREATE TABLE `compra_detalle` (
  `id_detalle` int(11) NOT NULL,
  `id_compra` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `costo_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config_sistema`
--

CREATE TABLE `config_sistema` (
  `clave` varchar(50) NOT NULL,
  `valor` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `config_sistema`
--

INSERT INTO `config_sistema` (`clave`, `valor`) VALUES
('esquema', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cortes_caja`
--

CREATE TABLE `cortes_caja` (
  `id_corte` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_apertura` datetime NOT NULL,
  `fondo_inicial` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fecha_cierre` datetime DEFAULT NULL,
  `total_efectivo` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_tarjeta` decimal(10,2) NOT NULL DEFAULT 0.00,
  `efectivo_esperado` decimal(10,2) NOT NULL DEFAULT 0.00,
  `efectivo_contado` decimal(10,2) DEFAULT NULL,
  `diferencia` decimal(10,2) DEFAULT NULL,
  `estado` enum('abierta','cerrada') NOT NULL DEFAULT 'abierta'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cortes_caja`
--

INSERT INTO `cortes_caja` (`id_corte`, `id_usuario`, `fecha_apertura`, `fondo_inicial`, `fecha_cierre`, `total_efectivo`, `total_tarjeta`, `efectivo_esperado`, `efectivo_contado`, `diferencia`, `estado`) VALUES
(1, 2, '2026-10-20 23:23:22', 500.00, '2026-10-20 23:25:23', 30.00, 0.00, 530.00, 500.00, -30.00, 'cerrada'),
(2, 2, '2026-10-20 23:45:19', 500.00, '2026-10-20 23:49:10', 60.00, 0.00, 560.00, 500.00, -60.00, 'cerrada'),
(3, 2, '2026-10-21 08:37:41', 500.00, NULL, 0.00, 0.00, 0.00, NULL, NULL, 'abierta'),
(4, 4, '2026-10-21 18:03:10', 500.00, NULL, 0.00, 0.00, 0.00, NULL, NULL, 'abierta');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo` enum('Terminado','Materia Prima') NOT NULL,
  `stock_actual` int(11) NOT NULL DEFAULT 0,
  `direccion_almacen` varchar(150) NOT NULL,
  `descripcion` text NOT NULL,
  `alergenos` varchar(100) DEFAULT NULL,
  `estado_calidad` enum('aprobado','rechazado') NOT NULL,
  `fecha_caducidad` date NOT NULL,
  `hora_revision` time NOT NULL,
  `etiqueta_lote` char(7) DEFAULT NULL,
  `foto_producto` varchar(255) DEFAULT NULL,
  `correo_supervisor` varchar(100) NOT NULL,
  `id_usuario` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`id_producto`, `nombre`, `tipo`, `stock_actual`, `direccion_almacen`, `descripcion`, `alergenos`, `estado_calidad`, `fecha_caducidad`, `hora_revision`, `etiqueta_lote`, `foto_producto`, `correo_supervisor`, `id_usuario`, `fecha_registro`) VALUES
(1, 'yessef', 'Terminado', 1, 'prueba', 'prueba', 'gluten', 'aprobado', '2026-10-20', '07:23:00', '#ffff00', NULL, 'yessefmartin516@gmail.com', '$2y$10$aBzHxJDJ41jJNXQ27xthXePsrjsrRljwIntsZyraC29itHPBJQRPO', '2026-10-20 13:23:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `tipo` enum('terminado','insumo') NOT NULL DEFAULT 'terminado',
  `unidad` varchar(20) NOT NULL DEFAULT 'pieza',
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `costo` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock_minimo` decimal(10,2) NOT NULL DEFAULT 5.00,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre`, `id_categoria`, `tipo`, `unidad`, `precio`, `costo`, `stock`, `stock_minimo`, `activo`, `fecha_registro`) VALUES
(1, 'Concha de vainilla', 1, 'terminado', 'pieza', 12.00, 5.00, 40.00, 10.00, 1, '2026-10-21 05:23:02'),
(2, 'Cuernito de mantequilla', 1, 'terminado', 'pieza', 10.00, 4.50, 35.00, 10.00, 1, '2026-10-21 05:23:02'),
(3, 'Oreja', 1, 'terminado', 'pieza', 14.00, 6.00, 30.00, 8.00, 1, '2026-10-21 05:23:02'),
(4, 'Dona de chocolate', 1, 'terminado', 'pieza', 15.00, 7.00, 30.00, 8.00, 1, '2026-10-21 05:23:02'),
(5, 'Polvorón de naranja', 1, 'terminado', 'pieza', 10.00, 4.00, 40.00, 10.00, 1, '2026-10-21 05:23:02'),
(6, 'Pan de coco', 1, 'terminado', 'pieza', 13.00, 5.50, 30.00, 8.00, 1, '2026-10-21 05:23:02'),
(7, 'Bolillo', 2, 'terminado', 'pieza', 3.00, 1.20, 150.00, 30.00, 1, '2026-10-21 05:23:02'),
(8, 'Telera para torta', 2, 'terminado', 'pieza', 5.00, 2.00, 80.00, 20.00, 1, '2026-10-21 05:23:02'),
(9, 'Pan de queso de bola', 2, 'terminado', 'pieza', 18.00, 9.00, 25.00, 8.00, 1, '2026-10-21 05:23:02'),
(10, 'Pan de chaya y queso', 2, 'terminado', 'pieza', 20.00, 9.00, 20.00, 8.00, 1, '2026-10-21 05:23:02'),
(11, 'Empanada de cazón', 2, 'terminado', 'pieza', 25.00, 12.00, 20.00, 8.00, 1, '2026-10-21 05:23:02'),
(12, 'Pan de elote (rebanada)', 3, 'terminado', 'pieza', 20.00, 8.00, 24.00, 6.00, 1, '2026-10-21 05:23:02'),
(13, 'Rosca de xtabentún', 3, 'terminado', 'pieza', 25.00, 11.00, 15.00, 5.00, 1, '2026-10-21 05:23:02'),
(14, 'Pan de plátano macho', 3, 'terminado', 'pieza', 22.00, 9.00, 15.00, 5.00, 1, '2026-10-21 05:23:02'),
(15, 'Mini marquesita', 3, 'terminado', 'pieza', 18.00, 7.00, 20.00, 5.00, 1, '2026-10-21 05:23:02'),
(16, 'Pan de muerto (Hanal Pixán)', 4, 'terminado', 'pieza', 30.00, 13.00, 16.00, 5.00, 1, '2026-10-21 05:23:02'),
(17, 'Rosca de Reyes (rebanada)', 4, 'terminado', 'pieza', 35.00, 15.00, 20.00, 5.00, 1, '2026-10-21 05:23:02'),
(18, 'Pastel tres leches (rebanada)', 5, 'terminado', 'pieza', 38.00, 16.00, 16.00, 4.00, 1, '2026-10-21 05:23:02'),
(19, 'Flan napolitano (rebanada)', 5, 'terminado', 'pieza', 30.00, 12.00, 12.00, 4.00, 1, '2026-10-21 05:23:02'),
(20, 'Horchata (vaso)', 6, 'terminado', 'pieza', 20.00, 6.00, 40.00, 10.00, 1, '2026-10-21 05:23:02'),
(21, 'Café de olla (vaso)', 6, 'terminado', 'pieza', 18.00, 5.00, 39.00, 10.00, 1, '2026-10-21 05:23:02'),
(22, 'Agua de chaya con limón (vaso)', 6, 'terminado', 'pieza', 20.00, 5.00, 30.00, 8.00, 1, '2026-10-21 05:23:02'),
(23, 'Atole de maíz nuevo (vaso)', 6, 'terminado', 'pieza', 18.00, 5.00, 30.00, 8.00, 1, '2026-10-21 05:23:02'),
(24, 'Harina de trigo', 7, 'insumo', 'kg', 0.00, 22.00, 80.00, 20.00, 1, '2026-10-21 05:23:02'),
(25, 'Azúcar', 7, 'insumo', 'kg', 0.00, 28.00, 50.00, 15.00, 1, '2026-10-21 05:23:02'),
(26, 'Manteca vegetal', 7, 'insumo', 'kg', 0.00, 55.00, 30.00, 8.00, 1, '2026-10-21 05:23:02'),
(27, 'Mantequilla', 7, 'insumo', 'kg', 0.00, 160.00, 15.00, 5.00, 1, '2026-10-21 05:23:02'),
(28, 'Levadura fresca', 7, 'insumo', 'kg', 0.00, 90.00, 5.00, 2.00, 1, '2026-10-21 05:23:02'),
(29, 'Huevo', 7, 'insumo', 'pieza', 0.00, 3.20, 300.00, 60.00, 1, '2026-10-21 05:23:02'),
(30, 'Leche', 7, 'insumo', 'litro', 0.00, 24.00, 40.00, 10.00, 1, '2026-10-21 05:23:02'),
(31, 'Queso de bola', 7, 'insumo', 'kg', 0.00, 280.00, 10.00, 3.00, 1, '2026-10-21 05:23:02'),
(32, 'Coco rallado', 7, 'insumo', 'kg', 0.00, 120.00, 6.00, 2.00, 1, '2026-10-21 05:23:02'),
(33, 'Elote tierno', 7, 'insumo', 'kg', 0.00, 30.00, 15.00, 5.00, 1, '2026-10-21 05:23:02'),
(34, 'Xtabentún', 7, 'insumo', 'litro', 0.00, 180.00, 3.00, 1.00, 1, '2026-10-21 05:23:02'),
(35, 'Chaya', 7, 'insumo', 'manojo', 0.00, 15.00, 10.00, 3.00, 1, '2026-10-21 05:23:02'),
(36, 'Plátano macho', 7, 'insumo', 'kg', 0.00, 25.00, 12.00, 4.00, 1, '2026-10-21 05:23:02'),
(37, 'Cazón deshebrado', 7, 'insumo', 'kg', 0.00, 180.00, 8.00, 2.00, 1, '2026-10-21 05:23:02'),
(38, 'Chocolate', 7, 'insumo', 'kg', 0.00, 150.00, 6.00, 2.00, 1, '2026-10-21 05:23:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id_proveedor` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `municipio` varchar(60) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id_proveedor`, `nombre`, `telefono`, `correo`, `municipio`, `direccion`, `fecha_registro`) VALUES
(1, 'Harinera y Abarrotes Mérida', NULL, NULL, 'Mérida', NULL, '2026-10-21 05:23:02'),
(2, 'Lácteos de Valladolid', NULL, NULL, 'Valladolid', NULL, '2026-10-21 05:23:02'),
(3, 'Abarrotes Tizimín', NULL, NULL, 'Tizimín', NULL, '2026-10-21 05:23:02'),
(4, 'Granja Avícola de Izamal', NULL, NULL, 'Izamal', NULL, '2026-10-21 05:23:02'),
(5, 'Insumos Enrique SA de CV', '9981512307', 'insumose@gmail.com', 'Tizimín', 'calle 24 x 56 #223', '2026-10-21 23:17:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tickets`
--

CREATE TABLE `tickets` (
  `id_ticket` int(11) NOT NULL,
  `id_corte` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL DEFAULT 1,
  `fecha_hora` datetime NOT NULL,
  `metodo_pago` enum('Efectivo','Tarjeta') NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `recibido` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cambio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estado` enum('activo','cancelado') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tickets`
--

INSERT INTO `tickets` (`id_ticket`, `id_corte`, `id_usuario`, `id_cliente`, `fecha_hora`, `metodo_pago`, `total`, `recibido`, `cambio`, `estado`) VALUES
(1, 1, 2, 1, '2026-10-20 23:24:18', 'Efectivo', 30.00, 50.00, 20.00, 'activo'),
(2, 2, 2, 1, '2026-10-20 23:47:11', 'Efectivo', 60.00, 200.00, 140.00, 'activo'),
(3, 4, 4, 1, '2026-10-21 18:06:06', 'Efectivo', 48.00, 100.00, 52.00, 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ticket_detalle`
--

CREATE TABLE `ticket_detalle` (
  `id_detalle` int(11) NOT NULL,
  `id_ticket` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ticket_detalle`
--

INSERT INTO `ticket_detalle` (`id_detalle`, `id_ticket`, `id_producto`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 1, 16, 1.00, 30.00, 30.00),
(2, 2, 16, 2.00, 30.00, 60.00),
(3, 3, 16, 1.00, 30.00, 30.00),
(4, 3, 21, 1.00, 18.00, 18.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `clave_hash` varchar(255) NOT NULL,
  `rol` varchar(20) NOT NULL DEFAULT 'cajero',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `correo`, `clave_hash`, `rol`, `activo`, `fecha_registro`) VALUES
(1, 'yessef', 'yessefmartin516@gmail.com', '$2y$10$nerucKvMmIPlBhnl3PcCa.jX0pBQkRg.DYsn92YqWTlq9XxLbnlqa', 'sistemas', 1, '2026-10-20 23:10:56'),
(2, 'Felix', '24890325@ittizimin.edu.mx', '$2y$10$K00FAJDINrM2bz4DQX.3pe6DSEAr46Uxg9f/hMpb8kBZhsT6H9wvW', 'cajero', 1, '2026-10-20 23:17:53'),
(3, 'Jairo', 'jairo@gmail.com', '$2y$10$123456789012345678901ujczD5TiARVFtc68bZCAlbEg1fCIexfO', 'sistemas', 1, '2026-10-21 23:09:40'),
(4, 'junior', 'junior@gmail.com', '$2y$10$CeVFICFjJRqCBwLm8RgkDuim4XuaVlUFuSlfpqTaIRh.Yo5jugk.q', 'cajero', 1, '2026-10-21 23:24:05'),
(5, 'Mario Castañeda', 'mario@gmail.com', '$2y$10$1SbCVpZZ5HQV2Al.Wh13AOvPZR937pS2XuHvrPtICWMgCpnXo9feu', 'jefe', 1, '2026-10-21 23:28:27'),
(6, 'Enrique cesar', 'enrique@gmail.com', '$2y$10$t4rqW7b7jvz3d58XI1Q9Y.mDr2y6ZFpPPPAQtTdMEpdq4/BOWiBqS', 'administrador', 1, '2026-10-21 23:29:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL,
  `nombre_cliente` varchar(100) NOT NULL,
  `correo_cliente` varchar(100) NOT NULL,
  `id_usuario` varchar(255) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `direccion_entrega` text NOT NULL,
  `extras` varchar(100) DEFAULT NULL,
  `metodo_pago` enum('Efectivo','Tarjeta') NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `color_glaseado` char(7) DEFAULT NULL,
  `comprobante` varchar(255) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD PRIMARY KEY (`id_bitacora`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id_compra`),
  ADD KEY `id_proveedor` (`id_proveedor`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `compra_detalle`
--
ALTER TABLE `compra_detalle`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_compra` (`id_compra`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `config_sistema`
--
ALTER TABLE `config_sistema`
  ADD PRIMARY KEY (`clave`);

--
-- Indices de la tabla `cortes_caja`
--
ALTER TABLE `cortes_caja`
  ADD PRIMARY KEY (`id_corte`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id_producto`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id_proveedor`);

--
-- Indices de la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id_ticket`),
  ADD KEY `id_corte` (`id_corte`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Indices de la tabla `ticket_detalle`
--
ALTER TABLE `ticket_detalle`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_ticket` (`id_ticket`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `id_producto` (`id_producto`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `id_bitacora` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id_compra` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compra_detalle`
--
ALTER TABLE `compra_detalle`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cortes_caja`
--
ALTER TABLE `cortes_caja`
  MODIFY `id_corte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id_proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id_ticket` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `ticket_detalle`
--
ALTER TABLE `ticket_detalle`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id_proveedor`),
  ADD CONSTRAINT `compras_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `compra_detalle`
--
ALTER TABLE `compra_detalle`
  ADD CONSTRAINT `compra_detalle_ibfk_1` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compra`),
  ADD CONSTRAINT `compra_detalle_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `cortes_caja`
--
ALTER TABLE `cortes_caja`
  ADD CONSTRAINT `cortes_caja_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`);

--
-- Filtros para la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`id_corte`) REFERENCES `cortes_caja` (`id_corte`),
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `tickets_ibfk_3` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`);

--
-- Filtros para la tabla `ticket_detalle`
--
ALTER TABLE `ticket_detalle`
  ADD CONSTRAINT `ticket_detalle_ibfk_1` FOREIGN KEY (`id_ticket`) REFERENCES `tickets` (`id_ticket`),
  ADD CONSTRAINT `ticket_detalle_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `inventario` (`id_producto`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

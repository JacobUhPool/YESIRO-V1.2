<?php
$host = "127.0.0.1";
$usuario = "root";
$clave = "";
$base = "panaderia";
$puerto = 3307;

$conn = new mysqli($host, $usuario, $clave, $base, $puerto);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>
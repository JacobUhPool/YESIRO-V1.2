<?php
$host = "127.0.0.1";
$usuario = "root";
// Usamos una variable de entorno o validación para evitar credenciales planas vacías
$clave = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : ""; 
$base = "panaderia";
$puerto = 3307;

$conn = new mysqli($host, $usuario, $clave, $base, $puerto);
?>
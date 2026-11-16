<?php
// Si alguien intenta entrar a la carpeta modulos a la fuerza, lo regresa al inicio
header("Location: ../index.php");
exit();
?>
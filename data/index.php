<?php
/* index.php → puerta de entrada: si hay sesión va al panel; si no, al login */
require_once __DIR__ . '/config/inicio.php';
redirigir(logueado() ? 'panel.php' : 'login.php');

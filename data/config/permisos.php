<?php
/* =====================================================================
   config/permisos.php  →  SEGREGACIÓN ESTRICTA DE FUNCIONES
   ===================================================================== */

function roles() {
    return [
        'sistemas'      => 'Sistemas',
        'jefe'          => 'Jefe',
        'administrador' => 'Administrador',
        'cajero'        => 'Vendedor',  // Internamente es 'cajero', visualmente 'Vendedor'
    ];
}

function nombre_rol($rol) {
    $r = roles();
    return $r[$rol] ?? $rol;
}

function permisos() {
    return [
        // ----- Ventas y caja (100% EXCLUSIVO DEL VENDEDOR) -----
        'ventas.cobrar'       => ['cajero',],
        'caja.operar'         => ['cajero',],

        // ----- Reportes de Movimientos (Jefe vigila todo, Vendedor sus tickets) -----
        'ventas.historial'    => ['cajero', 'jefe', 'administrador',], 
        'ventas.todas'        => ['jefe', 'administrador'],
        'caja.todas'          => ['jefe', 'administrador'],
        'ventas.cancelar'     => ['jefe'], // Solo el Jefe aprueba cancelaciones

        // ----- Clientes (Vendedor los atiende, Admin los gestiona) -----
        'clientes.ver'        => ['cajero', 'administrador', 'jefe'],
        'clientes.agregar'    => ['cajero', 'administrador'],
        'clientes.editar'     => ['administrador'],

        // ----- Inventario y Compras (100% EXCLUSIVO DEL ADMINISTRADOR) -----
        'productos.gestionar'    => ['administrador'],
        'proveedores.gestionar'  => ['administrador'],
        'compras.gestionar'      => ['administrador'],

        // ----- Administración (100% EXCLUSIVO DE SISTEMAS) -----
        'usuarios.gestionar'  => ['sistemas', 'administrador'],

        // ----- Auditoría (Sistemas supervisa, Jefe analiza) -----
        'bitacora.ver'        => ['sistemas', 'jefe', 'administrador'], 
    ];
}

function tiene_permiso($permiso) {
    $rol = $_SESSION['rol'] ?? '';
    $mapa = permisos();
    return isset($mapa[$permiso]) && in_array($rol, $mapa[$permiso], true);
}

function roles_asignables() {
    $rol = $_SESSION['rol'] ?? '';
    // Solo Sistemas puede crear, editar o borrar usuarios. Nadie más.
    if ($rol === 'sistemas') return array_keys(roles());
    return [];
}

function puede_gestionar_usuario($rol_objetivo) {
    return in_array($rol_objetivo, roles_asignables(), true);
}
?>
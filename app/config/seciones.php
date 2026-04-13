<?php
session_start();

// Prevenir Session Hijacking re-generando el ID periódicamente
if (!isset($_SESSION['initiating_time'])) {
    $_SESSION['initiating_time'] = time();
} else if (time() - $_SESSION['initiating_time'] > 1800) { // 30 min
    session_regenerate_id(true);
    $_SESSION['initiating_time'] = time();
}

// Funciones para manejo de sesión
function estaLogueado() {
    return isset($_SESSION['usuario_id']);
}

function rolUsuario() {
    return isset($_SESSION['rol']) ? $_SESSION['rol'] : null;
}

function requerirRol($rol) {
    if (!estaLogueado()) {
        header("Location: /app/view/pages/login.php");
        exit;
    }
    // Si se requiere empleado, un admin también debería poder entrar en general, 
    // pero para seguridad estricta pediremos coincidencia o ser admin
    if (rolUsuario() !== $rol && rolUsuario() !== 'admin') {
        die("Acceso denegado. Se requiere nivel de $rol o superior.");
    }
}

function requerirLogin() {
    if (!estaLogueado()) {
        header("Location: /app/view/pages/login.php");
        exit;
    }
}
?>

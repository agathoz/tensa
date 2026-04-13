<?php
require_once 'seciones.php';
require_once 'db.php';
require_once 'seguridad.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre      = limpiarCadenas($_POST['nombre'] ?? '');
    $correo      = limpiarCadenas($_POST['correo'] ?? '');
    $correo_conf = limpiarCadenas($_POST['correo_conf'] ?? '');
    $password      = $_POST['password'] ?? '';
    $password_conf = $_POST['password_conf'] ?? '';

    // Campos requeridos
    if (empty($nombre) || empty($correo) || empty($password)) {
        header("Location: /app/view/pages/register.php?error=Campos requeridos");
        exit;
    }

    if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]{2,50}$/', $nombre)) {
        header("Location: /app/view/pages/register.php?error=Nombre inválido");
        exit;
    }

    if ($correo !== $correo_conf) {
        header("Location: /app/view/pages/register.php?error=Los correos no coinciden");
        exit;
    }

    if ($password !== $password_conf) {
        header("Location: /app/view/pages/register.php?error=Las contraseñas no coinciden");
        exit;
    }

    // validarCorreo definida aquí por si no está en seguridad.php
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header("Location: /app/view/pages/register.php?error=Formato de correo inválido");
        exit;
    }

    try {
        // Verificar correo duplicado
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $stmt->execute([$correo]);
        if ($stmt->fetch()) {
            header("Location: /app/view/pages/register.php?error=El correo ya está registrado");
            exit;
        }

        // Verificar nombre duplicado
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE nombre = ?");
        $stmt->execute([$nombre]);
        if ($stmt->fetch()) {
            header("Location: /app/view/pages/register.php?error=Ese nombre de usuario ya está en uso");
            exit;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $pdo->prepare(
            "INSERT INTO usuarios (nombre, correo, password_hash, rol)
             VALUES (?, ?, ?, 'usuario')"
        );
        $stmt->execute([$nombre, $correo, $hash]);

        header("Location: validacioncorreo.php?simular_registro=" . urlencode($correo));
        exit;

    } catch (PDOException $e) {
        // En desarrollo puedes ver el error real:
        // error_log($e->getMessage());
        header("Location: /app/view/pages/register.php?error=Error del servidor");
        exit;
    }
}
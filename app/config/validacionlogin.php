<?php
require_once 'seciones.php';
require_once 'db.php';
require_once 'seguridad.php';

if(isset($_GET['logout'])) {
    session_destroy();
    header("Location: /");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = limpiarCadenas($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($correo) || empty($password)) {
        header("Location: /app/view/pages/login.php?error=Campos vacíos");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ?");
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['password_hash'])) {
            if ($usuario['status'] === 'baneado') {
                header("Location: /app/view/pages/login.php?error=Esta cuenta ha sido baneada y no puede acceder.");
                exit;
            }

            if (!$usuario['correo_confirmado'] && $usuario['rol'] !== 'admin') {
                header("Location: /app/view/pages/login.php?error=Por favor, confirma tu correo primero.");
                exit;
            }

            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['correo'] = $usuario['correo'];
            $_SESSION['rol'] = $usuario['rol'];
            
            session_regenerate_id(true);
            $_SESSION['initiating_time'] = time();

            header("Location: /");
            exit;
        } else {
            header("Location: /app/view/pages/login.php?error=Credenciales inválidas");
            exit;
        }
    } catch(PDOException $e) {
        header("Location: /app/view/pages/login.php?error=Error del servidor");
        exit;
    }
}
?>

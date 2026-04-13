<?php
require_once '../../config/seciones.php';
if(estaLogueado()) { header("Location: /"); exit; }

require_once '../../view/layout/header.php';
require_once '../../view/layout/navbar.php';
?>

<main class="container py-5 min-vh-80 d-flex justify-content-center align-items-center">
    <div class="card p-4 mx-auto w-100" style="max-width: 500px;">
        <h3 class="text-center mb-4" style="color: var(--ctp-green);">Registrarse</h3>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <form action="/app/config/validacionregister.php" method="POST">
               <div class="mb-3">
                <label for="correo" class="form-label">Nombre de Usuario</label>
                <input type="nombre" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="correo" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="correo" name="correo" required>
            </div>
            <div class="mb-3">
                <label for="correo_conf" class="form-label">Confirmar Correo</label>
                <input type="email" class="form-control" id="correo_conf" name="correo_conf" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="password_conf" class="form-label">Confirmar Contraseña</label>
                <input type="password" class="form-control" id="password_conf" name="password_conf" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Crear Cuenta</button>
        </form>
    </div>
</main>

<?php require_once '../../view/layout/footer.php'; ?>

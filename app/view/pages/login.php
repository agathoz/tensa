<?php
require_once '../../config/seciones.php';
if(estaLogueado()) { header("Location: /"); exit; }

require_once '../../view/layout/header.php';
require_once '../../view/layout/navbar.php';
?>

<main class="container py-5 min-vh-80 d-flex justify-content-center align-items-center">
    <div class="card p-4 mx-auto w-100" style="max-width: 400px;">
        <h3 class="text-center mb-4" style="color: var(--ctp-blue);">Iniciar Sesión</h3>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success" style="background-color: var(--ctp-green); color: var(--ctp-mantle); border:none;">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <form action="/app/config/validacionlogin.php" method="POST">
            <div class="mb-3">
                <label for="correo" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="correo" name="correo" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>
        <div class="text-center mt-3">
            <a href="/app/view/pages/register.php" style="color: var(--ctp-subtext0);">¿No tienes cuenta? Regístrate</a>
        </div>
    </div>
</main>

<?php require_once '../../view/layout/footer.php'; ?>

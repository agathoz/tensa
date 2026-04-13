<?php
// Asume que session_start() y app/config/seciones.php se incluyeron previamente
$logueado = estaLogueado();
$rol = rolUsuario();
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" style="color: var(--ctp-mauve) !important;" href="/">
        <img src="/assets/img/logo.png" alt="Logo" style="height: 32px; width: 32px; object-fit: contain; border-radius: 6px;">
        BELAMITECH
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
            <a class="nav-link" href="/"><i class="bi bi-house-door me-1"></i>Inicio</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/app/view/pages/directorio.php"><i class="bi bi-people me-1"></i>Directorio</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/app/view/pages/servicios.php"><i class="bi bi-gear me-1"></i>Servicios</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/app/view/pages/productos.php"><i class="bi bi-box-seam me-1"></i>Productos</a>
        </li>
      </ul>
      <ul class="navbar-nav ms-auto align-items-center gap-2">
        <!-- Theme Toggle -->
        <li class="nav-item">
            <button class="theme-toggle" onclick="BelaTech.toggleTheme()" title="Cambiar tema" id="theme-toggle-btn">
                <i class="bi bi-sun-fill icon-sun"></i>
                <i class="bi bi-moon-stars-fill icon-moon"></i>
            </button>
        </li>

        <!-- Cart Button -->
        <li class="nav-item">
            <button class="cart-nav-btn" id="cart-nav-btn" title="Carrito de compras">
                <i class="bi bi-cart3"></i>
                <span class="cart-badge" id="cart-badge" style="display:none;">0</span>
            </button>
        </li>

        <?php if ($logueado): ?>
            <li class="nav-item">
                <a class="nav-link" href="/app/view/nopublico/perfilpanel.php" style="color: var(--ctp-green) !important;">
                    <i class="bi bi-person-circle me-1"></i>Mi Perfil
                </a>
            </li>
            
            <?php if ($rol === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link text-warning" href="/app/view/nopublico/admin_panel.php">
                        <i class="bi bi-shield-lock me-1"></i>Admin
                    </a>
                </li>
            <?php endif; ?>
            
            <?php if ($rol === 'empleado' || $rol === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link text-info" href="/app/view/nopublico/empleado_panel.php">
                        <i class="bi bi-briefcase me-1"></i>Panel
                    </a>
                </li>
            <?php endif; ?>
            
            <li class="nav-item">
                <a class="nav-link text-danger" href="/app/config/validacionlogin.php?logout=1">
                    <i class="bi bi-box-arrow-right me-1"></i>Salir
                </a>
            </li>
        <?php else: ?>
            <li class="nav-item">
                <a class="nav-link" href="/app/view/pages/login.php"><i class="bi bi-box-arrow-in-right me-1"></i>Iniciar Sesión</a>
            </li>
            <li class="nav-item">
                <a class="nav-link btn btn-primary px-3 ms-2" style="color: var(--ctp-mantle) !important;" href="/app/view/pages/register.php">Registrarse</a>
            </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Cart Sidebar -->
<div class="cart-overlay" id="cart-overlay"></div>
<div class="cart-sidebar" id="cart-sidebar">
    <div class="cart-sidebar-header">
        <h5 style="margin:0; color: var(--ctp-text);"><i class="bi bi-cart3 me-2"></i>Mi Carrito</h5>
        <button class="btn-close" id="cart-close-btn" style="filter: invert(0.8);"></button>
    </div>
    <div class="cart-sidebar-body" id="cart-sidebar-body">
        <div class="cart-empty-msg">
            <i class="bi bi-cart-x"></i>
            <p>Tu carrito está vacío</p>
        </div>
    </div>
    <div class="cart-sidebar-footer">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <strong style="color: var(--ctp-text);">Total:</strong>
            <strong style="color: var(--ctp-green); font-size: 1.15rem;" id="cart-total">$0.00</strong>
        </div>
        <a href="/app/view/nopublico/checkout.php" class="btn btn-primary w-100" style="border-radius: 10px;">
            <i class="bi bi-credit-card me-1"></i>Proceder al Pago
        </a>
        <a href="/app/view/pages/productos.php" class="btn btn-outline-secondary w-100 mt-2" style="border-radius: 10px;">
            Seguir Comprando
        </a>
    </div>
</div>

<?php
require_once '../../config/seciones.php';
requerirLogin();
require_once '../../config/db.php';

require_once '../../view/layout/header.php';
require_once '../../view/layout/navbar.php';
?>

<main class="container py-5 min-vh-80">
    <div class="row">
        <!-- Resumen del Carrito (Izquierda) -->
        <div class="col-lg-6 mb-4">
            <h3 style="color: var(--ctp-sapphire);"><i class="bi bi-cart-check me-2"></i>Resumen de tu Pedido</h3>
            <div class="card card-glass p-4 mt-3">
                <div id="checkout-cart-items">
                    <!-- Javascript will inject the cart here -->
                    <p class="text-center" style="color: var(--ctp-subtext1);">Cargando carrito...</p>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top" style="border-color: var(--ctp-surface1) !important;">
                    <h5 style="color: var(--ctp-subtext0); margin: 0;">Total a pagar:</h5>
                    <h4 style="color: var(--ctp-green); margin: 0; font-weight: 700;" id="checkout-total-price">$0.00 MXN</h4>
                </div>
            </div>
        </div>

        <!-- Formulario de Envío y Pago (Derecha) -->
        <div class="col-lg-6">
            <h3 style="color: var(--ctp-peach);"><i class="bi bi-credit-card me-2"></i>Datos de Envío y Pago</h3>
            <div class="card card-glass p-4 mt-3">
                <form action="/app/config/checkout_validar.php" method="POST" id="checkout-form">
                    
                    <!-- Este input oculto enviará todo el JSON del carrito al servidor -->
                    <input type="hidden" name="cart_payload" id="cart_payload" value="">

                    <h6 class="mb-3" style="color: var(--ctp-text);">Opciones de Envío</h6>
                    <div class="mb-4">
                        <select name="tipo_envio" class="form-select" id="tipoEnvio" onchange="toggleDireccion()" required style="background-color: var(--ctp-crust); color: var(--ctp-text); border-color: var(--ctp-surface1);">
                            <option value="sucursal">Recoger en Sucursal</option>
                            <option value="domicilio">Envío a Domicilio</option>
                        </select>
                    </div>
                    
                    <div class="mb-4" id="domicilioDiv" style="display:none;">
                        <label style="color: var(--ctp-subtext1); font-size: 0.9rem; margin-bottom: 5px;">Dirección Completa de Envío</label>
                        <textarea name="direccion_envio" class="form-control" rows="3" placeholder="Calle, Número, Colonia, Código Postal, Ciudad..." style="background-color: var(--ctp-crust); color: var(--ctp-text); border-color: var(--ctp-surface1);"></textarea>
                    </div>

                    <h6 class="mb-3" style="color: var(--ctp-text);">Método de Pago</h6>
                    <div class="mb-4">
                        <select name="metodo_pago" class="form-select" required style="background-color: var(--ctp-crust); color: var(--ctp-text); border-color: var(--ctp-surface1);">
                            <option value="paypal">PayPal (Pago en línea)</option>
                            <option value="tarjeta">Tarjeta de Crédito / Débito</option>
                            <option value="sucursal">Pagar directamente en Sucursal</option>
                        </select>
                    </div>

                    <!-- Mensaje de advertencia -->
                    <div class="alert alert-warning d-flex align-items-center gap-2 mb-4" style="background-color: rgba(250, 179, 135, 0.1); border: 1px solid var(--ctp-peach); color: var(--ctp-peach);">
                        <i class="bi bi-info-circle-fill"></i>
                        <small>Al confirmar el pedido, las unidades se descontarán del inventario y se generará tu factura automáticamente.</small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-lg" id="btn-confirm-order" style="border-radius: 12px; font-weight: 600;">
                        Confirmar y Generar Factura
                    </button>
                    <a href="/app/view/pages/productos.php" class="btn btn-outline-secondary w-100 mt-2" style="border-radius: 12px;">Seguir Comprando</a>
                </form>
            </div>
        </div>
    </div>
</main>

<!-- Modales de Pago Simulado -->

<!-- Modal Tarjeta de Crédito -->
<div class="modal fade" id="modalTarjeta" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: var(--ctp-base); border: 1px solid var(--ctp-surface1);">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title" style="color: var(--ctp-text);"><i class="bi bi-credit-card-2-front me-2 text-primary"></i>Pago Seguro con Tarjeta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) grayscale(100%) brightness(200%);"></button>
            </div>
            <div class="modal-body pt-4">
                <div class="alert alert-info text-center" style="font-size: 0.85rem;">🔒 Conexión cifrada a la pasarela de pagos.</div>
                <div class="mb-3">
                    <label style="color: var(--ctp-subtext1); font-size: 0.9rem;">Nombre en la tarjeta</label>
                    <input type="text" class="form-control mt-1" id="mockCardName" placeholder="EJ. JUAN PEREZ" style="background-color: var(--ctp-crust); color: var(--ctp-text); border-color: var(--ctp-surface1);">
                </div>
                <div class="mb-3">
                    <label style="color: var(--ctp-subtext1); font-size: 0.9rem;">Número de tarjeta</label>
                    <div class="input-group mt-1">
                        <span class="input-group-text" style="background-color: var(--ctp-surface0); border-color: var(--ctp-surface1);"><i class="bi bi-credit-card"></i></span>
                        <input type="text" class="form-control" id="mockCardNum" placeholder="0000 0000 0000 0000" maxlength="19" style="background-color: var(--ctp-crust); color: var(--ctp-text); border-color: var(--ctp-surface1);">
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label style="color: var(--ctp-subtext1); font-size: 0.9rem;">Vencimiento</label>
                        <input type="text" class="form-control mt-1" id="mockCardExp" placeholder="MM/YY" maxlength="5" style="background-color: var(--ctp-crust); color: var(--ctp-text); border-color: var(--ctp-surface1);">
                    </div>
                    <div class="col-6 mb-3">
                        <label style="color: var(--ctp-subtext1); font-size: 0.9rem;">CVC</label>
                        <input type="password" class="form-control mt-1" id="mockCardCvc" placeholder="123" maxlength="4" style="background-color: var(--ctp-crust); color: var(--ctp-text); border-color: var(--ctp-surface1);">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0 justify-content-center pb-4">
                <button type="button" class="btn btn-primary w-100" id="btn-process-tarjeta" style="font-weight: 600; border-radius: 8px;">Procesar Pago</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal PayPal -->
<div class="modal fade" id="modalPaypal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: var(--ctp-base); border: 1px solid var(--ctp-surface1);">
            <div class="modal-header border-bottom-0 pb-0 justify-content-center position-relative">
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) grayscale(100%) brightness(200%);"></button>
                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" alt="PayPal" style="height: 30px; filter: brightness(0.9);">
            </div>
            <div class="modal-body text-center pt-4 pb-4">
                <h5 style="color: var(--ctp-text); margin-bottom: 20px;">Inicia sesión para pagar con PayPal</h5>
                <input type="email" class="form-control mb-3 mx-auto" placeholder="Correo electrónico" value="<?php echo $_SESSION['correo'] ?? ''; ?>" style="max-width: 80%; background-color: var(--ctp-crust); color: var(--ctp-text); border-color: var(--ctp-surface1);" readonly>
                <input type="password" class="form-control mb-4 mx-auto" id="mockPaypalPass" placeholder="Contraseña de PayPal" style="max-width: 80%; background-color: var(--ctp-crust); color: var(--ctp-text); border-color: var(--ctp-surface1);">
                
                <button type="button" class="btn btn-primary w-100 mx-auto" id="btn-process-paypal" style="max-width: 80%; font-weight: 600; border-radius: 20px; background-color: #0070ba; border-color: #0070ba;">Iniciar Sesión y Pagar</button>
                <p class="mt-3 mb-0" style="color: var(--ctp-overlay1); font-size: 0.8rem;">Modo Simulación Activo</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    renderCheckoutCart();

    var form = document.getElementById('checkout-form');
    var modalTarjeta = new bootstrap.Modal(document.getElementById('modalTarjeta'));
    var modalPaypal = new bootstrap.Modal(document.getElementById('modalPaypal'));

    form.addEventListener('submit', function(e) {
        // Obtenemos el payload para verificar que no este vacio
        var payloadObj = [];
        try {
            payloadObj = JSON.parse(document.getElementById('cart_payload').value);
        } catch(err) {}
        
        if (!payloadObj || payloadObj.length === 0) {
            e.preventDefault();
            alert("Tu carrito está vacío. Agrega productos antes de continuar.");
            window.location.href = "/app/view/pages/productos.php";
            return false;
        }

        var metodoPago = document.querySelector('select[name="metodo_pago"]').value;
        
        // Si no es sucursal, detenemos el flujo para mostrar el panel de pago
        if(metodoPago === 'tarjeta' && !form.dataset.paymentCleared) {
            e.preventDefault();
            modalTarjeta.show();
        } else if(metodoPago === 'paypal' && !form.dataset.paymentCleared) {
            e.preventDefault();
            modalPaypal.show();
        } else {
            // Ya paso el pago o es sucursal
            var btn = document.getElementById('btn-confirm-order');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Procesando...';
        }
    });

    // Validacion botones modales
    document.getElementById('btn-process-tarjeta').addEventListener('click', function() {
        var num = document.getElementById('mockCardNum').value;
        if(num.length < 15) { alert("Completa el número de tarjeta."); return; }
        
        this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando...';
        this.disabled = true;
        
        // Simulate network delay
        setTimeout(() => {
            modalTarjeta.hide();
            form.dataset.paymentCleared = 'true';
            form.submit();
        }, 1500);
    });

    document.getElementById('btn-process-paypal').addEventListener('click', function() {
        var pass = document.getElementById('mockPaypalPass').value;
        if(!pass) { alert("Ingresa una contraseña para la simulación."); return; }
        
        this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Verificando...';
        this.disabled = true;
        
        setTimeout(() => {
            modalPaypal.hide();
            form.dataset.paymentCleared = 'true';
            form.submit();
        }, 1500);
    });
});

function toggleDireccion() {
    var tipo = document.getElementById('tipoEnvio').value;
    document.getElementById('domicilioDiv').style.display = (tipo === 'domicilio') ? 'block' : 'none';
    if(tipo === 'domicilio') {
        document.querySelector('textarea[name="direccion_envio"]').required = true;
    } else {
        document.querySelector('textarea[name="direccion_envio"]').required = false;
    }
}

function renderCheckoutCart() {
    var cartItemsContainer = document.getElementById('checkout-cart-items');
    var totalEl = document.getElementById('checkout-total-price');
    var payloadInput = document.getElementById('cart_payload');
    
    // Obtenemos el cart del objeto global (si existe)
    var cart = [];
    if (window.BelaTech && window.BelaTech.getCart) {
        cart = window.BelaTech.getCart();
    } else {
        // Fallback pidiendo localStorage directo de forma cruda si no está disponible la UI
        try {
            cart = JSON.parse(localStorage.getItem('belamitech_cart')) || [];
        } catch (e) {
            cart = [];
        }
    }

    if (cart.length === 0) {
        cartItemsContainer.innerHTML = '<div class="alert alert-info" style="background-color: var(--ctp-crust); color: var(--ctp-text);">Tu carrito está vacío.</div>';
        payloadInput.value = "[]";
        document.getElementById('btn-confirm-order').disabled = true;
        return;
    }

    var html = '<div class="table-responsive"><table class="table" style="color: var(--ctp-text); border-color: var(--ctp-surface1);"><thead><tr><th style="border-bottom: none;">Producto</th><th class="text-center" style="border-bottom: none;">Cant.</th><th class="text-end" style="border-bottom: none;">Subtotal</th></tr></thead><tbody>';
    var total = 0;
    var payloadArray = [];

    for (var i = 0; i < cart.length; i++) {
        var item = cart[i];
        var itemTotal = item.price * item.qty;
        total += itemTotal;
        var imgUrl = item.img || '/assets/img/computer_sagyouin_woman.png';
        
        html += '<tr style="border-color: var(--ctp-surface0);">' +
                '<td class="align-middle"><div class="d-flex align-items-center gap-2"><img src="' + imgUrl + '" style="width:40px; height:40px; object-fit:cover; border-radius:6px; flex-shrink: 0;"><span>' + item.name + '</span></div></td>' +
                '<td class="align-middle text-center">' + item.qty + '</td>' +
                '<td class="align-middle text-end font-monospace" style="color: var(--ctp-green);">$' + itemTotal.toFixed(2) + '</td>' +
                '</tr>';
        
        payloadArray.push({ id: item.id, qty: item.qty });
    }

    html += '</tbody></table></div>';
    cartItemsContainer.innerHTML = html;
    
    var totalText = total.toFixed(2);
    totalEl.textContent = '$' + totalText + ' MXN';
    
    // Set text on buttons
    document.getElementById('btn-process-tarjeta').innerText = 'Pagar $' + totalText + ' MXN';
    
    // Guardar JSON
    payloadInput.value = JSON.stringify(payloadArray);
}
</script>

<?php require_once '../../view/layout/footer.php'; ?>

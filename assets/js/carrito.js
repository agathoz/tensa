/**
 * BELAMITECH — Shopping Cart (localStorage persistence)
 * Manages cart state, rendering, and sidebar interactions
 */
(function () {
    var STORAGE_KEY = 'belamitech_cart';

    function getCart() {
        try {
            return JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
        } catch (e) {
            return [];
        }
    }

    function saveCart(cart) {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
        } catch (e) {}
        updateBadge();
        renderSidebar();
    }

    function addToCart(product) {
        var cart = getCart();
        var existing = null;
        for (var i = 0; i < cart.length; i++) {
            if (cart[i].id == product.id) {
                existing = cart[i];
                break;
            }
        }
        if (existing) {
            existing.qty += 1;
        } else {
            cart.push({
                id: product.id,
                name: product.name,
                price: parseFloat(product.price),
                img: product.img || '',
                qty: 1
            });
        }
        saveCart(cart);
        showCartSidebar();
    }

    function removeFromCart(productId) {
        var cart = getCart();
        var filtered = [];
        for (var i = 0; i < cart.length; i++) {
            if (cart[i].id != productId) {
                filtered.push(cart[i]);
            }
        }
        saveCart(filtered);
    }

    function updateQuantity(productId, delta) {
        var cart = getCart();
        for (var i = 0; i < cart.length; i++) {
            if (cart[i].id == productId) {
                cart[i].qty += delta;
                if (cart[i].qty <= 0) {
                    removeFromCart(productId);
                    return;
                }
                break;
            }
        }
        saveCart(cart);
    }

    function clearCart() {
        try {
            localStorage.removeItem(STORAGE_KEY);
        } catch (e) {}
        updateBadge();
        renderSidebar();
    }

    function getTotal() {
        var cart = getCart();
        var sum = 0;
        for (var i = 0; i < cart.length; i++) {
            sum += cart[i].price * cart[i].qty;
        }
        return sum;
    }

    function getTotalItems() {
        var cart = getCart();
        var sum = 0;
        for (var i = 0; i < cart.length; i++) {
            sum += cart[i].qty;
        }
        return sum;
    }

    /* ---- UI ---- */

    function updateBadge() {
        var badge = document.getElementById('cart-badge');
        if (!badge) return;
        var count = getTotalItems();
        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }

    function renderSidebar() {
        var body = document.getElementById('cart-sidebar-body');
        var totalEl = document.getElementById('cart-total');
        if (!body) return;

        var cart = getCart();
        if (cart.length === 0) {
            body.innerHTML =
                '<div class="cart-empty-msg">' +
                    '<i class="bi bi-cart-x"></i>' +
                    '<p>Tu carrito está vacío</p>' +
                '</div>';
            if (totalEl) totalEl.textContent = '$0.00';
            return;
        }

        var html = '';
        for (var i = 0; i < cart.length; i++) {
            var item = cart[i];
            var imgSrc = item.img || '/assets/img/computer_sagyouin_woman.png';
            var escapedName = item.name.replace(/</g, '&lt;').replace(/>/g, '&gt;');
            html +=
                '<div class="cart-item" data-id="' + item.id + '">' +
                    '<img src="' + imgSrc + '" alt="' + escapedName + '" class="cart-item-img">' +
                    '<div class="cart-item-info">' +
                        '<div class="cart-item-name">' + escapedName + '</div>' +
                        '<div class="cart-item-price">$' + (item.price * item.qty).toFixed(2) + ' MXN</div>' +
                    '</div>' +
                    '<div class="cart-qty-control">' +
                        '<button class="cart-qty-btn" onclick="BelaTech.updateQty(' + item.id + ', -1)">−</button>' +
                        '<span class="cart-qty-num">' + item.qty + '</span>' +
                        '<button class="cart-qty-btn" onclick="BelaTech.updateQty(' + item.id + ', 1)">+</button>' +
                    '</div>' +
                    '<button class="cart-remove-btn" onclick="BelaTech.removeItem(' + item.id + ')" title="Eliminar">' +
                        '<i class="bi bi-trash3"></i>' +
                    '</button>' +
                '</div>';
        }
        body.innerHTML = html;
        // Update main cart sidebar CTA for checkout
        var footerHtml = 
            '<div class="d-flex justify-content-between align-items-center mb-3">' +
                '<strong style="color: var(--ctp-text);">Total:</strong>' +
                '<strong style="color: var(--ctp-green); font-size: 1.15rem;" id="cart-total">$' + getTotal().toFixed(2) + ' MXN</strong>' +
            '</div>' +
            '<a href="/app/view/nopublico/checkout.php" class="btn btn-primary w-100" style="border-radius: 10px;">' +
                '<i class="bi bi-credit-card me-1"></i>Proceder al Pago' +
            '</a>' +
            '<a href="/app/view/pages/productos.php" class="btn btn-outline-secondary w-100 mt-2" style="border-radius: 10px;">' +
                'Seguir Comprando' +
            '</a>';
        
        var footerEl = document.querySelector('.cart-sidebar-footer');
        if(footerEl) footerEl.innerHTML = footerHtml;
    }

    function showCartSidebar() {
        var overlay = document.getElementById('cart-overlay');
        var sidebar = document.getElementById('cart-sidebar');
        if (overlay) overlay.classList.add('active');
        if (sidebar) sidebar.classList.add('active');
        renderSidebar();
    }

    function hideCartSidebar() {
        var overlay = document.getElementById('cart-overlay');
        var sidebar = document.getElementById('cart-sidebar');
        if (overlay) overlay.classList.remove('active');
        if (sidebar) sidebar.classList.remove('active');
    }

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function () {
        updateBadge();

        // Close sidebar on overlay click
        var overlay = document.getElementById('cart-overlay');
        if (overlay) {
            overlay.addEventListener('click', hideCartSidebar);
        }

        // Close button
        var closeBtn = document.getElementById('cart-close-btn');
        if (closeBtn) {
            closeBtn.addEventListener('click', hideCartSidebar);
        }

        // Open sidebar on cart nav click
        var cartNavBtn = document.getElementById('cart-nav-btn');
        if (cartNavBtn) {
            cartNavBtn.addEventListener('click', function (e) {
                e.preventDefault();
                showCartSidebar();
            });
        }
    });

    // Expose globally — merge with existing (don't overwrite theme functions)
    if (!window.BelaTech) window.BelaTech = {};
    window.BelaTech.addToCart = addToCart;
    window.BelaTech.removeItem = removeFromCart;
    window.BelaTech.updateQty = updateQuantity;
    window.BelaTech.clearCart = clearCart;
    window.BelaTech.showCart = showCartSidebar;
    window.BelaTech.hideCart = hideCartSidebar;
    window.BelaTech.getCart = getCart;
})();

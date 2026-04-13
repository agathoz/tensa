<?php
require_once '../../config/seciones.php';
require_once '../../config/db.php';

$id = $_GET['id'] ?? null;
if(!$id) die("Producto no especificado.");

$stmt = $pdo->prepare("SELECT p.*, pr.nombre AS proveedor_nombre FROM productos p LEFT JOIN proveedores pr ON p.proveedor_id = pr.id WHERE p.id = ? AND p.status = 'activo'");
$stmt->execute([$id]);
$prod = $stmt->fetch();

if(!$prod) die("El producto no existe o está inactivo.");

// Manejo de la galería JSON
$galeria = [];
if(!empty($prod['galeria'])) {
    $decoded = json_decode($prod['galeria'], true);
    if(is_array($decoded)) {
        $galeria = $decoded;
    }
}
// Si la galería está vacía pero hay foto URL (por la migración inicial manual), la metemos
if(empty($galeria) && $prod['foto_url']) {
    $galeria[] = $prod['foto_url'];
}

require_once '../../view/layout/header.php';
require_once '../../view/layout/navbar.php';
?>

<main class="container py-5 min-vh-80" id="product_detail">
    
    <div class="mb-4">
        <a href="/app/view/pages/productos.php" class="btn btn-outline-secondary" style="border-radius: 20px;">
            <i class="bi bi-arrow-left me-1"></i>Regresar a Productos
        </a>
    </div>

    <div class="row g-5">
        <!-- Carrusel de Imágenes (Izquierda) -->
        <div class="col-lg-6">
            <div class="card card-glass p-1 border-0 h-100 shadow-sm" style="background-color: var(--ctp-surface0);">
                <?php if(count($galeria) > 1): ?>
                    <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <?php foreach($galeria as $index => $img): ?>
                                <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="<?php echo $index; ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>"></button>
                            <?php endforeach; ?>
                        </div>
                        <div class="carousel-inner" style="border-radius: 12px; overflow: hidden; aspect-ratio: 4/3;">
                            <?php foreach($galeria as $index => $img): ?>
                                <?php $src = ($img != 'default.png') ? '/app/assets/uploads/' . htmlspecialchars($img) : '/assets/img/computer_sagyouin_woman.png'; ?>
                                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?> h-100">
                                    <img src="<?php echo $src; ?>" class="d-block w-100 h-100" style="object-fit: contain; background: var(--ctp-crust);" alt="Imagen del producto <?php echo $index+1; ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev" style="filter: drop-shadow(0 0 5px rgba(0,0,0,0.5));">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next" style="filter: drop-shadow(0 0 5px rgba(0,0,0,0.5));">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Siguiente</span>
                        </button>
                    </div>
                <?php else: ?>
                    <!-- Si solo hay una imagen -->
                    <div style="border-radius: 12px; overflow: hidden; aspect-ratio: 4/3; background: var(--ctp-crust);">
                        <?php 
                        $img = $galeria[0] ?? 'default.png';
                        $src = ($img != 'default.png') ? '/app/assets/uploads/' . htmlspecialchars($img) : '/assets/img/computer_sagyouin_woman.png'; 
                        ?>
                        <img src="<?php echo $src; ?>" class="d-block w-100 h-100" style="object-fit: contain;" alt="<?php echo htmlspecialchars($prod['nombre']); ?>">
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Detalles Técnicos y Compra (Derecha) -->
        <div class="col-lg-6">
            <h1 style="color: var(--ctp-text); font-weight: 800; font-size: 2.5rem; letter-spacing: -1px; margin-bottom: 0;">
                <?php echo htmlspecialchars($prod['nombre']); ?>
            </h1>
            
            <div class="d-flex align-items-center gap-3 mt-2 mb-4">
                <?php if(!empty($prod['marca'])): ?>
                    <span class="badge" style="background-color: var(--ctp-blue); font-size: 0.85rem; padding: 6px 12px;"><?php echo htmlspecialchars($prod['marca']); ?></span>
                <?php endif; ?>
                <span style="color: var(--ctp-subtext0); font-family: monospace;">SKU: <?php echo htmlspecialchars($prod['sku'] ?: 'N/A'); ?></span>
                <span class="text-muted" style="font-family: monospace;">COD: <?php echo htmlspecialchars($prod['codigo'] ?: 'N/A'); ?></span>
            </div>

            <div class="mb-4">
                <h2 style="color: var(--ctp-green); font-weight: 800; font-size: 2.5rem; margin: 0;">
                    $<?php echo number_format($prod['precio'], 2); ?> <span style="font-size: 1.2rem; color: var(--ctp-subtext1);">MXN</span>
                </h2>
                <div class="mt-2">
                    <?php if($prod['stock'] > 0): ?>
                        <span class="badge bg-success" style="font-size: 0.9rem;"><i class="bi bi-box-seam me-1"></i>En Stock (<?php echo $prod['stock']; ?> unidades)</span>
                    <?php else: ?>
                        <span class="badge bg-danger" style="font-size: 0.9rem;"><i class="bi bi-x-circle me-1"></i>Agotado temporalmente</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card p-4 border-0 mb-4" style="background-color: var(--ctp-mantle); border-radius: 16px;">
                <h5 style="color: var(--ctp-peach); margin-bottom: 12px;"><i class="bi bi-info-circle me-2"></i>Descripción del Producto</h5>
                <p style="color: var(--ctp-subtext1); line-height: 1.7; margin-bottom: 0; white-space: pre-line;">
                    <?php echo htmlspecialchars($prod['descripcion']); ?>
                </p>
            </div>

            <div class="d-flex gap-3">
                <?php if($prod['stock'] > 0): ?>
                    <!-- Variables para el script del carrito -->
                    <?php 
                        $mainImageStr = ($galeria[0] ?? 'default.png') !== 'default.png' ? '/app/assets/uploads/' . htmlspecialchars($galeria[0]) : '/assets/img/computer_sagyouin_woman.png';
                    ?>
                    <button class="btn btn-primary btn-lg flex-grow-1" style="border-radius: 12px; font-weight: 700; background-color: var(--ctp-green); border-color: var(--ctp-green); color: var(--ctp-base);"
                        data-prod-id="<?php echo $prod['id']; ?>"
                        data-prod-name="<?php echo htmlspecialchars($prod['nombre'], ENT_QUOTES, 'UTF-8'); ?>"
                        data-prod-price="<?php echo $prod['precio']; ?>"
                        data-prod-img="<?php echo $mainImageStr; ?>"
                        onclick="var d=this.dataset;BelaTech.addToCart({id:parseInt(d.prodId),name:d.prodName,price:d.prodPrice,img:d.prodImg});
                        // Visual feedback (Toast)
                        var b = this; var old = b.innerHTML; b.innerHTML='<i class=\'bi bi-check2-circle\'></i> Agregado'; b.classList.add('bg-success');
                        setTimeout(()=>{ b.innerHTML=old; b.classList.remove('bg-success'); }, 2000);">
                        <i class="bi bi-cart-plus me-2"></i>Agregar al Carrito
                    </button>
                    
                    <?php if(estaLogueado()): ?>
                        <button class="btn btn-secondary btn-lg" style="border-radius: 12px; font-weight: 600;"
                                data-prod-id="<?php echo $prod['id']; ?>"
                                data-prod-name="<?php echo htmlspecialchars($prod['nombre'], ENT_QUOTES, 'UTF-8'); ?>"
                                data-prod-price="<?php echo $prod['precio']; ?>"
                                data-prod-img="<?php echo $mainImageStr; ?>"
                                onclick="var d=this.dataset;BelaTech.addToCart({id:parseInt(d.prodId),name:d.prodName,price:d.prodPrice,img:d.prodImg}); window.location.href='/app/view/nopublico/checkout.php';">
                            <i class="bi bi-lightning-charge-fill me-1" style="color: #f9e2af;"></i>Comprar Ya
                        </button>
                    <?php endif; ?>
                <?php else: ?>
                    <button class="btn btn-secondary w-100 btn-lg" disabled style="border-radius: 12px; font-weight: 600;">
                        Sin existencias
                    </button>
                <?php endif; ?>
            </div>
            
            <div class="mt-4 pt-3 border-top pb-2" style="border-color: var(--ctp-surface1) !important;">
                <small style="color: var(--ctp-overlay0);">Vendido y garantizado por BELAMITECH. Pagos cifrados.</small>
            </div>
            
        </div>
    </div>
</main>

<?php require_once '../../view/layout/footer.php'; ?>

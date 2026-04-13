<?php
require_once '../../config/seciones.php';
require_once '../../config/db.php';

$stmt = $pdo->query("SELECT * FROM productos WHERE status = 'activo' ORDER BY id DESC");
$productos = $stmt->fetchAll();

require_once '../../view/layout/header.php';
require_once '../../view/layout/navbar.php';
?>

<main class="container py-5 min-vh-80">
    <h2 style="color: var(--ctp-green);" class="text-center mb-2">
        <i class="bi bi-box-seam me-2"></i>Nuestros Productos
    </h2>
    <p class="text-center mb-5" style="color: var(--ctp-subtext0); max-width: 550px; margin: 0 auto;">
        Equipos y componentes tecnológicos de las mejores marcas con garantía y soporte técnico incluido.
    </p>
    
    <div class="row g-4">
        <?php foreach($productos as $prod): ?>
        <?php 
            // Resolve image path - use uploaded photo if not default
            $foto = ($prod['foto_url'] && $prod['foto_url'] !== 'default.png') 
                ? '/app/assets/uploads/' . htmlspecialchars($prod['foto_url']) 
                : '/assets/img/computer_sagyouin_woman.png';
        ?>
        <div class="col-md-4 animate-in">
            <div class="card product-card h-100">
                <a href="/app/view/pages/producto_detalle.php?id=<?php echo $prod['id']; ?>" class="d-block" style="text-decoration: none;">
                    <div class="product-img-wrap" style="cursor: pointer; transition: transform 0.2s ease;">
                        <img src="<?php echo $foto; ?>" alt="<?php echo htmlspecialchars($prod['nombre']); ?>">
                    </div>
                </a>
                <div class="p-3 d-flex flex-column" style="flex: 1;">
                    <a href="/app/view/pages/producto_detalle.php?id=<?php echo $prod['id']; ?>" style="text-decoration: none;">
                        <h5 style="color: var(--ctp-text); font-weight: 700; transition: color 0.2s;" class="mb-1 product-title-hover"><?php echo htmlspecialchars($prod['nombre']); ?></h5>
                    </a>
                    <?php if(!empty($prod['marca'])): ?>
                        <span class="tool-badge mb-2" style="align-self: flex-start;"><?php echo htmlspecialchars($prod['marca']); ?></span>
                    <?php endif; ?>
                    <p style="color: var(--ctp-subtext0); font-size: 0.88rem; flex-grow: 1;"><?php echo htmlspecialchars($prod['descripcion']); ?></p>
                    
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <h5 style="color: var(--ctp-blue); margin: 0; font-weight: 700;">$<?php echo number_format($prod['precio'], 2); ?> <small style="font-size: 0.7rem; color: var(--ctp-subtext0);">MXN</small></h5>
                        <?php if($prod['stock'] > 0): ?>
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i><?php echo $prod['stock']; ?> disponibles</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Agotado</span>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <!-- Add to Cart Button -->
                        <button class="btn-cart flex-grow-1" 
                            data-prod-id="<?php echo $prod['id']; ?>"
                            data-prod-name="<?php echo htmlspecialchars($prod['nombre'], ENT_QUOTES, 'UTF-8'); ?>"
                            data-prod-price="<?php echo $prod['precio']; ?>"
                            data-prod-img="<?php echo $foto; ?>"
                            onclick="var d=this.dataset;BelaTech.addToCart({id:parseInt(d.prodId),name:d.prodName,price:d.prodPrice,img:d.prodImg})">
                            <i class="bi bi-cart-plus"></i> Agregar al Carrito
                        </button>
                        
                        <?php if(estaLogueado()): ?>
                            <!-- Comprar Ahora Directo -->
                            <button class="btn btn-success" style="border-radius: 10px;" title="Comprar ahora"
                                data-prod-id="<?php echo $prod['id']; ?>"
                                data-prod-name="<?php echo htmlspecialchars($prod['nombre'], ENT_QUOTES, 'UTF-8'); ?>"
                                data-prod-price="<?php echo $prod['precio']; ?>"
                                data-prod-img="<?php echo $foto; ?>"
                                onclick="var d=this.dataset;BelaTech.addToCart({id:parseInt(d.prodId),name:d.prodName,price:d.prodPrice,img:d.prodImg}); window.location.href='/app/view/nopublico/checkout.php';">
                                <i class="bi bi-bag-check"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if(count($productos) === 0): ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-box-seam" style="font-size: 3rem; color: var(--ctp-overlay0);"></i>
                <p class="mt-3" style="color: var(--ctp-overlay1);">No hay productos disponibles por el momento.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once '../../view/layout/footer.php'; ?>

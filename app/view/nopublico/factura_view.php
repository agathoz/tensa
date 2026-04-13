<?php
require_once '../../config/seciones.php';
requerirLogin();
require_once '../../config/db.php';

$factura_id = $_GET['id'] ?? null;
if(!$factura_id) { die("ID de factura no válido"); }

// Si un usuario normal intenta verla, solo si le pertenece. Si es empleado o admin, pueden ver todo.
$es_empleado_o_admin = (rolUsuario() === 'empleado' || rolUsuario() === 'admin');

// Recuperar la factura
$stmtF = $pdo->prepare("SELECT f.*, c.estado_pago, c.metodo_pago, c.tipo_envio, c.direccion_envio, c.fecha_compra, u.nombre AS cliente_nombre, u.correo AS cliente_correo, u.id AS id_cliente 
                        FROM facturas f 
                        JOIN compras c ON f.compra_id = c.id 
                        JOIN usuarios u ON c.usuario_id = u.id 
                        WHERE f.id = ?");
$stmtF->execute([$factura_id]);
$factura = $stmtF->fetch();

if(!$factura) { die("Factura no encontrada."); }

if(!$es_empleado_o_admin && $factura['id_cliente'] != $_SESSION['usuario_id']) {
    die("No tienes permiso para ver esta factura.");
}

// Recuperar items
$stmtItems = $pdo->prepare("SELECT ci.*, p.nombre AS producto_nombre, p.codigo FROM compra_items ci JOIN productos p ON ci.producto_id = p.id WHERE ci.compra_id = ?");
$stmtItems->execute([$factura['compra_id']]);
$items = $stmtItems->fetchAll();

// Add JS flag to clear cart if coming from success
$clear_cart_js = "";
if (isset($_GET['clear_cart']) && $_GET['clear_cart'] == '1') {
    $clear_cart_js = "<script>document.addEventListener('DOMContentLoaded', function() { if(window.BelaTech && window.BelaTech.clearCart) window.BelaTech.clearCart(); });</script>";
}

require_once '../../view/layout/header.php';
require_once '../../view/layout/navbar.php';
?>

<main class="container py-5 min-vh-80" id="factura-page">
    <?php echo $clear_cart_js; ?>
    
    <div class="row">
        <div class="col-lg-10 mx-auto">
            
            <!-- Botones de Acción Superiores -->
            <div class="d-flex justify-content-end gap-3 mb-4 non-printable">
                <?php if($es_empleado_o_admin): ?>
                    <a href="/app/view/nopublico/empleado_panel.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Volver al Panel</a>
                <?php else: ?>
                    <a href="/app/view/nopublico/perfilpanel.php" class="btn btn-outline-secondary"><i class="bi bi-person"></i> Ir a mi Perfil</a>
                <?php endif; ?>
                
                <button onclick="window.print()" class="btn btn-primary" style="border-radius: 8px;">
                    <i class="bi bi-printer me-1"></i>Imprimir Factura
                </button>
            </div>

            <!-- Contenedor Principal de la Factura -->
            <div class="card card-glass p-0 overflow-hidden factura-container" style="border-radius: 16px; background: var(--ctp-base);">
                
                <!-- HEADER FACTURA -->
                <div class="p-5 d-flex justify-content-between align-items-center" style="background-color: var(--ctp-mantle); border-bottom: 2px dashed var(--ctp-surface2);">
                    <div class="d-flex align-items-center gap-3">
                        <!-- Img with absolute path for print compatibility -->
                        <img src="/assets/img/logo.png" alt="Logo" style="height: 60px; object-fit: contain;">
                        <div>
                            <h2 style="color: var(--ctp-text); font-weight: 800; margin: 0; letter-spacing: -1px;">BELAMITECH</h2>
                            <p style="color: var(--ctp-subtext0); margin: 0; font-size: 0.9rem;">Consultoría Tecnológica</p>
                        </div>
                    </div>
                    <div class="text-end">
                        <h4 style="color: var(--ctp-mauve); font-weight: 700; margin-bottom: 0;">FACTURA</h4>
                        <p style="color: var(--ctp-text); font-weight: 600; font-family: monospace; font-size: 1.1rem; margin-bottom: 0;">#<?php echo $factura['folio']; ?></p>
                        <p style="color: var(--ctp-subtext1); font-size: 0.85rem; margin-bottom: 0; mt-1">Fecha Emisión: <?php echo date('d/m/Y', strtotime($factura['fecha_emision'])); ?></p>
                    </div>
                </div>

                <!-- BODY FACTURA -->
                <div class="p-5">
                    
                    <div class="row mb-5">
                        <div class="col-sm-6">
                            <h6 style="color: var(--ctp-overlay1); font-size: 0.8rem; text-transform: uppercase;">Facturar A:</h6>
                            <h5 style="color: var(--ctp-text); font-weight: 700; margin-bottom: 2px;"><?php echo htmlspecialchars($factura['cliente_nombre']); ?></h5>
                            <p style="color: var(--ctp-subtext1); margin-bottom: 0;"><i class="bi bi-envelope me-1 text-muted"></i> <?php echo htmlspecialchars($factura['cliente_correo']); ?></p>
                            <?php if ($factura['tipo_envio'] === 'domicilio'): ?>
                                <p style="color: var(--ctp-subtext1); margin-bottom: 0; margin-top:5px;"><i class="bi bi-geo-alt me-1 text-muted"></i> <strong>Envío a Domicilio:</strong><br><?php echo nl2br(htmlspecialchars($factura['direccion_envio'])); ?></p>
                            <?php else: ?>
                                <p style="color: var(--ctp-subtext1); margin-bottom: 0; margin-top:5px;"><i class="bi bi-geo-alt me-1 text-muted"></i> <strong>Recoger en Sucursal</strong></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-sm-6 text-sm-end mt-4 mt-sm-0 text-end">
                            <h6 style="color: var(--ctp-overlay1); font-size: 0.8rem; text-transform: uppercase;">Estado del Pago:</h6>
                            <?php if($factura['estado_pago'] === 'completado'): ?>
                                <span class="badge bg-success fs-6"><i class="bi bi-check-circle me-1"></i>Completado</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark fs-6"><i class="bi bi-info-circle me-1"></i>Pendiente por Pagar</span>
                            <?php endif; ?>
                            <p style="color: var(--ctp-subtext1); margin-top: 10px; font-size: 0.9rem;">
                                <strong>Método de pago: </strong><?php echo ucfirst($factura['metodo_pago']); ?>
                            </p>
                        </div>
                    </div>

                    <!-- TABLA ARTICULOS -->
                    <div class="table-responsive mb-4">
                        <table class="table" style="color: var(--ctp-text); border-color: var(--ctp-surface1);">
                            <thead>
                                <tr style="background-color: var(--ctp-surface0);">
                                    <th style="border-top-left-radius: 8px; border-bottom: none;">Código</th>
                                    <th style="border-bottom: none;">Descripción</th>
                                    <th class="text-center" style="border-bottom: none;">Cant.</th>
                                    <th class="text-end" style="border-bottom: none;">Precio Unitario</th>
                                    <th class="text-end" style="border-top-right-radius: 8px; border-bottom: none;">Importe</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($items as $i): ?>
                                <tr>
                                    <td class="font-monospace text-muted align-middle"><?php echo htmlspecialchars($i['codigo'] ?: 'N/A'); ?></td>
                                    <td class="align-middle fw-semibold"><?php echo htmlspecialchars($i['producto_nombre']); ?></td>
                                    <td class="text-center align-middle"><?php echo $i['cantidad']; ?></td>
                                    <td class="text-end align-middle font-monospace">$<?php echo number_format($i['precio_unitario'], 2); ?></td>
                                    <td class="text-end align-middle font-monospace" style="color: var(--ctp-sapphire);">$<?php echo number_format($i['subtotal'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- TOTALES -->
                    <div class="row justify-content-end">
                        <div class="col-sm-5 col-md-4">
                            <table class="table table-borderless table-sm mb-0 rounded" style="background-color: var(--ctp-surface0); color: var(--ctp-text);">
                                <tbody>
                                    <tr>
                                        <td class="ps-3 pt-3">Subtotal</td>
                                        <td class="text-end pe-3 pt-3 font-monospace">$<?php echo number_format($factura['subtotal'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-3">IVA (16%)</td>
                                        <td class="text-end pe-3 font-monospace">$<?php echo number_format($factura['iva'], 2); ?></td>
                                    </tr>
                                    <tr style="border-top: 1px solid var(--ctp-surface2);">
                                        <td class="ps-3 pb-3 pt-2 font-weight-bold" style="color: var(--ctp-green);"><strong>TOTAL MXN</strong></td>
                                        <td class="text-end pe-3 pb-3 pt-2 font-monospace" style="color: var(--ctp-green); font-size: 1.2rem;"><strong>$<?php echo number_format($factura['total'], 2); ?></strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                
                <!-- FOOTER FACTURA -->
                <div class="p-4 text-center" style="background-color: var(--ctp-crust); border-top: 1px solid var(--ctp-surface0);">
                    <p style="color: var(--ctp-subtext0); margin: 0; font-size: 0.85rem;">
                        <strong>BELAMITECH S.A. de C.V.</strong> | contacto@belamitech.com | +52 (55) 1234-5678<br>
                        "Optimizados para el futuro"
                    </p>
                    <p style="color: var(--ctp-overlay0); font-size: 0.75rem; margin-top: 10px; margin-bottom: 0;">
                        Este documento es una representación impresa de un testamentillo de compra. 
                        Cualquier detalle sobre su garantía contactar al soporte técnico.
                    </p>
                </div>
            </div>
            
        </div>
    </div>
</main>

<style>
/* Estilos para impresión nativa desde el navegador */
@media print {
    /* Ocultar elementos irrelevantes para la factura física */
    body > nav, 
    body > footer, 
    .non-printable, 
    .theme-toggle, 
    .cart-sidebar,
    .cart-overlay {
        display: none !important;
    }

    body {
        background-color: #fff !important; /* forzar fondo blanco para ahorrar tinta */
        color: #000 !important;
    }

    /* Forzar que el modo claro se aplique para impresion limpia si esta en modo oscuro */
    :root {
        --ctp-text: #4c4f69 !important;
        --ctp-subtext1: #5c5f77 !important;
        --ctp-subtext0: #6c6f85 !important;
        --ctp-surface2: #acb0be !important;
        --ctp-surface1: #bcc0cc !important;
        --ctp-surface0: #ccd0da !important;
        --ctp-base: #eff1f5 !important;
        --ctp-mantle: #e6e9ef !important;
        --ctp-crust: #dce0e8 !important;
    }

    .factura-container {
        border: 1px solid #ccc !important;
        box-shadow: none !important;
        margin: 0 !important;
        padding: 0 !important;
        page-break-inside: avoid;
    }

    /* Asegurar que los backgrounds se impriman en Chrome/Edge */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>

<?php require_once '../../view/layout/footer.php'; ?>

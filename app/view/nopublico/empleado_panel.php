<?php
require_once '../../config/seciones.php';
requerirRol('empleado'); // Empleado o admin 
require_once '../../config/db.php';

// Identificar tipo de empleado
$stmt = $pdo->prepare("SELECT tipo_empleado FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$mi_tipo = $stmt->fetchColumn() ?? 'soporte';

// Obtener servicios
$stmt = $pdo->prepare("SELECT s.*, u.correo AS cliente_correo FROM servicios_contacto s JOIN usuarios u ON s.usuario_id = u.id ORDER BY s.fecha_contacto DESC");
$stmt->execute();
$servicios = $stmt->fetchAll();

// Obtener compras pendientes (ahora con facturas asociadas)
$compras_stmt = $pdo->query("SELECT c.*, u.correo, f.id AS factura_id 
                             FROM compras c 
                             JOIN usuarios u ON c.usuario_id = u.id 
                             LEFT JOIN facturas f ON f.compra_id = c.id 
                             ORDER BY c.fecha_compra DESC");
$compras = $compras_stmt->fetchAll();

require_once '../../view/layout/header.php';
require_once '../../view/layout/navbar.php';
?>

<main class="container py-5 min-vh-80">
    <h2 style="color: var(--ctp-teal);">Panel de Empleado <span class="fs-6 badge bg-info"><?php echo strtoupper($mi_tipo ?: 'GENERAL'); ?></span></h2>
    
    <?php if ($mi_tipo === 'almacen' || $_SESSION['rol'] === 'admin'): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card p-4 mb-4" style="background: var(--ctp-surface0); border-color: var(--ctp-surface1);">
                <h4 style="color: var(--ctp-green);">Gestión de Almacén</h4>
                <p style="color: var(--ctp-text);">Como empleado de almacén tienes acceso directo a la gestión de productos y proveedores.</p>
                <div class="d-flex gap-3">
                    <a href="/app/view/nopublico/crud_productos.php" class="btn btn-outline-success">Gestionar Productos</a>
                    <a href="/app/view/nopublico/crud_proveedores.php" class="btn btn-outline-info">Gestionar Proveedores</a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="mt-4">
        <h4 style="color: var(--ctp-peach);">Solicitudes de Servicio (Tickets)</h4>
        <div class="table-responsive">
            <table class="table table-hover" style="color: var(--ctp-text);">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Asunto</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($servicios as $srv): ?>
                    <tr>
                        <td>#<?php echo $srv['id']; ?></td>
                        <td><?php echo htmlspecialchars($srv['cliente_correo']); ?></td>
                        <td><?php echo htmlspecialchars($srv['asunto']); ?></td>
                        <td>
                            <?php
                            $badgeC = ['abierto'=>'bg-info', 'pago_pendiente'=>'bg-warning text-dark', 'en_proceso'=>'bg-primary', 'cerrado'=>'bg-success'];
                            $estadoV = $badgeC[$srv['estado']] ?? 'bg-secondary';
                            ?>
                            <span class="badge <?php echo $estadoV; ?>"><?php echo str_replace('_', ' ', strtoupper($srv['estado'])); ?></span>
                        </td>
                        <td><a href="/app/view/nopublico/ticket_gestionar.php?id=<?php echo $srv['id']; ?>" class="btn btn-sm btn-outline-info"><i class="bi bi-sliders me-1"></i>Gestionar</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        <h4 style="color: var(--ctp-green);">Vistazo General de Compras</h4>
        <div class="table-responsive">
            <table class="table table-hover" style="color: var(--ctp-text);">
                <thead>
                    <tr>
                        <th>Orden ID</th>
                        <th>Cliente</th>
                        <th>Total MXN</th>
                        <th>Tipo Envío</th>
                        <th>Met. Pago</th>
                        <th>Estado Pago</th>
                        <th>Facturar/PDF</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($compras as $com): ?>
                    <tr>
                        <td>#<?php echo $com['id']; ?></td>
                        <td><?php echo htmlspecialchars($com['correo']); ?></td>
                        <td>$<?php echo number_format($com['total'], 2); ?></td>
                        <td><?php echo htmlspecialchars($com['tipo_envio']); ?></td>
                        <td><?php echo htmlspecialchars($com['metodo_pago']); ?></td>
                        <td><span class="badge bg-<?php echo $com['estado_pago']=='completado'?'success':'warning';?>"><?php echo $com['estado_pago']; ?></span></td>
                        <td>
                            <?php if($com['factura_id']): ?>
                                <a href="/app/view/nopublico/factura_view.php?id=<?php echo $com['factura_id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-receipt me-1"></i>Ver Factura PDF</a>
                            <?php else: ?>
                                <span class="text-muted small">Sin factura</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once '../../view/layout/footer.php'; ?>

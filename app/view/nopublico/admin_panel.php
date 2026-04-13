<?php
require_once '../../config/seciones.php';
requerirRol('admin'); // solo admin
require_once '../../config/db.php';
require_once '../../config/seguridad.php';

$mensaje = "";

// Crear nuevo empleado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['crear_empleado'])) {
    $nombre = limpiarCadenas($_POST['nombre'] ?? '');
    $correo = limpiarCadenas($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';
    $tipo_empleado = $_POST['tipo_empleado'] ?? 'soporte';

    if (!empty($nombre) && !empty($correo) && !empty($password)) {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, correo, password_hash, rol, correo_confirmado, tipo_empleado) VALUES (?, ?, ?, 'empleado', 1, ?)");
            $stmt->execute([$nombre, $correo, $hash, $tipo_empleado]);
            $mensaje = "<div class='alert alert-success'>Empleado creado exitosamente.</div>";
        } catch(PDOException $e) {
            $mensaje = "<div class='alert alert-danger'>Error al crear empleado (el correo podría ya existir).</div>";
        }
    } else {
        $mensaje = "<div class='alert alert-warning'>Llena los campos requeridos.</div>";
    }
}

// Banear / Desbanear Usuario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['toggle_ban'])) {
    $id_usuario = (int)$_POST['id_usuario'];
    $nuevo_status = $_POST['nuevo_status'] === 'activo' ? 'activo' : 'baneado';
    
    // Evitar auto-ban
    if ($id_usuario !== $_SESSION['usuario_id']) {
        $stmt = $pdo->prepare("UPDATE usuarios SET status = ? WHERE id = ?");
        $stmt->execute([$nuevo_status, $id_usuario]);
        $mensaje = "<div class='alert alert-info'>Status de usuario actualizado.</div>";
    }
}

// Obtener métricas rápidas
$tot_usuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$tot_compras = $pdo->query("SELECT COUNT(*) FROM compras WHERE estado_pago = 'completado'")->fetchColumn();
$tot_servicios = $pdo->query("SELECT COUNT(*) FROM servicios_contacto")->fetchColumn();

// Ingresos Totales Brutos
$ingresos_totales = $pdo->query("SELECT COALESCE(SUM(total), 0) FROM compras WHERE estado_pago = 'completado'")->fetchColumn();

// Tickets Desglose
$tickets_abierto = $pdo->query("SELECT COUNT(*) FROM servicios_contacto WHERE estado = 'abierto'")->fetchColumn();
$tickets_proceso = $pdo->query("SELECT COUNT(*) FROM servicios_contacto WHERE estado = 'en_proceso' OR estado = 'pago_pendiente'")->fetchColumn();
$tickets_cerrado = $pdo->query("SELECT COUNT(*) FROM servicios_contacto WHERE estado = 'cerrado'")->fetchColumn();

// Top 5 Productos Vendidos
$top_productos = $pdo->query("SELECT p.nombre, SUM(ci.cantidad) as total_vendido 
                              FROM compra_items ci 
                              JOIN productos p ON ci.producto_id = p.id 
                              JOIN compras c ON ci.compra_id = c.id
                              WHERE c.estado_pago = 'completado'
                              GROUP BY p.id 
                              ORDER BY total_vendido DESC LIMIT 5")->fetchAll();

// Listar usuarios generales para baneo y empleados
$todos_usuarios = $pdo->query("SELECT id, nombre, correo, rol, status, tipo_empleado FROM usuarios ORDER BY id DESC LIMIT 50")->fetchAll();

require_once '../../view/layout/header.php';
require_once '../../view/layout/navbar.php';
?>

<!-- Importar Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main class="container py-5 min-vh-80">
    <h2 style="color: var(--ctp-red);">Resumen Ejecutivo (Panel de Administración)</h2>
    <?php echo $mensaje; ?>
    
    <div class="row mt-4 mb-4">
        <div class="col-md-3">
            <div class="card p-3 text-center mb-3 h-100 justify-content-center border-0 shadow-sm" style="background-color: var(--ctp-surface0); border-left: 4px solid var(--ctp-green) !important;">
                <p style="color: var(--ctp-subtext1); font-weight: 600; text-transform: uppercase; font-size: 0.85rem; margin-bottom: 5px;">Ingresos Brutos Totales</p>
                <h2 style="color: var(--ctp-green); font-weight: 800; font-family: monospace;">$<?php echo number_format($ingresos_totales, 2); ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 text-center mb-3 h-100 justify-content-center border-0 shadow-sm" style="background-color: var(--ctp-surface0); border-left: 4px solid var(--ctp-blue) !important;">
                <p style="color: var(--ctp-subtext1); font-weight: 600; text-transform: uppercase; font-size: 0.85rem; margin-bottom: 5px;">Ventas Completadas</p>
                <h2 style="color: var(--ctp-blue); font-weight: 800;"><?php echo $tot_compras; ?> <small style="font-size: 1rem; color: var(--ctp-overlay0);">órdenes</small></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 text-center mb-3 h-100 justify-content-center border-0 shadow-sm" style="background-color: var(--ctp-surface0); border-left: 4px solid var(--ctp-peach) !important;">
                <p style="color: var(--ctp-subtext1); font-weight: 600; text-transform: uppercase; font-size: 0.85rem; margin-bottom: 5px;">Tickets de Soporte</p>
                <h2 style="color: var(--ctp-peach); font-weight: 800;"><?php echo $tot_servicios; ?> <small style="font-size: 1rem; color: var(--ctp-overlay0);">peticiones</small></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 text-center mb-3 h-100 justify-content-center border-0 shadow-sm" style="background-color: var(--ctp-surface0); border-left: 4px solid var(--ctp-mauve) !important;">
                <p style="color: var(--ctp-subtext1); font-weight: 600; text-transform: uppercase; font-size: 0.85rem; margin-bottom: 5px;">Usuarios Registrados</p>
                <h2 style="color: var(--ctp-mauve); font-weight: 800;"><?php echo $tot_usuarios; ?> <small style="font-size: 1rem; color: var(--ctp-overlay0);">cuentas</small></h2>
            </div>
        </div>
    </div>

    <!-- Gráficas y Top Sellers -->
    <div class="row mt-4 mb-5 g-4">
        <!-- Gráfica de Tickets -->
        <div class="col-lg-4">
            <div class="card p-4 h-100 border-0 shadow-sm" style="background-color: var(--ctp-mantle);">
                <h6 style="color: var(--ctp-text); font-weight: 700; margin-bottom: 20px;"><i class="bi bi-pie-chart-fill me-2" style="color: var(--ctp-yellow);"></i>Desglose de Tickets</h6>
                <div style="height: 250px; width: 100%; display: flex; justify-content: center;">
                    <canvas id="ticketsChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Top Productos -->
        <div class="col-lg-8">
            <div class="card p-4 h-100 border-0 shadow-sm" style="background-color: var(--ctp-mantle);">
                <h6 style="color: var(--ctp-text); font-weight: 700; margin-bottom: 20px;"><i class="bi bi-trophy-fill me-2" style="color: var(--ctp-yellow);"></i>Top 5 Productos Más Vendidos</h6>
                
                <?php if(count($top_productos) > 0): ?>
                    <div style="height: 250px; width: 100%;">
                        <canvas id="salesChart"></canvas>
                    </div>
                <?php else: ?>
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <p style="color: var(--ctp-overlay1);">No hay suficientes ventas completadas para mostrar estadísticas.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Grafica Tickets
        var ctxTickets = document.getElementById('ticketsChart').getContext('2d');
        new Chart(ctxTickets, {
            type: 'doughnut',
            data: {
                labels: ['Abiertos', 'En Proceso/Pdte.', 'Cerrados'],
                datasets: [{
                    data: [<?php echo $tickets_abierto; ?>, <?php echo $tickets_proceso; ?>, <?php echo $tickets_cerrado; ?>],
                    backgroundColor: [
                        '#89b4fa', // blue for abiertos (newly received basically) or info
                        '#f9e2af', // yellow
                        '#a6e3a1'  // green
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#cdd6f4' } }
                }
            }
        });

        // Grafica Top Sellers
        <?php if(count($top_productos) > 0): ?>
            <?php 
                $labels = []; $data = [];
                foreach($top_productos as $tp) {
                    $labels[] = "'" . addslashes(mb_strimwidth($tp['nombre'], 0, 15, '...')) . "'";
                    $data[] = $tp['total_vendido'];
                }
            ?>
            var ctxSales = document.getElementById('salesChart').getContext('2d');
            new Chart(ctxSales, {
                type: 'bar',
                data: {
                    labels: [<?php echo implode(',', $labels); ?>],
                    datasets: [{
                        label: 'Unidades Vendidas',
                        data: [<?php echo implode(',', $data); ?>],
                        backgroundColor: '#cba6f7', // mauve
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#bac2de' } },
                        x: { grid: { display:-false }, ticks: { color: '#bac2de' } }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        <?php endif; ?>
    });
    </script>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card p-4" style="background: var(--ctp-surface0); border-color: var(--ctp-surface1);">
                <h4 style="color: var(--ctp-mauve);">Crear Empleado</h4>
                <form action="admin_panel.php" method="POST">
                    <input type="hidden" name="crear_empleado" value="1">
                    <div class="mb-3">
                        <label style="color: var(--ctp-text);">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label style="color: var(--ctp-text);">Correo</label>
                        <input type="email" name="correo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label style="color: var(--ctp-text);">Contraseña Temporal</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label style="color: var(--ctp-text);">Tipo de Empleado</label>
                        <select name="tipo_empleado" class="form-select">
                            <option value="almacen">Almacén (Productos y Proveedores)</option>
                            <option value="programador">Programador</option>
                            <option value="soporte">Soporte</option>
                            <option value="ciberseguridad">Ciberseguridad</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Registrar Empleado</button>
                </form>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card p-4" style="background: var(--ctp-surface0); border-color: var(--ctp-surface1);">
                <h4 style="color: var(--ctp-mauve);">Gestión de Usuarios (Baneo)</h4>
                <div class="table-responsive">
                    <table class="table" style="color: var(--ctp-text);">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Rol / Tipo</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($todos_usuarios as $usr): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($usr['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($usr['correo']); ?></td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo strtoupper($usr['rol']); ?></span>
                                        <?php if($usr['tipo_empleado']) echo "<span class='badge bg-info'>{$usr['tipo_empleado']}</span>"; ?>
                                    </td>
                                    <td>
                                        <?php if($usr['status'] == 'activo'): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Baneado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($usr['id'] !== $_SESSION['usuario_id']): ?>
                                            <form action="admin_panel.php" method="POST" style="display:inline;">
                                                <input type="hidden" name="toggle_ban" value="1">
                                                <input type="hidden" name="id_usuario" value="<?php echo $usr['id']; ?>">
                                                <input type="hidden" name="nuevo_status" value="<?php echo $usr['status'] === 'activo' ? 'baneado' : 'activo'; ?>">
                                                <button class="btn btn-sm <?php echo $usr['status'] === 'activo' ? 'btn-outline-danger' : 'btn-outline-success'; ?>">
                                                    <?php echo $usr['status'] === 'activo' ? 'Banear' : 'Desbanear'; ?>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../view/layout/footer.php'; ?>

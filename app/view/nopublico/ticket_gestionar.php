<?php
require_once '../../config/seciones.php';
requerirRol('empleado'); // Empleado o admin
require_once '../../config/db.php';
require_once '../../config/seguridad.php';

$ticket_id = $_GET['id'] ?? null;
if(!$ticket_id) { die("ID de ticket no proporcionado."); }

$usuario_id = $_SESSION['usuario_id'];
$mensaje_ui = '';

// Procesar Actualización de Empleado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actualizar_ticket'])) {
    $nuevo_estado = $_POST['estado'] ?? 'abierto';
    $precio_acordado = floatval($_POST['precio_acordado'] ?? 0);
    $notas_internas = limpiarCadenas($_POST['notas_empleado'] ?? '');
    $respuesta_cliente = limpiarCadenas($_POST['respuesta_empleado'] ?? '');

    try {
        $stmtU = $pdo->prepare("UPDATE servicios_contacto SET estado = ?, precio_acordado = ?, notas_empleado = ?, respuesta_empleado = ?, empleado_id = ? WHERE id = ?");
        $stmtU->execute([$nuevo_estado, $precio_acordado, $notas_internas, $respuesta_cliente, $usuario_id, $ticket_id]);
        $mensaje_ui = "<div class='alert alert-success mt-3'><i class="bi bi-check-circle me-2"></i>Ticket actualizado correctamente.</div>";
    } catch(Exception $e) {
        $mensaje_ui = "<div class='alert alert-danger mt-3'>Error al actualizar el ticket.</div>";
    }
}

// Obtener datos del ticket después de la posible actualización
$stmt = $pdo->prepare("SELECT s.*, u.nombre AS cliente_nombre, u.correo AS cliente_correo, e.nombre AS empleado_nombre 
                       FROM servicios_contacto s 
                       JOIN usuarios u ON s.usuario_id = u.id 
                       LEFT JOIN usuarios e ON s.empleado_id = e.id 
                       WHERE s.id = ?");
$stmt->execute([$ticket_id]);
$t = $stmt->fetch();

if(!$t) { die("Ticket no encontrado."); }

require_once '../../view/layout/header.php';
require_once '../../view/layout/navbar.php';
?>

<main class="container py-5 min-vh-80">
    <!-- Header Navegación -->
    <div class="d-flex align-items-center mb-4">
        <a href="/app/view/nopublico/empleado_panel.php" class="btn btn-outline-secondary me-3" style="border-radius: 50%; width: 44px; height: 44px; padding: 0; display: flex; align-items: center; justify-content: center;">
            <i class="bi bi-arrow-left" style="font-size: 1.2rem;"></i>
        </a>
        <div>
            <h3 style="color: var(--ctp-mauve); margin: 0;">Gestión de Ticket #<?php echo $t['id']; ?></h3>
            <span style="color: var(--ctp-overlay1); font-size: 0.9rem;">Asignado a: <?php echo $t['empleado_nombre'] ? htmlspecialchars($t['empleado_nombre']) : '<span style="color: var(--ctp-red);">Sin Asignar</span>'; ?></span>
        </div>
    </div>

    <?php echo $mensaje_ui; ?>

    <div class="row g-4 mt-2">
        <!-- Izquierda: Información del Cliente y Mensajes -->
        <div class="col-lg-7">
            <div class="card card-glass p-0 h-100 border-0 overflow-hidden">
                <div class="p-4" style="background-color: var(--ctp-mantle); border-bottom: 1px solid var(--ctp-surface1);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 style="color: var(--ctp-text); font-weight: 700; margin-bottom: 4px;"><?php echo htmlspecialchars($t['asunto']); ?></h5>
                            <p style="color: var(--ctp-subtext1); margin-bottom: 0;"><i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($t['cliente_nombre']); ?> (<?php echo htmlspecialchars($t['cliente_correo']); ?>)</p>
                        </div>
                        <span style="color: var(--ctp-overlay0); font-family: monospace; font-size: 0.85rem;"><?php echo date('d/m/Y H:i', strtotime($t['fecha_contacto'])); ?></span>
                    </div>
                </div>

                <div class="p-4">
                    <h6 style="color: var(--ctp-peach); font-weight: 600;"><i class="bi bi-chat-left-text me-2"></i>Mensaje(s) del Cliente:</h6>
                    <div class="p-3 rounded mb-4" style="background-color: var(--ctp-crust); border: 1px solid var(--ctp-surface0);">
                        <p style="color: var(--ctp-text); font-size: 0.95rem; margin-bottom: 0; white-space: pre-wrap; font-family: 'Inter', sans-serif;"><?php echo htmlspecialchars($t['mensaje_usuario']); ?></p>
                    </div>

                    <h6 style="color: var(--ctp-green); font-weight: 600;"><i class="bi bi-chat-right-text me-2"></i>Nuestra Última Respuesta:</h6>
                    <div class="p-3 rounded" style="background-color: rgba(166, 227, 161, 0.05); border: 1px dashed var(--ctp-green);">
                        <?php if(empty($t['respuesta_empleado'])): ?>
                            <p style="color: var(--ctp-overlay1); font-size: 0.9rem; font-style: italic; margin-bottom: 0;">No se ha enviado respuesta al cliente aún.</p>
                        <?php else: ?>
                            <p style="color: var(--ctp-text); font-size: 0.95rem; margin-bottom: 0; white-space: pre-wrap;"><?php echo htmlspecialchars($t['respuesta_empleado']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Derecha: Formulario de Control -->
        <div class="col-lg-5">
            <div class="card card-glass p-4 border-0 h-100 position-relative">
                <!-- Ribbon state -->
                <?php
                $estadosFmt = [
                    'abierto' => ['label' => 'Abierto', 'color' => 'bg-info'],
                    'pago_pendiente' => ['label' => 'Pago Pdte.', 'color' => 'bg-warning'],
                    'en_proceso' => ['label' => 'En Proceso', 'color' => 'bg-primary'],
                    'cerrado' => ['label' => 'Cerrado', 'color' => 'bg-success']
                ];
                $bgC = $estadosFmt[$t['estado']]['color'] ?? 'bg-secondary';
                $lblC = $estadosFmt[$t['estado']]['label'] ?? 'General';
                ?>
                <div class="position-absolute top-0 end-0 mt-3 me-3">
                    <span class="badge <?php echo $bgC; ?> fs-6"><?php echo $lblC; ?></span>
                </div>

                <h4 style="color: var(--ctp-sapphire);"><i class="bi bi-sliders me-2"></i>Control Interno</h4>
                <p style="color: var(--ctp-subtext0); font-size: 0.85rem; margin-bottom: 24px;">Modifica el estado, envía respuestas y cotiza el servicio.</p>

                <form action="ticket_gestionar.php?id=<?php echo $t['id']; ?>" method="POST">
                    <input type="hidden" name="actualizar_ticket" value="1">
                    
                    <div class="mb-3">
                        <label style="color: var(--ctp-text); font-weight: 500;">Estado del Ticket</label>
                        <select name="estado" class="form-select mt-1" style="background-color: var(--ctp-crust); color: var(--ctp-text); border-color: var(--ctp-surface1);">
                            <option value="abierto" <?php if($t['estado']=='abierto') echo 'selected'; ?>>Abierto (Revisión Inicial)</option>
                            <option value="pago_pendiente" <?php if($t['estado']=='pago_pendiente') echo 'selected'; ?>>Esperando Pago (Cotizado)</option>
                            <option value="en_proceso" <?php if($t['estado']=='en_proceso') echo 'selected'; ?>>En Proceso (Trabajando)</option>
                            <option value="cerrado" <?php if($t['estado']=='cerrado') echo 'selected'; ?>>Cerrado (Finalizado)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label style="color: var(--ctp-text); font-weight: 500;">Precio Acordado / Cotización ($ MXN)</label>
                        <div class="input-group mt-1 flex-nowrap">
                            <span class="input-group-text" style="background-color: var(--ctp-surface0); border-color: var(--ctp-surface1); color: var(--ctp-text);">$</span>
                            <input type="number" step="0.01" min="0" name="precio_acordado" class="form-control" value="<?php echo htmlspecialchars($t['precio_acordado'] ?? '0.00'); ?>" style="background-color: var(--ctp-crust); color: var(--ctp-text); border-color: var(--ctp-surface1);">
                        </div>
                        <small style="color: var(--ctp-overlay1); font-size: 0.75rem;">Si es soporte gratuito, deja en 0.</small>
                    </div>

                    <div class="mb-3">
                        <label style="color: var(--ctp-green); font-weight: 500;">Respuesta al Cliente (Visible)</label>
                        <textarea name="respuesta_empleado" class="form-control mt-1" rows="3" placeholder="Escribe la respuesta o cotización que verá el cliente..." style="background-color: var(--ctp-crust); color: var(--ctp-text); border-color: var(--ctp-green);"><?php echo htmlspecialchars($t['respuesta_empleado'] ?? ''); ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label style="color: var(--ctp-text); font-weight: 500;">Notas Internas (Solo Empleados)</label>
                        <textarea name="notas_empleado" class="form-control mt-1" rows="2" placeholder="Anotaciones técnicas privadas..." style="background-color: var(--ctp-surface0); color: var(--ctp-text); border-color: var(--ctp-surface1);"><?php echo htmlspecialchars($t['notas_empleado'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-lg" style="border-radius: 12px; font-weight: 600;">
                        <i class="bi bi-floppy2-fill me-2"></i>Guardar Cambios y Asignarme
                    </button>
                </form>

            </div>
        </div>
    </div>
</main>

<?php require_once '../../view/layout/footer.php'; ?>

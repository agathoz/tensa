<?php
require_once '../../config/seciones.php';
requerirLogin();
require_once '../../config/db.php';
require_once '../../config/seguridad.php';

$usuario_id = $_SESSION['usuario_id'];

// Procesar nuevo mensaje si se envía
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nuevo_mensaje'])) {
    $ticket_id = (int)$_POST['ticket_id'];
    $mensaje_adicional = limpiarCadenas($_POST['mensaje_adicional'] ?? '');
    
    // Verificar que el ticket pertenezca al usuario
    $stmtVerificar = $pdo->prepare("SELECT id, mensaje_usuario FROM servicios_contacto WHERE id = ? AND usuario_id = ?");
    $stmtVerificar->execute([$ticket_id, $usuario_id]);
    $ticketExistente = $stmtVerificar->fetch();

    if ($ticketExistente && !empty($mensaje_adicional)) {
        // Appending new message to the existing thread
        $nuevo_thread = $ticketExistente['mensaje_usuario'] . "\n\n--- Actualización del Cliente (" . date('d/m/Y H:i') . ") ---\n" . $mensaje_adicional;
        $stmtUpdate = $pdo->prepare("UPDATE servicios_contacto SET mensaje_usuario = ? WHERE id = ?");
        $stmtUpdate->execute([$nuevo_thread, $ticket_id]);
        $msg_flotante = "<div class='alert alert-success'>Mensaje enviado al equipo de soporte.</div>";
    }
}

// Obtener tickets del usuario
$stmt = $pdo->prepare("SELECT s.*, e.nombre AS nombre_empleado 
                       FROM servicios_contacto s 
                       LEFT JOIN usuarios e ON s.empleado_id = e.id 
                       WHERE s.usuario_id = ? ORDER BY s.fecha_actualizacion DESC");
$stmt->execute([$usuario_id]);
$tickets = $stmt->fetchAll();

require_once '../../view/layout/header.php';
require_once '../../view/layout/navbar.php';
?>

<main class="container py-5 min-vh-80">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color: var(--ctp-peach);"><i class="bi bi-ticket-detailed me-2"></i>Mis Tickets de Servicio</h2>
        <a href="/app/view/pages/servicios.php" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Nuevo Servicio</a>
    </div>

    <?php if(!empty($msg_flotante)) echo $msg_flotante; ?>

    <?php if(empty($tickets)): ?>
        <div class="card card-glass p-5 text-center">
            <i class="bi bi-inbox" style="font-size: 3rem; color: var(--ctp-overlay0);"></i>
            <h4 class="mt-3" style="color: var(--ctp-subtext1);">Aún no has solicitado ningún servicio.</h4>
            <p style="color: var(--ctp-overlay1);">Si necesitas mantenimiento, desarrollo o auditorías de ciberseguridad, puedes crear una solicitud.</p>
            <a href="/app/view/pages/servicios.php" class="btn btn-outline-primary mt-2">Ver Catálogo de Servicios</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach($tickets as $t): 
                $badgeColors = [
                    'abierto' => 'bg-info',
                    'pago_pendiente' => 'bg-warning text-dark',
                    'en_proceso' => 'bg-primary',
                    'cerrado' => 'bg-success'
                ];
                $color = $badgeColors[$t['estado']] ?? 'bg-secondary';
                $estadoFmt = str_replace('_', ' ', strtoupper($t['estado']));
            ?>
            <div class="col-md-6 mb-3">
                <div class="card card-glass h-100 p-0 overflow-hidden" style="border: 1px solid var(--ctp-surface1);">
                    <div class="p-3 d-flex justify-content-between align-items-center" style="background-color: var(--ctp-surface0); border-bottom: 1px solid var(--ctp-surface1);">
                        <div>
                            <span class="badge <?php echo $color; ?> me-2"><?php echo $estadoFmt; ?></span>
                            <span style="color: var(--ctp-subtext0); font-size: 0.85rem;">Actualizado: <?php echo date('d/m/Y H:i', strtotime($t['fecha_actualizacion'])); ?></span>
                        </div>
                        <span style="color: var(--ctp-overlay1); font-family: monospace;">#<?php echo $t['id']; ?></span>
                    </div>
                    
                    <div class="p-4">
                        <h5 style="color: var(--ctp-text); font-weight: 700;"><?php echo htmlspecialchars($t['asunto']); ?></h5>
                        
                        <div class="mt-3 p-3 rounded" style="background-color: var(--ctp-crust); border: 1px solid var(--ctp-surface0);">
                            <small style="color: var(--ctp-peach); font-weight: 600;">Mi Solicitud:</small>
                            <p style="color: var(--ctp-subtext1); font-size: 0.9rem; margin-bottom: 0; white-space: pre-wrap;"><?php echo htmlspecialchars($t['mensaje_usuario']); ?></p>
                        </div>

                        <?php if(!empty($t['respuesta_empleado'])): ?>
                            <div class="mt-3 p-3 rounded position-relative" style="background-color: rgba(166, 227, 161, 0.1); border: 1px solid var(--ctp-green);">
                                <small style="color: var(--ctp-green); font-weight: 600;">
                                    Respuesta de <?php echo htmlspecialchars($t['nombre_empleado'] ?: 'Soporte BELAMITECH'); ?>:
                                </small>
                                <p style="color: var(--ctp-text); font-size: 0.9rem; margin-bottom: 0; white-space: pre-wrap;"><?php echo htmlspecialchars($t['respuesta_empleado']); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if($t['estado'] === 'pago_pendiente' && $t['precio_acordado'] > 0): ?>
                            <div class="mt-4 p-3 text-center rounded" style="background-color: rgba(250, 179, 135, 0.15); border: 1px dashed var(--ctp-peach);">
                                <h6 style="color: var(--ctp-peach); margin-bottom: 5px;"><i class="bi bi-wallet2 me-2"></i>Cotización Aprobada</h6>
                                <h3 style="color: var(--ctp-text); font-weight: 700; margin: 0;">$<?php echo number_format($t['precio_acordado'], 2); ?> <span style="font-size: 1rem; color: var(--ctp-subtext0);">MXN</span></h3>
                                <button class="btn btn-warning mt-3 w-100" style="font-weight: 600;">Proceder al Pago en Línea (Próximamente)</button>
                            </div>
                        <?php endif; ?>

                        <?php if($t['estado'] !== 'cerrado'): ?>
                            <!-- Formulario para responder al hilo -->
                            <div class="mt-4 border-top pt-3" style="border-color: var(--ctp-surface1) !important;">
                                <a data-bs-toggle="collapse" href="#replyFor<?php echo $t['id']; ?>" role="button" aria-expanded="false" style="color: var(--ctp-blue); font-size: 0.9rem; text-decoration: none;">
                                    <i class="bi bi-reply-fill"></i> Añadir un comentario o información adicional
                                </a>
                                <div class="collapse mt-2" id="replyFor<?php echo $t['id']; ?>">
                                    <form action="mis_tickets.php" method="POST">
                                        <input type="hidden" name="nuevo_mensaje" value="1">
                                        <input type="hidden" name="ticket_id" value="<?php echo $t['id']; ?>">
                                        <textarea class="form-control mb-2" name="mensaje_adicional" rows="2" required placeholder="Escribe aquí..." style="background-color: var(--ctp-crust); color: var(--ctp-text); border-color: var(--ctp-surface1);"></textarea>
                                        <button type="submit" class="btn btn-sm btn-primary">Enviar Comentario</button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php require_once '../../view/layout/footer.php'; ?>

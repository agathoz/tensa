<?php
require_once '../../config/seciones.php';
require_once '../../config/db.php';

// Si el usuario manda un formulario de servicio
$mensaje = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && estaLogueado()) {
    $asunto = $_POST['asunto'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    
    if(!empty($asunto) && !empty($descripcion)){
        $stmt = $pdo->prepare("INSERT INTO servicios_contacto (usuario_id, asunto, mensaje_usuario) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['usuario_id'], $asunto, $descripcion]);
        $mensaje = "<div class='alert alert-success mt-3'>Solicitud de servicio enviada. Un empleado de BELAMITECH revisará tu solicitud y se contactará para acordar el pago en tu Panel de Servicios.</div>";
    }
}

// Obtener empleados para el carrusel
$stmtEmp = $pdo->prepare("SELECT nombre, correo, tipo_empleado, foto_perfil FROM usuarios WHERE rol = 'empleado' AND status = 'activo'");
$stmtEmp->execute();
$empleados = $stmtEmp->fetchAll();

require_once '../../view/layout/header.php';
require_once '../../view/layout/navbar.php';
?>

<main class="container py-5 min-vh-80">
    <h2 style="color: var(--ctp-mauve);" class="text-center mb-2">
        <i class="bi bi-gear-wide-connected me-2"></i>Nuestros Servicios Profesionales
    </h2>
    <p class="text-center mb-5" style="color: var(--ctp-subtext0); max-width: 600px; margin: 0 auto;">
        Soluciones tecnológicas integrales diseñadas para potenciar tu negocio con la más alta calidad y seguridad.
    </p>
    
    <!-- Services Grid -->
    <div class="row g-4">
        <!-- Mantenimiento de Equipo -->
        <div class="col-md-6 animate-in">
            <div class="card card-glass service-card accent-sapphire h-100">
                <div class="service-icon bg-sapphire">
                    <i class="bi bi-tools"></i>
                </div>
                <h3 style="color: var(--ctp-sapphire); font-size: 1.3rem; font-weight: 700;">Mantenimiento de Equipo</h3>
                <p style="color: var(--ctp-subtext1); font-size: 0.92rem;">
                    Nuestros técnicos están altamente capacitados para diagnosticar, optimizar y reparar equipos informáticos empresariales y personales asegurando el más alto rendimiento y seguridad.
                </p>
                <ul style="color: var(--ctp-subtext0); font-size: 0.88rem; padding-left: 1.2rem;">
                    <li>Limpieza profunda y sustitución de pasta térmica</li>
                    <li>Optimización de sistemas operativos</li>
                    <li>Recuperación de datos y manejo de redes</li>
                    <li>Diagnóstico avanzado de hardware</li>
                </ul>
                <div class="mt-auto pt-2">
                    <span class="tool-badge">Diagnóstico HW</span>
                    <span class="tool-badge">Redes LAN/WAN</span>
                    <span class="tool-badge">Active Directory</span>
                </div>
            </div>
        </div>

        <!-- Desarrollo de Software -->
        <div class="col-md-6 animate-in">
            <div class="card card-glass service-card accent-peach h-100">
                <div class="service-icon bg-peach">
                    <i class="bi bi-code-slash"></i>
                </div>
                <h3 style="color: var(--ctp-peach); font-size: 1.3rem; font-weight: 700;">Desarrollo de Software a Medida</h3>
                <p style="color: var(--ctp-subtext1); font-size: 0.92rem;">
                    Equipo apasionado y especializado en desarrollo de aplicaciones, con un enfoque integral que combina diseño, arquitectura tecnológica, calidad y evolución del producto.
                </p>
                <ul style="color: var(--ctp-subtext0); font-size: 0.88rem; padding-left: 1.2rem;">
                    <li>Desarrollo web y aplicaciones móviles</li>
                    <li>Diseño UX/UI centrado en el usuario</li>
                    <li>Evolución y mantenimiento del producto</li>
                    <li>Aseguramiento de calidad (QA)</li>
                </ul>
                <div class="mt-auto pt-2">
                    <span class="tool-badge">PHP</span>
                    <span class="tool-badge">Python</span>
                    <span class="tool-badge">JavaScript</span>
                    <span class="tool-badge">React</span>
                    <span class="tool-badge">MySQL</span>
                </div>
            </div>
        </div>

        <!-- Ciberseguridad -->
        <div class="col-md-6 animate-in">
            <div class="card card-glass service-card accent-red h-100">
                <div class="service-icon bg-red">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <h3 style="color: var(--ctp-red); font-size: 1.3rem; font-weight: 700;">Ciberseguridad</h3>
                <p style="color: var(--ctp-subtext1); font-size: 0.92rem;">
                    Protegemos tu infraestructura digital contra amenazas modernas. Nuestro equipo de seguridad ofensiva y defensiva identifica vulnerabilidades antes de que los atacantes lo hagan.
                </p>
                <ul style="color: var(--ctp-subtext0); font-size: 0.88rem; padding-left: 1.2rem;">
                    <li>Pentesting y auditorías de seguridad</li>
                    <li>Análisis de vulnerabilidades y remediación</li>
                    <li>Hardening de servidores y redes</li>
                    <li>Respuesta a incidentes y forense digital</li>
                    <li>Capacitación en seguridad para equipos</li>
                </ul>
                <div class="mt-auto pt-2">
                    <span class="tool-badge">Burp Suite</span>
                    <span class="tool-badge">Nmap</span>
                    <span class="tool-badge">Metasploit</span>
                    <span class="tool-badge">Wireshark</span>
                    <span class="tool-badge">OWASP ZAP</span>
                    <span class="tool-badge">Kali Linux</span>
                    <span class="tool-badge">Nessus</span>
                </div>
            </div>
        </div>

        <!-- Venta de Equipos Tech -->
        <div class="col-md-6 animate-in">
            <div class="card card-glass service-card accent-green h-100">
                <div class="service-icon bg-green">
                    <i class="bi bi-pc-display"></i>
                </div>
                <h3 style="color: var(--ctp-green); font-size: 1.3rem; font-weight: 700;">Venta de Equipos Tech</h3>
                <p style="color: var(--ctp-subtext1); font-size: 0.92rem;">
                    Ofrecemos equipos de cómputo de alto rendimiento para empresas y profesionales. Desde workstations especializadas hasta periféricos, con garantía y soporte post-venta.
                </p>
                <ul style="color: var(--ctp-subtext0); font-size: 0.88rem; padding-left: 1.2rem;">
                    <li>Laptops y workstations empresariales</li>
                    <li>Servidores y almacenamiento NAS</li>
                    <li>Periféricos y accesorios profesionales</li>
                    <li>Componentes y ensamblaje a medida</li>
                    <li>Garantía extendida y soporte técnico</li>
                </ul>
                <div class="mt-auto pt-2">
                    <span class="tool-badge">Lenovo</span>
                    <span class="tool-badge">HP</span>
                    <span class="tool-badge">Dell</span>
                    <span class="tool-badge">ASUS</span>
                    <span class="tool-badge">Intel</span>
                    <span class="tool-badge">AMD</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Cómo Opera Nuestro Equipo -->
    <div class="row mt-5">
        <div class="col-12 animate-in">
            <div class="card card-glass ops-section">
                <h3 class="mb-1" style="color: var(--ctp-mauve);"><i class="bi bi-diagram-3 me-2"></i>Cómo Opera Nuestro Equipo</h3>
                <p style="color: var(--ctp-subtext0); font-size: 0.92rem;" class="mb-4">
                    Utilizamos metodologías ágiles y herramientas profesionales para garantizar entregas de calidad en cada proyecto.
                </p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="ops-area">
                            <h6 style="color: var(--ctp-sapphire); font-weight: 700; margin-bottom: 6px;">
                                <i class="bi bi-kanban me-1"></i> Gestión de Proyectos
                            </h6>
                            <p style="color: var(--ctp-subtext0); font-size: 0.85rem; margin: 0;">
                                Metodologías Scrum/Kanban con sprints de 2 semanas. Seguimiento en tiempo real y reportes semanales de avance al cliente.
                            </p>
                            <div class="mt-2">
                                <span class="tool-badge">Jira</span>
                                <span class="tool-badge">Trello</span>
                                <span class="tool-badge">Notion</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="ops-area">
                            <h6 style="color: var(--ctp-blue); font-weight: 700; margin-bottom: 6px;">
                                <i class="bi bi-git me-1"></i> Control de Versiones & CI/CD
                            </h6>
                            <p style="color: var(--ctp-subtext0); font-size: 0.85rem; margin: 0;">
                                Git flow profesional con code review obligatorio. Pipelines de integración y despliegue continuo automatizados.
                            </p>
                            <div class="mt-2">
                                <span class="tool-badge">Git</span>
                                <span class="tool-badge">GitHub</span>
                                <span class="tool-badge">Docker</span>
                                <span class="tool-badge">CI/CD</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="ops-area">
                            <h6 style="color: var(--ctp-green); font-weight: 700; margin-bottom: 6px;">
                                <i class="bi bi-terminal me-1"></i> Entornos de Desarrollo
                            </h6>
                            <p style="color: var(--ctp-subtext0); font-size: 0.85rem; margin: 0;">
                                Entornos aislados con Docker, ambientes de staging idénticos a producción. Testing automatizado antes de cada release.
                            </p>
                            <div class="mt-2">
                                <span class="tool-badge">VS Code</span>
                                <span class="tool-badge">NeoVim</span>
                                <span class="tool-badge">Docker Compose</span>
                                <span class="tool-badge">Linux</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="ops-area">
                            <h6 style="color: var(--ctp-peach); font-weight: 700; margin-bottom: 6px;">
                                <i class="bi bi-chat-dots me-1"></i> Comunicación & Colaboración
                            </h6>
                            <p style="color: var(--ctp-subtext0); font-size: 0.85rem; margin: 0;">
                                Comunicación transparente con canales dedicados por proyecto. Reuniones de sincronización diarias y demos al cliente.
                            </p>
                            <div class="mt-2">
                                <span class="tool-badge">Slack</span>
                                <span class="tool-badge">Discord</span>
                                <span class="tool-badge">Google Meet</span>
                                <span class="tool-badge">Figma</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Carrusel de Empleados -->
    <div class="row mt-5">
        <div class="col-12 animate-in">
            <h3 class="mb-4 text-center" style="color: var(--ctp-blue);"><i class="bi bi-people-fill me-2"></i>Nuestro Equipo</h3>
            
            <div id="empleadosCarousel" class="carousel slide card card-glass p-4" data-bs-ride="carousel" data-bs-interval="4000">
                <div class="carousel-inner">
                    <?php 
                    $active = true;
                    foreach ($empleados as $emp): 
                        $foto = ($emp['foto_perfil'] && $emp['foto_perfil'] != 'default.png') ? '/app/assets/uploads/' . $emp['foto_perfil'] : '/assets/img/computer_sagyouin_woman.png';
                        $tipoLabels = [
                            'programador' => 'Programador',
                            'soporte' => 'Soporte Técnico',
                            'almacen' => 'Almacén',
                            'ciberseguridad' => 'Ciberseguridad'
                        ];
                        $tipoLabel = $tipoLabels[$emp['tipo_empleado']] ?? ucfirst($emp['tipo_empleado']);
                    ?>
                        <div class="carousel-item <?php echo $active ? 'active' : ''; ?> text-center">
                            <img src="<?php echo htmlspecialchars($foto); ?>" class="d-block mx-auto rounded-circle mb-3 border border-3" style="height: 130px; width: 130px; object-fit: cover; border-color: var(--ctp-blue) !important;" alt="Empleado">
                            <h5 style="color: var(--ctp-text);"><?php echo htmlspecialchars($emp['nombre']); ?></h5>
                            <p style="color: var(--ctp-subtext1);">
                                <span class="tool-badge"><?php echo htmlspecialchars($tipoLabel); ?></span>
                            </p>
                            <a href="/app/view/pages/directorio.php" class="btn btn-sm btn-outline-info mt-1" style="border-radius: 10px;">
                                <i class="bi bi-person-lines-fill me-1"></i>Ver Directorio
                            </a>
                        </div>
                    <?php $active = false; endforeach; ?>

                    <?php if(empty($empleados)): ?>
                        <div class="carousel-item active text-center py-4">
                            <i class="bi bi-people" style="font-size: 2.5rem; color: var(--ctp-overlay0);"></i>
                            <p class="mt-2" style="color: var(--ctp-overlay1);">No hay empleados registrados por el momento.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if(count($empleados) > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#empleadosCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true" style="filter: invert(1);"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#empleadosCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true" style="filter: invert(1);"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Formulario de Contacto -->
    <div class="row mt-5">
        <div class="col-lg-8 mx-auto animate-in">
            <div class="card card-glass p-4">
                <h4 style="color: var(--ctp-green);"><i class="bi bi-envelope-paper me-2"></i>Solicitar un Servicio</h4>
                <?php if(estaLogueado()): ?>
                    <?php echo $mensaje; ?>
                    <form action="servicios.php" method="POST" class="mt-3">
                        <div class="mb-3">
                            <label style="color: var(--ctp-subtext1);">Asunto (Qué necesitas)</label>
                            <input type="text" name="asunto" class="form-control" required placeholder="Ej: Auditoría de seguridad web">
                        </div>
                        <div class="mb-3">
                            <label style="color: var(--ctp-subtext1);">Descripción detallada</label>
                            <textarea name="descripcion" class="form-control" rows="4" required placeholder="Describe tu necesidad con el mayor detalle posible..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i>Enviar Solicitud
                        </button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-info mt-3" style="background-color: var(--ctp-surface1); border:none; color: var(--ctp-text); border-radius: 12px;">
                        <i class="bi bi-info-circle me-2"></i>Por favor, <a href="login.php" style="color: var(--ctp-blue);">inicia sesión</a> para enviar una solicitud de servicio.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../view/layout/footer.php'; ?>

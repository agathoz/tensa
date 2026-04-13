<?php
require_once 'app/config/seciones.php';
require_once 'app/config/db.php';

require_once 'app/view/layout/header.php';
require_once 'app/view/layout/navbar.php';
?>

<main class="container py-5 min-vh-80">
    <!-- Hero -->
    <div class="row align-items-center mb-5">
        <div class="col-lg-6 mb-4 animate-in">
            <h1 class="display-4 fw-bold" style="color: var(--ctp-mauve);">Soluciones tecnológicas</h1>
            <p class="lead mt-3" style="color: var(--ctp-subtext1);">
                En BELAMITECH nos enfocamos en ofrecer soluciones tecnológicas limpias, seguras y altamente optimizadas.
            </p>
            <div class="d-flex gap-3 mt-4">
                <a href="/app/view/pages/servicios.php" class="btn btn-primary btn-lg"><i class="bi bi-gear me-1"></i>Explorar Servicios</a>
                <a href="/app/view/pages/productos.php" class="btn btn-outline-light btn-lg" style="border-color: var(--ctp-overlay0); color: var(--ctp-text);"><i class="bi bi-box-seam me-1"></i>Ver Productos</a>
            </div>
        </div>
        <div class="col-lg-6 animate-in">
            <div class="card card-glass p-4 text-center">
                <img src="/assets/img/logo.png" alt="Belamitech Logo" class="img-fluid mb-3 mx-auto" style="max-height: 150px; object-fit: contain;">
                <h3 style="color: var(--ctp-green);">Optimizados para el futuro</h3>
                <p style="color: var(--ctp-subtext0);">
                    Nuestra metodología minimalista garantiza que obtengas solo lo que necesitas, al instante y bajo los máximos estándares de seguridad.
                </p>
            </div>
        </div>
    </div>

    <!-- Historia -->
    <div class="row g-4 mb-4">
        <div class="col-12 animate-in">
            <div class="card card-glass info-box">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="info-box-icon" style="background: rgba(245, 169, 127, 0.15); color: var(--ctp-peach);">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h3 style="color: var(--ctp-peach); margin: 0; font-weight: 700;">Nuestra Historia</h3>
                </div>
                <p style="color: var(--ctp-subtext1); line-height: 1.7;">
                    BELAMITECH  fue fundada el <strong style="color: var(--ctp-text);">26 de julio de 2019</strong> con la visión de ofrecer soluciones tecnológicas innovadoras que transformen los procesos empresariales. Desde su inicio, la empresa ha estado comprometida con la creación de soluciones personalizadas que optimicen el funcionamiento de las organizaciones a través de la tecnología.
                </p>
                <p style="color: var(--ctp-subtext1); line-height: 1.7;">
                    Hoy, somos una consultoría tecnológica enfocada en ofrecer soluciones minimalistas, de alto rendimiento y centradas en la seguridad integral. Nuestro equipo está conformado por expertos en optimización y arquitectura de software, trabajando con tecnologías precisas y modernas sin depender de frameworks innecesariamente pesados.
                </p>
            </div>
        </div>
    </div>

    <!-- Misión, Visión, Objetivo -->
    <div class="row g-4 mb-4">
        <div class="col-md-4 animate-in">
            <div class="card card-glass info-box">
                <div class="info-box-icon" style="background: rgba(138, 173, 244, 0.15); color: var(--ctp-blue);">
                    <i class="bi bi-bullseye"></i>
                </div>
                <h4 style="color: var(--ctp-blue); font-weight: 700;">Misión</h4>
                <p style="color: var(--ctp-subtext1); font-size: 0.92rem; line-height: 1.65;">
                    Ofrecer soluciones tecnológicas innovadoras y personalizadas que optimizan los procesos de nuestros clientes. Diseñamos software a la medida que impulsa la transformación digital y mejora la competitividad.
                </p>
            </div>
        </div>
        <div class="col-md-4 animate-in">
            <div class="card card-glass info-box">
                <div class="info-box-icon" style="background: rgba(198, 160, 246, 0.15); color: var(--ctp-mauve);">
                    <i class="bi bi-eye"></i>
                </div>
                <h4 style="color: var(--ctp-mauve); font-weight: 700;">Visión</h4>
                <p style="color: var(--ctp-subtext1); font-size: 0.92rem; line-height: 1.65;">
                    Ser el socio estratégico para empresas que buscan soluciones tecnológicas de vanguardia, creando software innovador que potencie su crecimiento y optimice sus procesos.
                </p>
            </div>
        </div>
        <div class="col-md-4 animate-in">
            <div class="card card-glass info-box">
                <div class="info-box-icon" style="background: rgba(166, 218, 149, 0.15); color: var(--ctp-green);">
                    <i class="bi bi-rocket-takeoff"></i>
                </div>
                <h4 style="color: var(--ctp-green); font-weight: 700;">Objetivo</h4>
                <p style="color: var(--ctp-subtext1); font-size: 0.92rem; line-height: 1.65;">
                    Brindar herramientas tecnológicas avanzadas que mejoren la competitividad y eficiencia empresarial mediante software a medida, enfocado en la transformación digital.
                </p>
            </div>
        </div>
    </div>

    <!-- Equipo Especializado -->
    <div class="row g-4 mb-4">
        <div class="col-md-8 animate-in">
            <div class="card card-glass info-box">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="info-box-icon" style="background: rgba(139, 213, 202, 0.15); color: var(--ctp-teal);">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h4 style="color: var(--ctp-teal); margin: 0; font-weight: 700;">Nuestro Equipo Especializado</h4>
                </div>
                <p style="color: var(--ctp-subtext1); font-size: 0.92rem;">
                    Nuestro equipo se conforma por áreas específicas para brindarte el mejor servicio posible:
                </p>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="ops-area">
                            <h6 style="color: var(--ctp-green); font-weight: 700; margin-bottom: 4px;">
                                <i class="bi bi-box-seam me-1"></i> Almacén
                            </h6>
                            <p style="color: var(--ctp-subtext0); font-size: 0.83rem; margin: 0;">
                                Gestionan el inventario, proveedores y aseguran que los envíos de hardware y productos lleguen a tiempo.
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="ops-area">
                            <h6 style="color: var(--ctp-mauve); font-weight: 700; margin-bottom: 4px;">
                                <i class="bi bi-code-slash me-1"></i> Programador
                            </h6>
                            <p style="color: var(--ctp-subtext0); font-size: 0.83rem; margin: 0;">
                                Desarrollan soluciones a medida, desde páginas web minimalistas hasta infraestructuras backend masivas.
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="ops-area">
                            <h6 style="color: var(--ctp-peach); font-weight: 700; margin-bottom: 4px;">
                                <i class="bi bi-headset me-1"></i> Soporte
                            </h6>
                            <p style="color: var(--ctp-subtext0); font-size: 0.83rem; margin: 0;">
                                Proporcionan asistencia continua, capacitación y resuelven incidencias tecnológicas presencial y en línea.
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="ops-area">
                            <h6 style="color: var(--ctp-red); font-weight: 700; margin-bottom: 4px;">
                                <i class="bi bi-shield-check me-1"></i> Ciberseguridad
                            </h6>
                            <p style="color: var(--ctp-subtext0); font-size: 0.83rem; margin: 0;">
                                Auditorías de penetración, análisis de vulnerabilidades y hardening de servidores para proteger tus datos.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 animate-in">
            <div class="card card-glass info-box text-center d-flex align-items-center justify-content-center">
                <img src="/assets/img/logo.png" alt="BELAMITECH Logo" class="img-fluid mb-3" style="max-height: 160px; object-fit: contain;">
                <h5 style="color: var(--ctp-mauve); font-weight: 700;">BELAMITECH</h5>
                <p style="color: var(--ctp-subtext0); font-size: 0.85rem;">Consultoría Tecnológica</p>
                <p style="color: var(--ctp-overlay1); font-size: 0.8rem;">Fundada en 2019</p>
            </div>
        </div>
    </div>

    <!-- Contacto -->
    <div class="row g-4">
        <div class="col-12 animate-in">
            <div class="card card-glass info-box">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="info-box-icon" style="background: rgba(183, 189, 248, 0.15); color: var(--ctp-lavender);">
                        <i class="bi bi-telephone"></i>
                    </div>
                    <h4 style="color: var(--ctp-lavender); margin: 0; font-weight: 700;">Contacto</h4>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="contact-item">
                            <i class="bi bi-envelope-fill" style="color: var(--ctp-blue);"></i>
                            <div>
                                <small style="color: var(--ctp-overlay1);">Correo electrónico</small><br>
                                <span style="color: var(--ctp-text);">contacto@belamitech.com</span>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="bi bi-telephone-fill" style="color: var(--ctp-green);"></i>
                            <div>
                                <small style="color: var(--ctp-overlay1);">Teléfono</small><br>
                                <span style="color: var(--ctp-text);">+52 (55) 1234-5678</span>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="bi bi-geo-alt-fill" style="color: var(--ctp-red);"></i>
                            <div>
                                <small style="color: var(--ctp-overlay1);">Ubicación</small><br>
                                <span style="color: var(--ctp-text);">Ciudad de México, México</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="contact-item">
                            <i class="bi bi-clock-fill" style="color: var(--ctp-peach);"></i>
                            <div>
                                <small style="color: var(--ctp-overlay1);">Horario de atención</small><br>
                                <span style="color: var(--ctp-text);">Lunes a Viernes: 9:00 AM - 6:00 PM</span>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="bi bi-chat-dots-fill" style="color: var(--ctp-mauve);"></i>
                            <div>
                                <small style="color: var(--ctp-overlay1);">Soporte en línea</small><br>
                                <span style="color: var(--ctp-text);">Disponible 24/7 para clientes activos</span>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="bi bi-globe2" style="color: var(--ctp-sapphire);"></i>
                            <div>
                                <small style="color: var(--ctp-overlay1);">Redes sociales</small><br>
                                <div class="d-flex gap-2 mt-1">
                                    <a href="#" class="social-link github" title="GitHub"><i class="bi bi-github"></i></a>
                                    <a href="#" class="social-link linkedin" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                                    <a href="#" class="social-link twitter" title="X / Twitter"><i class="bi bi-twitter-x"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'app/view/layout/footer.php'; ?>

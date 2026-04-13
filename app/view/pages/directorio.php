<?php
require_once '../../config/seciones.php';
require_once '../../config/db.php';

// Check if social media columns exist
$hasSocialCols = false;
try {
    $checkCol = $pdo->query("SHOW COLUMNS FROM usuarios LIKE 'github_url'");
    $hasSocialCols = $checkCol->rowCount() > 0;
} catch (Exception $e) {
    $hasSocialCols = false;
}

// Build query
$selectCols = "id, nombre, correo, rol, tipo_empleado, descripcion, region, foto_perfil, fecha_creacion";
if ($hasSocialCols) {
    $selectCols .= ", github_url, linkedin_url, twitter_url, website_url";
}

$stmt = $pdo->prepare("SELECT $selectCols FROM usuarios WHERE status = 'activo' ORDER BY rol ASC, nombre ASC");
$stmt->execute();
$usuarios = $stmt->fetchAll();

// Categorize
$admins = [];
$empleados = [];
$users = [];

foreach ($usuarios as $u) {
    if ($u['rol'] === 'admin') {
        $admins[] = $u;
    } elseif ($u['rol'] === 'empleado') {
        $empleados[] = $u;
    } else {
        $users[] = $u;
    }
}

// Employee sub-group config
$tipoConfig = [
    'programador' => ['label' => 'Programadores', 'icon' => 'bi-code-slash', 'color' => 'var(--ctp-mauve)'],
    'soporte' => ['label' => 'Soporte', 'icon' => 'bi-headset', 'color' => 'var(--ctp-peach)'],
    'almacen' => ['label' => 'Almacén', 'icon' => 'bi-box-seam', 'color' => 'var(--ctp-green)'],
    'ciberseguridad' => ['label' => 'Ciberseguridad', 'icon' => 'bi-shield-lock', 'color' => 'var(--ctp-red)']
];

require_once '../../view/layout/header.php';
require_once '../../view/layout/navbar.php';
?>

<main class="container py-5 min-vh-80">
    <h2 class="text-center mb-2" style="color: var(--ctp-blue);">
        <i class="bi bi-people-fill me-2"></i>Directorio de BELAMITECH
    </h2>
    <p class="text-center mb-4" style="color: var(--ctp-subtext0); max-width: 600px; margin: 0 auto;">
        Conoce al equipo y la comunidad. Haz clic en cualquier perfil para ver información detallada.
    </p>

    <!-- Team Description -->
    <div class="row mb-5">
        <div class="col-lg-10 mx-auto animate-in">
            <div class="card card-glass p-4 text-center">
                <div class="d-flex justify-content-center align-items-center gap-3 mb-3">
                    <img src="/assets/img/logo.png" alt="Logo" style="height: 48px; width: 48px; object-fit: contain; border-radius: 10px;">
                    <h4 style="color: var(--ctp-mauve); margin: 0; font-weight: 700;">Equipo BELAMITECH</h4>
                </div>
                <p style="color: var(--ctp-subtext1); max-width: 700px; margin: 0 auto; line-height: 1.7;">
                    Somos un equipo multidisciplinario de profesionales apasionados por la tecnología. 
                    Desde administradores de sistemas hasta desarrolladores, especialistas en ciberseguridad y soporte técnico, 
                    cada miembro aporta experiencia única para crear soluciones integrales. 
                    Trabajamos bajo metodologías ágiles con un enfoque en seguridad, rendimiento y satisfacción del cliente.
                </p>
            </div>
        </div>
    </div>

    <!-- 3-Column Directory Layout -->
    <div class="row g-4">

        <!-- Column 1: Administradores -->
        <div class="col-lg-4 animate-in">
            <div class="card card-glass p-3 h-100">
                <!-- Column Header (clickable hamburger) -->
                <div class="dir-column-header" data-bs-toggle="collapse" data-bs-target="#colAdmins" role="button" aria-expanded="true">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-shield-fill-check" style="color: var(--ctp-red); font-size: 1.3rem;"></i>
                        <h5 style="color: var(--ctp-red); margin: 0; font-weight: 700;">Administradores</h5>
                        <span class="badge bg-danger ms-auto"><?php echo count($admins); ?></span>
                        <i class="bi bi-chevron-down dir-chevron" style="color: var(--ctp-overlay1); transition: transform 0.3s;"></i>
                    </div>
                </div>

                <div class="collapse show mt-3" id="colAdmins">
                    <?php if (empty($admins)): ?>
                        <p class="text-center" style="color: var(--ctp-overlay1); font-size: 0.88rem;">Sin administradores.</p>
                    <?php else: ?>
                        <?php foreach ($admins as $u): ?>
                            <?php echo renderDirCard($u, $hasSocialCols, 'var(--ctp-red)'); ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Column 2: Empleados -->
        <div class="col-lg-4 animate-in">
            <div class="card card-glass p-3 h-100">
                <div class="dir-column-header" data-bs-toggle="collapse" data-bs-target="#colEmployees" role="button" aria-expanded="true">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-briefcase-fill" style="color: var(--ctp-peach); font-size: 1.3rem;"></i>
                        <h5 style="color: var(--ctp-peach); margin: 0; font-weight: 700;">Empleados</h5>
                        <span class="badge bg-warning ms-auto"><?php echo count($empleados); ?></span>
                        <i class="bi bi-chevron-down dir-chevron" style="color: var(--ctp-overlay1); transition: transform 0.3s;"></i>
                    </div>
                </div>

                <div class="collapse show mt-3" id="colEmployees">
                    <?php if (empty($empleados)): ?>
                        <p class="text-center" style="color: var(--ctp-overlay1); font-size: 0.88rem;">Sin empleados registrados.</p>
                    <?php else: ?>
                        <?php 
                        // Group by tipo_empleado
                        $grouped = [];
                        foreach ($empleados as $e) {
                            $t = $e['tipo_empleado'] ?? 'soporte';
                            $grouped[$t][] = $e;
                        }
                        foreach ($tipoConfig as $tipo => $cfg):
                            if (empty($grouped[$tipo])) continue;
                        ?>
                            <div class="mb-2">
                                <small class="d-flex align-items-center gap-1 mb-2 px-1" style="color: <?php echo $cfg['color']; ?>; font-weight: 600;">
                                    <i class="bi <?php echo $cfg['icon']; ?>"></i> <?php echo $cfg['label']; ?>
                                    <span class="badge bg-secondary ms-1" style="font-size: 0.65rem;"><?php echo count($grouped[$tipo]); ?></span>
                                </small>
                                <?php foreach ($grouped[$tipo] as $u): ?>
                                    <?php echo renderDirCard($u, $hasSocialCols, $cfg['color']); ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Column 3: Usuarios -->
        <div class="col-lg-4 animate-in">
            <div class="card card-glass p-3 h-100">
                <div class="dir-column-header" data-bs-toggle="collapse" data-bs-target="#colUsers" role="button" aria-expanded="true">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-person-fill" style="color: var(--ctp-blue); font-size: 1.3rem;"></i>
                        <h5 style="color: var(--ctp-blue); margin: 0; font-weight: 700;">Usuarios</h5>
                        <span class="badge bg-primary ms-auto"><?php echo count($users); ?></span>
                        <i class="bi bi-chevron-down dir-chevron" style="color: var(--ctp-overlay1); transition: transform 0.3s;"></i>
                    </div>
                </div>

                <div class="collapse show mt-3" id="colUsers">
                    <?php if (empty($users)): ?>
                        <p class="text-center" style="color: var(--ctp-overlay1); font-size: 0.88rem;">Sin usuarios registrados.</p>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <?php echo renderDirCard($u, $hasSocialCols, 'var(--ctp-blue)'); ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Profile Detail Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profileModalTitle" style="color: var(--ctp-text);">Perfil del Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center" id="profileModalBody">
            </div>
        </div>
    </div>
</div>

<script>
var usuariosData = <?php echo json_encode(array_map(function($u) use ($hasSocialCols) {
    $foto = ($u['foto_perfil'] && $u['foto_perfil'] !== 'default.png') 
        ? '/app/assets/uploads/' . $u['foto_perfil'] 
        : '/assets/img/computer_sagyouin_woman.png';
    
    $data = [
        'id' => $u['id'],
        'nombre' => $u['nombre'],
        'correo' => $u['correo'],
        'rol' => $u['rol'],
        'tipo_empleado' => $u['tipo_empleado'] ?? '',
        'descripcion' => $u['descripcion'] ?: 'Sin descripción establecida.',
        'region' => $u['region'] ?? '',
        'foto' => $foto,
        'fecha' => date('d/m/Y', strtotime($u['fecha_creacion']))
    ];
    
    if ($hasSocialCols) {
        $data['github'] = $u['github_url'] ?? '';
        $data['linkedin'] = $u['linkedin_url'] ?? '';
        $data['twitter'] = $u['twitter_url'] ?? '';
        $data['website'] = $u['website_url'] ?? '';
    }
    
    return $data;
}, $usuarios), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

function openProfileModal(userId) {
    var user = null;
    for (var i = 0; i < usuariosData.length; i++) {
        if (usuariosData[i].id === userId) { user = usuariosData[i]; break; }
    }
    if (!user) return;

    var rolColors = { admin: 'var(--ctp-red)', empleado: 'var(--ctp-peach)', usuario: 'var(--ctp-blue)' };
    var rolLabels = { admin: 'Administrador', empleado: 'Empleado', usuario: 'Usuario' };
    var borderColor = rolColors[user.rol] || 'var(--ctp-blue)';
    
    var rolBadge = '<span class="badge" style="background:' + borderColor + '; color: var(--ctp-crust);">' + (rolLabels[user.rol] || user.rol) + '</span>';
    if (user.rol === 'empleado' && user.tipo_empleado) {
        rolBadge += ' <span class="badge bg-secondary">' + user.tipo_empleado.charAt(0).toUpperCase() + user.tipo_empleado.slice(1) + '</span>';
    }

    var socialHtml = '';
    if (user.github || user.linkedin || user.twitter || user.website) {
        socialHtml = '<div class="d-flex justify-content-center gap-2 mb-3">';
        if (user.github) socialHtml += '<a href="' + user.github + '" target="_blank" class="social-link github" title="GitHub"><i class="bi bi-github"></i></a>';
        if (user.linkedin) socialHtml += '<a href="' + user.linkedin + '" target="_blank" class="social-link linkedin" title="LinkedIn"><i class="bi bi-linkedin"></i></a>';
        if (user.twitter) socialHtml += '<a href="' + user.twitter + '" target="_blank" class="social-link twitter" title="X / Twitter"><i class="bi bi-twitter-x"></i></a>';
        if (user.website) socialHtml += '<a href="' + user.website + '" target="_blank" class="social-link website" title="Website"><i class="bi bi-globe2"></i></a>';
        socialHtml += '</div>';
    }

    document.getElementById('profileModalTitle').textContent = user.nombre;
    document.getElementById('profileModalBody').innerHTML = 
        '<img src="' + user.foto + '" alt="' + user.nombre + '" class="modal-profile-avatar" style="border-color: ' + borderColor + ';">' +
        '<h4 style="color: var(--ctp-text); font-weight: 700; margin-bottom: 0.25rem;">' + user.nombre + '</h4>' +
        '<div class="mb-3">' + rolBadge + '</div>' +
        socialHtml +
        '<div class="text-start" style="background: var(--ctp-surface0); border-radius: 12px; padding: 1rem;">' +
            '<div class="profile-detail-item"><i class="bi bi-envelope"></i><span>' + user.correo + '</span></div>' +
            (user.region ? '<div class="profile-detail-item"><i class="bi bi-geo-alt"></i><span>' + user.region + '</span></div>' : '') +
            '<div class="profile-detail-item"><i class="bi bi-calendar3"></i><span>Miembro desde: ' + user.fecha + '</span></div>' +
            '<div class="profile-detail-item"><i class="bi bi-card-text"></i><span>' + user.descripcion + '</span></div>' +
        '</div>';

    var modal = new bootstrap.Modal(document.getElementById('profileModal'));
    modal.show();
}

// Rotate chevron on collapse toggle
document.addEventListener('DOMContentLoaded', function() {
    var headers = document.querySelectorAll('.dir-column-header');
    for (var i = 0; i < headers.length; i++) {
        headers[i].addEventListener('click', function() {
            var chevron = this.querySelector('.dir-chevron');
            if (chevron) {
                var isExpanded = this.getAttribute('aria-expanded') === 'true';
                chevron.style.transform = isExpanded ? 'rotate(-180deg)' : 'rotate(0)';
                this.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
            }
        });
    }
});
</script>

<?php require_once '../../view/layout/footer.php'; ?>

<?php
// Helper: renders a compact profile card for column layout
function renderDirCard($u, $hasSocialCols, $accentColor) {
    $foto = ($u['foto_perfil'] && $u['foto_perfil'] !== 'default.png') 
        ? '/app/assets/uploads/' . htmlspecialchars($u['foto_perfil']) 
        : '/assets/img/computer_sagyouin_woman.png';
    
    $nombre = htmlspecialchars($u['nombre']);
    $desc = htmlspecialchars(mb_substr($u['descripcion'] ?: 'Sin descripción.', 0, 50)) . (mb_strlen($u['descripcion'] ?? '') > 50 ? '...' : '');

    // Social icons
    $socialIcons = '';
    if ($hasSocialCols) {
        $any = !empty($u['github_url']) || !empty($u['linkedin_url']) || !empty($u['twitter_url']) || !empty($u['website_url']);
        if ($any) {
            $socialIcons = '<div class="d-flex gap-1 mt-1">';
            if (!empty($u['github_url'])) $socialIcons .= '<i class="bi bi-github" style="color: var(--ctp-overlay1); font-size:0.75rem;"></i>';
            if (!empty($u['linkedin_url'])) $socialIcons .= '<i class="bi bi-linkedin" style="color: var(--ctp-overlay1); font-size:0.75rem;"></i>';
            if (!empty($u['twitter_url'])) $socialIcons .= '<i class="bi bi-twitter-x" style="color: var(--ctp-overlay1); font-size:0.75rem;"></i>';
            if (!empty($u['website_url'])) $socialIcons .= '<i class="bi bi-globe2" style="color: var(--ctp-overlay1); font-size:0.75rem;"></i>';
            $socialIcons .= '</div>';
        }
    }

    return '
    <div class="dir-user-card" onclick="openProfileModal(' . $u['id'] . ')">
        <img src="' . $foto . '" alt="' . $nombre . '" class="dir-user-avatar" style="border-color: ' . $accentColor . ';">
        <div class="dir-user-info">
            <div class="dir-user-name">' . $nombre . '</div>
            <div class="dir-user-desc">' . $desc . '</div>
            ' . $socialIcons . '
        </div>
        <i class="bi bi-chevron-right" style="color: var(--ctp-overlay0); font-size: 0.85rem;"></i>
    </div>';
}
?>

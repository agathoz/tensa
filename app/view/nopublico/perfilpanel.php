<?php
require_once '../../config/seciones.php';
requerirLogin();
require_once '../../config/db.php';
require_once '../../config/seguridad.php';

$usuario_id = $_SESSION['usuario_id'];
$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $descripcion = limpiarCadenas($_POST['descripcion'] ?? '');
    $region = limpiarCadenas($_POST['region'] ?? '');
    
    // Si el usuario es empleado o admin
    $tipoQuery = "";
    $params = [$descripcion, $region];
    
    // Subida de imagen
    $fotoQuery = "";
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $nombreFoto = subirImagenSegura($_FILES['foto_perfil']);
        if ($nombreFoto) {
            $fotoQuery = ", foto_perfil = ?";
            $params[] = $nombreFoto;
        } else {
            $mensaje = "<div class='alert alert-danger'>Error: Imagen inválida. Solo se permite png, jpg, gif formados correctamente.</div>";
        }
    }
    
    // Actualizar BD
    if(empty($mensaje) || strpos($mensaje, 'alert-danger') === false) {
        $sql = "UPDATE usuarios SET descripcion = ?, region = ? $fotoQuery WHERE id = ?";
        $params[] = $usuario_id;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $mensaje = "<div class='alert alert-success'>Perfil actualizado correctamente.</div>";
    }
}

// Cargar datos actuales
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$perfil = $stmt->fetch();

require_once '../../view/layout/header.php';
require_once '../../view/layout/navbar.php';
?>

<main class="container py-5 min-vh-80">
    <h2 style="color: var(--ctp-peach);">Mi Perfil</h2>
    
    <?php echo $mensaje; ?>
    
    <div class="row mt-4">
        <div class="col-md-4 text-center">
            <?php $foto = $perfil['foto_perfil'] ? $perfil['foto_perfil'] : 'default.png'; ?>
            <img src="/assets/uploads/<?php echo htmlspecialchars($foto); ?>" alt="Foto Perfil" class="img-fluid rounded-circle mb-3" style="width: 200px; height: 200px; object-fit: cover; border: 3px solid var(--ctp-blue);">
            <h4 style="color: var(--ctp-text); mt-3"><?php echo htmlspecialchars($perfil['correo']); ?></h4>
            <span class="badge bg-primary mb-4"><?php echo strtoupper($perfil['rol']); ?></span>
            
            <div class="d-flex flex-column gap-2 mt-3 p-3 rounded" style="background-color: var(--ctp-surface0); border: 1px solid var(--ctp-surface1);">
                <a href="/app/view/nopublico/mis_tickets.php" class="btn btn-outline-peach w-100 text-start" style="border-radius: 8px; border-color: var(--ctp-peach); color: var(--ctp-text);">
                    <i class="bi bi-ticket-detailed me-2 text-peach"></i>Mis Tickets de Servicio
                </a>
                <a href="/app/view/pages/productos.php" class="btn btn-outline-green w-100 text-start" style="border-radius: 8px; border-color: var(--ctp-green); color: var(--ctp-text);">
                    <i class="bi bi-bag-check me-2 text-green"></i>Comprar Productos
                </a>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card p-4">
                <form action="perfilpanel.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Foto de Perfil (JPG, PNG, GIF)</label>
                        <input type="file" name="foto_perfil" class="form-control" accept=".jpg,.jpeg,.png,.gif">
                    </div>
                    
                    <?php if(rolUsuario() === 'empleado' || rolUsuario() === 'admin'): ?>
                    <div class="mb-3">
                        <label class="form-label">Tipo de Empleado (Fijo)</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($perfil['tipo_empleado'] ?? 'No especificado'); ?>" disabled>
                    </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Región / Ubicación</label>
                        <input type="text" name="region" class="form-control" value="<?php echo htmlspecialchars($perfil['region'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción / Biografía</label>
                        <textarea name="descripcion" class="form-control" rows="5"><?php echo htmlspecialchars($perfil['descripcion'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../view/layout/footer.php'; ?>

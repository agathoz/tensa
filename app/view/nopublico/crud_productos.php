<?php
require_once '../../config/seciones.php';
requerirRol('empleado');
require_once '../../config/db.php';
require_once '../../config/seguridad.php';

// Validar que sea de almacén o admin
$stmt = $pdo->prepare("SELECT tipo_empleado FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$tipo = $stmt->fetchColumn();

if ($tipo !== 'almacen' && $_SESSION['rol'] !== 'admin') {
    die("Acceso denegado. No perteneces a Almacén.");
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['crear_producto'])) {
    $nombre = limpiarCadenas($_POST['nombre'] ?? '');
    $descripcion = limpiarCadenas($_POST['descripcion'] ?? '');
    $precio = (float)($_POST['precio'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $marca = limpiarCadenas($_POST['marca'] ?? '');
    $proveedor_id = !empty($_POST['proveedor_id']) ? (int)$_POST['proveedor_id'] : null;
    $status = $_POST['status'] ?? 'activo';
    
    // Auto-generación de SKU y Código si vienen vacíos
    $sku = limpiarCadenas($_POST['sku'] ?? '');
    if (empty($sku)) {
        $prefix_marca = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $marca ?: 'GEN'), 0, 3));
        $prefix_nombre = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $nombre), 0, 3));
        $sku = $prefix_marca . '-' . $prefix_nombre . '-' . rand(100, 999);
    }
    
    $codigo = limpiarCadenas($_POST['codigo'] ?? '');
    if (empty($codigo)) {
        // Código seguro como PRD-XYZ1234
        $codigo = 'PRD-' . strtoupper(substr(uniqid(), -6));
    }

    // Imágenes Múltiples (Galería)
    $galeria = [];
    $foto_url = 'default.png';
    
    if (isset($_FILES['fotos'])) {
        $total_files = count($_FILES['fotos']['name']);
        for($i = 0; $i < $total_files; $i++) {
            if ($_FILES['fotos']['error'][$i] === UPLOAD_ERR_OK) {
                // Reconstruir el formato de $_FILES para enviar individualmente a la función segura
                $fileInfo = [
                    'name' => $_FILES['fotos']['name'][$i],
                    'type' => $_FILES['fotos']['type'][$i],
                    'tmp_name' => $_FILES['fotos']['tmp_name'][$i],
                    'error' => $_FILES['fotos']['error'][$i],
                    'size' => $_FILES['fotos']['size'][$i]
                ];
                $nombreFoto = subirImagenSegura($fileInfo);
                if ($nombreFoto) {
                    $galeria[] = $nombreFoto;
                    if ($foto_url === 'default.png') {
                        $foto_url = $nombreFoto; // La primera es la principal
                    }
                }
            }
        }
    }
    
    $galeria_json = !empty($galeria) ? json_encode($galeria) : null;

    if($nombre && empty($mensaje)) {
        $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, marca, proveedor_id, sku, codigo, foto_url, galeria, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $descripcion, $precio, $stock, $marca, $proveedor_id, $sku, $codigo, $foto_url, $galeria_json, $status]);
        $mensaje = "<div class='alert alert-success'>Producto registrado correctamente. El SKU se fijó en <strong>{$sku}</strong> y Código <strong>{$codigo}</strong>.</div>";
    }
}

// Obtener datos para vista
$productos = $pdo->query("SELECT p.*, pr.nombre as proveedor_nombre FROM productos p LEFT JOIN proveedores pr ON p.proveedor_id = pr.id ORDER BY p.id DESC")->fetchAll();
$proveedores = $pdo->query("SELECT id, nombre FROM proveedores WHERE status = 'activo'")->fetchAll();

require_once '../../view/layout/header.php';
require_once '../../view/layout/navbar.php';
?>

<main class="container py-5 min-vh-80">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color: var(--ctp-teal);">Gestión de Productos (Almacén)</h2>
        <a href="/app/view/nopublico/empleado_panel.php" class="btn btn-secondary">Volver al Panel</a>
    </div>
    
    <?php echo $mensaje; ?>

    <div class="row">
        <!-- Formulario -->
        <div class="col-md-4">
            <div class="card p-4" style="background: var(--ctp-surface0); border-color: var(--ctp-surface1);">
                <h4 style="color: var(--ctp-mauve);">Nuevo Producto</h4>
                <form action="crud_productos.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="crear_producto" value="1">
                    
                    <div class="mb-2">
                        <label style="color: var(--ctp-text);">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-6 mb-2">
                            <label style="color: var(--ctp-text);">Precio</label>
                            <input type="number" step="0.01" name="precio" class="form-control" required>
                        </div>
                        <div class="col-6 mb-2">
                            <label style="color: var(--ctp-text);">Stock</label>
                            <input type="number" name="stock" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6 mb-2">
                            <label style="color: var(--ctp-text);">SKU <small class="text-muted">(Auto si vacío)</small></label>
                            <input type="text" name="sku" class="form-control" placeholder="Ej. LENOVO-123">
                        </div>
                        <div class="col-6 mb-2">
                            <label style="color: var(--ctp-text);">Código <small class="text-muted">(Auto si vacío)</small></label>
                            <input type="text" name="codigo" class="form-control" placeholder="Ej. PRD-18X">
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <label style="color: var(--ctp-text);">Marca</label>
                        <input type="text" name="marca" class="form-control">
                    </div>
                    
                    <div class="mb-2">
                        <label style="color: var(--ctp-text);">Proveedor</label>
                        <select name="proveedor_id" class="form-select">
                            <option value="">Ninguno</option>
                            <?php foreach($proveedores as $pr): ?>
                                <option value="<?php echo $pr['id']; ?>"><?php echo htmlspecialchars($pr['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label style="color: var(--ctp-text);">Fotos Múltiples (Galería)</label>
                        <input type="file" name="fotos[]" class="form-control" accept=".jpg,.png,.jpeg,.gif" multiple>
                        <small style="color: var(--ctp-overlay1); font-size: 0.75rem;">Puedes subir varias fotos (CTRL+Clic). La primera será la principal.</small>
                    </div>

                    <div class="mb-3">
                        <label style="color: var(--ctp-text);">Status</label>
                        <select name="status" class="form-select">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                            <option value="baja">Baja</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label style="color: var(--ctp-text);">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100 mt-2">Registrar Producto</button>
                </form>
            </div>
        </div>
        
        <!-- Tabla -->
        <div class="col-md-8">
            <div class="card p-4" style="background: var(--ctp-surface0); border-color: var(--ctp-surface1);">
                <div class="table-responsive">
                    <table class="table" style="color: var(--ctp-text);">
                        <thead>
                            <tr>
                                <th>IMG</th>
                                <th>Nombre</th>
                                <th>SKU</th>
                                <th>Precio / Stock</th>
                                <th>Proveedor</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($productos as $p): ?>
                            <tr>
                                <td>
                                    <?php $img = $p['foto_url'] != 'default.png' ? '/app/assets/uploads/'.$p['foto_url'] : '/assets/img/computer_sagyouin_woman.png'; ?>
                                    <img src="<?php echo htmlspecialchars($img); ?>" width="40" height="40" style="object-fit:cover; border-radius:50%;">
                                </td>
                                <td><?php echo htmlspecialchars($p['nombre']); ?><br><small class="text-muted"><?php echo htmlspecialchars($p['marca']); ?></small></td>
                                <td><?php echo htmlspecialchars($p['sku']); ?></td>
                                <td>$<?php echo number_format($p['precio'],2); ?> <br> <span class="badge bg-secondary"><?php echo $p['stock']; ?> un.</span></td>
                                <td><?php echo htmlspecialchars($p['proveedor_nombre'] ?? '-'); ?></td>
                                <td>
                                    <?php 
                                        $b_class = 'secondary';
                                        if($p['status']=='activo') $b_class = 'success';
                                        if($p['status']=='baja') $b_class = 'danger';
                                    ?>
                                    <span class="badge bg-<?php echo $b_class; ?>"><?php echo strtoupper($p['status']); ?></span>
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

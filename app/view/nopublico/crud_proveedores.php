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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['crear_proveedor'])) {
    $nombre = limpiarCadenas($_POST['nombre'] ?? '');
    $telefono = limpiarCadenas($_POST['telefono'] ?? '');
    $direccion = limpiarCadenas($_POST['direccion'] ?? '');
    $status = $_POST['status'] ?? 'activo';
    
    if($nombre) {
        $stmt = $pdo->prepare("INSERT INTO proveedores (nombre, telefono, direccion, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $telefono, $direccion, $status]);
        $mensaje = "<div class='alert alert-success'>Proveedor creado con éxito.</div>";
    }
}

$proveedores = $pdo->query("SELECT * FROM proveedores ORDER BY id DESC")->fetchAll();

require_once '../../view/layout/header.php';
require_once '../../view/layout/navbar.php';
?>

<main class="container py-5 min-vh-80">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color: var(--ctp-teal);">Gestión de Proveedores (Almacén)</h2>
        <a href="/app/view/nopublico/empleado_panel.php" class="btn btn-secondary">Volver al Panel</a>
    </div>
    
    <?php echo $mensaje; ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card p-4" style="background: var(--ctp-surface0); border-color: var(--ctp-surface1);">
                <h4 style="color: var(--ctp-mauve);">Nuevo Proveedor</h4>
                <form method="POST">
                    <input type="hidden" name="crear_proveedor" value="1">
                    <div class="mb-3">
                        <label style="color: var(--ctp-text);">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label style="color: var(--ctp-text);">Teléfono</label>
                        <input type="text" name="telefono" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label style="color: var(--ctp-text);">Dirección</label>
                        <textarea name="direccion" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label style="color: var(--ctp-text);">Status</label>
                        <select name="status" class="form-select">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                    <button class="btn btn-success w-100">Guardar</button>
                </form>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card p-4" style="background: var(--ctp-surface0); border-color: var(--ctp-surface1);">
                <table class="table" style="color: var(--ctp-text);">
                    <thead>
                        <tr>
                            <th>ID</th><th>Nombre</th><th>Contacto</th><th>Ingreso</th><th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($proveedores as $prov): ?>
                        <tr>
                            <td><?php echo $prov['id']; ?></td>
                            <td><?php echo htmlspecialchars($prov['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($prov['telefono']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($prov['ingreso'])); ?></td>
                            <td><span class="badge bg-<?php echo $prov['status'] == 'activo' ? 'success' : 'secondary'; ?>"><?php echo strtoupper($prov['status']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../view/layout/footer.php'; ?>

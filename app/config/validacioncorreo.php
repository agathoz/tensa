<?php
require_once 'seciones.php';
require_once 'db.php';

if(isset($_GET['simular_registro'])) {
    $correo = $_GET['simular_registro'];
    
    // Aquí iría el código de PHPMailer:
    // $mail = new PHPMailer(true);
    // $mail->addAddress($correo); // ... envia
    
    // Para prueba:
    $stmt = $pdo->prepare("UPDATE usuarios SET correo_confirmado = 1 WHERE correo = ?");
    $stmt->execute([$correo]);

    header("Location: /app/view/pages/login.php?success=" . urlencode("Modo Prueba: Correo de confirmación enviado simuladamente y cuenta activada. Inicia sesión."));
    exit;
}

if(isset($_GET['simular_factura']) && isset($_GET['compra_id'])) {
    requerirRol('admin'); // solo por si el admin prueba
    // MOCK PDF AL CORREO
    echo "<h1>Simulación de PHPMailer y PDF Adjunto</h1>";
    echo "<p>Factura de compra ID: " . htmlspecialchars($_GET['compra_id']) . " enviada satisfactoriamente con PDF.</p>";
    echo "<a href='/app/view/nopublico/admin_panel.php'>Volver al Panel</a>";
}
?>

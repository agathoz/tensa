<?php
require_once 'seciones.php';
requerirLogin();
require_once 'db.php';
require_once 'seguridad.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $cart_payload_json = $_POST['cart_payload'] ?? '';
    $tipo_envio = $_POST['tipo_envio'] ?? 'sucursal';
    $direccion_envio = limpiarCadenas($_POST['direccion_envio'] ?? '');
    $metodo_pago = $_POST['metodo_pago'] ?? 'sucursal';
    
    $usuario_id = $_SESSION['usuario_id'];

    // Validar JSON del carrito
    $cart_items = json_decode($cart_payload_json, true);
    
    if(!$cart_items || !is_array($cart_items) || count($cart_items) == 0) {
        die("Error: El carrito estaba vacío o los datos son inválidos.");
    }

    $estado_pago = ($metodo_pago === 'sucursal') ? 'pendiente' : 'completado';
    
    try {
        $pdo->beginTransaction();

        // Calcular total global verificando contra BD directamente para evitar fraudes en el precio frontal
        $total_compra = 0;
        $items_procesados = [];

        foreach($cart_items as $item) {
            $p_id = (int)($item['id'] ?? 0);
            $qty = (int)($item['qty'] ?? 1);
            
            if ($qty <= 0) continue;

            $stmtProd = $pdo->prepare("SELECT precio, stock FROM productos WHERE id = ? AND status = 'activo' FOR UPDATE");
            $stmtProd->execute([$p_id]);
            $productoInfo = $stmtProd->fetch();

            if (!$productoInfo) {
                throw new Exception("El producto con ID $p_id no existe o ya no está activo.");
            }

            if ($productoInfo['stock'] < $qty) {
                throw new Exception("No hay stock suficiente para uno o más productos. (ID: $p_id)");
            }

            $precio_unit = $productoInfo['precio'];
            $subtot = $precio_unit * $qty;
            $total_compra += $subtot;

            $items_procesados[] = [
                'id' => $p_id,
                'qty' => $qty,
                'unit' => $precio_unit,
                'sub' => $subtot
            ];
        }

        // Crear la compra maestra
        $stmtCompra = $pdo->prepare("INSERT INTO compras (usuario_id, tipo_envio, direccion_envio, metodo_pago, estado_pago, total) VALUES (?, ?, ?, ?, ?, ?)");
        $stmtCompra->execute([
            $usuario_id,
            $tipo_envio,
            $direccion_envio,
            $metodo_pago,
            $estado_pago,
            $total_compra
        ]);
        
        $compra_id = $pdo->lastInsertId();

        // Insertar items de compra y decrementar stock
        $stmtItem = $pdo->prepare("INSERT INTO compra_items (compra_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmtStock = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");

        foreach($items_procesados as $pi) {
            $stmtItem->execute([$compra_id, $pi['id'], $pi['qty'], $pi['unit'], $pi['sub']]);
            $stmtStock->execute([$pi['qty'], $pi['id']]);
        }

        // Generar Factura
        // Folio format: BT-YYYY-ID zero-padded
        $folio = "BT-" . date("Y") . "-" . str_pad($compra_id, 4, '0', STR_PAD_LEFT);
        
        // 16% IVA desglose (assuming total include IVA, so subtotal = total / 1.16)
        $subtotal_factura = $total_compra / 1.16;
        $iva_factura = $total_compra - $subtotal_factura;
        
        $stmtFactura = $pdo->prepare("INSERT INTO facturas (compra_id, folio, subtotal, iva, total) VALUES (?, ?, ?, ?, ?)");
        $stmtFactura->execute([$compra_id, $folio, $subtotal_factura, $iva_factura, $total_compra]);
        
        $factura_id = $pdo->lastInsertId();

        $pdo->commit();

        // Redirect to factura visualization success with flag to clear frontend cart
        header("Location: /app/view/nopublico/factura_view.php?id=" . $factura_id . "&clear_cart=1");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error procesando la compra: " . $e->getMessage() . " <br><a href='/app/view/nopublico/checkout.php'>Volver</a>");
    }
} else {
    header("Location: /");
}

<?php
require __DIR__ . '/includes/bootstrap.php';
if (!($_SESSION['carrito'] ?? [])) { flash('Añade productos antes de tramitar un pedido.'); redirect('carrito.php'); }
$errors = []; $data = ['nombre' => '', 'email' => '', 'calle' => '', 'ciudad' => '', 'cp' => ''];
if (uid()) {
    $stmt = db()->prepare('SELECT nombre, email FROM usuarios WHERE id = ?'); $stmt->execute([uid()]);
    $data = array_merge($data, $stmt->fetch() ?: []);
    $stmt = db()->prepare("SELECT calle, ciudad, cp FROM direcciones WHERE usuario_id = ? AND tipo = 'entrega' ORDER BY principal DESC, id ASC LIMIT 1");
    $stmt->execute([uid()]); $data = array_merge($data, $stmt->fetch() ?: []);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    foreach ($data as $key => $value) $data[$key] = post($key);
    $errors = address_errors($data);
    if ($data['nombre'] === '' || mb_strlen($data['nombre']) > 100) $errors[] = 'Introduce un nombre de hasta 100 caracteres.';
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || strlen($data['email']) > 190) $errors[] = 'Introduce un correo válido.';
    // Token de un solo uso: un reenvío del formulario no crea un segundo pedido.
    if (!is_string($_POST['checkout_token'] ?? null) || !isset($_SESSION['checkout_token']) || !hash_equals($_SESSION['checkout_token'], $_POST['checkout_token'])) $errors[] = 'El carrito ha cambiado o el pedido ya se ha enviado. Revisa el resumen.';
    if (!$errors) {
        $pdo = db(); $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('INSERT INTO pedidos (usuario_id, nombre, email, calle, ciudad, cp, total, token) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([uid(), $data['nombre'], $data['email'], $data['calle'], $data['ciudad'], $data['cp'], number_format(cart_total() / 100, 2, '.', ''), $_SESSION['checkout_token']]);
            $orderId = (int) $pdo->lastInsertId();
            $stmt = $pdo->prepare('INSERT INTO pedido_lineas (pedido_id, producto_id, producto, precio_unitario, cantidad) VALUES (?, ?, ?, ?, ?)');
            foreach ($_SESSION['carrito'] as $id => $quantity) {
                $product = products()[$id];
                $stmt->execute([$orderId, $id, $product['nombre'], number_format($product['precio'] / 100, 2, '.', ''), $quantity]);
            }
            $pdo->commit();
        } catch (Throwable $error) { if ($pdo->inTransaction()) $pdo->rollBack(); throw $error; }
        $_SESSION['ultimo_pedido'] = $orderId;
        unset($_SESSION['carrito'], $_SESSION['checkout_token']);
        redirect('pedido-confirmado.php');
    }
}
$_SESSION['checkout_token'] ??= bin2hex(random_bytes(32));
$title = 'Checkout'; require __DIR__ . '/includes/header.php'; ?>
<p class="eyebrow">El último paso</p><h1>Finalizar pedido</h1><p>Demostración académica: no se realiza ningún cobro. Envío gratuito a España.</p>
<?php if (!uid()): ?><div class="alert alert-info">¿Ya tienes cuenta? <a href="login.php?next=checkout">Inicia sesión para ir más rápido</a>.</div><?php else: ?><div class="alert alert-info">Usamos tu dirección de entrega principal, si la tienes guardada. Puedes cambiarla para este pedido.</div><?php endif; ?>
<div class="row"><section class="col-lg-7"><div class="panel"><h2>Datos de envío</h2><?php show_errors($errors); ?><form method="post"><?php csrf_field(); ?><input type="hidden" name="checkout_token" value="<?= e($_SESSION['checkout_token']) ?>">
<label class="form-label" for="nombre">Nombre del destinatario</label><input class="form-control mb-3" name="nombre" id="nombre" maxlength="100" required value="<?= e($data['nombre']) ?>"><label class="form-label" for="email">Correo electrónico</label><input class="form-control mb-3" type="email" id="email" name="email" maxlength="190" required value="<?= e($data['email']) ?>"><?php address_fields($data); ?><button class="btn btn-primary">Confirmar pedido de demostración</button></form></div></section>
<aside class="col-lg-5"><div class="panel"><h2>Resumen</h2><?php foreach ($_SESSION['carrito'] as $id => $quantity): ?><div class="order-line"><?= e(products()[$id]['nombre']) ?> × <?= $quantity ?> <strong class="float-end"><?= money(products()[$id]['precio'] * $quantity) ?></strong></div><?php endforeach; ?><p class="mt-4 fs-4">Total: <strong><?= money(cart_total()) ?></strong></p><a href="carrito.php">Editar carrito</a></div></aside></div>
<?php require __DIR__ . '/includes/footer.php'; ?>

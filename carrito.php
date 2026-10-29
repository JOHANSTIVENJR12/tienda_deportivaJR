<?php
require __DIR__ . '/includes/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf(); $id = (int) post('producto_id'); $action = post('accion');
    if (!isset(products()[$id]) || !in_array($action, ['agregar', 'actualizar', 'eliminar'], true)) { http_response_code(400); exit('Producto o acción no válidos.'); }
    if ($action === 'agregar') $_SESSION['carrito'][$id] = min(20, ($_SESSION['carrito'][$id] ?? 0) + 1);
    if ($action === 'eliminar') unset($_SESSION['carrito'][$id]);
    if ($action === 'actualizar') {
        $quantity = filter_var(post('cantidad'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 20]]);
        if ($quantity === false) { http_response_code(400); exit('La cantidad debe estar entre 1 y 20.'); }
        $_SESSION['carrito'][$id] = $quantity;
    }
    unset($_SESSION['checkout_token']);
    redirect('carrito.php');
}
$title = 'Carrito'; require __DIR__ . '/includes/header.php'; ?>
<h1>Tu carrito</h1><div class="panel"><?php if (!($_SESSION['carrito'] ?? [])): ?><p>Tu carrito está vacío.</p><a class="btn btn-primary" href="index.php#productos">Ver productos</a><?php else: ?>
<?php foreach ($_SESSION['carrito'] as $id => $quantity): $product = products()[$id]; ?>
<div class="order-line d-flex flex-wrap justify-content-between gap-3"><div><strong><?= e($product['nombre']) ?></strong><p class="mb-0"><?= money($product['precio']) ?> / unidad</p></div><form class="d-flex align-items-center gap-2" method="post"><?php csrf_field(); ?><input type="hidden" name="producto_id" value="<?= $id ?>"><label for="cantidad-<?= $id ?>">Cantidad</label><input class="form-control" style="width:80px" type="number" id="cantidad-<?= $id ?>" name="cantidad" min="1" max="20" value="<?= $quantity ?>"><button class="btn btn-outline-dark" name="accion" value="actualizar">Actualizar</button><button class="btn btn-outline-danger" name="accion" value="eliminar">Eliminar</button></form><strong><?= money($product['precio'] * $quantity) ?></strong></div>
<?php endforeach; ?><p class="fs-4 mt-4">Total: <strong><?= money(cart_total()) ?></strong></p><a class="btn btn-primary" href="checkout.php">Continuar al checkout</a><?php endif; ?></div>
<?php require __DIR__ . '/includes/footer.php'; ?>

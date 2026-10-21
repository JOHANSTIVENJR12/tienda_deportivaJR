<?php
require __DIR__ . '/includes/bootstrap.php';
if (!isset($_SESSION['ultimo_pedido'])) redirect('index.php');
$title = 'Pedido confirmado'; require __DIR__ . '/includes/header.php'; ?>
<div class="panel text-center py-5"><p class="eyebrow">Todo listo</p><h1>Pedido confirmado</h1><p class="lead">Tu pedido de demostración #<?= (int) $_SESSION['ultimo_pedido'] ?> se ha guardado correctamente.</p><p>No se ha realizado ningún cobro.</p><?php if (uid()): ?><a class="btn btn-primary" href="mi-cuenta.php">Ver mis pedidos</a><?php endif; ?> <a class="btn btn-outline-dark" href="index.php">Seguir comprando</a></div>
<?php require __DIR__ . '/includes/footer.php'; ?>

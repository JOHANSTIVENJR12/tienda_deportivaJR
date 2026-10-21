<?php
require __DIR__ . '/includes/bootstrap.php';
$title = 'Tu próxima jugada empieza aquí';
require __DIR__ . '/includes/header.php';
?>
<section class="hero"><p class="eyebrow">Equipación para darlo todo</p><h1>Tu próxima jugada empieza aquí.</h1><p class="lead">Tus productos de siempre, ahora con tu propia cuenta.</p><a class="btn btn-primary" href="#productos">Ver productos</a></section>
<section id="productos"><h2 class="mb-4">Nuestros productos</h2><div class="row g-4">
<?php foreach (products() as $id => $product): ?>
<article class="col-md-6 col-lg-4"><div class="card h-100 shadow-sm"><img src="imagen/<?= e($product['imagen']) ?>" class="card-img-top" alt="<?= e($product['nombre']) ?>"><div class="card-body"><h3 class="card-title"><?= e($product['nombre']) ?></h3><p><?= money($product['precio']) ?></p><form action="carrito.php" method="post"><?php csrf_field(); ?><input type="hidden" name="accion" value="agregar"><input type="hidden" name="producto_id" value="<?= $id ?>"><button class="btn btn-primary">Añadir al carrito</button></form></div></div></article>
<?php endforeach; ?></div></section>
<section class="panel mt-5"><h2>Preguntas frecuentes</h2><details><summary>¿Cuánto tarda el envío?</summary><p>Entre 2 y 5 días laborables en una tienda real. Esta práctica registra pedidos ficticios.</p></details><details><summary>¿Puedo comprar sin cuenta?</summary><p>Sí. También puedes registrarte para guardar tus direcciones y consultar tus pedidos.</p></details><details><summary>¿Se realiza un cobro?</summary><p>No. El checkout es una demostración académica y no solicita datos bancarios.</p></details></section>
<?php require __DIR__ . '/includes/footer.php'; ?>

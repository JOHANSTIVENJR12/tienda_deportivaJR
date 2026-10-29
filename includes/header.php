<?php /* Se incluye después de procesar formularios y redirecciones. */ ?>
<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'Tienda Deportiva JR') ?> | JR</title>
<link rel="stylesheet" href="frameworks/css/bootstrap.min.css"><link rel="stylesheet" href="CSS/styles.css"><link rel="stylesheet" href="CSS/cuenta.css"></head><body>
<nav class="navbar navbar-dark bg-dark"><div class="container gap-3"><a class="navbar-brand fw-bold" href="index.php">Tienda Deportiva JR</a>
<div class="d-flex flex-wrap gap-3 align-items-center"><a href="index.php#productos">Productos</a><a href="contacto.html">Contacto</a><a href="carrito.php">Carrito (<?= array_sum($_SESSION['carrito'] ?? []) ?>)</a>
<?php if (uid()): ?><a href="mi-cuenta.php">Mi Cuenta</a><form action="logout.php" method="post"><?php csrf_field(); ?><button class="btn btn-outline-light btn-sm">Cerrar sesión</button></form>
<?php else: ?><a href="login.php">Iniciar sesión</a><a class="btn btn-light btn-sm" href="registro.php">Crear cuenta</a><?php endif; ?></div></div></nav>
<main class="container py-4">
<?php if (isset($_SESSION['flash'])): ?><div class="alert alert-success" role="status"><?= e($_SESSION['flash']) ?></div><?php unset($_SESSION['flash']); endif; ?>

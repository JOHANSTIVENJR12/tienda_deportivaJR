<?php
require __DIR__ . '/includes/bootstrap.php';
require_login();
$errors = []; $data = ['calle' => '', 'ciudad' => '', 'cp' => '', 'tipo' => 'entrega'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $data = ['calle' => post('calle'), 'ciudad' => post('ciudad'), 'cp' => post('cp'), 'tipo' => post('tipo')];
    $errors = address_errors($data);
    if (!in_array($data['tipo'], ['entrega', 'facturación'], true)) $errors[] = 'Elige un tipo de dirección válido.';
    if (!$errors) {
        $pdo = db(); $pdo->beginTransaction();
        try {
            // Serializa los cambios del mismo usuario para mantener una principal por tipo.
            $pdo->prepare('SELECT id FROM usuarios WHERE id = ? FOR UPDATE')->execute([uid()]);
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM direcciones WHERE usuario_id = ? AND tipo = ?');
            $stmt->execute([uid(), $data['tipo']]);
            $principal = post('principal') === '1' || (int) $stmt->fetchColumn() === 0;
            if ($principal) $pdo->prepare('UPDATE direcciones SET principal = 0 WHERE usuario_id = ? AND tipo = ?')->execute([uid(), $data['tipo']]);
            $pdo->prepare('INSERT INTO direcciones (usuario_id, calle, ciudad, cp, tipo, principal) VALUES (?, ?, ?, ?, ?, ?)')->execute([uid(), $data['calle'], $data['ciudad'], $data['cp'], $data['tipo'], (int) $principal]);
            $pdo->commit();
        } catch (Throwable $error) { if ($pdo->inTransaction()) $pdo->rollBack(); throw $error; }
        flash('Dirección guardada. La primera de cada tipo se establece como principal.');
        redirect('mi-cuenta.php');
    }
}
$stmt = db()->prepare('SELECT calle, ciudad, cp, tipo, principal FROM direcciones WHERE usuario_id = ? ORDER BY tipo, principal DESC, id DESC');
$stmt->execute([uid()]); $addresses = $stmt->fetchAll();
// El filtro procede de la sesión, nunca de un parámetro enviado por el cliente.
$stmt = db()->prepare('SELECT id, total, estado, fecha, calle, ciudad, cp FROM pedidos WHERE usuario_id = ? ORDER BY fecha DESC, id DESC');
$stmt->execute([uid()]); $orders = $stmt->fetchAll();
$title = 'Mi Cuenta'; require __DIR__ . '/includes/header.php';
?>
<p class="eyebrow">Tu espacio JR</p><h1>Mi Cuenta</h1><p class="lead">Hola, <?= e($_SESSION['nombre']) ?>.</p>
<div class="row"><section class="col-lg-7"><div class="panel"><h2>Mis direcciones</h2>
<?php if (!$addresses): ?><p>Aún no has guardado ninguna dirección.</p><?php endif; ?>
<?php foreach ($addresses as $address): ?><div class="border rounded p-3 mb-3"><strong><?= e(ucfirst($address['tipo'])) ?></strong> <?php if ($address['principal']): ?><span class="badge bg-dark">Principal</span><?php endif; ?><p class="mb-0 mt-2"><?= e($address['calle']) ?><br><?= e($address['cp']) ?> · <?= e($address['ciudad']) ?></p></div><?php endforeach; ?></div>
<div class="panel"><h2>Mis pedidos</h2><p>Historial exclusivo de tu cuenta.</p><?php if (!$orders): ?><p>Aún no has realizado pedidos. <a href="index.php#productos">Ver productos</a></p><?php else: ?>
<div class="table-responsive"><table class="table"><thead><tr><th>Pedido</th><th>Fecha</th><th>Total</th><th>Estado</th><th>Envío</th></tr></thead><tbody><?php foreach ($orders as $order): ?><tr><td>#<?= (int) $order['id'] ?></td><td><?= e($order['fecha']) ?></td><td><?= e(number_format((float) $order['total'], 2, ',', '.')) ?> €</td><td><?= e($order['estado']) ?></td><td><?= e($order['calle']) ?><br><?= e($order['cp'] . ' ' . $order['ciudad']) ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></div></section>
<section class="col-lg-5"><div class="panel"><h2>Nueva dirección</h2><?php show_errors($errors); ?><form method="post"><?php csrf_field(); ?><?php address_fields($data); ?>
<label class="form-label" for="tipo">Tipo</label><select class="form-select mb-3" name="tipo" id="tipo"><option value="entrega" <?= $data['tipo'] === 'entrega' ? 'selected' : '' ?>>Entrega</option><option value="facturación" <?= $data['tipo'] === 'facturación' ? 'selected' : '' ?>>Facturación</option></select>
<div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="principal" value="1" id="principal"><label class="form-check-label" for="principal">Usar como principal de este tipo</label></div><button class="btn btn-primary">Guardar dirección</button></form></div></section></div>
<?php require __DIR__ . '/includes/footer.php'; ?>

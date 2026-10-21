<?php
declare(strict_types=1);
require __DIR__ . '/../config/database.php';
$pdo = db();
function verify(bool $condition, string $message): void {
    if (!$condition) throw new RuntimeException($message);
    echo "OK: $message\n";
}
foreach ($pdo->query('SELECT password FROM usuarios') as $user) {
    verify(password_verify('DemoJR2026!', $user['password']), 'Contraseña de prueba verificable mediante hash');
    verify($user['password'] !== 'DemoJR2026!', 'No hay contraseña de prueba en texto plano');
}
verify((int) $pdo->query('SELECT COUNT(*) FROM pedidos WHERE usuario_id IS NULL')->fetchColumn() === 2, 'Pedido nuevo de invitado con usuario_id NULL');
verify((int) $pdo->query('SELECT COUNT(*) FROM pedidos WHERE usuario_id = 1')->fetchColumn() === 2, 'Pedido nuevo vinculado a Ana');
verify((int) $pdo->query('SELECT COUNT(*) FROM pedidos')->fetchColumn() === 5, 'El reenvío no duplica pedidos');
verify((int) $pdo->query('SELECT COUNT(*) FROM (SELECT usuario_id,tipo FROM direcciones WHERE principal=1 GROUP BY usuario_id,tipo HAVING COUNT(*)>1) d')->fetchColumn() === 0, 'Una dirección principal por usuario y tipo');
verify((int) $pdo->query('SELECT COUNT(*) FROM pedidos p WHERE p.total != (SELECT SUM(l.precio_unitario*l.cantidad) FROM pedido_lineas l WHERE l.pedido_id=p.id)')->fetchColumn() === 0, 'Totales coherentes con las líneas del pedido');
verify((int) $pdo->query("SELECT COUNT(*) FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE()")->fetchColumn() === 3, 'Tres claves foráneas creadas');

<?php
require __DIR__ . '/includes/bootstrap.php';
if (uid()) redirect('mi-cuenta.php');
$errors = []; $email = '';
// Solo se admiten destinos internos concretos, evitando redirecciones abiertas.
$next = ($_GET['next'] ?? '') === 'checkout' ? 'checkout.php' : 'mi-cuenta.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf(); $email = strtolower(post('email'));
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    $stmt = db()->prepare('SELECT id, nombre, password FROM usuarios WHERE email = ?');
    $stmt->execute([$email]); $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            db()->prepare('UPDATE usuarios SET password = ? WHERE id = ?')->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
        }
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = (int) $user['id'];
        $_SESSION['nombre'] = $user['nombre'];
        unset($_SESSION['csrf'], $_SESSION['checkout_token']);
        redirect($next);
    }
    $errors[] = 'Correo o contraseña incorrectos.';
}
$title = 'Iniciar sesión'; require __DIR__ . '/includes/header.php';
?>
<div class="auth panel"><p class="eyebrow">Bienvenido de nuevo</p><h1>Iniciar sesión</h1><?php show_errors($errors); ?>
<form method="post"><?php csrf_field(); ?><label class="form-label" for="email">Correo electrónico</label><input class="form-control mb-3" type="email" id="email" name="email" autocomplete="username" required value="<?= e($email) ?>">
<label class="form-label" for="password">Contraseña</label><input class="form-control mb-4" type="password" name="password" id="password" autocomplete="current-password" required><button class="btn btn-primary w-100">Entrar</button></form><p class="mt-3 mb-0">¿Es tu primera visita? <a href="registro.php">Crea tu cuenta</a></p></div>
<?php require __DIR__ . '/includes/footer.php'; ?>

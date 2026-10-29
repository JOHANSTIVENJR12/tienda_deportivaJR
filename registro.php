<?php
require __DIR__ . '/includes/bootstrap.php';
if (uid()) redirect('mi-cuenta.php');
$errors = []; $nombre = ''; $email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $nombre = post('nombre'); $email = strtolower(post('email'));
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    if ($nombre === '' || mb_strlen($nombre) > 100) $errors[] = 'El nombre es obligatorio y debe tener como máximo 100 caracteres.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) $errors[] = 'Introduce un correo electrónico válido.';
    // PASSWORD_DEFAULT usa bcrypt en PHP 8.2: no aceptar truncamientos a 72 bytes.
    if (strlen($password) < 8 || strlen($password) > 72 || str_contains($password, "\0")) $errors[] = 'La contraseña debe ocupar entre 8 y 72 bytes.';
    if ($password !== ($_POST['confirmacion'] ?? null)) $errors[] = 'Las contraseñas no coinciden.';
    if (!$errors) {
        try {
            $stmt = db()->prepare('INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)');
            $stmt->execute([$nombre, $email, password_hash($password, PASSWORD_DEFAULT)]);
            flash('Cuenta creada. Ya puedes iniciar sesión.');
            redirect('login.php');
        } catch (PDOException $error) {
            if (($error->errorInfo[1] ?? null) !== 1062) throw $error;
            $errors[] = 'Ese correo ya tiene una cuenta. Inicia sesión.';
        }
    }
}
$title = 'Crear cuenta'; require __DIR__ . '/includes/header.php';
?>
<div class="auth panel"><p class="eyebrow">Únete a JR</p><h1>Crear cuenta</h1><p>Guarda tus direcciones y consulta tus pedidos.</p><?php show_errors($errors); ?>
<form method="post"><?php csrf_field(); ?><label class="form-label" for="nombre">Nombre</label><input class="form-control mb-3" name="nombre" id="nombre" autocomplete="name" maxlength="100" required value="<?= e($nombre) ?>">
<label class="form-label" for="email">Correo electrónico</label><input class="form-control mb-3" type="email" name="email" id="email" autocomplete="email" maxlength="190" required value="<?= e($email) ?>">
<label class="form-label" for="password">Contraseña</label><input class="form-control mb-2" type="password" id="password" name="password" autocomplete="new-password" minlength="8" required><p class="small text-muted">Entre 8 y 72 bytes. No uses una contraseña personal en esta demostración.</p>
<label class="form-label" for="confirmacion">Repite la contraseña</label><input class="form-control mb-3" type="password" id="confirmacion" name="confirmacion" autocomplete="new-password" required>
<button class="btn btn-primary w-100">Crear cuenta</button></form><p class="mt-3 mb-0">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p></div>
<?php require __DIR__ . '/includes/footer.php'; ?>

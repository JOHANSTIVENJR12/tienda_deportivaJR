<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_set_cookie_params(['httponly' => true, 'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', 'samesite' => 'Lax', 'path' => '/']);
session_start();
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

// El detalle técnico se registra en el servidor; nunca se muestran credenciales.
set_exception_handler(function (Throwable $error): void {
    error_log((string) $error);
    http_response_code(500);
    echo '<h1>No se ha podido completar la operación</h1><p>Comprueba la conexión y la importación de la base de datos. Vuelve a intentarlo.</p>';
});

function e(mixed $value): string { return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function redirect(string $path): never { header('Location: ' . $path, true, 303); exit; }
function uid(): ?int { return isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : null; }
function require_login(): void { if (!uid()) redirect('login.php'); }
function csrf(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
function csrf_field(): void { echo '<input type="hidden" name="csrf" value="' . e(csrf()) . '">'; }
function check_csrf(): void
{
    if (!is_string($_POST['csrf'] ?? null) || !hash_equals(csrf(), $_POST['csrf'])) {
        http_response_code(403);
        exit('Solicitud caducada o no válida. Recarga el formulario.');
    }
}
function post(string $key): string { return is_string($_POST[$key] ?? null) ? trim($_POST[$key]) : ''; }
function flash(string $message): void { $_SESSION['flash'] = $message; }
function money(int $cents): string { return number_format($cents / 100, 2, ',', '.') . ' €'; }

// Un único catálogo en servidor: el navegador nunca decide los precios.
function products(): array
{
    return [
        1 => ['nombre' => 'Camiseta Deportiva roja', 'precio' => 3000, 'imagen' => 'camiseta-1.jpg'],
        2 => ['nombre' => 'Camiseta Deportiva blanca', 'precio' => 3000, 'imagen' => 'camiseta-2.jpg'],
        3 => ['nombre' => 'Nike Superfly', 'precio' => 12000, 'imagen' => 'botasnikesuperfly.jpg'],
        4 => ['nombre' => 'Adidas F50', 'precio' => 12000, 'imagen' => 'f50.jpg'],
        5 => ['nombre' => 'Medias Deportivas', 'precio' => 1500, 'imagen' => 'mediasdeportivas.jpg'],
        6 => ['nombre' => 'Banda Elástica', 'precio' => 800, 'imagen' => 'bandaelastica.jpg'],
    ];
}
function cart_total(): int
{
    $total = 0;
    foreach ($_SESSION['carrito'] ?? [] as $id => $quantity) $total += products()[$id]['precio'] * $quantity;
    return $total;
}
function address_errors(array $data): array
{
    $errors = [];
    foreach (['calle' => 200, 'ciudad' => 100] as $field => $limit) {
        if ($data[$field] === '' || mb_strlen($data[$field]) > $limit) $errors[] = ucfirst($field) . ': introduce un valor válido (máximo ' . $limit . ' caracteres).';
    }
    if (!preg_match('/^[0-9]{5}$/D', $data['cp'])) $errors[] = 'El código postal debe tener cinco cifras (envíos a España).';
    return $errors;
}
function show_errors(array $errors): void
{
    if (!$errors) return;
    echo '<div class="alert alert-danger" role="alert"><ul class="mb-0">';
    foreach ($errors as $error) echo '<li>' . e($error) . '</li>';
    echo '</ul></div>';
}
function address_fields(array $data): void
{
    foreach (['calle' => ['Calle y número', 200], 'ciudad' => ['Ciudad', 100], 'cp' => ['Código postal', 5]] as $field => [$label, $max]) {
        echo '<div class="mb-3"><label class="form-label" for="' . $field . '">' . $label . '</label><input class="form-control" id="' . $field . '" name="' . $field . '" maxlength="' . $max . '" required value="' . e($data[$field] ?? '') . '"' . ($field === 'cp' ? ' inputmode="numeric" pattern="[0-9]{5}"' : '') . '></div>';
    }
}

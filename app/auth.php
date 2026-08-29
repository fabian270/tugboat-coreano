<?php
declare(strict_types=1);

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function cuerpo_json(): array
{
    static $datos = null;
    if ($datos === null) {
        $datos = json_decode(file_get_contents('php://input') ?: '', true) ?: [];
    }
    return $datos;
}

function csrf_check(): void
{
    $enviado = $_POST['csrf'] ?? (cuerpo_json()['csrf'] ?? '');
    $esperado = (string) ($_SESSION['csrf'] ?? '');
    if (!is_string($enviado) || $enviado === '' || !hash_equals($esperado, $enviado)) {
        http_response_code(403);
        exit('Sesión inválida (CSRF). Recarga la página e inténtalo de nuevo.');
    }
}

function usuario_actual(): ?array
{
    static $usuario = null;
    static $cargado = false;
    if (!$cargado) {
        $cargado = true;
        $id = (int) ($_SESSION['usuario_id'] ?? 0);
        if ($id > 0) {
            $stmt = db()->prepare('SELECT * FROM usuarios WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            $fila = $stmt->fetch();
            if ($fila && ((int) $fila['activo'] === 1 || (int) $fila['debe_cambiar_password'] === 1)) {
                $usuario = $fila;
            } else {
                unset($_SESSION['usuario_id']);
            }
        }
    }
    return $usuario;
}

function login(string $nombre_usuario, string $password): bool
{
    $stmt = db()->prepare('SELECT * FROM usuarios WHERE usuario = ? LIMIT 1');
    $stmt->execute([$nombre_usuario]);
    $fila = $stmt->fetch();
    if (!$fila || !password_verify($password, $fila['password_hash'])) {
        return false;
    }
    $pendiente = (int) ($fila['debe_cambiar_password'] ?? 0) === 1;
    if ((int) $fila['activo'] !== 1 && !$pendiente) {
        return false;
    }
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = (int) $fila['id'];
    return true;
}

function logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

function requiere_cambio_password(): void
{
    $u = usuario_actual();
    if ($u !== null && (int) $u['debe_cambiar_password'] === 1) {
        redirigir('cambiar-contrasena');
    }
}

function requiere_login(): void
{
    if (usuario_actual() === null) {
        redirigir('login');
    }
    requiere_cambio_password();
}

function requiere_admin(): void
{
    $u = usuario_actual();
    if ($u === null || $u['rol'] !== 'admin') {
        redirigir('inicio');
    }
    requiere_cambio_password();
}

function es_admin(): bool
{
    $u = usuario_actual();
    return $u !== null && $u['rol'] === 'admin';
}

function nombre_sesion(): string
{
    $u = usuario_actual();
    return $u['nombre'] ?? '';
}

function generar_password(): string
{
    $aces = 'abcdefghjkmnpqrstuvwxyz23456789';
    $pw = '';
    for ($i = 0; $i < 8; $i++) {
        $pw .= $aces[random_int(0, strlen($aces) - 1)];
    }
    return $pw;
}
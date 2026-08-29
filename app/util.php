<?php
declare(strict_types=1);

function url(string $ruta = ''): string
{
    $ruta = ltrim($ruta, '/');
    return '/' . $ruta;
}

function asset(string $ruta): string
{
    return '/' . ltrim($ruta, '/') . '?v=' . VERSION_ASSETS;
}

function redirigir(string $ruta): void
{
    header('Location: ' . url($ruta));
    exit;
}

function e(?string $texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

function flash(string $clave, ?string $valor = null): ?string
{
    if ($valor !== null) {
        $_SESSION['flash'][$clave] = $valor;
        return null;
    }
    $v = $_SESSION['flash'][$clave] ?? null;
    unset($_SESSION['flash'][$clave]);
    return $v;
}

function json_out(array $datos, int $codigo = 200): void
{
    http_response_code($codigo);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

function es_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}
<?php
declare(strict_types=1);

function procesar_login(): void
{
    if (usuario_actual() !== null) {
        redirigir('inicio');
    }

    $error = null;
    if (es_post()) {
        csrf_check();
        $nombre_usuario = trim((string) ($_POST['usuario'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($nombre_usuario === '' || $password === '') {
            $error = 'Ingresa tu usuario y contraseña.';
        } elseif (login($nombre_usuario, $password)) {
            redirigir('inicio');
        } else {
            $error = 'Usuario o contraseña incorrectos.';
        }
    }

    $titulo = 'Iniciar sesión';
    require BASE_DIR . '/vistas/login.php';
}

function procesar_logout(): void
{
    if (es_post()) {
        csrf_check();
    }
    logout();
    redirigir('login');
}
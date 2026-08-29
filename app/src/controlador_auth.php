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
            if ((int) (usuario_actual()['debe_cambiar_password'] ?? 0) === 1) {
                redirigir('cambiar-contrasena');
            }
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

function pag_cambiar_contrasena(): void
{
    $u = usuario_actual();
    if ($u === null) {
        redirigir('login');
    }
    if ((int) ($u['debe_cambiar_password'] ?? 0) !== 1) {
        redirigir('inicio');
    }

    $error = null;
    if (es_post()) {
        csrf_check();
        $clave = (string) ($_POST['password'] ?? '');
        $repite = (string) ($_POST['password2'] ?? '');

        if (strlen($clave) < 4) {
            $error = 'La contraseña debe tener al menos 4 caracteres.';
        } elseif ($clave !== $repite) {
            $error = 'Las contraseñas no coinciden.';
        } elseif (password_verify($clave, $u['password_hash'])) {
            $error = 'La nueva contraseña debe ser diferente a la actual.';
        } else {
            $stmt = db()->prepare(
                'UPDATE usuarios SET password_hash = ?, activo = 1, debe_cambiar_password = 0 WHERE id = ?'
            );
            $stmt->execute([password_hash($clave, PASSWORD_DEFAULT), (int) $u['id']]);
            flash('ok', 'Contraseña actualizada. ¡Ya puedes continuar!');
            redirigir('inicio');
        }
    }

    $titulo = 'Cambiar contraseña';
    require BASE_DIR . '/vistas/cambiar_contrasena.php';
}
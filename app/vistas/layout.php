<?php
declare(strict_types=1);

function tema_usuario(): string
{
    $u = usuario_actual();
    return ($u['tema'] ?? '') === 'claro' ? 'claro' : 'oscuro';
}

function inicio_pagina(string $titulo_pagina): void
{
    $usuario = usuario_actual();
    $tema = tema_usuario();
    $es_admin = $usuario !== null && $usuario['rol'] === 'admin';
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="<?= $tema === 'oscuro' ? 'dark' : 'light' ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#212529">
<title><?= e($titulo_pagina) ?> · <?= NOMBRE_APP ?></title>
<link rel="stylesheet" href="<?= asset('assets/vendor/bootstrap/bootstrap.min.css') ?>">
<link rel="stylesheet" href="<?= asset('assets/css/app.css') ?>">
<script>
(function () {
    var t = localStorage.getItem('coreano_tema');
    if (!t) { t = <?= json_encode($tema) ?>; }
    document.documentElement.dataset.bsTheme = t === 'claro' ? 'light' : 'dark';
})();
</script>
</head>
<body data-tema="<?= e($tema) ?>">
<nav class="navbar navbar-expand-md bg-body-tertiary sticky-top app-navbar">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= url('inicio') ?>">
            <span class="logo-hangul me-1">한</span><?= NOMBRE_APP ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal"
                aria-controls="menuPrincipal" aria-expanded="false" aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= url('inicio') ?>">Inicio</a></li>
                <?php if ($es_admin): ?>
                <li class="nav-item"><a class="nav-link" href="<?= url('admin/cartillas') ?>">Cartillas</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= url('admin/usuarios') ?>">Usuarios</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= url('admin/progreso') ?>">Progreso</a></li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav align-items-md-center">
                <li class="nav-item me-md-2">
                    <button id="btn-tema" class="btn btn-sm btn-outline-secondary" type="button" aria-label="Cambiar tema">
                        <span id="icono-tema"></span>
                    </button>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?= e($usuario['nombre']) ?>
                        <?php if ($es_admin): ?><span class="badge text-bg-warning ms-1">admin</span><?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text small text-body-secondary"><?= e($usuario['usuario']) ?></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="post" action="<?= url('logout') ?>">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <button class="dropdown-item" type="submit">Cerrar sesión</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<main class="container py-4">
<?php
    $aviso = flash('ok');
    $error = flash('error');
    if ($aviso !== null):
?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= e($aviso) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
<?php
    endif;
    if ($error !== null):
?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= e($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
<?php
    endif;
}

function fin_pagina(): void
{
?>
</main>
<script src="<?= asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= asset('assets/js/app.js') ?>"></script>
</body>
</html>
<?php
}
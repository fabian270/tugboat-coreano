<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#212529">
<title><?= e($titulo ?? 'Cambiar contraseña') ?> · <?= NOMBRE_APP ?></title>
<link rel="stylesheet" href="<?= asset('assets/vendor/bootstrap/bootstrap.min.css') ?>">
<link rel="stylesheet" href="<?= asset('assets/css/app.css') ?>">
<script>
(function () {
    var t = localStorage.getItem('coreano_tema');
    if (!t) { t = 'oscuro'; }
    document.documentElement.dataset.bsTheme = t === 'claro' ? 'light' : 'dark';
})();
</script>
</head>
<body data-tema="oscuro" class="pantalla-login">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-9 col-md-6 col-lg-4">
            <div class="text-center mb-4">
                <div class="logo-hangul logo-grande mx-auto mb-2">한국</div>
                <h1 class="h3 fw-bold mb-1"><?= NOMBRE_APP ?></h1>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 mb-1">Cambia tu contraseña</h2>
                    <p class="text-body-secondary small mb-4">
                        Hola, <?= e($u['nombre']) ?>. Tu cuenta exige cambiar la
                        contraseña antes de continuar.
                    </p>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger py-2" role="alert"><?= e($error) ?></div>
                    <?php endif; ?>
                    <form method="post" action="<?= url('cambiar-contrasena') ?>" novalidate>
                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                        <div class="mb-3">
                            <label class="form-label" for="password">Nueva contraseña</label>
                            <input class="form-control form-control-lg" type="password" id="password"
                                   name="password" autocomplete="new-password" required minlength="4" autofocus>
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="password2">Repite la contraseña</label>
                            <input class="form-control form-control-lg" type="password" id="password2"
                                   name="password2" autocomplete="new-password" required minlength="4">
                        </div>
                        <button class="btn btn-primary btn-lg w-100" type="submit">Guardar y continuar</button>
                    </form>
                    <div class="text-center mt-3">
                        <form method="post" action="<?= url('logout') ?>" class="d-inline">
                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                            <button class="btn btn-link btn-sm" type="submit">Cerrar sesión</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= asset('assets/js/app.js') ?>"></script>
</body>
</html>
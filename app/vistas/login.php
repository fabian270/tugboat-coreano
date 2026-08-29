<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#212529">
<title><?= e($titulo ?? 'Iniciar sesión') ?> · <?= NOMBRE_APP ?></title>
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
                <p class="text-body-secondary mb-0">Aprende coreano con cartillas de fichas</p>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 mb-4">Iniciar sesión</h2>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger py-2" role="alert"><?= e($error) ?></div>
                    <?php endif; ?>
                    <form method="post" action="<?= url('login') ?>" novalidate>
                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                        <div class="mb-3">
                            <label class="form-label" for="usuario">Usuario</label>
                            <input class="form-control form-control-lg" type="text" id="usuario" name="usuario"
                                   autocomplete="username" required autofocus inputmode="text">
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="password">Contraseña</label>
                            <input class="form-control form-control-lg" type="password" id="password" name="password"
                                   autocomplete="current-password" required>
                        </div>
                        <button class="btn btn-primary btn-lg w-100" type="submit">Entrar</button>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <button id="btn-tema" class="btn btn-sm btn-outline-secondary" type="button">
                    <span id="icono-tema"></span>
                </button>
            </div>
        </div>
    </div>
</div>
<script src="<?= asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= asset('assets/js/app.js') ?>"></script>
</body>
</html>
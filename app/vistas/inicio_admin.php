<?php
declare(strict_types=1);
inicio_pagina($titulo);
?>
<div class="mb-4">
    <h1 class="h3 mb-1">Panel del administrador</h1>
    <p class="text-body-secondary mb-0">Resumen general de la aplicación.</p>
</div>

<div class="row g-3">
    <div class="col-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="fs-2 fw-bold"><?= $totales['usuarios'] ?></div>
                <div class="text-body-secondary">Usuarios</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="fs-2 fw-bold"><?= $totales['cartillas'] ?></div>
                <div class="text-body-secondary">Cartillas</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="fs-2 fw-bold"><?= $totales['fichas'] ?></div>
                <div class="text-body-secondary">Fichas</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="fs-2 fw-bold"><?= $totales['registros'] ?></div>
                <div class="text-body-secondary">Respuestas registradas</div>
            </div>
        </div>
    </div>
</div>

<h2 class="h5 mt-4 mb-3">Accesos rápidos</h2>
<div class="d-flex flex-column flex-sm-row gap-2">
    <a class="btn btn-primary" href="<?= url('admin/cartillas') ?>">Administrar cartillas y fichas</a>
    <a class="btn btn-outline-primary" href="<?= url('admin/usuarios') ?>">Administrar usuarios</a>
    <a class="btn btn-outline-primary" href="<?= url('admin/progreso') ?>">Ver progreso de alumnos</a>
</div>
<?php fin_pagina(); ?>
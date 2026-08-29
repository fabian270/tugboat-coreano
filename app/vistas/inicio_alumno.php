<?php
declare(strict_types=1);
inicio_pagina($titulo);
?>
<div class="mb-4">
    <h1 class="h3 mb-1">¡Hola, <?= e($usuario['nombre']) ?>!</h1>
    <p class="text-body-secondary mb-0">Elige una cartilla para estudiar.</p>
</div>

<?php if (!$cartillas): ?>
    <div class="alert alert-info" role="alert">
        Todavía no hay cartillas. Pídele al administrador que cree algunas.
    </div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($cartillas as $c):
            $total = (int) $c['total'];
            $dominadas = (int) $c['dominadas'];
            $pct = $total > 0 ? (int) round($dominadas * 100 / $total) : 0;
        ?>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card card-cartilla h-100">
                <span class="cinta-color text-bg-<?= e($c['color']) ?>"></span>
                <div class="card-body d-flex flex-column">
                    <h2 class="h5 card-title d-flex align-items-center gap-2">
                        <span class="badge text-bg-<?= e($c['color']) ?>">ㅎ</span>
                        <?= e($c['titulo']) ?>
                    </h2>
                    <p class="card-text small text-body-secondary flex-grow-1"><?= e($c['descripcion']) ?></p>
                    <?php if ($total > 0): ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>Dominas <?= $dominadas ?> de <?= $total ?></span>
                                <span><?= $pct ?>%</span>
                            </div>
                            <div class="progress" role="progressbar" aria-valuenow="<?= $pct ?>"
                                 aria-valuemin="0" aria-valuemax="100" style="height: 8px">
                                <div class="progress-bar" style="width: <?= $pct ?>%"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="d-flex gap-2">
                        <a class="btn btn-primary flex-fill" href="<?= url('estudiar?id=' . $c['id']) ?>">Estudiar</a>
                        <?php if ($total > 0 && $dominadas < $total): ?>
                            <a class="btn btn-outline-warning flex-fill"
                               href="<?= url('estudiar?id=' . $c['id'] . '&solo=repasar') ?>">Repasar</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php fin_pagina(); ?>
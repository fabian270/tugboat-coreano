<?php
declare(strict_types=1);
inicio_pagina($titulo);
?>
<h1 class="h3 mb-4">Progreso de alumnos</h1>

<form method="get" action="<?= url('admin/progreso') ?>" class="mb-4">
    <div class="row g-2 align-items-end">
        <div class="col-12 col-sm-8 col-md-6 col-lg-4">
            <label class="form-label" for="sel-usuario">Elegir usuario</label>
            <select class="form-select" id="sel-usuario" name="usuario" onchange="this.form.submit()">
                <?php foreach ($usuarios as $u): ?>
                    <option value="<?= (int) $u['id'] ?>" <?= $seleccion && (int) $seleccion['id'] === (int) $u['id'] ? 'selected' : '' ?>>
                        <?= e($u['nombre']) ?> (<?= $u['rol'] === 'admin' ? 'admin' : 'alumno' ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</form>

<?php if ($seleccion === null): ?>
    <div class="alert alert-info" role="alert">No hay usuarios para consultar.</div>
<?php else: ?>
    <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
        <h2 class="h5 mb-0"><?= e($seleccion['nombre']) ?></h2>
        <?php if ($resumen['total'] > 0): ?>
            <?php $pct = (int) round($resumen['dominadas'] * 100 / $resumen['total']); ?>
            <div class="flex-grow-1" style="max-width: 420px">
                <div class="d-flex justify-content-between small mb-1">
                    <span>Dominadas <?= $resumen['dominadas'] ?> de <?= $resumen['total'] ?> fichas</span>
                    <span><?= $pct ?>%</span>
                </div>
                <div class="progress" role="progressbar" aria-valuenow="<?= $pct ?>" aria-valuemin="0"
                     aria-valuemax="100" style="height: 10px">
                    <div class="progress-bar" style="width: <?= $pct ?>%"></div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!$por_cartilla): ?>
        <div class="alert alert-info" role="alert">Sin datos todavía.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Cartilla</th>
                        <th class="text-center">Fichas</th>
                        <th class="text-center">Dominadas</th>
                        <th class="text-center">Repasar</th>
                        <th class="text-center">Sin ver</th>
                        <th>Avance</th>
                        <th class="text-end">Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($por_cartilla as $c): ?>
                        <?php
                        $total = (int) $c['total'];
                        $dominadas = (int) $c['dominadas'];
                        $repasar = (int) $c['repasar'];
                        $sin_ver = (int) $c['sin_ver'];
                        $pct = $total > 0 ? (int) round($dominadas * 100 / $total) : 0;
                        $cartilla_activa = $cartilla_id > 0 && $cartilla_id === (int) $c['id'];
                        ?>
                    <tr <?= $cartilla_activa ? 'class="table-active"' : '' ?>>
                        <td class="fw-semibold"><?= e($c['titulo']) ?></td>
                        <td class="text-center"><?= $total ?></td>
                        <td class="text-center text-success fw-semibold"><?= $dominadas ?></td>
                        <td class="text-center text-warning"><?= $repasar ?></td>
                        <td class="text-center text-body-secondary"><?= $sin_ver ?></td>
                        <td style="min-width: 120px">
                            <div class="progress" role="progressbar" aria-valuenow="<?= $pct ?>" aria-valuemin="0"
                                 aria-valuemax="100" style="height: 8px">
                                <div class="progress-bar" style="width: <?= $pct ?>%"></div>
                            </div>
                            <small class="text-body-secondary"><?= $pct ?>%</small>
                        </td>
                        <td class="text-end">
                            <a class="btn btn-sm <?= $cartilla_activa ? 'btn-secondary' : 'btn-outline-secondary' ?>"
                               href="<?= url('admin/progreso?usuario=' . $seleccion['id'] . ($cartilla_activa ? '' : '&cartilla=' . $c['id'])) ?>">
                                <?= $cartilla_activa ? 'Ocultar' : 'Ver' ?>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php if ($detalle): ?>
        <?php $cartilla_titulo = $por_cartilla ? current(array_filter($por_cartilla, fn($c) => (int) $c['id'] === $cartilla_id)) : null; ?>
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Fichas de «<?= e($cartilla_titulo ? $cartilla_titulo['titulo'] : '') ?>»</strong>
                <span class="text-body-secondary small">estado por ficha</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Hangul</th>
                            <th>Traducción</th>
                            <th class="text-center">Vistas</th>
                            <th>Estado</th>
                            <th>Última actualización</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalle as $d): ?>
                        <tr>
                            <td class="hangul-tabla"><?= e($d['hangul']) ?></td>
                            <td><?= e($d['traduccion']) ?></td>
                            <td class="text-center"><?= (int) $d['vistas'] ?></td>
                            <td>
                                <?php if ($d['estado'] === 'lo_se'): ?>
                                    <span class="badge text-bg-success">Lo sé</span>
                                <?php elseif ($d['estado'] === 'repasar'): ?>
                                    <span class="badge text-bg-warning">Repasar</span>
                                <?php else: ?>
                                    <span class="badge text-bg-secondary">Sin ver</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-body-secondary small">
                                <?= $d['actualizado_en'] ? e($d['actualizado_en']) : '—' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php fin_pagina(); ?>
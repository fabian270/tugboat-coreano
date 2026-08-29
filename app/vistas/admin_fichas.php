<?php
declare(strict_types=1);
inicio_pagina($titulo);
?>
<div class="d-flex align-items-center gap-2 mb-4">
    <a class="btn btn-sm btn-outline-secondary" href="<?= url('admin/cartillas') ?>">← Cartillas</a>
    <div>
        <h1 class="h4 mb-0"><?= e($cartilla['titulo']) ?></h1>
        <p class="text-body-secondary small mb-0"><?= e($cartilla['descripcion']) ?></p>
    </div>
</div>

<?php if ($editar): ?>
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">Editar ficha #<?= (int) $editar['id'] ?></h2>
            <form method="post" action="<?= url('admin/fichas?cartilla=' . $cartilla['id']) ?>">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id" value="<?= (int) $editar['id'] ?>">
                <input type="hidden" name="cartilla_id" value="<?= (int) $cartilla['id'] ?>">
                <?php require BASE_DIR . '/vistas/_form_ficha.php'; ?>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Guardar cambios</button>
                    <a class="btn btn-outline-secondary"
                       href="<?= url('admin/fichas?cartilla=' . $cartilla['id']) ?>">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
<?php else: ?>
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">Nueva ficha</h2>
            <form method="post" action="<?= url('admin/fichas?cartilla=' . $cartilla['id']) ?>">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="accion" value="crear">
                <input type="hidden" name="cartilla_id" value="<?= (int) $cartilla['id'] ?>">
                <?php require BASE_DIR . '/vistas/_form_ficha.php'; ?>
                <button class="btn btn-primary" type="submit">Agregar ficha</button>
            </form>
        </div>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-2">
    <h2 class="h5 mb-0">Fichas de la cartilla</h2>
    <span class="text-body-secondary small"><?= count($fichas) ?> ficha(s)</span>
</div>

<?php if (!$fichas): ?>
    <div class="alert alert-info" role="alert">Esta cartilla no tiene fichas todavía.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Hangul</th>
                    <th>Romanización</th>
                    <th>Traducción</th>
                    <th>Ejemplo</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fichas as $f): ?>
                <tr>
                    <td class="hangul-tabla"><?= e($f['hangul']) ?></td>
                    <td class="text-body-secondary"><em><?= e($f['romanizacion']) ?></em></td>
                    <td><?= e($f['traduccion']) ?></td>
                    <td class="text-body-secondary small">
                        <?php if ($f['ejemplo'] !== ''): ?>
                            <?= e($f['ejemplo']) ?><?= $f['ejemplo_traduccion'] !== '' ? ' · ' . e($f['ejemplo_traduccion']) : '' ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="d-flex gap-1 justify-content-end flex-wrap">
                            <a class="btn btn-sm btn-outline-primary"
                               href="<?= url('admin/fichas?cartilla=' . $cartilla['id'] . '&editar=' . $f['id']) ?>">Editar</a>
                            <form method="post"
                                  action="<?= url('admin/fichas?cartilla=' . $cartilla['id']) ?>"
                                  onsubmit="return confirm('¿Eliminar esta ficha?');">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="id" value="<?= (int) $f['id'] ?>">
                                <input type="hidden" name="cartilla_id" value="<?= (int) $cartilla['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php fin_pagina(); ?>
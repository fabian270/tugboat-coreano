<?php
declare(strict_types=1);
inicio_pagina($titulo);
?>
<h1 class="h3 mb-1">Cartillas</h1>
<p class="text-body-secondary mb-4">Cada cartilla agrupa fichas de estudio. Usa «Ver fichas» para agregarlas.</p>

<?php if ($editar): ?>
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">Editar «<?= e($editar['titulo']) ?>»</h2>
            <form method="post" action="<?= url('admin/cartillas') ?>">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id" value="<?= (int) $editar['id'] ?>">
                <?php require BASE_DIR . '/vistas/_form_cartilla.php'; ?>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Guardar cambios</button>
                    <a class="btn btn-outline-secondary" href="<?= url('admin/cartillas') ?>">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
<?php else: ?>
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">Nueva cartilla</h2>
            <form method="post" action="<?= url('admin/cartillas') ?>">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="accion" value="crear">
                <?php require BASE_DIR . '/vistas/_form_cartilla.php'; ?>
                <button class="btn btn-primary" type="submit">Crear cartilla</button>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php if (!$cartillas): ?>
    <div class="alert alert-info" role="alert">Aún no hay cartillas. Crea la primera.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Color</th>
                    <th class="text-center">Fichas</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartillas as $c): ?>
                <tr>
                    <td class="fw-semibold"><?= e($c['titulo']) ?></td>
                    <td class="text-body-secondary small"><?= e($c['descripcion']) ?></td>
                    <td><span class="badge text-bg-<?= e($c['color']) ?>">&nbsp;</span></td>
                    <td class="text-center"><?= (int) $c['fichas'] ?></td>
                    <td>
                        <div class="d-flex gap-1 justify-content-end flex-wrap">
                            <a class="btn btn-sm btn-primary"
                               href="<?= url('admin/fichas?cartilla=' . $c['id']) ?>">Ver fichas</a>
                            <a class="btn btn-sm btn-outline-primary"
                               href="<?= url('admin/cartillas?editar=' . $c['id']) ?>">Editar</a>
                            <form method="post" action="<?= url('admin/cartillas') ?>"
                                  onsubmit="return confirm('¿Eliminar la cartilla «<?= e(addslashes($c['titulo'])) ?>» y todas sus fichas?');">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
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
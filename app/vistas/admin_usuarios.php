<?php
declare(strict_types=1);
inicio_pagina($titulo);
?>
<h1 class="h3 mb-1">Usuarios</h1>
<p class="text-body-secondary mb-4">Crea y administra las cuentas de los alumnos y otros administradores.</p>

<?php if ($editar): ?>
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">Editar a «<?= e($editar['nombre']) ?>»</h2>
            <form method="post" action="<?= url('admin/usuarios') ?>">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id" value="<?= (int) $editar['id'] ?>">
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <label class="form-label">Nombre completo</label>
                        <input class="form-control" type="text" name="nombre" value="<?= e($editar['nombre']) ?>" required>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label">Usuario (login)</label>
                        <input class="form-control" type="text" name="usuario" value="<?= e($editar['usuario']) ?>" required>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label">Rol</label>
                        <select class="form-select" name="rol">
                            <option value="alumno" <?= $editar['rol'] === 'alumno' ? 'selected' : '' ?>>Alumno</option>
                            <option value="admin" <?= $editar['rol'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label">Nueva contraseña <span class="text-body-secondary">(vacía = no cambiar)</span></label>
                        <input class="form-control" type="password" name="password" autocomplete="new-password">
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="activo" id="usuario-activo"
                                   value="1" <?= (int) $editar['activo'] === 1 ? 'checked' : '' ?>>
                            <label class="form-check-label" for="usuario-activo">Cuenta activa (puede iniciar sesión)</label>
                        </div>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button class="btn btn-primary" type="submit">Guardar cambios</button>
                        <a class="btn btn-outline-secondary" href="<?= url('admin/usuarios') ?>">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php else: ?>
    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">Nuevo usuario</h2>
            <form method="post" action="<?= url('admin/usuarios') ?>">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="accion" value="crear">
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <label class="form-label">Nombre completo</label>
                        <input class="form-control" type="text" name="nombre" required>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label">Usuario (login)</label>
                        <input class="form-control" type="text" name="usuario" required>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label">Rol</label>
                        <select class="form-select" name="rol">
                            <option value="alumno" selected>Alumno</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label">Contraseña <span class="text-body-secondary">(vacía = se genera)</span></label>
                        <input class="form-control" type="password" name="password" autocomplete="new-password">
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary" type="submit">Crear usuario</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php if (!$usuarios): ?>
    <div class="alert alert-info" role="alert">Aún no hay usuarios.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th class="text-center">Fichas dominadas</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= e($u['nombre']) ?></td>
                    <td><code><?= e($u['usuario']) ?></code></td>
                    <td>
                        <?php if ($u['rol'] === 'admin'): ?>
                            <span class="badge text-bg-warning">Admin</span>
                        <?php else: ?>
                            <span class="badge text-bg-primary">Alumno</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ((int) $u['activo'] === 1): ?>
                            <span class="badge text-bg-success">Activo</span>
                        <?php else: ?>
                            <span class="badge text-bg-secondary">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?= (int) $u['dominadas'] ?></td>
                    <td>
                        <div class="d-flex gap-1 justify-content-end flex-wrap">
                            <a class="btn btn-sm btn-outline-primary"
                               href="<?= url('admin/usuarios?editar=' . $u['id']) ?>">Editar</a>
                            <form method="post" action="<?= url('admin/usuarios') ?>">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="accion" value="reset_password">
                                <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                <button class="btn btn-sm btn-outline-warning" type="submit"
                                        title="Generar nueva contraseña">Nueva contraseña</button>
                            </form>
                            <form method="post" action="<?= url('admin/usuarios') ?>">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="accion" value="activar">
                                <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                <button class="btn btn-sm btn-outline-secondary" type="submit">
                                    <?= (int) $u['activo'] === 1 ? 'Desactivar' : 'Activar' ?>
                                </button>
                            </form>
                            <form method="post" action="<?= url('admin/usuarios') ?>"
                                  onsubmit="return confirm('¿Eliminar a «<?= e(addslashes($u['nombre'])) ?>» y su progreso?');">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
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
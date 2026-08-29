<?php
declare(strict_types=1);
$val = $editar ?? [];
$titulo_val = $val['titulo'] ?? '';
$desc_val = $val['descripcion'] ?? '';
$color_val = $val['color'] ?? 'primary';
$orden_val = (int) ($val['orden'] ?? 0);
?>
<div class="row g-3 mb-3">
    <div class="col-12 col-sm-6">
        <label class="form-label" for="cartilla-titulo">Título</label>
        <input class="form-control" type="text" id="cartilla-titulo" name="titulo"
               value="<?= e($titulo_val) ?>" required autocomplete="off">
    </div>
    <div class="col-12 col-sm-3">
        <label class="form-label" for="cartilla-color">Color</label>
        <select class="form-select" id="cartilla-color" name="color">
            <?php foreach (colores_cartilla() as $color): ?>
                <option value="<?= e($color) ?>" <?= $color === $color_val ? 'selected' : '' ?>>
                    <?= e(ucfirst($color)) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-12 col-sm-3">
        <label class="form-label" for="cartilla-orden">Orden</label>
        <input class="form-control" type="number" id="cartilla-orden" name="orden"
               value="<?= $orden_val ?>" min="0">
    </div>
    <div class="col-12">
        <label class="form-label" for="cartilla-descripcion">Descripción</label>
        <textarea class="form-control" id="cartilla-descripcion" name="descripcion" rows="2"
                  autocomplete="off"><?= e($desc_val) ?></textarea>
    </div>
</div>
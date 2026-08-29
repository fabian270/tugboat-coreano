<?php
declare(strict_types=1);
$val = $editar ?? [];
$hangul_val = $val['hangul'] ?? '';
$roman_val = $val['romanizacion'] ?? '';
$trad_val = $val['traduccion'] ?? '';
$ejemplo_val = $val['ejemplo'] ?? '';
$ejemplo_trad_val = $val['ejemplo_traduccion'] ?? '';
$orden_val = (int) ($val['orden'] ?? 0);
?>
<div class="row g-3 mb-3">
    <div class="col-12 col-sm-4">
        <label class="form-label" for="ficha-hangul">Hangul (coreano, frente)</label>
        <input class="form-control" type="text" id="ficha-hangul" name="hangul"
               value="<?= e($hangul_val) ?>" required autocomplete="off" placeholder="Ej. 안녕하세요">
    </div>
    <div class="col-12 col-sm-4">
        <label class="form-label" for="ficha-romanizacion">Romanización</label>
        <input class="form-control" type="text" id="ficha-romanizacion" name="romanizacion"
               value="<?= e($roman_val) ?>" autocomplete="off" placeholder="Ej. annyeonghaseyo">
    </div>
    <div class="col-12 col-sm-4">
        <label class="form-label" for="ficha-orden">Orden</label>
        <input class="form-control" type="number" id="ficha-orden" name="orden"
               value="<?= $orden_val ?>" min="0">
    </div>
    <div class="col-12">
        <label class="form-label" for="ficha-traduccion">Traducción / significado (dorso)</label>
        <input class="form-control" type="text" id="ficha-traduccion" name="traduccion"
               value="<?= e($trad_val) ?>" required autocomplete="off" placeholder="Ej. Hola (formal)">
    </div>
    <div class="col-12 col-sm-6">
        <label class="form-label" for="ficha-ejemplo">Ejemplo en coreano <span class="text-body-secondary">(opcional)</span></label>
        <input class="form-control" type="text" id="ficha-ejemplo" name="ejemplo"
               value="<?= e($ejemplo_val) ?>" autocomplete="off">
    </div>
    <div class="col-12 col-sm-6">
        <label class="form-label" for="ficha-ejemplo-trad">Traducción del ejemplo <span class="text-body-secondary">(opcional)</span></label>
        <input class="form-control" type="text" id="ficha-ejemplo-trad" name="ejemplo_traduccion"
               value="<?= e($ejemplo_trad_val) ?>" autocomplete="off">
    </div>
</div>
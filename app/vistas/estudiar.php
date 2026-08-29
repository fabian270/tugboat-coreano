<?php
declare(strict_types=1);
inicio_pagina($titulo);
?>
<div class="estudio">
    <div class="d-flex align-items-center gap-2 mb-3">
        <a class="btn btn-sm btn-outline-secondary" href="<?= url('inicio') ?>">← Cartillas</a>
        <h1 class="h5 mb-0 text-center flex-grow-1 text-truncate"><?= e($cartilla['titulo']) ?></h1>
        <span class="btn btn-sm btn-outline-secondary disabled" id="contador-ficha">– / –</span>
    </div>

    <div class="progress mb-3" style="height: 6px">
        <div class="progress-bar" id="barra-estudio" style="width: 0%"></div>
    </div>

    <div id="zona-tarjeta">
        <div id="tarjeta" class="flashcard" role="button" tabindex="0"
             aria-label="Toca para voltear la tarjeta">
            <span class="flip-inner">
                <span class="flip-cara cara-frente">
                    <span class="hangul" id="txt-hangul"></span>
                    <span class="romanizacion text-body-secondary" id="txt-romanizacion"></span>
                </span>
                <span class="flip-cara cara-dorso">
                    <span class="traduccion" id="txt-traduccion"></span>
                    <span class="ejemplo" id="txt-ejemplo"></span>
                </span>
            </span>
        </div>
    </div>

    <div id="zona-ver-respuesta" class="my-3">
        <button type="button" id="btn-ver-respuesta" class="btn btn-lg btn-outline-info w-100">
            Toca para ver la respuesta
        </button>
    </div>

    <div class="botonera d-none my-3" id="botonera">
        <button type="button" class="btn btn-lg btn-outline-warning flex-fill" id="btn-repasar" disabled>Repasar</button>
        <button type="button" class="btn btn-lg btn-success flex-fill" id="btn-lo-se" disabled>Lo sé</button>
    </div>

    <div class="d-none text-center my-5" id="resumen">
        <div class="fs-1 mb-2">✓</div>
        <h2 class="h4 mb-1">¡Cartilla completada!</h2>
        <p class="text-body-secondary mb-1" id="resumen-texto"></p>
        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center mt-4">
            <button type="button" class="btn btn-lg btn-primary" id="btn-repetir-todo">Estudiar todas otra vez</button>
            <button type="button" class="btn btn-lg btn-outline-warning d-none" id="btn-repetir-pendientes">Repasar pendientes</button>
        </div>
    </div>
</div>

<script>
window.COREANO = {
    cartillaId: <?= (int) $cartilla['id'] ?>,
    csrf: <?= json_encode(csrf_token(), JSON_UNESCAPED_UNICODE) ?>,
    solo: <?= json_encode($solo, JSON_THROW_ON_ERROR) ?>
};
</script>
<?php fin_pagina(); ?>
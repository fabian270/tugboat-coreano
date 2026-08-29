(function () {
    'use strict';

    var CONFIG = window.COREANO || { cartillaId: 0, csrf: '', solo: '' };

    /* ================= Tema oscuro / claro ================= */

    function modoActual() {
        return document.documentElement.dataset.bsTheme === 'light' ? 'claro' : 'oscuro';
    }

    function aplicarTema(modo) {
        var oscuro = modo === 'oscuro';
        document.documentElement.dataset.bsTheme = oscuro ? 'dark' : 'light';
        var icono = document.getElementById('icono-tema');
        if (icono) {
            icono.textContent = oscuro ? '☀' : '☾';
        }
    }

    function guardarTema(modo) {
        try {
            localStorage.setItem('coreano_tema', modo);
        } catch (e) { /* sin almacenamiento local */ }
        if (CONFIG.csrf) {
            fetch('/api/tema', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ csrf: CONFIG.csrf, tema: modo })
            }).catch(function () { /* sin red: solo se queda en localStorage */ });
        }
    }

    function iniciarTema() {
        var guardado = null;
        try {
            guardado = localStorage.getItem('coreano_tema');
        } catch (e) { /* ignorar */ }
        var cuerpo = document.body;
        var inicial = guardado ||
            (cuerpo ? cuerpo.getAttribute('data-tema') : 'oscuro') ||
            'oscuro';
        aplicarTema(inicial);

        var boton = document.getElementById('btn-tema');
        if (boton) {
            boton.addEventListener('click', function () {
                var siguiente = modoActual() === 'oscuro' ? 'claro' : 'oscuro';
                aplicarTema(siguiente);
                guardarTema(siguiente);
            });
        }
    }

    /* ================= Estudio de cartillas ================= */

    function barajar(lista) {
        for (var i = lista.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var aux = lista[i];
            lista[i] = lista[j];
            lista[j] = aux;
        }
        return lista;
    }

    function iniciarEstudio() {
        var tarjeta = document.getElementById('tarjeta');
        if (!tarjeta) {
            return;
        }

        var txtHangul = document.getElementById('txt-hangul');
        var txtRoman = document.getElementById('txt-romanizacion');
        var txtTrad = document.getElementById('txt-traduccion');
        var txtEj = document.getElementById('txt-ejemplo');
        var contador = document.getElementById('contador-ficha');
        var barra = document.getElementById('barra-estudio');
        var botonera = document.getElementById('botonera');
        var btnLoSe = document.getElementById('btn-lo-se');
        var btnRepasar = document.getElementById('btn-repasar');
        var zonaVerRespuesta = document.getElementById('zona-ver-respuesta');
        var btnVerRespuesta = document.getElementById('btn-ver-respuesta');
        var resumen = document.getElementById('resumen');
        var resumenTexto = document.getElementById('resumen-texto');
        var btnTodo = document.getElementById('btn-repetir-todo');
        var btnPendientes = document.getElementById('btn-repetir-pendientes');

        var fichas = [];
        var pendientes = [];
        var indice = 0;
        var aciertos = 0;
        var tiempo = 0; // ms de la sesión actual

        function ocultarResumen() {
            resumen.classList.add('d-none');
            btnPendientes.classList.add('d-none');
        }

        function mostrarResumen(mensaje, hayPendientes) {
            resumenTexto.textContent = mensaje;
            resumen.classList.remove('d-none');
            if (hayPendientes) {
                btnPendientes.classList.remove('d-none');
            }
        }

        function cerrarResumen() {
            resumen.classList.add('d-none');
            btnPendientes.classList.add('d-none');
            zonaVerRespuesta.classList.remove('d-none');
            botonera.classList.add('d-none');
        }

        function pintarFicha() {
            var f = fichas[indice];
            txtHangul.textContent = f.hangul;
            txtRoman.textContent = f.romanizacion;
            txtTrad.textContent = f.traduccion;

            if (f.ejemplo) {
                txtEj.textContent = f.ejemplo + (f.ejemplo_traduccion ? ' · ' + f.ejemplo_traduccion : '');
            } else {
                txtEj.textContent = '';
            }

            tarjeta.classList.remove('girada');
            zonaVerRespuesta.classList.remove('d-none');
            botonera.classList.add('d-none');
            btnLoSe.disabled = true;
            btnRepasar.disabled = true;

            contador.textContent = (indice + 1) + ' / ' + fichas.length;
            barra.style.width = Math.round((indice / fichas.length) * 100) + '%';
        }

        function enviar(ficha, estado) {
            return fetch('/api/progreso', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ csrf: CONFIG.csrf, ficha_id: ficha.id, estado: estado })
            }).then(function (r) {
                if (!r.ok) {
                    throw new Error('No se pudo registrar');
                }
            });
        }

        function avanzar() {
            indice++;
            if (indice >= fichas.length) {
                terminar();
            } else {
                pintarFicha();
            }
        }

        function terminar() {
            tarjeta.classList.remove('girada');
            zonaVerRespuesta.classList.add('d-none');
            botonera.classList.add('d-none');
            barra.style.width = '100%';
            contador.textContent = fichas.length + ' / ' + fichas.length;

            var seg = Math.round(tiempo / 1000);
            var min = Math.floor(seg / 60);
            var tiempoTxt = min > 0 ? min + ' min ' + (seg % 60) + ' s' : seg + ' s';
            var mensaje = 'Respondiste ' + fichas.length + ' ficha(s).';

            if (pendientes.length === 0) {
                mostrarResumen(mensaje + ' Dominaste todas. ¡Bien hecho!  (' + tiempoTxt + ')', false);
            } else {
                mostrarResumen(
                    mensaje + ' Repasaste ' + (fichas.length - pendientes.length) +
                    ' y quedaron ' + pendientes.length + ' a repasar.  (' + tiempoTxt + ')',
                    true
                );
            }
        }

        function cargar(pendientesSolo) {
            cerrarResumen();
            tiempo = 0;
            var url = '/api/fichas?cartilla=' + CONFIG.cartillaId + (pendientesSolo ? '&solo=repasar' : '');
            fetch(url)
                .then(function (r) { return r.json(); })
                .then(function (datos) {
                    if (datos.error) {
                        throw new Error(datos.error);
                    }
                    fichas = pendientesSolo ? datos.fichas : barajar(datos.fichas.slice());
                    pendientes = [];
                    indice = 0;
                    aciertos = 0;
                    if (fichas.length === 0) {
                        zonaVerRespuesta.classList.add('d-none');
                        botonera.classList.add('d-none');
                        contador.textContent = '0 / 0';
                        barra.style.width = '0%';
                        if (pendientesSolo) {
                            mostrarResumen('¡No quedan fichas por repasar! Estudiaste todas.', false);
                        } else {
                            mostrarResumen('Esta cartilla no tiene fichas todavía.', false);
                        }
                        return;
                    }
                    pintarFicha();
                })
                .catch(function (err) {
                    alert('No se pudieron cargar las fichas: ' + err.message);
                });
        }

        function continuarCon(pendientesLista) {
            cerrarResumen();
            fichas = barajar(pendientesLista.slice());
            pendientes = [];
            indice = 0;
            aciertos = 0;
            if (fichas.length === 0) {
                terminar();
                return;
            }
            pintarFicha();
        }

        var inicioSesion = Date.now();

        function voltear() {
            if (botonera.classList.contains('d-none') && zonaVerRespuesta.classList.contains('d-none')) {
                return; // en pantalla de resumen
            }
            if (fichas.length === 0) {
                return;
            }
            if (!tarjeta.classList.contains('girada')) {
                tarjeta.classList.add('girada');
                zonaVerRespuesta.classList.add('d-none');
                botonera.classList.remove('d-none');
                btnLoSe.disabled = false;
                btnRepasar.disabled = false;
            }
        }

        tarjeta.addEventListener('click', voltear);
        if (btnVerRespuesta) {
            btnVerRespuesta.addEventListener('click', voltear);
        }
        tarjeta.addEventListener('keydown', function (evento) {
            if (evento.key === 'Enter' || evento.key === ' ') {
                evento.preventDefault();
                voltear();
            }
        });

        btnLoSe.addEventListener('click', function () {
            if (this.disabled) { return; }
            var f = fichas[indice];
            aciertos++;
            this.disabled = true;
            btnRepasar.disabled = true;
            enviar(f, 'lo_se')
                .catch(function () { /* el progreso local igual avanza */ })
                .finally(function () { avanzar(); });
        });

        btnRepasar.addEventListener('click', function () {
            if (this.disabled) { return; }
            var f = fichas[indice];
            pendientes.push(f);
            this.disabled = true;
            btnLoSe.disabled = true;
            enviar(f, 'repasar')
                .catch(function () { /* el progreso local igual avanza */ })
                .finally(function () { avanzar(); });
        });

        btnTodo.addEventListener('click', function () {
            cargar(false);
        });

        btnPendientes.addEventListener('click', function () {
            continuarCon(pendientes);
        });

        setInterval(function () {
            if (!resumen.classList.contains('d-none')) {
                tiempo = Date.now() - inicioSesion;
                inicioSesion = Date.now();
            }
        }, 1000);

        cargar(CONFIG.solo === 'repasar');
    }

    /* ================= Arranque ================= */

    document.addEventListener('DOMContentLoaded', function () {
        iniciarTema();
        iniciarEstudio();
    });
})();
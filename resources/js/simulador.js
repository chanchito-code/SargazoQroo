import { APP, on, state, pronosticoDe, fmt, mesNombre } from './state.js';
import { pintarPlayas } from './pronostico.js';

let temporizador = null;
let ultimoAbort = null;

function controles() {
    return Array.from(document.querySelectorAll('.slider-var[data-var]'));
}

function valoresActuales() {
    const valores = {};
    controles().forEach((bloque) => {
        const input = bloque.querySelector('input[type="range"]');
        valores[bloque.dataset.var] = Number(input.value);
    });
    return valores;
}

function pintarSlider(bloque) {
    const input = bloque.querySelector('input[type="range"]');
    const min = Number(bloque.dataset.min);
    const max = Number(bloque.dataset.max);
    const valor = Number(input.value);
    const pct = ((valor - min) / (max - min)) * 100;

    input.style.setProperty('--pct', `${pct}%`);

    const paso = Number(input.step);
    const texto = paso < 1 ? valor.toFixed(1) : Math.round(valor);
    bloque.querySelector('.var-valor').textContent = `${texto} ${bloque.dataset.unidad}`.trim();
}

function restaurarDefaults() {
    controles().forEach((bloque) => {
        const input = bloque.querySelector('input[type="range"]');
        input.value = bloque.dataset.default;
        pintarSlider(bloque);
    });
}

function resumenTexto(res) {
    const delta = res.delta;
    const base = res.nivel_base.label;
    const nuevo = res.nivel_simulado.label;

    if (Math.abs(delta) <= 1) {
        return `Las condiciones elegidas no alteran el pronóstico base de <b>${res.mes_nombre}</b>:
            el escenario se mantiene en <b>${nuevo}</b> (índice ${res.simulado}).`;
    }

    const direccion = delta < 0 ? 'reduce' : 'incrementa';
    const cambioNivel = base !== nuevo ? ` y el mes pasa de <b>${base}</b> a <b>${nuevo}</b>` : '';

    return `El escenario ${direccion} el índice en <b>${Math.abs(delta)} puntos</b> respecto al pronóstico
        base (${res.base})${cambioNivel}. La variable con mayor influencia es
        <b>${res.mayor_impacto}</b>.`;
}

function pintarResultado(res, playas) {
    document.getElementById('simBase').textContent = res.base;
    document.getElementById('simNivelBase').textContent = res.nivel_base.label;

    const nuevo = document.getElementById('simNuevo');
    nuevo.textContent = res.simulado;
    nuevo.style.color = res.nivel_simulado.color;

    document.getElementById('simNivelNuevo').textContent = res.nivel_simulado.label;

    const bloque = document.getElementById('simBloqueNuevo');
    bloque.style.background = res.nivel_simulado.bg;
    bloque.style.borderColor = `${res.nivel_simulado.color}33`;

    document.getElementById('simFactor').textContent = `×${res.factor}`;
    document.getElementById('simTonBase').textContent = `${fmt(res.toneladas_base)} t`;
    document.getElementById('simTonNueva').textContent = `${fmt(res.toneladas_simuladas)} t`;

    document.getElementById('simResumen').innerHTML =
        `<i class="bi bi-lightbulb-fill me-1"></i> ${resumenTexto(res)}`;

    document.getElementById('simMayorImpacto').textContent = res.mayor_impacto;

    document.getElementById('simImpactos').innerHTML = res.variables
        .map(
            (v) => `
            <div class="impacto-item">
                <span style="min-width:190px">${v.label}</span>
                <span class="track"><i style="width:${Math.max(4, v.contribucion)}%"></i></span>
                <span class="pct">${v.contribucion}%</span>
            </div>`
        )
        .join('');

    pintarPlayas(document.getElementById('simPlayas'), playas.slice(0, 8), false);
}

async function simular() {
    if (ultimoAbort) ultimoAbort.abort();
    ultimoAbort = new AbortController();

    try {
        const respuesta = await fetch(APP.urlSimular, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': APP.csrf,
                Accept: 'application/json',
            },
            signal: ultimoAbort.signal,
            body: JSON.stringify({
                mes: state.mes,
                anio: state.anio,
                valores: valoresActuales(),
            }),
        });

        if (!respuesta.ok) throw new Error('Error en la simulación');

        const datos = await respuesta.json();
        pintarResultado(datos.simulacion, datos.playas);
    } catch (error) {
        if (error.name === 'AbortError') return;
        const caja = document.getElementById('simResumen');
        if (caja) {
            caja.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-1"></i>
                No fue posible calcular el escenario. Verifica que el servidor esté activo.`;
        }
    }
}

function programar() {
    clearTimeout(temporizador);
    temporizador = setTimeout(simular, 250);
}

export function initSimulador() {
    controles().forEach((bloque) => {
        pintarSlider(bloque);
        const input = bloque.querySelector('input[type="range"]');
        input.addEventListener('input', () => {
            pintarSlider(bloque);
            programar();
        });
    });

    document.getElementById('btnReset')?.addEventListener('click', () => {
        restaurarDefaults();
        simular();
    });

    on('mes:changed', simular);
    on('anio:changed', simular);
}

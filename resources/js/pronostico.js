import { APP, on, mesNombre, distribucionDe, pronosticoDe, fmt } from './state.js';

function pintarRecomendaciones(recomendaciones) {
    const cont = document.getElementById('pronReco');
    if (!cont) return;
    cont.innerHTML = recomendaciones
        .map((reco) => `<li><i class="bi bi-check-circle-fill"></i><span>${reco}</span></li>`)
        .join('');
}

function pintarPlayas(cont, playas, conZona = true) {
    if (!cont) return;

    const mapaZonas = {};
    APP.zonas.forEach((z) => {
        mapaZonas[z.key] = z.nombre;
    });

    cont.innerHTML = playas
        .map((p) => {
            const zona = conZona && mapaZonas[p.zona] ? `<small>${mapaZonas[p.zona]}</small>` : '';
            return `
                <div class="playa-row">
                    <span class="pin" style="background:${p.color}"></span>
                    <span class="nombre">${p.nombre}${zona}</span>
                    <span class="idx">${p.indice}</span>
                </div>`;
        })
        .join('');
}

function pintarDistribucion(distribucion) {
    const cont = document.getElementById('listaDistribucion');
    if (!cont) return;
    cont.innerHTML = distribucion
        .map(
            (d) => `
            <div class="impacto-item">
                <span style="min-width:210px">${d.nombre}</span>
                <span class="track"><i style="width:${Math.round(d.peso * 100)}%"></i></span>
                <span class="pct">${fmt(d.toneladas)} t</span>
            </div>`
        )
        .join('');
}

function render() {
    const pron = pronosticoDe();
    if (!pron) return;

    const titulo = document.getElementById('pronMesTitulo');
    if (titulo) titulo.textContent = `${pron.mes_nombre} ${pron.anio}`;

    const toneladas = document.getElementById('pronToneladas');
    if (toneladas) {
        toneladas.innerHTML = `${fmt(pron.toneladas)} <span>toneladas</span>`;
    }

    const detalle = document.getElementById('pronDetalle');
    if (detalle) {
        const signo = pron.vs_promedio >= 0 ? '+' : '';
        detalle.textContent = `${signo}${pron.vs_promedio}% respecto al promedio histórico del mes`;
    }

    const nivel = document.getElementById('pronNivel');
    if (nivel) {
        nivel.style.background = pron.nivel.bg;
        nivel.style.color = pron.nivel.color;
        nivel.innerHTML = `<span class="dot"></span> ${pron.nivel.label}`;
    }

    const indice = document.getElementById('pronIndice');
    if (indice) indice.textContent = pron.indice;

    const dias = document.getElementById('pronDias');
    if (dias) dias.textContent = pron.dias_arribo === 0 ? 'sin arribo' : `${pron.dias_arribo} días`;

    const confianza = document.getElementById('pronConfianza');
    if (confianza) confianza.textContent = pron.confianza;

    const promedio = document.getElementById('pronPromedio');
    if (promedio) promedio.textContent = `${fmt(pron.promedio_historico)} t`;

    const fuente = document.getElementById('pronFuente');
    if (fuente) fuente.textContent = pron.fuente;

    pintarRecomendaciones(pron.recomendaciones);
    pintarPlayas(document.getElementById('pronPlayas'), pron.playas);
    pintarPlayas(document.getElementById('mapaPlayas'), pron.playas, false);
    pintarDistribucion(distribucionDe(pron));
}

export function initPronostico() {
    render();
    on('mes:changed', render);
    on('anio:changed', render);
}

export { pintarPlayas, mesNombre };

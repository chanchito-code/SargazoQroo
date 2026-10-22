import { APP, on, pronosticoDe, fmt, mesNombre, state } from './state.js';

const NS = 'http://www.w3.org/2000/svg';

function renderMarcadores(playas) {
    const grupo = document.getElementById('mapMarkers');
    const tooltip = document.getElementById('mapaTooltip');
    const wrap = document.getElementById('mapaWrap');
    if (!grupo) return;

    grupo.innerHTML = '';

    playas.forEach((playa) => {
        const g = document.createElementNS(NS, 'g');
        g.setAttribute('class', 'marker-playa');
        g.setAttribute('tabindex', '0');
        g.setAttribute('role', 'button');
        g.setAttribute('aria-label', `${playa.nombre}: ${playa.nivel_label}`);

        const halo = document.createElementNS(NS, 'circle');
        halo.setAttribute('cx', playa.x);
        halo.setAttribute('cy', playa.y);
        halo.setAttribute('r', '16');
        halo.setAttribute('fill', playa.color);
        halo.setAttribute('fill-opacity', '0.25');

        const punto = document.createElementNS(NS, 'circle');
        punto.setAttribute('cx', playa.x);
        punto.setAttribute('cy', playa.y);
        punto.setAttribute('r', '10');
        punto.setAttribute('fill', playa.color);
        punto.setAttribute('stroke', '#ffffff');
        punto.setAttribute('stroke-width', '3.5');
        punto.setAttribute('filter', 'url(#sombraMarker)');

        const nucleo = document.createElementNS(NS, 'circle');
        nucleo.setAttribute('cx', playa.x);
        nucleo.setAttribute('cy', playa.y);
        nucleo.setAttribute('r', '3.2');
        nucleo.setAttribute('fill', 'rgba(255,255,255,.9)');

        const titulo = document.createElementNS(NS, 'title');
        titulo.textContent = `${playa.nombre} — ${playa.nivel_label} (${playa.indice}/100)`;

        g.append(halo, punto, nucleo, titulo);

        const mostrar = (evento) => {
            if (!tooltip || !wrap) return;
            const rect = wrap.getBoundingClientRect();
            const x = (evento.clientX ?? rect.left + rect.width / 2) - rect.left;
            const y = (evento.clientY ?? rect.top + rect.height / 2) - rect.top;

            tooltip.innerHTML = `
                <b>${playa.nombre}</b>
                <span style="color:${playa.color};font-weight:800">${playa.nivel_label}</span> ·
                índice ${playa.indice}/100<br>
                <span style="opacity:.75">Mes: ${mesNombre(state.mes)} ${state.anio}</span>`;

            tooltip.classList.add('show');

            const izquierda = Math.min(Math.max(x + 16, 8), rect.width - 244);
            const arriba = Math.min(Math.max(y - 20, 8), rect.height - 96);
            tooltip.style.left = `${izquierda}px`;
            tooltip.style.top = `${arriba}px`;
        };

        const ocultar = () => tooltip?.classList.remove('show');

        g.addEventListener('mouseenter', mostrar);
        g.addEventListener('mousemove', mostrar);
        g.addEventListener('mouseleave', ocultar);
        g.addEventListener('focus', mostrar);
        g.addEventListener('blur', ocultar);
        g.addEventListener('click', mostrar);

        grupo.appendChild(g);
    });
}

function renderTabla(zonas) {
    const cuerpo = document.querySelector('#tablaZonas tbody');
    if (!cuerpo) return;

    cuerpo.innerHTML = zonas
        .map(
            (z) => `
            <tr>
                <td><b>${z.nombre}</b></td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <span class="barra flex-grow-1"><i style="width:${z.indice}%; background:${z.color}"></i></span>
                        <small><b>${z.indice}</b></small>
                    </div>
                </td>
                <td>
                    <span class="badge-nivel" style="background:#eef5f7; color:${z.color}">
                        <span class="dot"></span>${z.nivel_label}
                    </span>
                </td>
            </tr>`
        )
        .join('');
}

function render() {
    const pron = pronosticoDe();
    if (!pron) return;
    renderMarcadores(pron.playas);
    renderTabla(pron.zonas);
}

export function initMapa() {
    render();
    on('mes:changed', render);
    on('anio:changed', render);
}

import { state, on, setMes, setAnio, pronosticoDe, mesNombre, emit } from './state.js';

const TITULOS = {
    dashboard: 'Panel general',
    pronostico: 'Pronóstico por mes',
    mapa: 'Mapa de playas',
    graficas: 'Gráficas históricas',
    simulador: 'Simulador de variables',
};

export function activarTab(tab) {
    document.querySelectorAll('[data-tab]').forEach((btn) => {
        btn.classList.toggle('active', btn.dataset.tab === tab);
    });

    document.querySelectorAll('.tab-pane-app').forEach((pane) => {
        pane.classList.toggle('active', pane.id === `tab-${tab}`);
    });

    const titulo = document.getElementById('topbarTitulo');
    if (titulo) titulo.textContent = TITULOS[tab] || 'Sargazo Q. Roo';

    cerrarSidebar();
    emit('tab:changed', { tab });
}

export function initUI() {
    document.querySelectorAll('[data-tab]').forEach((btn) => {
        btn.addEventListener('click', () => activarTab(btn.dataset.tab));
    });

    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebarApp');
    const backdrop = document.getElementById('backdropApp');

    toggle?.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        backdrop.classList.toggle('show');
    });
    backdrop?.addEventListener('click', cerrarSidebar);

    // Selectores de mes
    document.querySelectorAll('[data-selector-mes] .mes-btn').forEach((btn) => {
        btn.addEventListener('click', () => setMes(Number(btn.dataset.mes)));
    });

    // Selectores de año
    document.querySelectorAll('.anio-switch button').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const anio = Number(btn.dataset.anio);
            document.body.classList.add('cargando');
            try {
                await setAnio(anio);
            } finally {
                document.body.classList.remove('cargando');
            }
        });
    });

    on('mes:changed', sincronizarSelecciones);
    on('anio:changed', sincronizarSelecciones);
    sincronizarSelecciones();
}

function cerrarSidebar() {
    document.getElementById('sidebarApp')?.classList.remove('open');
    document.getElementById('backdropApp')?.classList.remove('show');
}

function sincronizarSelecciones() {
    const pron = pronosticoDe();

    document.querySelectorAll('[data-selector-mes] .mes-btn').forEach((btn) => {
        const mes = Number(btn.dataset.mes);
        btn.classList.toggle('active', mes === state.mes);

        const p = pronosticoDe(mes, state.anio);
        if (p) {
            btn.querySelector('small').textContent = p.nivel.label;
            const barra = btn.querySelector('.mini-bar i');
            if (barra) {
                barra.style.width = `${Math.max(4, p.indice)}%`;
                barra.style.background = p.nivel.color;
            }
            btn.title = `${mesNombre(mes)}: ${p.nivel.label} (${p.indice}/100)`;
        }
    });

    document.querySelectorAll('.anio-switch button').forEach((btn) => {
        btn.classList.toggle('active', Number(btn.dataset.anio) === state.anio);
    });

    document.querySelectorAll('[data-anio-label]').forEach((el) => {
        el.textContent = el.dataset.anioLabel ? `${el.dataset.anioLabel} ${state.anio}` : String(state.anio);
    });

    document.querySelectorAll('[data-mes-label]').forEach((el) => {
        el.textContent = el.dataset.mesLabel ? `${el.dataset.mesLabel} ${mesNombre(state.mes)}` : mesNombre(state.mes);
    });

    const chip = document.getElementById('chipMesActual');
    if (chip && pron) {
        chip.innerHTML = `<i class="bi bi-moon-stars"></i> ${mesNombre(state.mes)} ${state.anio}`;
    }

    const badge = document.getElementById('badgeMesActual');
    if (badge && pron) {
        badge.style.background = pron.nivel.bg;
        badge.style.color = pron.nivel.color;
        badge.innerHTML = `<span class="dot"></span> ${pron.nivel.label}`;
    }
}

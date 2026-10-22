import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'apexcharts/dist/apexcharts.css';
import 'bootstrap';

import { initUI } from './ui.js';
import { initCharts, renderizarPendientes } from './charts.js';
import { initPronostico } from './pronostico.js';
import { initMapa } from './mapa.js';
import { initSimulador } from './simulador.js';

function iniciar() {
    initUI();
    initPronostico();
    initMapa();
    initSimulador();
    initCharts();
    renderizarPendientes();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', iniciar);
} else {
    iniciar();
}

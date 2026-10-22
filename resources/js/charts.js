import ApexCharts from 'apexcharts';
import { APP, state, on, distribucionDe, pronosticoDe, fmt } from './state.js';

window.ApexCharts = ApexCharts;

const PALETA = ['#0fb5ba', '#0b3c5d', '#f97316', '#84cc16', '#eab308', '#7c6df2', '#ef4444'];
const MESES = Object.values(APP.mesesCortos);

const charts = {};
const pendientes = [];

function base(opciones) {
    return {
        chart: {
            toolbar: { show: false },
            fontFamily: 'Nunito, sans-serif',
            animations: { easing: 'easeinout', speed: 550 },
            background: 'transparent',
            stacked: true,
        },
        theme: { mode: 'light' },
        dataLabels: { enabled: false },
        grid: { borderColor: '#e2edf1', strokeDashArray: 4 },
        tooltip: { theme: 'light' },
        ...opciones,
    };
}

function crear(id, opciones) {
    const el = document.getElementById(id);
    if (!el) return null;
    if (charts[id]) {
        charts[id].destroy();
        delete charts[id];
    }
    charts[id] = new ApexCharts(el, base(opciones));
    pendientes.push(() => charts[id].render());
    return charts[id];
}

export function renderizarPendientes() {
    while (pendientes.length) {
        const fn = pendientes.shift();
        try {
            fn();
        } catch (e) {
            console.error('Chart error', e);
        }
    }
}

function redimensionar() {
    Object.values(charts).forEach((c) => {
        try {
            c.windowResize();
        } catch (e) {
            /* ignorar */
        }
    });
}

/* ---------------- gráficas estáticas ---------------- */

function chartTemporada() {
    const seleccionado = [];
    const resto = [];

    for (let mes = 1; mes <= 12; mes++) {
        const p = pronosticoDe(mes, state.anio);
        const valor = p ? p.toneladas : 0;
        if (mes === state.mes) {
            seleccionado.push(valor);
            resto.push(null);
        } else {
            seleccionado.push(null);
            resto.push(valor);
        }
    }

    crear('chartTemporada', {
        series: [
            { name: 'Resto de la temporada', data: resto },
            { name: 'Mes seleccionado', data: seleccionado },
        ],
        colors: ['#9fdde1', '#0b3c5d'],
        chart: { type: 'bar', height: 300, toolbar: { show: false }, stacked: true },
        plotOptions: { bar: { borderRadius: 6, columnWidth: '58%' } },
        xaxis: { categories: MESES, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis: {
            labels: {
                formatter: (v) => (v >= 1000 ? `${Math.round((v / 1000) * 10) / 10}k` : Math.round(v)),
            },
        },
        legend: { show: false },
        tooltip: { shared: false, y: { formatter: (v) => (v == null ? '—' : `${fmt(v)} toneladas`) } },
    });
}

function chartAnios() {
    const oficial = [];
    const estimado = [];

    APP.aniosTotales.forEach((f) => {
        oficial.push(f.oficial ? f.total : null);
        estimado.push(f.oficial ? null : f.total);
    });

    crear('chartAnios', {
        series: [
            { name: 'Dato oficial', data: oficial },
            { name: 'Estimación', data: estimado },
        ],
        colors: ['#0fb5ba', '#bfe6ea'],
        chart: { type: 'bar', height: 300, toolbar: { show: false }, stacked: true },
        plotOptions: { bar: { borderRadius: 7, columnWidth: '62%' } },
        xaxis: {
            categories: APP.aniosTotales.map((f) => String(f.anio)),
            labels: { style: { fontWeight: 700 } },
        },
        yaxis: { labels: { formatter: (v) => (v >= 1000 ? `${Math.round(v / 1000)}k` : Math.round(v)) } },
        legend: { show: false },
        tooltip: { shared: false, y: { formatter: (v) => (v == null ? '—' : `${fmt(v)} toneladas`) } },
    });
}

function chartMensual() {
    const colores = { 2021: '#9fb6c2', 2024: '#84cc16', 2025: '#f97316', 2026: '#0fb5ba' };
    const anchos = APP.serieGrafica.map((s) => (String(s.name) === '2026' ? 4 : 2.5));

    crear('chartMensual', {
        series: APP.serieGrafica.map((s) => ({ name: s.name, data: s.data })),
        colors: APP.serieGrafica.map((s) => colores[Number(s.name)] || '#0b3c5d'),
        chart: { type: 'line', height: 340, toolbar: { show: false }, stacked: false },
        stroke: { curve: 'smooth', width: anchos },
        markers: { size: 0, hover: { size: 6 } },
        xaxis: { categories: MESES, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis: { labels: { formatter: (v) => `${Math.round(v / 1000)}k t` } },
        legend: { position: 'top', fontWeight: 700, markers: { radius: 3 } },
        tooltip: { shared: true, y: { formatter: (v) => `${fmt(v)} toneladas` } },
    });
}

function chartRanking() {
    const orden = [...APP.aniosTotales].sort((a, b) => a.total - b.total);
    const oficial = orden.map((f) => (f.oficial ? f.total : null));
    const estimado = orden.map((f) => (f.oficial ? null : f.total));

    crear('chartRanking', {
        series: [
            { name: 'Oficial', data: oficial },
            { name: 'Estimado', data: estimado },
        ],
        colors: ['#0b3c5d', '#a9c6d3'],
        chart: { type: 'bar', height: 340, toolbar: { show: false }, stacked: true },
        plotOptions: { bar: { borderRadius: 6, horizontal: true, barHeight: '58%' } },
        dataLabels: {
            enabled: true,
            formatter: (v) => (v == null ? '' : fmt(v)),
            style: { fontSize: '11px', fontWeight: 700, colors: ['#fff'] },
            offsetX: -8,
        },
        xaxis: {
            categories: orden.map((f) => String(f.anio)),
            labels: { formatter: (v) => (v >= 1000 ? `${Math.round(v / 1000)}k` : v), style: { fontWeight: 700 } },
        },
        yaxis: { labels: { style: { fontWeight: 800 } } },
        legend: { show: false },
        tooltip: { shared: false, y: { formatter: (v) => (v == null ? '—' : `${fmt(v)} toneladas`) } },
    });
}

/* ---------------- gráficas dinámicas ---------------- */

function gauge(indice, color, nivelLabel) {
    crear('gaugeIndice', {
        series: [indice],
        chart: { type: 'radialBar', height: 260, toolbar: { show: false }, stacked: false },
        colors: [color],
        plotOptions: {
            radialBar: {
                startAngle: -135,
                endAngle: 135,
                hollow: { size: '62%' },
                track: { background: '#eaf2f5', strokeWidth: '100%', margin: 0 },
                dataLabels: {
                    name: { show: true, fontSize: '13px', fontWeight: 700, color: '#6b8794', offsetY: 30 },
                    value: {
                        show: true,
                        fontSize: '40px',
                        fontWeight: 800,
                        color: color,
                        offsetY: -6,
                        formatter: (v) => `${Math.round(v)}`,
                    },
                },
            },
        },
        labels: [nivelLabel || 'Índice'],
    });
}

function donut(id, distribucion) {
    crear(id, {
        series: distribucion.map((d) => d.toneladas),
        labels: distribucion.map((d) => d.nombre.split('(')[0].trim()),
        chart: { type: 'donut', height: 270, toolbar: { show: false }, stacked: false },
        colors: PALETA,
        stroke: { width: 2, colors: ['#fff'] },
        legend: { position: 'bottom', fontSize: '12px', fontWeight: 700, markers: { radius: 3 } },
        dataLabels: {
            formatter: (v) => `${Math.round(v)}%`,
            style: { fontSize: '11px', fontWeight: 700 },
            dropShadow: { enabled: false },
        },
        tooltip: { y: { formatter: (v) => `${fmt(v)} toneladas` } },
        plotOptions: { pie: { donut: { size: '58%' } } },
    });
}

export function refrescarGraficas() {
    const pron = pronosticoDe();
    if (!pron) return;

    chartTemporada();
    gauge(pron.indice, pron.nivel.color, pron.nivel.label);

    const dist = distribucionDe(pron);
    donut('chartDistribucion', dist);
    donut('chartZonasAnio', dist);

    renderizarPendientes();
}

export function initCharts() {
    chartMensual();
    chartAnios();
    chartRanking();
    refrescarGraficas();

    on('mes:changed', refrescarGraficas);
    on('anio:changed', refrescarGraficas);
    on('tab:changed', () => requestAnimationFrame(redimensionar));
}

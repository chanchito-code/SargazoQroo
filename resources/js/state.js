export const APP = window.APP;

export const state = {
    mes: APP.mesInicial,
    anio: APP.anioInicial,
    cache: {
        [APP.anioInicial]: { meses: APP.pronostico },
    },
    listeners: {},
};

export function on(evento, fn) {
    (state.listeners[evento] ||= []).push(fn);
}

export function emit(evento, payload) {
    (state.listeners[evento] || []).forEach((fn) => fn(payload));
}

export const fmt = (n) => Number(n).toLocaleString('es-MX');

export function nivelPorIndice(indice) {
    return APP.niveles.reduce((elegido, nivel) => (indice >= nivel.min ? nivel : elegido), APP.niveles[0]);
}

export function mesNombre(mes) {
    return APP.meses[mes];
}

export function anioInfo(anio) {
    return APP.anios[anio];
}

export function distribucionDe(pronostico) {
    return APP.zonas.map((zona) => ({
        nombre: zona.nombre,
        peso: zona.peso,
        toneladas: Math.round(pronostico.toneladas * zona.peso),
    }));
}

async function descargarAnio(anio) {
    const respuesta = await fetch(APP.urlPrediccion, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': APP.csrf,
            Accept: 'application/json',
        },
        body: JSON.stringify({ anio }),
    });

    if (!respuesta.ok) {
        throw new Error('No fue posible obtener el pronóstico del año ' + anio);
    }

    const datos = await respuesta.json();
    state.cache[anio] = { meses: datos.meses };

    return datos.meses;
}

/** Asegura que el año esté en caché; devuelve los 12 meses. */
export async function cargarAnio(anio) {
    if (state.cache[anio]) {
        return state.cache[anio].meses;
    }
    return await descargarAnio(anio);
}

export function pronosticoDe(mes = state.mes, anio = state.anio) {
    return state.cache[anio]?.meses?.[mes] || null;
}

export async function setAnio(anio) {
    if (anio === state.anio) return;
    await cargarAnio(anio);
    state.anio = anio;
    emit('anio:changed', { anio });
    emit('mes:changed', { mes: state.mes, anio });
}

export function setMes(mes) {
    if (mes === state.mes) return;
    state.mes = mes;
    emit('mes:changed', { mes, anio: state.anio });
}

export async function refrescar() {
    await cargarAnio(state.anio);
    emit('mes:changed', { mes: state.mes, anio: state.anio });
}

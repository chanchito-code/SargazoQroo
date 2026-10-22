<div class="section-heading d-flex flex-wrap justify-content-between align-items-end gap-3">
    <div>
        <h2>Simulador de variables</h2>
        <p>Ajusta las condiciones oceánicas y climáticas para ver cómo cambiaría el pronóstico del mes elegido.</p>
    </div>
    <button class="btn btn-outline-secondary btn-sm rounded-pill px-3" type="button" id="btnReset">
        <i class="bi bi-arrow-counterclockwise me-1"></i> Restablecer valores actuales
    </button>
</div>

<div class="card-app mb-3">
    <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
        <div style="flex: 1 1 420px">
            <div class="card-title-app mb-2"><i class="bi bi-calendar3-fill"></i> Mes de análisis ·
                <span data-mes-label>{{ $meses[$mesInicial] }}</span> <span data-anio-label>· {{ $anioInicial }}</span></div>
            @include('partials.selector-mes', ['compact' => true])
        </div>
        <div class="anio-switch" id="anioSwitchSim">
            @foreach ($aniosDisponibles as $anio)
                <button type="button" class="{{ $anio === $anioInicial ? 'active' : '' }}" data-anio="{{ $anio }}">
                    {{ $anio }}
                </button>
            @endforeach
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="card-app">
            <div class="card-title-app"><i class="bi bi-sliders"></i> Variables del escenario</div>

            @foreach ($variablesSimulador as $key => $var)
                <div class="slider-var" data-var="{{ $key }}"
                    data-default="{{ $var['default'] }}" data-min="{{ $var['min'] }}"
                    data-max="{{ $var['max'] }}" data-sentido="{{ $var['sentido'] }}"
                    data-peso="{{ $var['peso'] }}" data-label="{{ $var['label'] }}"
                    data-unidad="{{ $var['unidad'] }}">
                    <div class="var-top">
                        <span class="var-label">{{ $var['label'] }}</span>
                        <span class="var-valor">{{ $var['default'] }} {{ $var['unidad'] }}</span>
                    </div>
                    <div class="var-help">{{ $var['help'] }}</div>
                    <input type="range" min="{{ $var['min'] }}" max="{{ $var['max'] }}"
                        step="{{ ($var['max'] - $var['min']) <= 10 ? '0.5' : '1' }}"
                        value="{{ $var['default'] }}" data-slider="{{ $key }}">
                    <div class="d-flex justify-content-between mt-1">
                        <span class="var-peso">Peso en el modelo: {{ round($var['peso'] * 100) }}%</span>
                        <span class="var-peso">{{ $var['sentido'] === -1 ? 'A mayor valor, menor arribo' : 'A mayor valor, mayor arribo' }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card-app mb-3">
            <div class="card-title-app"><i class="bi bi-arrow-left-right"></i> Resultado de la simulación</div>

            <div class="comparador">
                <div class="bloque">
                    <b id="simBase">{{ $estadoInicial['base'] }}</b>
                    <span id="simNivelBase">{{ $estadoInicial['nivel_base']['label'] }}</span>
                </div>
                <div class="flecha"><i class="bi bi-arrow-right"></i></div>
                <div class="bloque" id="simBloqueNuevo"
                    style="background: {{ $estadoInicial['nivel_simulado']['bg'] }}; border-color: {{ $estadoInicial['nivel_simulado']['color'] }}22">
                    <b id="simNuevo" style="color: {{ $estadoInicial['nivel_simulado']['color'] }}">{{ $estadoInicial['simulado'] }}</b>
                    <span id="simNivelNuevo" style="color: {{ $estadoInicial['nivel_simulado']['color'] }}">{{ $estadoInicial['nivel_simulado']['label'] }}</span>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-sm-4">
                    <div class="stat-mini" style="background:#f7fbfc; border-color: var(--borde)">
                        <b id="simFactor" style="color: var(--marino)">×{{ $estadoInicial['factor'] }}</b>
                        <span style="color: var(--muted)">factor del escenario</span>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="stat-mini" style="background:#f7fbfc; border-color: var(--borde)">
                        <b id="simTonBase" style="color: var(--marino)">{{ number_format($estadoInicial['toneladas_base'], 0, ',', '.') }} t</b>
                        <span style="color: var(--muted)">pronóstico base</span>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="stat-mini" style="background:#f7fbfc; border-color: var(--borde)">
                        <b id="simTonNueva" style="color: var(--marino)">{{ number_format($estadoInicial['toneladas_simuladas'], 0, ',', '.') }} t</b>
                        <span style="color: var(--muted)">escenario simulado</span>
                    </div>
                </div>
            </div>

            <div class="alerta-suave mt-3 mb-0" id="simResumen">
                <i class="bi bi-lightbulb-fill me-1"></i>
                Con las condiciones actuales el escenario coincide con el pronóstico base para
                <span data-mes-label>{{ $meses[$mesInicial] }}</span>.
            </div>
        </div>

        <div class="card-app mb-3">
            <div class="card-title-app"><i class="bi bi-diagram-3-fill"></i> Impacto relativo de cada variable</div>
            <div id="simImpactos">
                @foreach ($estadoInicial['variables'] as $item)
                    <div class="impacto-item">
                        <span style="min-width: 190px">{{ $item['label'] }}</span>
                        <span class="track"><i style="width: {{ max(4, $item['contribucion']) }}%"></i></span>
                        <span class="pct">{{ $item['contribucion'] }}%</span>
                    </div>
                @endforeach
            </div>
            <p class="fuente-nota mb-0 mt-2">
                Porcentaje de aporte al desplazamiento del índice respecto al escenario base. La variable con mayor
                aporte es <b id="simMayorImpacto">{{ $estadoInicial['mayor_impacto'] }}</b>.
            </p>
        </div>

        <div class="card-app">
            <div class="card-title-app"><i class="bi bi-geo-alt-fill"></i> Playas bajo el escenario simulado</div>
            <div class="lista-playas" id="simPlayas" style="max-height: 300px">
                @foreach (array_slice($pronosticoMeses[$mesInicial]['playas'], 0, 8) as $playa)
                    <div class="playa-row">
                        <span class="pin" style="background: {{ $playa['color'] }}"></span>
                        <span class="nombre">{{ $playa['nombre'] }}</span>
                        <span class="idx">{{ $playa['indice'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="section-heading d-flex flex-wrap justify-content-between align-items-end gap-3">
    <div>
        <h2>Pronóstico por mes</h2>
        <p>Selecciona un mes para ver el nivel esperado, las toneladas proyectadas y las playas más afectadas.</p>
    </div>
    <div class="anio-switch" id="anioSwitch">
        @foreach ($aniosDisponibles as $anio)
            <button type="button" class="{{ $anio === $anioInicial ? 'active' : '' }}" data-anio="{{ $anio }}">
                {{ $anio }}
            </button>
        @endforeach
    </div>
</div>

<div class="card-app mb-3">
    <div class="card-title-app"><i class="bi bi-calendar3-fill"></i> Elegir mes ·
        <span data-anio-label>{{ $anioInicial }}</span></div>
    @include('partials.selector-mes', ['compact' => false])
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-7">
        <div class="pronostico-hero">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 position-relative"
                style="z-index: 2">
                <div>
                    <div class="detalle mb-1">Pronóstico de arribo</div>
                    <h3 class="mes-titulo" id="pronMesTitulo">{{ $pronosticoMeses[$mesInicial]['mes_nombre'] }}
                        {{ $anioInicial }}</h3>
                    <div class="toneladas mt-3" id="pronToneladas">
                        {{ number_format($pronosticoMeses[$mesInicial]['toneladas'], 0, ',', '.') }}
                        <span>toneladas</span>
                    </div>
                    <div class="detalle mt-2" id="pronDetalle">
                        {{ $pronosticoMeses[$mesInicial]['vs_promedio'] >= 0 ? '+' : '' }}{{ $pronosticoMeses[$mesInicial]['vs_promedio'] }}%
                        respecto al promedio histórico del mes
                    </div>
                </div>
                <div class="text-end">
                    <span class="badge-nivel mb-3" id="pronNivel"
                        style="background: {{ $pronosticoMeses[$mesInicial]['nivel']['bg'] }}; color: {{ $pronosticoMeses[$mesInicial]['nivel']['color'] }}; font-size: 14px; padding: 9px 16px">
                        <span class="dot"></span> {{ $pronosticoMeses[$mesInicial]['nivel']['label'] }}
                    </span>
                    <div class="detalle mt-3">Índice <b id="pronIndice"
                            style="color:#fff">{{ $pronosticoMeses[$mesInicial]['indice'] }}</b>/100</div>
                </div>
            </div>

            <div class="row g-3 mt-3 position-relative" style="z-index: 2">
                <div class="col-4">
                    <div class="stat-mini">
                        <b id="pronDias">{{ $pronosticoMeses[$mesInicial]['dias_arribo'] }} días</b>
                        <span>arribo esperado</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="stat-mini">
                        <b id="pronConfianza">{{ $pronosticoMeses[$mesInicial]['confianza'] }}</b>
                        <span>confianza del dato</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="stat-mini">
                        <b id="pronPromedio">{{ number_format($pronosticoMeses[$mesInicial]['promedio_historico'], 0, ',', '.') }} t</b>
                        <span>promedio del mes</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-app mt-3">
            <div class="card-title-app"><i class="bi bi-list-check"></i> Recomendaciones</div>
            <ul class="reco-list" id="pronReco">
                @foreach ($pronosticoMeses[$mesInicial]['recomendaciones'] as $reco)
                    <li><i class="bi bi-check-circle-fill"></i><span>{{ $reco }}</span></li>
                @endforeach
            </ul>
            <p class="fuente-nota mt-3 mb-0">
                Fuente del escenario <span data-anio-label>{{ $anioInicial }}</span>:
                <b id="pronFuente">{{ $pronosticoMeses[$mesInicial]['fuente'] }}</b>
            </p>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card-app mb-3">
            <div class="card-title-app"><i class="bi bi-speedometer"></i> Índice de intensidad</div>
            <div id="gaugeIndice" class="chart-box-sm"></div>
            <p class="fuente-nota mb-0 text-center">
                Escala 0–100 construida con la mayor mensualidad registrada
                ({{ number_format($anios[2026]['total'] * 0.17, 0, ',', '.') }} t en junio).
            </p>
        </div>

        <div class="card-app">
            <div class="card-title-app"><i class="bi bi-geo-alt-fill"></i> Playas más afectadas</div>
            <div class="lista-playas" id="pronPlayas">
                @php
                    $mapaZonas = [];
                    foreach ($zonas as $z) {
                        $mapaZonas[$z['key']] = $z['nombre'];
                    }
                @endphp
                @foreach ($pronosticoMeses[$mesInicial]['playas'] as $playa)
                    <div class="playa-row">
                        <span class="pin" style="background: {{ $playa['color'] }}"></span>
                        <span class="nombre">{{ $playa['nombre'] }}
                            <small>{{ $mapaZonas[$playa['zona']] ?? '' }}</small>
                        </span>
                        <span class="idx">{{ $playa['indice'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="card-app">
    <div class="card-title-app"><i class="bi bi-pie-chart-fill"></i> Reparto estimado por municipio ·
        <span data-mes-label>{{ $meses[$mesInicial] }}</span>
    </div>
    <div class="row align-items-center g-3">
        <div class="col-md-6">
            <div id="chartDistribucion" class="chart-box-sm"></div>
        </div>
        <div class="col-md-6">
            <div id="listaDistribucion">
                @foreach ($distribucionInicial as $item)
                    <div class="impacto-item">
                        <span style="min-width: 210px">{{ $item['nombre'] }}</span>
                        <span class="track"><i style="width: {{ round($item['peso'] * 100) }}%"></i></span>
                        <span class="pct">{{ number_format($item['toneladas'], 0, ',', '.') }} t</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

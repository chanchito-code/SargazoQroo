<div class="section-heading d-flex flex-wrap justify-content-between align-items-end gap-3">
    <div>
        <h2>Panel general</h2>
        <p>Resumen de la temporada de sargazo en Quintana Roo y pronóstico del mes en curso.</p>
    </div>
    <div class="leyenda-niveles">
        @foreach ($niveles as $nivel)
            <span class="ln" style="background: {{ $nivel['bg'] }}; color: {{ $nivel['color'] }}">
                <i class="bi bi-circle-fill" style="font-size: 8px"></i> {{ $nivel['label'] }}
            </span>
        @endforeach
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="kpi" style="--kpi-color: #0a8f94; --kpi-tint: rgba(15,181,186,.14)">
            <div class="kpi-icon"><i class="bi bi-bar-chart-fill"></i></div>
            <div class="kpi-body">
                <div class="kpi-label">Proyección {{ $dashboard['anio'] }}</div>
                <div class="kpi-value">{{ number_format($dashboard['pronostico_anual'], 0, ',', '.') }} <small
                        style="font-size:14px">t</small></div>
                <div class="kpi-note" style="color: {{ $dashboard['variacion_anual'] >= 0 ? '#ef4444' : '#16a34a' }}">
                    {{ $dashboard['variacion_anual'] >= 0 ? '+' : '' }}{{ $dashboard['variacion_anual'] }}% vs
                    {{ $dashboard['anio'] - 1 }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="kpi" style="--kpi-color: {{ $dashboard['nivel_actual']['color'] }}; --kpi-tint: {{ $dashboard['nivel_actual']['bg'] }}">
            <div class="kpi-icon"><i class="bi bi-calendar2-day-fill"></i></div>
            <div class="kpi-body">
                <div class="kpi-label">{{ $dashboard['mes_nombre'] }} {{ $dashboard['anio'] }}</div>
                <div class="kpi-value" style="font-size: 22px">{{ $dashboard['nivel_actual']['label'] }}</div>
                <div class="kpi-note">Índice {{ $dashboard['mes_actual']['indice'] }}/100</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="kpi" style="--kpi-color: #f97316; --kpi-tint: rgba(249,115,22,.14)">
            <div class="kpi-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div class="kpi-body">
                <div class="kpi-label">Playas en alerta este mes</div>
                <div class="kpi-value">{{ $dashboard['playas_en_alerta'] }}<small
                        style="font-size:15px">/{{ $dashboard['playas_monitor'] }}</small></div>
                <div class="kpi-note">{{ $dashboard['playas_libres'] }} en nivel bajo o sin presencia</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="kpi" style="--kpi-color: #0b3c5d; --kpi-tint: rgba(11,60,93,.12)">
            <div class="kpi-icon"><i class="bi bi-water"></i></div>
            <div class="kpi-body">
                <div class="kpi-label">Pico de la temporada</div>
                <div class="kpi-value" style="font-size: 20px">May – Ago</div>
                <div class="kpi-note">Arribo máximo histórico</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-7">
        <div class="card-app">
            <div class="card-title-app"><i class="bi bi-graph-up"></i> Curva de arribo {{ $dashboard['anio'] }} (toneladas
                por mes)</div>
            <div id="chartTemporada" class="chart-box"></div>
            <p class="fuente-nota mb-0 mt-2">
                El mes resaltado corresponde al mes en curso. La temporada oficial va del
                <b>{{ $temporada['inicio'] }}</b> al <b>{{ $temporada['fin'] }}</b>; en {{ $dashboard['anio'] }} los
                arribos se anticiparon a <b>{{ $temporada['inicio_2026'] }}</b>.
            </p>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card-app">
            <div class="card-title-app"><i class="bi bi-trophy-fill"></i> Total recolectado por temporada</div>
            <div id="chartAnios" class="chart-box"></div>
            <p class="fuente-nota mb-0 mt-2">
                Barra sólida = dato oficial publicado por la <b>SEMA-Q. Roo</b> y la <b>Semar</b>; barra punteada =
                estimación de cierre.
            </p>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card-app">
            <div class="card-title-app"><i class="bi bi-buildings-fill"></i> Municipio más afectado ·
                {{ $dashboard['mes_nombre'] }}</div>
            @foreach ($dashboard['mes_actual']['zonas'] as $i => $zona)
                @if ($i < 5)
                    <div class="impacto-item">
                        <span style="min-width: 165px">{{ $zona['nombre'] }}</span>
                        <span class="track"><i style="width: {{ $zona['indice'] }}%; background: {{ $zona['color'] }}"></i></span>
                        <span class="pct" style="background: #eef5f7; color: {{ $zona['color'] }}">
                            {{ $zona['nivel_label'] }}
                        </span>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card-app">
            <div class="card-title-app"><i class="bi bi-info-circle-fill"></i> Contexto de la temporada</div>
            <div class="alerta-suave mb-3">
                <i class="bi bi-patch-exclamation me-1"></i>
                La temporada 2026 es la más intensa desde que existe monitoreo: la UNAM reportó
                <b>90,538 t diarias</b> navegando frente al Caribe (promedio histórico: 40,000 t), de las cuales
                ~<b>10%</b> recalcan en Quintana Roo.
            </div>
            <div class="fuente-nota">
                <p class="mb-2"><b>Semáforo del Sargazo (SEMA):</b> escala de cinco niveles que clasifica a diario
                    cada una de las playas monitoreadas, de <i>sin presencia</i> a <i>excesivo</i>.</p>
                <p class="mb-2"><b>Volúmenes de referencia:</b>
                    {{ number_format($anios[2020]['total'], 0, ',', '.') }} t (2020) ·
                    {{ number_format($anios[2024]['total'], 0, ',', '.') }} t (2024) ·
                    {{ number_format($anios[2025]['total'], 0, ',', '.') }} t (2025) ·
                    {{ number_format($anios[2026]['total'], 0, ',', '.') }} t ({{ $anios[2026]['fuente'] }}).
                </p>
                <p class="mb-0"><b>Nota:</b> los valores mensuales se reconstruyen a partir de la curva estacional
                    de arribo y el total anual publicado; sirven para planificación y no sustituyen el reporte
                    diario del semáforo.</p>
            </div>
        </div>
    </div>
</div>

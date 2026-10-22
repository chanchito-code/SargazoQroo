<div class="section-heading">
    <h2>Gráficas históricas</h2>
    <p>Comparativa de las temporadas registradas y evolución mes a mes del volumen recolectado.</p>
</div>

<div class="row g-3 mb-3">
    <div class="col-xl-8">
        <div class="card-app">
            <div class="card-title-app"><i class="bi bi-graph-up-arrow"></i> Evolución mensual por temporada
                (toneladas)</div>
            <div id="chartMensual" class="chart-box"></div>
            <p class="fuente-nota mb-0 mt-2">
                Cada línea representa una temporada. La línea destacada corresponde a
                <b>{{ $dashboard['anio'] }}</b>, el año con mayor volumen registrado en la historia del monitoreo.
            </p>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card-app">
            <div class="card-title-app"><i class="bi bi-award-fill"></i> Ranking anual</div>
            <div id="chartRanking" class="chart-box"></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card-app">
            <div class="card-title-app"><i class="bi bi-pie-chart-fill"></i> Reparto por municipio ·
                <span data-mes-label>{{ $meses[$mesInicial] }}</span></div>
            <div id="chartZonasAnio" class="chart-box-sm"></div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card-app">
            <div class="card-title-app"><i class="bi bi-table"></i> Base de datos de temporadas</div>
            <div class="table-responsive">
                <table class="table tabla-zonas mb-0">
                    <thead>
                        <tr>
                            <th>Año</th>
                            <th>Total (t)</th>
                            <th>vs año anterior</th>
                            <th>Fuente</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $anterior = null; @endphp
                        @foreach ($aniosTotales as $fila)
                            <tr>
                                <td><b>{{ $fila['anio'] }}</b></td>
                                <td>{{ number_format($fila['total'], 0, ',', '.') }}</td>
                                <td>
                                    @if ($anterior !== null)
                                        @php $var = round(($fila['total'] / $anterior - 1) * 100); @endphp
                                        <span class="badge-nivel"
                                            style="background: {{ $var >= 0 ? '#fee2e2' : '#dcfce7' }}; color: {{ $var >= 0 ? '#ef4444' : '#16a34a' }}">
                                            <span class="dot"></span>{{ $var >= 0 ? '+' : '' }}{{ $var }}%
                                        </span>
                                    @else
                                        <span class="text-muted-app">—</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted-app">
                                        {{ $fila['fuente'] }}
                                        @unless($fila['oficial'])
                                            <i class="bi bi-question-circle" title="Estimación"></i>
                                        @endunless
                                    </small>
                                </td>
                            </tr>
                            @php $anterior = $fila['total']; @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

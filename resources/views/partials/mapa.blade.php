<div class="section-heading d-flex flex-wrap justify-content-between align-items-end gap-3">
    <div>
        <h2>Mapa de playas</h2>
        <p>Semáforo pronosticado por playa para el mes seleccionado. Pasa el cursor sobre un marcador para ver el
            detalle.</p>
    </div>
    <div class="mapa-leyenda" id="leyendaMapa">
        @foreach ($niveles as $nivel)
            <span class="item"><i style="background: {{ $nivel['color'] }}"></i> {{ $nivel['label'] }}</span>
        @endforeach
    </div>
</div>

<div class="card-app mb-3">
    <div class="card-title-app"><i class="bi bi-calendar3-fill"></i> Mes a representar ·
        <span data-mes-label>{{ $meses[$mesInicial] }}</span> <span data-anio-label>· {{ $anioInicial }}</span></div>
    @include('partials.selector-mes', ['compact' => true])
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="mapa-wrap" id="mapaWrap">
            <svg viewBox="0 0 640 1000" role="img" aria-label="Mapa de la costa de Quintana Roo con el semáforo del sargazo por playa">
                <defs>
                    <linearGradient id="gMar" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="#bdeef4" />
                        <stop offset="55%" stop-color="#7fd8e6" />
                        <stop offset="100%" stop-color="#4cbcd6" />
                    </linearGradient>
                    <linearGradient id="gTierra" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="#f7ecd6" />
                        <stop offset="100%" stop-color="#e7d2ab" />
                    </linearGradient>
                    <linearGradient id="gIsla" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="#f9f1e0" />
                        <stop offset="100%" stop-color="#ead9b8" />
                    </linearGradient>
                    <filter id="sombraMarker" x="-60%" y="-60%" width="220%" height="220%">
                        <feDropShadow dx="0" dy="3" stdDeviation="4" flood-color="#0b3c5d" flood-opacity="0.35" />
                    </filter>
                </defs>

                <rect width="640" height="1000" fill="url(#gMar)" />

                <g stroke="#ffffff" stroke-opacity="0.35" fill="none" stroke-width="3" stroke-linecap="round">
                    <path d="M480 220 q 25 -14 50 0 t 50 0" />
                    <path d="M500 480 q 25 -14 50 0 t 50 0" />
                    <path d="M430 700 q 25 -14 50 0 t 50 0" />
                    <path d="M520 840 q 25 -14 50 0 t 50 0" />
                    <path d="M420 140 q 25 -14 50 0 t 50 0" />
                    <path d="M560 600 q 22 -12 44 0 t 44 0" />
                </g>

                <path fill="url(#gTierra)" stroke="#d9c294" stroke-width="2.5"
                    d="M0 0 L300 0 C340 25 375 50 405 85 C445 125 495 150 505 185 C495 215 470 245 452 262
                       C442 285 432 300 415 315 C400 345 375 365 345 378 C325 392 310 400 300 412
                       C292 425 288 438 285 452 C275 485 265 525 262 570 L258 640
                       C255 700 254 770 256 830 C250 858 235 878 210 898 C178 926 145 960 128 1000
                       L0 1000 Z" />

                <ellipse cx="430" cy="48" rx="55" ry="9" transform="rotate(-8 430 48)" fill="url(#gIsla)"
                    stroke="#d9c294" stroke-width="2" />
                <ellipse cx="518" cy="135" rx="12" ry="38" transform="rotate(8 518 135)" fill="url(#gIsla)"
                    stroke="#d9c294" stroke-width="2" />
                <ellipse cx="450" cy="357" rx="27" ry="57" transform="rotate(-18 450 357)" fill="url(#gIsla)"
                    stroke="#d9c294" stroke-width="2.5" />

                <g fill="#a8925f" font-family="Nunito, sans-serif" font-size="17" font-weight="800"
                    letter-spacing="1.5">
                    <text x="150" y="250">YUCATÁN</text>
                    <text x="70" y="700">CAMPECHE</text>
                    <text x="120" y="905">BACALAR</text>
                </g>
                <g fill="#0b3c5d" font-family="Nunito, sans-serif" font-size="16" font-weight="800"
                    letter-spacing="1.2">
                    <text x="140" y="420">QUINTANA</text>
                    <text x="140" y="442">ROO</text>
                </g>
                <g fill="#0f5f74" font-family="Nunito, sans-serif" font-size="14" font-weight="800"
                    letter-spacing="1">
                    <text x="255" y="275">Zona Norte</text>
                    <text x="175" y="545">Riviera Maya</text>
                    <text x="145" y="790">Zona Sur</text>
                </g>
                <g fill="#ffffff" fill-opacity="0.85" font-family="Nunito, sans-serif" font-size="15"
                    font-weight="800" letter-spacing="2">
                    <text x="500" y="330">MAR CARIBE</text>
                    <text x="470" y="760">CARIBE SUR</text>
                </g>
                <g fill="#ffffff" fill-opacity="0.7" font-family="Nunito, sans-serif" font-size="12"
                    font-weight="700" letter-spacing="1.4">
                    <text x="470" y="70">CANAL DE YUCATÁN</text>
                </g>

                <g fill="#0b3c5d" font-family="Nunito, sans-serif" font-size="13" font-weight="800">
                    <text x="470" y="215">Cancún</text>
                    <text x="380" y="330">P. del Carmen</text>
                    <text x="300" y="455">Tulum</text>
                    <text x="390" y="415">Cozumel</text>
                    <text x="272" y="855">Mahahual</text>
                    <text x="400" y="32">Holbox</text>
                    <text x="534" y="140">I. Mujeres</text>
                </g>

                <g transform="translate(600,40)" fill="#0b3c5d" opacity="0.75">
                    <path d="M0 -22 L7 8 L0 1 L-7 8 Z" fill="#0b3c5d" />
                    <text x="-5" y="26" font-family="Nunito, sans-serif" font-size="14" font-weight="800">N</text>
                </g>

                <g id="mapMarkers"></g>
            </svg>

            <div class="mapa-tooltip" id="mapaTooltip"></div>
        </div>

        <p class="fuente-nota mt-2 mb-0">
            Mapa esquemático: la posición de cada marcador corresponde a la ubicación real de la playa sobre el
            litoral de Quintana Roo. El color muestra el nivel pronosticado para
            <b data-mes-label>{{ $meses[$mesInicial] }}</b>.
        </p>
    </div>

    <div class="col-lg-4">
        <div class="card-app mb-3">
            <div class="card-title-app"><i class="bi bi-sort-numeric-down"></i> Ranking de playas</div>
            <div class="lista-playas" id="mapaPlayas" style="max-height: 430px">
                @foreach ($pronosticoMeses[$mesInicial]['playas'] as $playa)
                    <div class="playa-row">
                        <span class="pin" style="background: {{ $playa['color'] }}"></span>
                        <span class="nombre">{{ $playa['nombre'] }}</span>
                        <span class="idx">{{ $playa['indice'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card-app">
            <div class="card-title-app"><i class="bi bi-buildings"></i> Por municipio</div>
            <div class="table-responsive">
                <table class="table tabla-zonas mb-0" id="tablaZonas">
                    <thead>
                        <tr>
                            <th>Zona</th>
                            <th>Índice</th>
                            <th>Nivel</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pronosticoMeses[$mesInicial]['zonas'] as $zona)
                            <tr>
                                <td><b>{{ $zona['nombre'] }}</b></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="barra flex-grow-1"><i
                                                style="width: {{ $zona['indice'] }}%; background: {{ $zona['color'] }}"></i></span>
                                        <small><b>{{ $zona['indice'] }}</b></small>
                                    </div>
                                </td>
                                <td><span class="badge-nivel" style="background: #eef5f7; color: {{ $zona['color'] }}">
                                        <span class="dot"></span>{{ $zona['nivel_label'] }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

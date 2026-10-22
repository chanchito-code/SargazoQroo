<div class="meses-grid {{ ($compact ?? false) ? 'compact' : '' }}" data-selector-mes>
    @foreach ($meses as $num => $nombre)
        @php
            $p = $pronosticoMeses[$num];
            $activo = $num === $mesInicial;
        @endphp
        <button class="mes-btn {{ $activo ? 'active' : '' }}" data-mes="{{ $num }}" type="button"
            title="{{ $nombre }}: {{ $p['nivel']['label'] }} ({{ $p['indice'] }}/100)">
            <strong>{{ $mesesCortos[$num] }}</strong>
            <small>{{ $p['nivel']['label'] }}</small>
            <div class="mini-bar">
                <i style="width: {{ max(4, $p['indice']) }}%; background: {{ $p['nivel']['color'] }};"></i>
            </div>
        </button>
    @endforeach
</div>

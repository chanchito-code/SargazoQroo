<?php

namespace App\Services;

use App\Support\SargazoData;

class SargazoPredictor
{
    /**
     * Variables del simulador.
     * peso: aporte al factor (suman 1). sentido: 1 = más empeora, -1 = más mejora.
     */
    public const VARIABLES = [
        'biomasa' => [
            'label' => 'Biomasa en el Atlántico',
            'unidad' => 'millones de t',
            'min' => 0,
            'max' => 80,
            'default' => 40,
            'peso' => 0.30,
            'sentido' => 1,
            'help' => 'Volumen flotando frente al Atlántico tropical y el Caribe.',
        ],
        'temperatura' => [
            'label' => 'Temperatura del mar',
            'unidad' => '°C',
            'min' => 24,
            'max' => 32,
            'default' => 28,
            'peso' => 0.20,
            'sentido' => 1,
            'help' => 'El agua caliente acelera la duplicación del sargazo (cada 3 días).',
        ],
        'vientos' => [
            'label' => 'Vientos alisios',
            'unidad' => 'km/h',
            'min' => 5,
            'max' => 45,
            'default' => 20,
            'peso' => 0.20,
            'sentido' => 1,
            'help' => 'Del este al oeste, empujan el sargazo hacia la costa caribeña.',
        ],
        'corrientes' => [
            'label' => 'Corrientes y nutrientes',
            'unidad' => 'índice 0-100',
            'min' => 0,
            'max' => 100,
            'default' => 60,
            'peso' => 0.15,
            'sentido' => 1,
            'help' => 'Nitrógeno y fósforo (fertilizantes) más corriente del canal de Yucatán.',
        ],
        'limpieza' => [
            'label' => 'Esfuerzo de limpieza',
            'unidad' => 'índice 0-100',
            'min' => 0,
            'max' => 100,
            'default' => 50,
            'peso' => 0.15,
            'sentido' => -1,
            'help' => 'Barreras sargaceras, recolección en altamar y cuadrillas en playa.',
        ],
    ];

    /** Índice base 0-100 para un mes/año (sin ajuste de zona). */
    public function indiceMes(int $mes, int $anio): float
    {
        if (! isset(SargazoData::ANIOS[$anio])) {
            $anio = SargazoData::ANIO_BASE;
        }

        $max = SargazoData::refMaxima();
        if ($max <= 0) {
            return 0.0;
        }

        $toneladas = SargazoData::toneladasMes($anio, $mes);

        return $this->limitar($toneladas / $max * 100);
    }

    /** Índice ajustado a la sensibilidad de una zona o playa. */
    public function indicePara(float $indice, float $sensibilidad): float
    {
        return $this->limitar($indice * $sensibilidad);
    }

    public function predecirMes(int $mes, int $anio): array
    {
        $indice = $this->indiceMes($mes, $anio);
        $nivel = SargazoData::nivelPorIndice($indice);
        $toneladas = SargazoData::toneladasMes($anio, $mes);

        $playas = $this->calcularPlayas($indice);
        $zonas = $this->calcularZonas($playas);

        $promedio = $this->promedioHistoricoMes($mes);

        return [
            'anio' => $anio,
            'mes' => $mes,
            'mes_nombre' => SargazoData::MESES[$mes],
            'toneladas' => $toneladas,
            'indice' => round($indice, 1),
            'nivel' => $nivel,
            'confianza' => SargazoData::ANIOS[$anio]['oficial'] ? 'Alta' : 'Media',
            'fuente' => SargazoData::ANIOS[$anio]['fuente'],
            'playas' => $playas,
            'zonas' => $zonas,
            'recomendaciones' => SargazoData::recomendaciones($nivel['key']),
            'vs_promedio' => $promedio > 0 ? round(($toneladas / $promedio - 1) * 100) : 0,
            'promedio_historico' => round($promedio),
            'dias_arribo' => $this->diasArribo($nivel['key']),
            'curva' => SargazoData::curva($anio),
        ];
    }

    /** Nivel de sargazo esperado para cada playa con un índice base dado. */
    public function calcularPlayas(float $indice): array
    {
        $playas = [];
        foreach (SargazoData::playas() as $playa) {
            $i = $this->indicePara($indice, (float) $playa['sensibilidad']);
            $n = SargazoData::nivelPorIndice($i);
            $playas[] = [
                'key' => $playa['key'],
                'nombre' => $playa['nombre'],
                'zona' => $playa['zona'],
                'x' => $playa['x'],
                'y' => $playa['y'],
                'indice' => round($i, 1),
                'nivel' => $n['key'],
                'nivel_label' => $n['label'],
                'color' => $n['color'],
            ];
        }

        usort($playas, fn ($a, $b) => $b['indice'] <=> $a['indice']);

        return $playas;
    }

    /** Municipio más afectado a partir de la lista de playas. */
    public function calcularZonas(array $playas): array
    {
        $top = [];
        foreach ($playas as $p) {
            $top[$p['zona']] = max($top[$p['zona']] ?? 0, (float) $p['indice']);
        }
        arsort($top);

        $zonas = [];
        foreach ($top as $key => $i) {
            $zona = SargazoData::zona((string) $key);
            $n = SargazoData::nivelPorIndice($i);
            $zonas[] = [
                'key' => $key,
                'nombre' => $zona['nombre'] ?? $key,
                'indice' => round($i, 1),
                'nivel' => $n['key'],
                'nivel_label' => $n['label'],
                'color' => $n['color'],
            ];
        }

        return $zonas;
    }

    /** Toneladas promedio de ese mes en todos los años del dataset. */
    public function promedioHistoricoMes(int $mes): float
    {
        $totales = [];
        foreach (SargazoData::ANIOS as $anio => $_) {
            $totales[] = SargazoData::toneladasMes((int) $anio, $mes);
        }

        return $totales ? array_sum($totales) / count($totales) : 0;
    }

    public function dashboard(): array
    {
        $anioActual = SargazoData::ANIO_BASE;
        $mesActual = (int) date('n');
        $anioHoy = (int) date('Y');

        if (isset(SargazoData::ANIOS[$anioHoy])) {
            $anioActual = $anioHoy;
        }

        $actual = $this->predecirMes($mesActual, $anioActual);
        $previo = $this->predecirMes($mesActual, $anioActual - 1);

        $acumulado2026 = 0;
        for ($m = 1; $m <= 12; $m++) {
            $acumulado2026 += SargazoData::toneladasMes($anioActual, $m);
        }
        $totalPrevio = SargazoData::totalAnio($anioActual - 1);

        $enAlerta = 0;
        foreach ($actual['playas'] as $p) {
            if ($p['indice'] >= 55) {
                $enAlerta++;
            }
        }

        return [
            'anio' => $anioActual,
            'mes' => $mesActual,
            'mes_nombre' => SargazoData::MESES[$mesActual],
            'pronostico_anual' => SargazoData::totalAnio($anioActual),
            'total_anio_previo' => $totalPrevio,
            'variacion_anual' => $totalPrevio > 0 ? round((SargazoData::totalAnio($anioActual) / $totalPrevio - 1) * 100) : 0,
            'mes_actual' => $actual,
            'mes_previo' => $previo,
            'playas_monitor' => count(SargazoData::PLAYAS),
            'playas_en_alerta' => $enAlerta,
            'playas_libres' => count(SargazoData::PLAYAS) - $enAlerta,
            'pico' => SargazoData::TEMPORADA['pico'],
            'temporada' => SargazoData::TEMPORADA,
            'nivel_actual' => $actual['nivel'],
        ];
    }

    /** @param array<string, float> $valores valores crudos de cada slider */
    public function simular(array $valores, int $mes, int $anio): array
    {
        $base = $this->indiceMes($mes, $anio);

        $factor = 1.0;
        $impactos = [];

        foreach (self::VARIABLES as $key => $config) {
            $valor = (float) ($valores[$key] ?? $config['default']);
            $valor = max($config['min'], min($config['max'], $valor));

            $ratio = $config['sentido'] === -1
                ? ($config['max'] - $valor) / max(1, $config['max'] - $config['default'])
                : $valor / max(0.0001, $config['default']);

            $ratio = max(0.0, min(3.0, $ratio));

            $factor += $config['peso'] * ($ratio - 1);

            $impactos[$key] = [
                'label' => $config['label'],
                'valor' => $valor,
                'unidad' => $config['unidad'],
                'peso' => $config['peso'],
                'ratio' => round($ratio, 3),
                'impacto' => round($config['peso'] * ($ratio - 1), 4),
            ];
        }

        $factor = max(0.05, min(3.0, $factor));
        $simulado = $this->limitar($base * $factor);

        $totalImpacto = 0;
        foreach ($impactos as $item) {
            $totalImpacto += abs($item['impacto']);
        }
        foreach ($impactos as $key => $item) {
            $impactos[$key]['contribucion'] = $totalImpacto > 0
                ? round(abs($item['impacto']) / $totalImpacto * 100)
                : 0;
        }
        arsort($impactos);

        $nivelBase = SargazoData::nivelPorIndice($base);
        $nivelNuevo = SargazoData::nivelPorIndice($simulado);

        $toneladasBase = SargazoData::toneladasMes($anio, $mes);
        $toneladasSimuladas = round($toneladasBase * $factor);

        $mayor = reset($impactos);

        return [
            'anio' => $anio,
            'mes' => $mes,
            'mes_nombre' => SargazoData::MESES[$mes],
            'factor' => round($factor, 3),
            'base' => round($base, 1),
            'simulado' => round($simulado, 1),
            'delta' => round($simulado - $base, 1),
            'nivel_base' => $nivelBase,
            'nivel_simulado' => $nivelNuevo,
            'toneladas_base' => $toneladasBase,
            'toneladas_simuladas' => $toneladasSimuladas,
            'variables' => array_values($impactos),
            'mayor_impacto' => $mayor['label'] ?? '',
            'puede_mejorar' => $simulado < $base - 1,
            'puede_empeorar' => $simulado > $base + 1,
        ];
    }

    /** Serie de toneladas por mes para los años indicados. */
    public function serieGrafica(array $anios): array
    {
        $series = [];
        foreach ($anios as $anio) {
            $anio = (int) $anio;
            if (! isset(SargazoData::ANIOS[$anio])) {
                continue;
            }

            $datos = [];
            for ($m = 1; $m <= 12; $m++) {
                $datos[] = SargazoData::toneladasMes($anio, $m);
            }

            $series[] = [
                'name' => (string) $anio,
                'data' => $datos,
                'oficial' => SargazoData::ANIOS[$anio]['oficial'],
                'total' => SargazoData::totalAnio($anio),
            ];
        }

        return $series;
    }

    /** Reparto estimado por municipio para un mes/año. */
    public function distribucionZonas(int $mes, int $anio): array
    {
        $totalMes = SargazoData::toneladasMes($anio, $mes);
        $salida = [];

        foreach (SargazoData::ZONAS as $zona) {
            $salida[] = [
                'key' => $zona['key'],
                'nombre' => $zona['nombre'],
                'toneladas' => round($totalMes * $zona['peso']),
                'peso' => $zona['peso'],
                'sensibilidad' => $zona['sensibilidad'],
            ];
        }

        return $salida;
    }

    private function diasArribo(string $nivel): int
    {
        return match ($nivel) {
            'libre' => 0,
            'bajo' => 3,
            'moderado' => 9,
            'alto' => 16,
            default => 24,
        };
    }

    private function limitar(float $valor): float
    {
        return max(0.0, min(100.0, $valor));
    }
}

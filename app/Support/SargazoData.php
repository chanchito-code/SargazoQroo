<?php

namespace App\Support;

class SargazoData
{
    public const TEMPORADA = [
        'inicio' => '1 de abril',
        'fin' => '31 de octubre',
        'inicio_2026' => 'enero (adelantada 3 meses)',
        'pico' => 'mayo, junio, julio y agosto',
    ];

    /**
     * Curvas estacionales: porcentaje del total anual que arriba en cada mes.
     */
    public const CURVAS = [
        'normal' => [
            1 => 0.5, 2 => 1, 3 => 2, 4 => 7, 5 => 15, 6 => 19,
            7 => 18, 8 => 15, 9 => 12, 10 => 8, 11 => 2, 12 => 0.5,
        ],
        'adelantada' => [
            1 => 3, 2 => 4, 3 => 6, 4 => 12, 5 => 16, 6 => 17,
            7 => 15, 8 => 13, 9 => 9, 10 => 3, 11 => 1.5, 12 => 0.5,
        ],
    ];

    /**
     * Totales anuales recolectados en Quintana Roo (toneladas).
     * Fuentes: SEMA-Q. Roo, Semar y reportes oficiales.
     */
    public const ANIOS = [
        2020 => ['total' => 19054, 'curva' => 'normal', 'fuente' => 'Semar', 'oficial' => true],
        2021 => ['total' => 44913, 'curva' => 'normal', 'fuente' => 'Semar', 'oficial' => true],
        2022 => ['total' => 29500, 'curva' => 'normal', 'fuente' => 'Estimación (cierre de temporada)', 'oficial' => false],
        2023 => ['total' => 23500, 'curva' => 'normal', 'fuente' => 'Estimación (cierre de temporada)', 'oficial' => false],
        2024 => ['total' => 37000, 'curva' => 'normal', 'fuente' => 'SEMA-Q. Roo', 'oficial' => true],
        2025 => ['total' => 91506, 'curva' => 'normal', 'fuente' => 'SEMA-Q. Roo', 'oficial' => true],
        2026 => ['total' => 130000, 'curva' => 'adelantada', 'fuente' => 'SEMA/SEMAR (proyección de cierre)', 'oficial' => false],
    ];

    public const ANIO_BASE = 2026;

    public const NIVELES = [
        ['key' => 'libre', 'label' => 'Sin presencia', 'min' => 0, 'color' => '#16a34a', 'bg' => '#dcfce7'],
        ['key' => 'bajo', 'label' => 'Bajo', 'min' => 15, 'color' => '#84cc16', 'bg' => '#ecfccb'],
        ['key' => 'moderado', 'label' => 'Moderado', 'min' => 35, 'color' => '#eab308', 'bg' => '#fef9c3'],
        ['key' => 'alto', 'label' => 'Alto', 'min' => 55, 'color' => '#f97316', 'bg' => '#ffedd5'],
        ['key' => 'excesivo', 'label' => 'Excesivo', 'min' => 75, 'color' => '#ef4444', 'bg' => '#fee2e2'],
    ];

    public const ZONAS = [
        ['key' => 'benito_juarez', 'nombre' => 'Benito Juárez (Cancún)', 'peso' => 0.30, 'sensibilidad' => 1.05],
        ['key' => 'solidaridad', 'nombre' => 'Solidaridad (Playa del Carmen)', 'peso' => 0.24, 'sensibilidad' => 1.15],
        ['key' => 'tulum', 'nombre' => 'Tulum', 'peso' => 0.14, 'sensibilidad' => 1.20],
        ['key' => 'cozumel', 'nombre' => 'Cozumel', 'peso' => 0.09, 'sensibilidad' => 0.80],
        ['key' => 'isla_mujeres', 'nombre' => 'Isla Mujeres', 'peso' => 0.06, 'sensibilidad' => 0.95],
        ['key' => 'puerto_morelos', 'nombre' => 'Puerto Morelos', 'peso' => 0.06, 'sensibilidad' => 1.00],
        ['key' => 'athon_p_blanco', 'nombre' => 'Othón P. Blanco (Mahahual)', 'peso' => 0.11, 'sensibilidad' => 1.25],
    ];

    /**
     * Playas monitoreadas. x/y = coordenadas en el mapa SVG (viewBox 0 0 640 1000).
     * sensibilidad: 0 = siempre limpia, 1.25 = muy expuesta.
     */
    public const PLAYAS = [
        ['key' => 'holbox', 'nombre' => 'Holbox', 'zona' => 'isla_mujeres', 'x' => 420, 'y' => 45, 'sensibilidad' => 0.40],
        ['key' => 'chiquila', 'nombre' => 'Chiquilá', 'zona' => 'isla_mujeres', 'x' => 350, 'y' => 68, 'sensibilidad' => 0.55],
        ['key' => 'playa_norte', 'nombre' => 'Playa Norte (Isla Mujeres)', 'zona' => 'isla_mujeres', 'x' => 515, 'y' => 115, 'sensibilidad' => 0.45],
        ['key' => 'marlin', 'nombre' => 'Playa Marlin', 'zona' => 'benito_juarez', 'x' => 495, 'y' => 150, 'sensibilidad' => 1.20],
        ['key' => 'delfines', 'nombre' => 'Playa Delfines', 'zona' => 'benito_juarez', 'x' => 505, 'y' => 172, 'sensibilidad' => 1.25],
        ['key' => 'nizuc', 'nombre' => 'Punta Nizuc', 'zona' => 'benito_juarez', 'x' => 498, 'y' => 195, 'sensibilidad' => 1.10],
        ['key' => 'morelos', 'nombre' => 'Playa Puerto Morelos', 'zona' => 'puerto_morelos', 'x' => 450, 'y' => 255, 'sensibilidad' => 1.00],
        ['key' => 'esmeralda', 'nombre' => 'Punta Esmeralda', 'zona' => 'solidaridad', 'x' => 424, 'y' => 300, 'sensibilidad' => 1.15],
        ['key' => 'playacar', 'nombre' => 'Playacar', 'zona' => 'solidaridad', 'x' => 410, 'y' => 318, 'sensibilidad' => 1.05],
        ['key' => 'cozumel_oeste', 'nombre' => 'Cozumel (costa oeste)', 'zona' => 'cozumel', 'x' => 428, 'y' => 345, 'sensibilidad' => 0.55],
        ['key' => 'cozumel_este', 'nombre' => 'Cozumel (costa este)', 'zona' => 'cozumel', 'x' => 472, 'y' => 362, 'sensibilidad' => 1.10],
        ['key' => 'xcacel', 'nombre' => 'Xcacel-Xcacelito', 'zona' => 'tulum', 'x' => 370, 'y' => 368, 'sensibilidad' => 1.15],
        ['key' => 'akumal', 'nombre' => 'Akumal', 'zona' => 'tulum', 'x' => 330, 'y' => 392, 'sensibilidad' => 1.10],
        ['key' => 'ruinas', 'nombre' => 'Playa Ruinas (Tulum)', 'zona' => 'tulum', 'x' => 305, 'y' => 405, 'sensibilidad' => 1.25],
        ['key' => 'paraiso', 'nombre' => 'Playa Paraíso (Tulum)', 'zona' => 'tulum', 'x' => 295, 'y' => 428, 'sensibilidad' => 1.20],
        ['key' => 'punta_allen', 'nombre' => 'Punta Allen', 'zona' => 'tulum', 'x' => 278, 'y' => 452, 'sensibilidad' => 1.05],
        ['key' => 'mahahual', 'nombre' => 'Mahahual', 'zona' => 'athon_p_blanco', 'x' => 253, 'y' => 825, 'sensibilidad' => 1.25],
        ['key' => 'chinchorro', 'nombre' => 'Banco Chinchorro', 'zona' => 'athon_p_blanco', 'x' => 470, 'y' => 880, 'sensibilidad' => 0.30],
    ];

    public const MESES = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
        7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
    ];

    public const MESES_CORTOS = [
        1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun',
        7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic',
    ];

    public static function anios(): array
    {
        return self::ANIOS;
    }

    public static function aniosDisponibles(): array
    {
        return array_keys(self::ANIOS);
    }

    public static function curva(int $anio): array
    {
        $nombre = self::ANIOS[$anio]['curva'] ?? 'normal';

        return self::CURVAS[$nombre];
    }

    public static function totalAnio(int $anio): float
    {
        return (float) (self::ANIOS[$anio]['total'] ?? 0);
    }

    public static function toneladasMes(int $anio, int $mes): float
    {
        $curva = self::curva($anio);
        $porcentaje = $curva[$mes] ?? 0;

        return round(self::totalAnio($anio) * $porcentaje / 100);
    }

    /** @return array<int, float> 12 valores (toneladas por mes) */
    public static function serieAnual(int $anio): array
    {
        $serie = [];
        for ($m = 1; $m <= 12; $m++) {
            $serie[$m] = self::toneladasMes($anio, $m);
        }

        return $serie;
    }

    /** Máximo histórico mensual: se usa como referencia para el índice 0-100. */
    public static function refMaxima(): float
    {
        $max = 0;
        foreach (self::ANIOS as $anio => $_) {
            foreach (self::serieAnual((int) $anio) as $t) {
                $max = max($max, $t);
            }
        }

        return $max;
    }

    public static function nivelPorIndice(float $indice): array
    {
        $elegido = self::NIVELES[0];
        foreach (self::NIVELES as $nivel) {
            if ($indice >= $nivel['min']) {
                $elegido = $nivel;
            }
        }

        return $elegido;
    }

    public static function zona(string $key): ?array
    {
        foreach (self::ZONAS as $zona) {
            if ($zona['key'] === $key) {
                return $zona;
            }
        }

        return null;
    }

    public static function playas(): array
    {
        return self::PLAYAS;
    }

    public static function zonas(): array
    {
        return self::ZONAS;
    }

    public static function niveles(): array
    {
        return self::NIVELES;
    }

    public static function recomendaciones(string $nivelKey): array
    {
        $base = [
            'libre' => [
                'Playas en condiciones ideales para baño y deportes acuáticos.',
                'Buena ventana para visitar cualquier punto de la Riviera Maya.',
                'Mantente alerta: el sargazo puede arribar en cuestión de horas si cambian los vientos.',
            ],
            'bajo' => [
                'Presencia mínima; puede verse una franja delgada junto a la orilla.',
                'Se recomienda bañarse en las primeras horas de la mañana.',
                'Los operadores turísticos mantienen rutas de tours a islas sin afectación.',
            ],
            'moderado' => [
                'Acumulaciones localizadas; elige playas de la costa occidental de Cozumel o Playa Norte.',
                'Evita las primeras horas de la tarde, cuando el alga se acumula contra la orilla.',
                'Verifica el semáforo el mismo día del viaje antes de salir.',
            ],
            'alto' => [
                'Arribo intenso: se recomienda preferir islas (Holbox, Isla Mujeres norte) y costa occidental de Cozumel.',
                'Las cuadrillas de limpieza trabajan desde temprano; llega a la playa en la mañana.',
                'Personas con asma o sensibilidad al olor deben evitar la arena húmeda.',
            ],
            'excesivo' => [
                'Condiciones críticas: gran parte del litoral reporta recale excesivo.',
                'Se desaconseja el baño; el sargazo en descomposición libera sulfuro de hidrógeno.',
                'Programa la visita a destinos alternativos (Holbox, Chiquilá, Isla Contoy, costa occidental de Cozumel).',
                'Sigue las indicaciones de la SEMA y de la Red de Monitoreo del Sargazo.',
            ],
        ];

        return $base[$nivelKey] ?? $base['moderado'];
    }
}

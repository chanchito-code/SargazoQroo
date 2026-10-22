<?php

namespace App\Http\Controllers;

use App\Services\SargazoPredictor;
use App\Support\SargazoData;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(private readonly SargazoPredictor $predictor) {}

    public function index(): View
    {
        $predictor = $this->predictor;

        $dashboard = $predictor->dashboard();

        $mesInicial = $dashboard['mes'];
        $anioInicial = $dashboard['anio'];

        $pronosticoMeses = [];
        for ($mes = 1; $mes <= 12; $mes++) {
            $pronosticoMeses[$mes] = $predictor->predecirMes($mes, $anioInicial);
        }

        $playasMes = $pronosticoMeses[$mesInicial]['playas'];

        $aniosGrafica = [2021, 2024, 2025, 2026];

        $aniosTotales = [];
        foreach (SargazoData::ANIOS as $anio => $info) {
            $aniosTotales[] = [
                'anio' => (int) $anio,
                'total' => $info['total'],
                'oficial' => $info['oficial'],
                'fuente' => $info['fuente'],
            ];
        }

        $estadoInicial = $predictor->simular(
            array_map(fn ($v) => $v['default'], SargazoPredictor::VARIABLES),
            $mesInicial,
            $anioInicial
        );

        return view('app', [
            'appData' => [
                'urlSimular' => route('api.simular'),
                'urlPrediccion' => route('api.prediccion'),
                'csrf' => csrf_token(),
                'mesInicial' => $mesInicial,
                'anioInicial' => $anioInicial,
                'meses' => SargazoData::MESES,
                'mesesCortos' => SargazoData::MESES_CORTOS,
                'niveles' => SargazoData::NIVELES,
                'pronostico' => $pronosticoMeses,
                'anios' => SargazoData::ANIOS,
                'aniosDisponibles' => SargazoData::aniosDisponibles(),
                'serieGrafica' => $predictor->serieGrafica($aniosGrafica),
                'aniosTotales' => $aniosTotales,
                'zonas' => SargazoData::ZONAS,
                'distribucion' => $predictor->distribucionZonas($mesInicial, $anioInicial),
                'variables' => SargazoPredictor::VARIABLES,
                'estadoInicial' => $estadoInicial,
                'temporada' => SargazoData::TEMPORADA,
                'dashboard' => [
                    'anio' => $dashboard['anio'],
                    'mes' => $dashboard['mes'],
                    'pronostico_anual' => $dashboard['pronostico_anual'],
                    'total_anio_previo' => $dashboard['total_anio_previo'],
                    'variacion_anual' => $dashboard['variacion_anual'],
                    'playas_monitor' => $dashboard['playas_monitor'],
                    'playas_en_alerta' => $dashboard['playas_en_alerta'],
                    'playas_libres' => $dashboard['playas_libres'],
                ],
            ],
            'dashboard' => $dashboard,
            'pronosticoMeses' => $pronosticoMeses,
            'mesInicial' => $mesInicial,
            'anioInicial' => $anioInicial,
            'playasMesInicial' => $playasMes,
            'meses' => SargazoData::MESES,
            'mesesCortos' => SargazoData::MESES_CORTOS,
            'niveles' => SargazoData::NIVELES,
            'playas' => SargazoData::PLAYAS,
            'zonas' => SargazoData::ZONAS,
            'anios' => SargazoData::ANIOS,
            'aniosDisponibles' => SargazoData::aniosDisponibles(),
            'serieGrafica' => $predictor->serieGrafica($aniosGrafica),
            'aniosTotales' => $aniosTotales,
            'distribucionInicial' => $predictor->distribucionZonas($mesInicial, $anioInicial),
            'temporada' => SargazoData::TEMPORADA,
            'variablesSimulador' => SargazoPredictor::VARIABLES,
            'estadoInicial' => $estadoInicial,
        ]);
    }
}

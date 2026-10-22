<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SargazoPredictor;
use App\Support\SargazoData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrediccionController extends Controller
{
    /**
     * Devuelve el pronóstico de los 12 meses de un año.
     */
    public function __invoke(Request $request, SargazoPredictor $predictor): JsonResponse
    {
        $datos = $request->validate([
            'anio' => ['required', 'integer', 'min:'.min(array_keys(SargazoData::ANIOS)), 'max:'.max(array_keys(SargazoData::ANIOS))],
        ]);

        $anio = (int) $datos['anio'];

        $meses = [];
        for ($mes = 1; $mes <= 12; $mes++) {
            $meses[$mes] = $predictor->predecirMes($mes, $anio);
        }

        return response()->json([
            'success' => true,
            'anio' => $anio,
            'meses' => $meses,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SargazoPredictor;
use App\Support\SargazoData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SimuladorController extends Controller
{
    public function __invoke(Request $request, SargazoPredictor $predictor): JsonResponse
    {
        $datos = $request->validate([
            'mes' => ['required', 'integer', 'min:1', 'max:12'],
            'anio' => ['required', 'integer', 'min:'.min(array_keys(SargazoData::ANIOS)), 'max:'.max(array_keys(SargazoData::ANIOS))],
            'valores' => ['sometimes', 'array'],
        ]);

        $valores = [];
        foreach ($predictor::VARIABLES as $key => $config) {
            $valores[$key] = (float) ($datos['valores'][$key] ?? $config['default']);
        }

        $resultado = $predictor->simular($valores, (int) $datos['mes'], (int) $datos['anio']);

        $playas = $predictor->calcularPlayas($resultado['simulado']);

        return response()->json([
            'success' => true,
            'simulacion' => $resultado,
            'playas' => $playas,
            'zonas' => $predictor->calcularZonas($playas),
        ]);
    }
}

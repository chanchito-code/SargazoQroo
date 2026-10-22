<?php

namespace Tests\Feature;

use App\Services\SargazoPredictor;
use App\Support\SargazoData;
use Tests\TestCase;

class SargazoPredictorTest extends TestCase
{
    private function predictor(): SargazoPredictor
    {
        return app(SargazoPredictor::class);
    }

    public function test_las_toneladas_mensuales_suman_el_total_del_anio(): void
    {
        foreach (SargazoData::ANIOS as $anio => $info) {
            $suma = 0;
            for ($mes = 1; $mes <= 12; $mes++) {
                $suma += SargazoData::toneladasMes((int) $anio, $mes);
            }

            $this->assertEqualsWithDelta($info['total'], $suma, 13, "Año {$anio}");
        }
    }

    public function test_junio_es_el_mes_de_mayor_indice_de_toda_la_serie(): void
    {
        $maximo = 0;
        $mesMaximo = 0;

        foreach (SargazoData::ANIOS as $anio => $_) {
            for ($mes = 1; $mes <= 12; $mes++) {
                $indice = $this->predictor()->indiceMes($mes, (int) $anio);
                if ($indice > $maximo) {
                    $maximo = $indice;
                    $mesMaximo = $mes;
                }
            }
        }

        $this->assertSame(6, $mesMaximo, 'Junio debe ser el pico histórico');
        $this->assertEqualsWithDelta(100.0, $maximo, 0.1);
    }

    public function test_el_indice_crece_durante_la_temporada_y_cae_en_invierno(): void
    {
        $enero = $this->predictor()->indiceMes(1, 2026);
        $abril = $this->predictor()->indiceMes(4, 2026);
        $junio = $this->predictor()->indiceMes(6, 2026);
        $octubre = $this->predictor()->indiceMes(10, 2026);
        $diciembre = $this->predictor()->indiceMes(12, 2026);

        $this->assertLessThan($abril, $enero);
        $this->assertLessThan($junio, $abril);
        $this->assertGreaterThan($octubre, $junio);
        $this->assertLessThan($octubre, $diciembre);
    }

    public function test_los_niveles_corresponden_al_indice(): void
    {
        $casos = [
            [5, 2026, 'excesivo'],
            [6, 2026, 'excesivo'],
            [1, 2020, 'libre'],
            [12, 2024, 'libre'],
        ];

        foreach ($casos as [$mes, $anio, $esperado]) {
            $pronostico = $this->predictor()->predecirMes($mes, $anio);

            $this->assertSame($esperado, $pronostico['nivel']['key'], "Mes {$mes} de {$anio}");
            $this->assertLessThanOrEqual(100, $pronostico['indice']);
            $this->assertGreaterThanOrEqual(0, $pronostico['indice']);
        }
    }

    public function test_las_playas_expuestas_superan_a_las_protegidas(): void
    {
        $junio = $this->predictor()->predecirMes(6, 2026);

        $playas = collect($junio['playas'])->keyBy('key');

        $this->assertGreaterThan(0, $playas['delfines']['indice']);
        $this->assertGreaterThan($playas['holbox']['indice'], $playas['delfines']['indice'] ?? 0);
        $this->assertGreaterThan($playas['chinchorro']['indice'], $playas['delfines']['indice'] ?? 0);
        $this->assertSame('excesivo', $playas['ruinas']['nivel']);
        $this->assertNotSame('excesivo', $playas['holbox']['nivel']);
    }

    public function test_el_simulador_con_valores_por_defecto_reproduce_el_indice_base(): void
    {
        $valores = array_map(fn ($v) => $v['default'], SargazoPredictor::VARIABLES);
        $resultado = $this->predictor()->simular($valores, 6, 2026);

        $this->assertEqualsWithDelta(1.0, $resultado['factor'], 0.001);
        $this->assertEqualsWithDelta($resultado['base'], $resultado['simulado'], 0.1);
    }

    public function test_aumentar_la_biomasa_y_reducir_la_limpieza_empeora_el_indice(): void
    {
        $resultado = $this->predictor()->simular([
            'biomasa' => 75,
            'temperatura' => 31,
            'vientos' => 40,
            'corrientes' => 95,
            'limpieza' => 5,
        ], 4, 2026);

        $this->assertGreaterThan(1.0, $resultado['factor']);
        $this->assertGreaterThan($resultado['base'], $resultado['simulado']);
        $this->assertSame($resultado['nivel_simulado']['key'], 'excesivo');
    }

    public function test_aumentar_el_esfuerzo_de_limpieza_reduce_el_indice(): void
    {
        $resultado = $this->predictor()->simular([
            'limpieza' => 100,
        ], 6, 2026);

        $this->assertLessThan($resultado['base'], $resultado['simulado']);
    }

    public function test_la_raiz_muestra_el_sistema(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Pronóstico por mes');
        $response->assertSee('Mapa de playas');
        $response->assertSee('Simulador de variables');
    }

    public function test_el_endpoint_de_prediccion_devuelve_los_doce_meses(): void
    {
        $response = $this->postJson('/api/prediccion', ['anio' => 2026]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $this->assertCount(12, $response->json('meses'));
        $this->assertSame('Junio', $response->json('meses.6.mes_nombre'));
    }

    public function test_el_endpoint_de_simulador_devuelve_un_indice(): void
    {
        $response = $this->postJson('/api/simular', [
            'mes' => 6,
            'anio' => 2026,
            'valores' => ['biomasa' => 70],
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $this->assertArrayHasKey('simulado', $response->json('simulacion'));
        $this->assertCount(18, $response->json('playas'));
    }

    public function test_el_endpoint_de_prediccion_rechaza_anios_fuera_del_dataset(): void
    {
        $this->postJson('/api/prediccion', ['anio' => 1990])->assertStatus(422);
    }
}

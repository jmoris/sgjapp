<?php

namespace Tests\Unit;

use App\Helpers\NotaCreditoRef;
use PHPUnit\Framework\TestCase;

class NotaCreditoRefTest extends TestCase
{
    private function ref(array $over = []): array
    {
        return array_merge(['tipo' => 33, 'folio' => 100, 'fecha' => '2026-11-01', 'razon' => 'ANULA DOCUMENTO', 'codigo' => 1], $over);
    }

    public function test_referencias_conserva_codigo_sii()
    {
        foreach ([1, 2, 3] as $c) {
            $out = NotaCreditoRef::referencias([$this->ref(['codigo' => $c])]);
            $this->assertSame($c, $out[0]['codigo']);
        }
    }

    public function test_codigo_invalido_cae_en_1_y_razon_por_defecto()
    {
        $out = NotaCreditoRef::referencias([$this->ref(['codigo' => 9, 'razon' => ''])]);
        $this->assertSame(1, $out[0]['codigo']);
        $this->assertSame('Referencia', $out[0]['razon']);
    }

    public function test_validar_requiere_al_menos_una_referencia_y_codigo()
    {
        $this->assertNotEmpty(NotaCreditoRef::validarReferencias([]));
        $this->assertNotEmpty(NotaCreditoRef::validarReferencias([$this->ref(['codigo' => 7])]));
        $this->assertNotEmpty(NotaCreditoRef::validarReferencias([$this->ref(['folio' => ''])]));
        $this->assertEmpty(NotaCreditoRef::validarReferencias([$this->ref()]));
    }

    public function test_codigo_2_con_montos_es_incoherente()
    {
        $refs = [$this->ref(['codigo' => 2])];
        $this->assertTrue(NotaCreditoRef::correccionTextoConMontos($refs, [['precio' => 1000, 'cantidad' => 1]]));
        $this->assertFalse(NotaCreditoRef::correccionTextoConMontos($refs, [['precio' => 0, 'cantidad' => 1]]));
        $this->assertFalse(NotaCreditoRef::correccionTextoConMontos([$this->ref(['codigo' => 3])], [['precio' => 1000, 'cantidad' => 1]]));
    }

    public function test_total_cero_solo_valido_con_codigo_2()
    {
        $cero = [['precio' => 0, 'cantidad' => 1]];
        $this->assertFalse(NotaCreditoRef::totalCeroInvalido([$this->ref(['codigo' => 2])], $cero));
        $this->assertTrue(NotaCreditoRef::totalCeroInvalido([$this->ref(['codigo' => 1])], $cero));
        $this->assertTrue(NotaCreditoRef::totalCeroInvalido([$this->ref(['codigo' => 3])], $cero));
        $this->assertFalse(NotaCreditoRef::totalCeroInvalido([$this->ref(['codigo' => 3])], [['precio' => 100, 'cantidad' => 1]]));
    }

    public function test_totales_con_iva_19()
    {
        $t = NotaCreditoRef::totales([['precio' => 1000, 'cantidad' => 2], ['precio' => 500, 'cantidad' => 1]]);
        $this->assertSame(['neto' => 2500.0, 'iva' => 475.0, 'total' => 2975.0], $t);
    }
}

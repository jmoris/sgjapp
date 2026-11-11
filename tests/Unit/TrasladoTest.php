<?php

namespace Tests\Unit;

use App\Helpers\Traslado;
use PHPUnit\Framework\TestCase;

class TrasladoTest extends TestCase
{
    public function test_normaliza_rut()
    {
        $this->assertSame('12345678-5', Traslado::normalizarRut('012.345.678-5'));
        $this->assertSame('76123456-0', Traslado::normalizarRut('761234560'));
        $this->assertNull(Traslado::normalizarRut(''));
    }

    public function test_rut_valido()
    {
        $this->assertTrue(Traslado::rutValido('12345678-5'));
        $this->assertFalse(Traslado::rutValido('12345678-9'));
    }

    public function test_prolongado_exige_llegada_y_palabra()
    {
        $e = Traslado::validar(['ind_traslado' => 11, 'tipo_despacho' => 2, 'fecha_salida' => '2026-11-02'],
            [['nombre' => 'X', 'precio' => 10]], [], true);
        $this->assertArrayHasKey('fecha_llegada', $e);
        $this->assertArrayHasKey('items', $e);
    }

    public function test_interno_no_exige_tipo_despacho_pero_si_motivo_sin_precio()
    {
        $e = Traslado::validar(['ind_traslado' => 5, 'fecha_salida' => '2026-11-02'],
            [['nombre' => 'Taladro', 'precio' => 0]], [], true);
        $this->assertArrayNotHasKey('tipo_despacho', $e);
        $this->assertArrayHasKey('items', $e);
    }

    public function test_tercerizado_exige_datos_y_referencia()
    {
        $e = Traslado::validar(['ind_traslado' => 10, 'tipo_despacho' => 2, 'fecha_salida' => '2026-11-02'], [['precio' => 5]], [], true);
        foreach (['patente', 'rut_transportista', 'rut_chofer', 'nombre_chofer', 'referencias'] as $k) {
            $this->assertArrayHasKey($k, $e);
        }
    }

    public function test_referencia_rut_otro()
    {
        $r = Traslado::referencias([['tipo' => 52, 'folio' => 1, 'fecha' => '2026-11-01', 'razon' => '', 'rut_otro' => '12.345.678-5']]);
        $this->assertSame('12345678-5', $r[0]['rut_otro']);
        $this->assertSame(' ', $r[0]['razon']);
    }
}

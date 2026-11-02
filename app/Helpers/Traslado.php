<?php

namespace App\Helpers;

/**
 * Res. Ex. SII N° 144/2026: datos de traslado para guías (52) y facturas (33).
 */
class Traslado
{
    const CAMPOS = ['patente', 'patente_carro', 'rut_transportista', 'rut_chofer', 'nombre_chofer',
        'direccion_destino', 'comuna_destino', 'fecha_salida', 'hora_salida', 'fecha_llegada'];

    /** RUT sin puntos, con guion y sin ceros a la izquierda ("12345678-9"). */
    public static function normalizarRut($rut): ?string
    {
        if ($rut === null || trim($rut) === '') {
            return null;
        }
        $rut = strtoupper(str_replace(['.', ' '], '', trim($rut)));
        if (strpos($rut, '-') === false && strlen($rut) > 1) {
            $rut = substr($rut, 0, -1).'-'.substr($rut, -1);
        }
        return ltrim($rut, '0');
    }

    public static function rutValido($rut): bool
    {
        if (! preg_match('/^\d{1,8}-[\dK]$/', (string) $rut)) {
            return false;
        }
        [$num, $dv] = explode('-', $rut);
        $suma = 0;
        $mul = 2;
        foreach (array_reverse(str_split($num)) as $d) {
            $suma += $d * $mul;
            $mul = $mul == 7 ? 2 : $mul + 1;
        }
        $res = 11 - ($suma % 11);
        $esperado = $res == 11 ? '0' : ($res == 10 ? 'K' : (string) $res);
        return $esperado === $dv;
    }

    /**
     * Valida el bloque de traslado. Devuelve array campo => [mensajes].
     * $esGuia: aplica reglas de ind_traslado (solo guías).
     */
    public static function validar(array $in, array $items, array $refs, bool $esGuia): array
    {
        $e = [];
        $v = fn ($k) => isset($in[$k]) && trim((string) $in[$k]) !== '' ? trim((string) $in[$k]) : null;
        $ind = $esGuia ? (int) ($in['ind_traslado'] ?? 0) : null;

        if ($esGuia && ($ind < 1 || $ind > 12)) {
            $e['ind_traslado'][] = 'El tipo de traslado debe estar entre 1 y 12.';
        }
        if ($esGuia && $ind !== 5 && ! in_array((int) ($in['tipo_despacho'] ?? 0), [1, 2, 3], true)) {
            $e['tipo_despacho'][] = 'El tipo de despacho es obligatorio (1 a 3).';
        }

        foreach (['rut_chofer', 'rut_transportista'] as $c) {
            if ($v($c) && ! self::rutValido(self::normalizarRut($v($c)))) {
                $e[$c][] = 'RUT inválido.';
            }
        }
        $largos = ['patente' => 8, 'patente_carro' => 8, 'nombre_chofer' => 30, 'direccion_destino' => 70];
        foreach ($largos as $c => $max) {
            if ($v($c) && mb_strlen($v($c)) > $max) {
                $e[$c][] = "Máximo $max caracteres.";
            }
        }
        if ($v('nombre_chofer') && ! $v('rut_chofer')) {
            $e['rut_chofer'][] = 'Si informa nombre del chofer debe informar su RUT.';
        }
        if ($v('rut_chofer') && ! $v('nombre_chofer')) {
            $e['nombre_chofer'][] = 'Si informa RUT del chofer debe informar su nombre.';
        }

        $salida = $v('fecha_salida') ? strtotime($v('fecha_salida')) : null;
        $llegada = $v('fecha_llegada') ? strtotime($v('fecha_llegada')) : null;
        if ($salida && $llegada && $llegada < $salida) {
            $e['fecha_llegada'][] = 'La fecha de llegada no puede ser anterior a la de salida.';
        }

        foreach ($refs as $i => $r) {
            if (! empty($r['rut_otro']) && ! self::rutValido(self::normalizarRut($r['rut_otro']))) {
                $e['referencias'][] = 'RUT inválido en la referencia '.($i + 1).'.';
            }
        }

        if ($esGuia) {
            if (! $salida) {
                $e['fecha_salida'][] = 'La fecha de salida es obligatoria.';
            }
            if (in_array($ind, [11, 12], true)) {
                if (! $llegada) {
                    $e['fecha_llegada'][] = 'La fecha de llegada es obligatoria en traslados prolongados.';
                }
                $ok = false;
                foreach ($items as $it) {
                    $txt = mb_strtolower(($it['nombre'] ?? '').' '.($it['descripcion'] ?? ''));
                    if (strpos($txt, 'prolongado') !== false) {
                        $ok = true;
                    }
                }
                if (! $ok) {
                    $e['items'][] = 'En traslado prolongado algún ítem debe contener la palabra "prolongado" (ej. "Traslado prolongado por distancia").';
                }
            }
            if ($ind === 10) {
                foreach (['patente', 'rut_transportista', 'rut_chofer', 'nombre_chofer'] as $c) {
                    if (! $v($c)) {
                        $e[$c][] = 'Obligatorio en traslado tercerizado.';
                    }
                }
                if (count($refs) < 1) {
                    $e['referencias'][] = 'Traslado tercerizado exige referencia al documento original.';
                }
            }
            if (! in_array($ind, [1, 2, 9, 11], true)) {
                foreach ($items as $i => $it) {
                    $precio = $it['precio'] ?? 0;
                    if ((float) $precio <= 0 && trim((string) ($it['descripcion'] ?? '')) === '') {
                        $e['items'][] = 'El ítem '.($i + 1).' no tiene precio: indique en la descripción el motivo del traslado.';
                    }
                }
            }
        }

        return $e;
    }

    /** Campos de traslado listos para el payload (omite vacíos). */
    public static function payload(array $in, bool $esGuia): array
    {
        $out = [];
        $texto = ['patente', 'patente_carro', 'nombre_chofer', 'direccion_destino'];
        foreach ($texto as $c) {
            if (isset($in[$c]) && trim($in[$c]) !== '') {
                $out[$c] = Herramientas::sanitizarString($in[$c]);
            }
        }
        foreach (['rut_chofer', 'rut_transportista'] as $c) {
            if ($r = self::normalizarRut($in[$c] ?? null)) {
                $out[$c] = $r;
            }
        }
        foreach (['fecha_salida', 'hora_salida', 'fecha_llegada', 'comuna_destino'] as $c) {
            if (isset($in[$c]) && trim($in[$c]) !== '') {
                $out[$c] = trim($in[$c]);
            }
        }
        return $out;
    }

    /** Asigna los campos de traslado a un modelo (GuiaDespacho / Factura). */
    public static function aplicar($modelo, array $in): void
    {
        $p = self::payload($in, true);
        foreach (['patente', 'patente_carro', 'rut_transportista', 'rut_chofer', 'nombre_chofer',
            'direccion_destino', 'fecha_salida', 'hora_salida', 'fecha_llegada'] as $c) {
            $modelo->{$c} = $p[$c] ?? null;
        }
        $modelo->comuna_destino_id = $p['comuna_destino'] ?? null;
    }

    /** Detalle y referencias para la API. */
    public static function referencias(array $refs): array
    {
        $out = [];
        foreach ($refs as $ref) {
            $r = [
                'tipo' => $ref['tipo'],
                'folio' => $ref['folio'],
                'fecha' => $ref['fecha'],
                'razon' => ! empty($ref['razon']) ? $ref['razon'] : ' ',
                'codigo' => false,
            ];
            if ($rut = self::normalizarRut($ref['rut_otro'] ?? null)) {
                $r['rut_otro'] = $rut;
            }
            $out[] = $r;
        }
        return $out;
    }
}

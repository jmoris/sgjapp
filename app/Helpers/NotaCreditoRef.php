<?php

namespace App\Helpers;

/**
 * Referencias y validaciones propias de la emisión de Notas de Crédito (DTE 61).
 * Código de referencia SII: 1 = anula documento, 2 = corrige texto, 3 = corrige montos.
 */
class NotaCreditoRef
{
    public const CODIGOS = [1, 2, 3];

    /** Referencias normalizadas para FacturAPI v3 (con código 1/2/3). */
    public static function referencias(array $refs): array
    {
        $out = [];
        foreach ($refs as $ref) {
            $codigo = (int) ($ref['codigo'] ?? 1);
            if (! in_array($codigo, self::CODIGOS, true)) {
                $codigo = 1;
            }
            $r = [
                'tipo' => $ref['tipo'],
                'folio' => $ref['folio'],
                'fecha' => $ref['fecha'],
                'razon' => ! empty($ref['razon']) ? mb_substr($ref['razon'], 0, 90) : 'Referencia',
                'codigo' => $codigo,
            ];
            if ($rut = Traslado::normalizarRut($ref['rut_otro'] ?? null)) {
                $r['rut_otro'] = $rut;
            }
            $out[] = $r;
        }
        return $out;
    }

    /**
     * Valida las referencias de una NC. Devuelve lista de mensajes de error (vacía si es válida).
     */
    public static function validarReferencias(array $refs): array
    {
        $errores = [];
        if (count($refs) < 1) {
            return ['La nota de crédito requiere al menos una referencia al documento original.'];
        }
        foreach ($refs as $i => $ref) {
            $n = $i + 1;
            if (empty($ref['tipo']) || empty($ref['folio']) || empty($ref['fecha'])) {
                $errores[] = "La referencia #$n debe tener tipo, folio y fecha.";
            }
            if (! in_array((int) ($ref['codigo'] ?? 0), self::CODIGOS, true)) {
                $errores[] = "La referencia #$n debe tener código de referencia 1, 2 o 3.";
            }
        }
        return $errores;
    }

    /** Motivo de la NC: código de la primera referencia válida (por defecto 1). */
    public static function motivo(array $refs): int
    {
        foreach ($refs as $ref) {
            $c = (int) ($ref['codigo'] ?? 0);
            if (in_array($c, self::CODIGOS, true)) {
                return $c;
            }
        }
        return 1;
    }

    /** ¿Alguna referencia corrige texto (código 2)? */
    public static function tieneCorreccionTexto(array $refs): bool
    {
        foreach ($refs as $ref) {
            if ((int) ($ref['codigo'] ?? 0) === 2) {
                return true;
            }
        }
        return false;
    }

    /**
     * Una NC con total $0 solo es válida si corrige texto (código 2): la línea de detalle
     * con monto 0 describe la corrección. Con código 1 o 3 un total 0 es un error del usuario.
     */
    public static function totalCeroInvalido(array $refs, array $items): bool
    {
        return ! self::tieneCorreccionTexto($refs) && self::totales($items)['neto'] == 0;
    }

    /** Regla facturacionv2: código 2 (corrige texto) no admite líneas con monto > 0. */
    public static function correccionTextoConMontos(array $refs, array $items): bool
    {
        if (! self::tieneCorreccionTexto($refs)) {
            return false;
        }
        foreach ($items as $item) {
            if (((float) ($item['precio'] ?? 0)) * ((float) ($item['cantidad'] ?? 0)) > 0) {
                return true;
            }
        }
        return false;
    }

    /** Totales locales estimados (neto, IVA 19%, total) para la vista previa. */
    public static function totales(array $items): array
    {
        $neto = 0;
        foreach ($items as $item) {
            $neto += ((float) $item['precio']) * ((float) $item['cantidad']);
        }
        $neto = round($neto);
        $iva = round($neto * 0.19);
        return ['neto' => $neto, 'iva' => $iva, 'total' => $neto + $iva];
    }
}

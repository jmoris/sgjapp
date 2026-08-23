<?php

namespace App\Services;

use App\FacturaCompra;
use App\NotaCreditoCompra;
use App\OrdenCompra;
use App\PagoFacturaCompra;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * Sincroniza documentos de compra locales (FacturaCompra / NotaCreditoCompra) contra el
 * endpoint unificado de FacturAPI v3 (/compras-unificadas), que ya cruza el RCV del SII
 * con el correo de intercambio DTE en una sola fila por documento (campo `fuente`).
 * Reemplaza el merge manual previo de dos llamadas (rcvDetalle + listarComprasIntercambio)
 * repetido en Kernel.php y en los controladores de compras.
 */
class ComprasUnificadasSyncService
{
    public function __construct(protected FacturapiService $facturapi)
    {
    }

    protected function modelClass(int $tipoDoc): string
    {
        return match ($tipoDoc) {
            33 => FacturaCompra::class,
            61 => NotaCreditoCompra::class,
            default => throw new InvalidArgumentException("Tipo de documento de compra no soportado: {$tipoDoc}"),
        };
    }

    /**
     * @return Collection<int, \Illuminate\Database\Eloquent\Model> documentos que recibieron su XML en esta corrida (para notificar)
     */
    public function sincronizar(int $tipoDoc, string $periodoYyyyMm): Collection
    {
        $modelClass = $this->modelClass($tipoDoc);

        $response = $this->facturapi->listarComprasUnificadas($periodoYyyyMm, ['tipo_doc' => $tipoDoc]);
        $filas = $response->data ?? [];

        $recienRecibidos = collect();

        foreach ($filas as $fila) {
            $rutEmisor = $fila->rut_proveedor ?? null;
            $folio = $fila->folio ?? null;
            if ($rutEmisor === null || $folio === null) {
                continue;
            }

            $doc = $modelClass::where('rut_emisor', $rutEmisor)->where('folio', intval($folio))->first();
            $esNuevo = $doc === null;

            if ($esNuevo) {
                $doc = new $modelClass();
                $doc->rut_emisor = $rutEmisor;
                $doc->razon_social_emisor = $fila->razon_social_proveedor;
                $doc->folio = $folio;
                $doc->fecha_emision = $fila->fecha_emision;
                $doc->monto_neto = $fila->monto_neto;
                $doc->monto_iva = $fila->monto_iva;
                $doc->monto_total = $fila->monto_total;
                $doc->tiene_xml = false;
            }

            $eraSinXml = ! $doc->tiene_xml;
            $tieneXmlAhora = (bool) ($fila->tiene_xml ?? false);

            if ($tieneXmlAhora && $eraSinXml) {
                $doc->facturapi_compra_id = $fila->intercambio_id ?? null;
                if (! empty($fila->fecha_vencimiento)) {
                    $doc->fecha_vencimiento = date('Y-m-d', strtotime($fila->fecha_vencimiento));
                }
                $doc->tiene_xml = true;
            }

            // Conciliación de OC: se evalúa en cada corrida (no solo la primera vez que llega el
            // XML) para poder backfillear documentos que ya estaban sincronizados antes de que
            // existiera esta lógica, o cuya OC se cargó en el sistema después de la factura.
            // Antes de aplicar el vínculo (proyecto + estado) se valida que el monto total y la
            // razón social del emisor coincidan con los de la OC — el folio por sí solo no es
            // garantía suficiente de que sea realmente el mismo documento.
            if ($modelClass === FacturaCompra::class && ! empty($fila->orden_compra_folio_ref ?? null)) {
                $doc->oc_id = $fila->orden_compra_folio_ref;
                $ocdoc = OrdenCompra::with('proveedor')->where('folio', $fila->orden_compra_folio_ref)->first();
                if ($ocdoc !== null) {
                    $montoCoincide = (int) $ocdoc->monto_total === (int) $doc->monto_total;
                    $razonCoincide = $ocdoc->proveedor !== null
                        && $this->normalizarTexto($ocdoc->proveedor->razon_social) === $this->normalizarTexto($doc->razon_social_emisor);

                    if ($montoCoincide && $razonCoincide) {
                        $doc->proyecto_id = $ocdoc->proyecto_id;
                        // 3 = FACTURADA: llegó una factura de compra referenciando esta OC.
                        // No se toca si ya está ANULADA (-1).
                        if ($ocdoc->estado != -1 && $ocdoc->estado != 3) {
                            $ocdoc->estado = 3;
                            $ocdoc->save();
                        }
                    } else {
                        Log::warning('ComprasUnificadasSyncService: el folio de OC referenciado coincide pero el monto o la razón social del emisor no calzan, no se concilia automáticamente', [
                            'factura_compra_rut' => $doc->rut_emisor,
                            'factura_compra_folio' => $doc->folio,
                            'oc_id' => $ocdoc->id,
                            'oc_folio' => $ocdoc->folio,
                            'monto_oc' => $ocdoc->monto_total,
                            'monto_factura' => $doc->monto_total,
                            'razon_social_oc' => $ocdoc->proveedor->razon_social ?? null,
                            'razon_social_factura' => $doc->razon_social_emisor,
                        ]);
                    }
                }
            }

            $doc->save();

            if ($modelClass === FacturaCompra::class && ($fila->forma_pago_contado ?? false) === true) {
                $this->asegurarPagoContado($doc);
            }

            if ($eraSinXml && $doc->tiene_xml) {
                $recienRecibidos->push($doc);
            }
        }

        return $recienRecibidos;
    }

    /**
     * Normaliza texto para comparar razones sociales tolerando diferencias de
     * mayúsculas/minúsculas, puntuación y espacios (ej. "SPA" vs "S.P.A.").
     */
    protected function normalizarTexto(?string $texto): string
    {
        $texto = mb_strtoupper(trim((string) $texto));
        $texto = preg_replace('/[.,]/', '', $texto);
        $texto = preg_replace('/\s+/', ' ', $texto);

        return trim((string) $texto);
    }

    protected function asegurarPagoContado(FacturaCompra $doc): void
    {
        $tienePago = PagoFacturaCompra::where('factura_compra_id', $doc->id)->exists();
        if ($tienePago) {
            return;
        }

        $pago = new PagoFacturaCompra();
        $pago->tipo_pago = 4; // Otro
        $pago->fecha_pago = $doc->fecha_emision;
        $pago->monto_pago = $doc->monto_total;
        $pago->factura_compra_id = $doc->id;
        $pago->glosa = 'Pago generado automáticamente: el proveedor informó modalidad de pago contado';
        $pago->save();
    }
}

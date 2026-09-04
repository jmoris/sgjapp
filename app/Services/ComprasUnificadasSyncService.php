<?php

namespace App\Services;

use App\CompraPendiente;
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
     * Periodos (YYYYMM) que se sincronizan por defecto: el mes actual y el anterior.
     * El SII deja los documentos como PENDIENTE de acuse hasta 8 días corridos, por lo que
     * a comienzos de mes siguen apareciendo pendientes del mes anterior que hay que traer.
     *
     * @return list<string>
     */
    public static function periodosPorDefecto(): array
    {
        return [
            date('Ym'),
            date('Ym', strtotime('first day of previous month')),
        ];
    }

    /**
     * @param  string|list<string>  $periodos  uno o varios periodos YYYYMM
     * @return Collection<int, \Illuminate\Database\Eloquent\Model> documentos que recibieron su XML en esta corrida (para notificar)
     */
    public function sincronizar(int $tipoDoc, string|array $periodos): Collection
    {
        $modelClass = $this->modelClass($tipoDoc);
        $periodos = array_values(array_unique((array) $periodos));
        $corridaEn = now();

        // Fuente de verdad para "pendiente de acuse": /rcv/pendientes, que lee de rcv_documentos
        // ya cacheado (estado_contab=PENDIENTE explícito en la URL) — no es un cálculo propio a
        // partir de otros campos, ni una llamada en vivo al SII. De paso deja `compra_pendientes`
        // al día (acumulativo, con poda segura) para la pantalla de acuse.
        $pendientesAcuse = $this->sincronizarPendientes($tipoDoc, $periodos);

        // Un documento puede venir en ambos periodos (ej. emitido a fin de mes): se deja una sola fila.
        // Todo sale de /compras-unificadas: FacturAPI es quien sincroniza contra el SII (RCV +
        // correo de intercambio), nosotros solo leemos lo que ya dejó sincronizado. No se le pide
        // estado_contab acá: sin ese filtro es como se obtiene la mezcla completa (RCV + correo de
        // intercambio), que es lo que necesita el listado principal.
        $filas = [];
        $shapeLogueado = false;
        foreach ($periodos as $periodo) {
            $response = $this->facturapi->listarComprasUnificadas($periodo, ['tipo_doc' => $tipoDoc]);
            foreach ($response->data ?? [] as $fila) {
                if (! $shapeLogueado) {
                    Log::info('ComprasUnificadasSyncService: shape de fila /compras-unificadas', [
                        'campos' => array_keys((array) $fila),
                        'muestra' => $fila,
                    ]);
                    $shapeLogueado = true;
                }
                if (($fila->rut_proveedor ?? null) === null || ($fila->folio ?? null) === null) {
                    continue;
                }
                $filas[$this->claveDocumento($fila->rut_proveedor, $fila->folio)] = $fila;
            }
        }

        $recienRecibidos = collect();

        foreach ($filas as $fila) {
            $rutEmisor = $fila->rut_proveedor ?? null;
            $folio = $fila->folio ?? null;
            if ($rutEmisor === null || $folio === null) {
                continue;
            }

            $doc = $modelClass::where('rut_emisor', $rutEmisor)->where('folio', intval($folio))->first();
            $esNuevo = $doc === null;

            $pendienteAcuse = isset($pendientesAcuse[$this->claveDocumento($rutEmisor, $folio)]);

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

            // El listado unificado no siempre expone el mismo nombre de campo para el id del
            // intercambio ni para la disponibilidad del XML (además, documentos migrados pueden
            // llegar sin marca aunque su XML exista). Se aceptan varios alias y la presencia de
            // cualquier identificador de intercambio o de un xml_path como señal de "tiene XML".
            $intercambioId = $fila->intercambio_id
                ?? $fila->documento_compra_intercambio_id
                ?? (isset($fila->intercambio->id) ? $fila->intercambio->id : null);
            $tieneXmlAhora = (bool) ($fila->tiene_xml ?? $fila->tiene_intercambio ?? false)
                || ! empty($fila->xml_path)
                || $intercambioId !== null;

            if ($tieneXmlAhora && $eraSinXml) {
                if ($intercambioId !== null) {
                    $doc->facturapi_compra_id = $intercambioId;
                }
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

            $doc->pendiente_acuse = $pendienteAcuse;

            $doc->save();

            if ($modelClass === FacturaCompra::class && ($fila->forma_pago_contado ?? false) === true) {
                $this->asegurarPagoContado($doc);
            }

            // Solo se notifica cuando el documento ya tiene acuse de recibo: mientras siga
            // PENDIENTE en el SII no aparece en el listado, así que no tiene sentido avisar.
            if ($eraSinXml && $doc->tiene_xml && ! $pendienteAcuse) {
                $recienRecibidos->push($doc);
            }
        }

        return $recienRecibidos;
    }

    /**
     * Trae los documentos PENDIENTE de acuse desde /rcv/pendientes (cacheado en rcv_documentos,
     * sin pegarle en vivo al SII), persiste/actualiza cada uno en la tabla local
     * `compra_pendientes` y poda las filas que ya no aparecen pendientes. Devuelve el mapa
     * "RUT-DV|folio" => true que usa sincronizar() para marcar `pendiente_acuse`.
     *
     * La poda solo aplica a periodos cuya llamada fue exitosa, para no perder pendientes locales
     * por un vacío transitorio de la API en algún periodo.
     *
     * @param  list<string>  $periodos
     * @return array<string, true>
     */
    protected function sincronizarPendientes(int $tipoDoc, array $periodos): array
    {
        $corridaEn = now();
        $pendientes = [];
        $periodosOk = [];

        foreach ($periodos as $periodo) {
            $response = $this->facturapi->rcvPendientes($periodo, $tipoDoc, 'compra');
            if (($response->success ?? null) !== false) {
                $periodosOk[] = $periodo;
            }

            foreach ($response->data ?? [] as $fila) {
                $rut = $fila->rut_contraparte ?? null;
                $dv = $fila->dv_contraparte ?? null;
                $folio = $fila->folio ?? null;
                if ($rut === null || $folio === null) {
                    continue;
                }
                $rutCompleto = ! empty($dv) ? "{$rut}-{$dv}" : (string) $rut;
                $pendientes[$this->claveDocumento($rutCompleto, $folio)] = true;

                CompraPendiente::updateOrCreate(
                    ['tipo_doc' => $tipoDoc, 'rut_emisor' => $rutCompleto, 'folio' => intval($folio)],
                    [
                        'razon_social' => $fila->razon_social_contraparte ?? null,
                        'fecha_emision' => $fila->fecha_emision ?? null,
                        'monto_total' => (int) ($fila->monto_total ?? 0),
                        'periodo' => $fila->periodo_tributario ?? $periodo,
                        'last_seen_at' => $corridaEn,
                    ]
                );
            }
        }

        if (! empty($periodosOk)) {
            CompraPendiente::where('tipo_doc', $tipoDoc)
                ->whereIn('periodo', $periodosOk)
                ->where('last_seen_at', '<', $corridaEn)
                ->delete();
        }

        return $pendientes;
    }

    /**
     * Clave normalizada para cruzar filas de /compras-unificadas con /rcv/pendientes y para
     * deduplicar entre periodos: RUT en mayúsculas y sin espacios + folio como entero.
     */
    protected function claveDocumento(string $rut, int|string $folio): string
    {
        $rut = mb_strtoupper(str_replace([' ', '.'], '', trim($rut)));

        return $rut.'|'.intval($folio);
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

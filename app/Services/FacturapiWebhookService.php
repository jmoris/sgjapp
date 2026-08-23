<?php

namespace App\Services;

use App\DocumentoPendiente;
use App\Factura;
use App\FacturapiWebhookDelivery;
use App\GuiaDespacho;
use App\NotaCredito;
use App\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use JsonException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Consumidor de webhooks FacturAPI v3 (firma HMAC, envelope con type + data).
 * Reemplaza el polling de estado como vía principal; el polling en Kernel.php
 * queda como respaldo de baja frecuencia por si un webhook no llega.
 */
class FacturapiWebhookService
{
    private const TIMESTAMP_TOLERANCE_SECONDS = 300;

    public const EVENT_DOCUMENT_ISSUED = 'document.issued';

    public const EVENT_DOCUMENT_STATUS_UPDATED = 'document.status_updated';

    public const EVENT_INTERCAMBIO_CORREO_SENT = 'document.intercambio_correo_sent';

    public const EVENT_INTERCAMBIO_CORREO_DELIVERED = 'document.intercambio_correo_delivered';

    public const EVENT_INTERCAMBIO_CORREO_FAILED = 'document.intercambio_correo_failed';

    public function handleRequest(Request $request): SymfonyResponse
    {
        $rawBody = $request->getContent();
        $timestampHeader = $request->header('X-FacturAPI-Timestamp', '');
        $signatureHeader = $request->header('X-FacturAPI-Signature', '');
        $deliveryIdHeader = $request->header('X-FacturAPI-Delivery-Id', '');
        $tenantHeader = trim((string) $request->header('X-Tenant', ''));

        if ($rawBody === '' || $timestampHeader === '' || $signatureHeader === '') {
            return response('Cabeceras o cuerpo faltantes.', 400);
        }

        try {
            $payload = json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            return response('JSON inválido.', 400);
        }

        if (! is_array($payload)) {
            return response('Cuerpo inválido.', 400);
        }

        $dataPre = isset($payload['data']) && is_array($payload['data']) ? $payload['data'] : [];
        $tenantFromBody = isset($dataPre['tenant_id']) && is_string($dataPre['tenant_id']) ? trim($dataPre['tenant_id']) : '';
        $tenantId = $tenantFromBody !== '' ? $tenantFromBody : $tenantHeader;

        if ($tenantFromBody !== '' && $tenantHeader !== '' && ! hash_equals($tenantFromBody, $tenantHeader)) {
            return response('Inconsistencia de tenant en cabecera y cuerpo.', 400);
        }

        if ($tenantId === '') {
            Log::warning('FacturapiWebhookService: sin tenant_id en cuerpo ni X-Tenant (previo a firma)');

            return response()->noContent(200);
        }

        $tenant = Tenant::where('facturapi_tenant_id', $tenantId)->first();
        if ($tenant === null) {
            Log::warning('FacturapiWebhookService: tenant desconocido (previo a firma)', ['facturapi_tenant_id' => $tenantId]);

            return response()->noContent(200);
        }

        $secret = trim((string) ($tenant->facturapi_webhook_secret ?? ''));
        if ($secret === '') {
            Log::info('FacturapiWebhookService: tenant sin secreto de webhook configurado (webhook no activo)', ['tenant_id' => $tenant->id]);

            return response()->noContent(200);
        }

        if (! ctype_digit($timestampHeader)) {
            return response('Timestamp inválido.', 400);
        }

        $timestamp = (int) $timestampHeader;
        if (abs(time() - $timestamp) > self::TIMESTAMP_TOLERANCE_SECONDS) {
            return response('Timestamp fuera de ventana.', 401);
        }

        if (! preg_match('/^v1=([a-f0-9]{64})$/', $signatureHeader, $m)) {
            return response('Firma con formato inválido.', 401);
        }
        $providedHex = $m[1];

        $message = $timestamp.'.'.$rawBody;
        $expectedHex = hash_hmac('sha256', $message, $secret);

        if (! hash_equals($expectedHex, $providedHex)) {
            return response('Firma incorrecta.', 401);
        }

        $eventId = isset($payload['id']) && is_string($payload['id']) ? $payload['id'] : $deliveryIdHeader;
        $type = isset($payload['type']) && is_string($payload['type']) ? $payload['type'] : null;
        $data = isset($payload['data']) && is_array($payload['data']) ? $payload['data'] : [];

        if ($type === null || $type === '') {
            return response('Evento sin tipo.', 400);
        }

        $tenant->makeCurrent();

        try {
            if ($eventId !== '') {
                $delivery = FacturapiWebhookDelivery::firstOrCreate(
                    ['delivery_id' => $eventId],
                    ['event_type' => $type]
                );
                if (! $delivery->wasRecentlyCreated) {
                    return response()->noContent(200);
                }
            } else {
                Log::warning('FacturapiWebhookService: sin id de entrega; idempotencia desactivada para este request');
            }

            $this->dispatchByType($type, $data);
        } finally {
            Tenant::forgetCurrent();
        }

        return response()->noContent(200);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function dispatchByType(string $type, array $data): void
    {
        if ($type === self::EVENT_DOCUMENT_ISSUED || $type === self::EVENT_DOCUMENT_STATUS_UPDATED) {
            $this->syncEstadoDocumento($type, $data);

            return;
        }

        if (
            $type === self::EVENT_INTERCAMBIO_CORREO_SENT
            || $type === self::EVENT_INTERCAMBIO_CORREO_DELIVERED
            || $type === self::EVENT_INTERCAMBIO_CORREO_FAILED
        ) {
            $this->syncCorreoIntercambio($type, $data);

            return;
        }

        Log::info('FacturapiWebhookService: tipo de evento no gestionado', ['type' => $type]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncEstadoDocumento(string $type, array $data): void
    {
        $tipoDoc = isset($data['tipo_doc']) ? (int) $data['tipo_doc'] : null;
        if ($tipoDoc === null) {
            Log::warning('FacturapiWebhookService: evento sin tipo_doc', ['type' => $type]);

            return;
        }

        $documento = $this->resolveDocumento($tipoDoc, $data);
        if ($documento === null) {
            Log::warning('FacturapiWebhookService: documento no encontrado', [
                'type' => $type,
                'tipo_doc' => $tipoDoc,
                'track_id' => $data['track_id'] ?? null,
                'folio' => $data['folio'] ?? null,
            ]);

            return;
        }

        $this->aplicarFolioYTrackId($documento, $data);

        $codigo = isset($data['estado_codigo']) ? (int) $data['estado_codigo'] : null;
        $nuevoDigitoSii = $type === self::EVENT_DOCUMENT_ISSUED ? 0 : $this->digitoSiiDesdeEstadoCodigo($codigo);

        if ($documento instanceof GuiaDespacho) {
            $actual = (int) $documento->estado;
            if ($actual !== 3 && $nuevoDigitoSii !== null) {
                $documento->estado = $nuevoDigitoSii;
            }
        } elseif ($documento instanceof Factura) {
            $this->actualizarDigitoSii($documento, 3, $nuevoDigitoSii);
        } elseif ($documento instanceof NotaCredito) {
            $this->actualizarDigitoSii($documento, 2, $nuevoDigitoSii);
        }

        $documento->save();

        DocumentoPendiente::where('tipo_doc', $tipoDoc)->where('folio', $documento->folio)->delete();

        Log::info('FacturapiWebhookService: estado de documento actualizado', [
            'type' => $type,
            'modelo' => get_class($documento),
            'id' => $documento->id,
            'estado' => $documento->estado,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncCorreoIntercambio(string $type, array $data): void
    {
        $tipoDoc = isset($data['tipo_doc']) ? (int) $data['tipo_doc'] : null;
        if ($tipoDoc === null) {
            Log::warning('FacturapiWebhookService: evento de correo sin tipo_doc', ['type' => $type]);

            return;
        }

        // Las guías de despacho solo trackean estado SII; hoy no distinguen estado de correo (igual que el polling actual).
        if ($tipoDoc === 52) {
            Log::info('FacturapiWebhookService: evento de correo intercambio ignorado para guía de despacho (no se trackea)', [
                'type' => $type,
                'folio' => $data['folio'] ?? null,
            ]);

            return;
        }

        $documento = $this->resolveDocumento($tipoDoc, $data);
        if ($documento === null) {
            Log::warning('FacturapiWebhookService: documento no encontrado (correo intercambio)', [
                'type' => $type,
                'tipo_doc' => $tipoDoc,
                'track_id' => $data['track_id'] ?? null,
                'folio' => $data['folio'] ?? null,
            ]);

            return;
        }

        $this->aplicarFolioYTrackId($documento, $data);

        $nuevoDigitoXml = match ($type) {
            self::EVENT_INTERCAMBIO_CORREO_DELIVERED => 1,
            self::EVENT_INTERCAMBIO_CORREO_FAILED => 2,
            default => null, // sent: sgjapp no distingue "enviado" de "en proceso", no se toca.
        };

        if ($nuevoDigitoXml !== null) {
            if ($documento instanceof Factura) {
                $this->actualizarDigitoXml($documento, 3, $nuevoDigitoXml);
            } elseif ($documento instanceof NotaCredito) {
                $this->actualizarDigitoXml($documento, 2, $nuevoDigitoXml);
            }
        }

        $documento->save();

        DocumentoPendiente::where('tipo_doc', $tipoDoc)->where('folio', $documento->folio)->delete();

        Log::info('FacturapiWebhookService: correo intercambio actualizado', [
            'type' => $type,
            'modelo' => get_class($documento),
            'id' => $documento->id,
            'estado' => $documento->estado,
        ]);
    }

    /**
     * Reemplaza el primer dígito (SII) del estado, salvo que ya esté anulado (3): eso no se pisa vía webhook.
     */
    private function actualizarDigitoSii($documento, int $longitud, ?int $nuevoDigito): void
    {
        if ($nuevoDigito === null) {
            return;
        }

        $estado = str_pad((string) $documento->estado, $longitud, '0', STR_PAD_LEFT);
        if ((int) $estado[0] === 3) {
            return;
        }

        $estado[0] = (string) $nuevoDigito;
        $documento->estado = $estado;
    }

    /**
     * Reemplaza el segundo dígito (XML/correo) del estado.
     */
    private function actualizarDigitoXml($documento, int $longitud, int $nuevoDigito): void
    {
        $estado = str_pad((string) $documento->estado, $longitud, '0', STR_PAD_LEFT);
        $estado[1] = (string) $nuevoDigito;
        $documento->estado = $estado;
    }

    /**
     * FacturAPI estado_codigo (0 sin envío, 1 pendiente SII, 2 aceptado, 3 rechazado)
     * → dígito SII local (0 en proceso, 1 aceptado, 2 rechazado). El dígito 3 (anulado)
     * no lo produce este mapeo, se setea por otra vía (nota de crédito, no vía webhook).
     */
    private function digitoSiiDesdeEstadoCodigo(?int $codigo): ?int
    {
        if ($codigo === null) {
            return null;
        }

        return match ($codigo) {
            0, 1 => 0,
            2 => 1,
            3 => 2,
            default => 0,
        };
    }

    private function resolveDocumento(int $tipoDoc, array $data): Factura|GuiaDespacho|NotaCredito|null
    {
        return match ($tipoDoc) {
            33 => $this->resolveFactura($data),
            52 => $this->resolveGuiaDespacho($data),
            61 => $this->resolveNotaCredito($data),
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function resolveFactura(array $data): ?Factura
    {
        $trackId = isset($data['track_id']) ? trim((string) $data['track_id']) : '';
        if ($trackId !== '' && $trackId !== '0') {
            $found = Factura::where('track_id', $trackId)->first();
            if ($found !== null) {
                return $found;
            }
        }

        return $this->resolvePorFolioYRut(Factura::class, $data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function resolveGuiaDespacho(array $data): ?GuiaDespacho
    {
        $trackId = isset($data['track_id']) ? trim((string) $data['track_id']) : '';
        if ($trackId !== '' && $trackId !== '0') {
            $found = GuiaDespacho::where('track_id', $trackId)->first();
            if ($found !== null) {
                return $found;
            }
        }

        return $this->resolvePorFolioYRut(GuiaDespacho::class, $data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function resolveNotaCredito(array $data): ?NotaCredito
    {
        $trackId = isset($data['track_id']) ? trim((string) $data['track_id']) : '';
        if ($trackId !== '' && $trackId !== '0') {
            $found = NotaCredito::where('track_id', $trackId)->first();
            if ($found !== null) {
                return $found;
            }
        }

        return $this->resolvePorFolioYRut(NotaCredito::class, $data);
    }

    /**
     * @param  class-string<Factura|GuiaDespacho|NotaCredito>  $modelo
     * @param  array<string, mixed>  $data
     */
    private function resolvePorFolioYRut(string $modelo, array $data)
    {
        $folio = isset($data['folio']) ? trim((string) $data['folio']) : '';
        $rutCliente = isset($data['rut_cliente']) ? trim((string) $data['rut_cliente']) : '';
        if ($folio === '' || $rutCliente === '') {
            return null;
        }

        $rutComparable = $this->rutComparable($rutCliente);
        if ($rutComparable === '') {
            return null;
        }

        return $modelo::where('folio', $folio)
            ->whereHas('cliente', function ($q) use ($rutComparable): void {
                $q->whereRaw(
                    "REPLACE(REPLACE(REPLACE(UPPER(TRIM(rut)), '.', ''), '-', ''), ' ', '') = ?",
                    [$rutComparable]
                );
            })
            ->first();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function aplicarFolioYTrackId($documento, array $data): void
    {
        $folio = isset($data['folio']) ? trim((string) $data['folio']) : '';
        $trackId = isset($data['track_id']) ? trim((string) $data['track_id']) : '';

        if ($folio !== '') {
            $documento->folio = $folio;
        }
        if ($trackId !== '' && $trackId !== '0') {
            $documento->track_id = $trackId;
        }
    }

    private function rutComparable(string $rut): string
    {
        return strtoupper(preg_replace('/[^0-9kK]/', '', $rut) ?? '');
    }
}

<?php

namespace App\Services;

use App\Tenant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/**
 * Cliente HTTP central para FacturAPI v3. Reemplaza los curl crudos dispersos
 * en los controladores; toda llamada nueva a FacturAPI debe agregarse aquí.
 */
class FacturapiService
{
    protected function tenant(): Tenant
    {
        $tenant = Tenant::current();
        if ($tenant === null) {
            throw new RuntimeException('No hay un tenant activo para resolver las credenciales de FacturAPI.');
        }

        return $tenant;
    }

    protected function client()
    {
        $tenant = $this->tenant();
        $endpoint = rtrim((string) config('services.facturapi.endpoint'), '/');

        return Http::withToken($tenant->facturapi_token)
            ->withHeaders(array_filter([
                'X-Tenant' => $tenant->facturapi_tenant_id,
                'Accept' => 'application/json',
            ]))
            ->timeout((int) config('services.facturapi.timeout', 60))
            ->baseUrl($endpoint);
    }

    protected function post(string $path, array $payload): object
    {
        try {
            $response = $this->client()->post($path, $payload);
        } catch (Throwable $e) {
            Log::error("FacturapiService: error de conexion en POST {$path}", ['error' => $e->getMessage()]);

            return (object) ['success' => false, 'msg' => 'No se pudo conectar con FacturAPI', 'error' => $e->getMessage()];
        }

        return $this->normalizarRespuesta($response, $path);
    }

    protected function get(string $path, array $query = []): object
    {
        try {
            $response = $this->client()->get($path, $query);
        } catch (Throwable $e) {
            Log::error("FacturapiService: error de conexion en GET {$path}", ['error' => $e->getMessage()]);

            return (object) ['success' => false, 'msg' => 'No se pudo conectar con FacturAPI', 'error' => $e->getMessage()];
        }

        return $this->normalizarRespuesta($response, $path);
    }

    protected function getRaw(string $path, array $query = []): ?string
    {
        try {
            $response = $this->client()->get($path, $query);
        } catch (Throwable $e) {
            Log::error("FacturapiService: error de conexion en GET {$path}", ['query' => $query, 'error' => $e->getMessage()]);

            return null;
        }

        if (! $response->successful()) {
            Log::warning("FacturapiService: respuesta no exitosa en GET {$path}", [
                'query' => $query,
                'status' => $response->status(),
                'body' => mb_substr($response->body(), 0, 2000),
            ]);

            return null;
        }

        $body = $response->body();
        if (trim((string) $body) === '') {
            Log::warning("FacturapiService: respuesta vacia (200) en GET {$path}", ['query' => $query]);

            return null;
        }

        return $body;
    }

    protected function normalizarRespuesta($response, string $path): object
    {
        $body = $response->object();

        if (! $response->successful() || ! is_object($body)) {
            Log::warning("FacturapiService: respuesta no exitosa en {$path}", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            $body = is_object($body) ? $body : (object) [];
            $body->success = $body->success ?? false;
            $body->msg = $body->msg ?? ($body->error ?? 'No se pudo completar la operación en FacturAPI');

            return $body;
        }

        return $body;
    }

    /*
    |--------------------------------------------------------------------------
    | Endpoints verificados contra la implementación real de v3
    |--------------------------------------------------------------------------
    */

    public function emitirDocumento(array $payload): object
    {
        $payload = array_merge(['return_ted' => false], $payload);

        return $this->post('/documentos/emitir', $payload);
    }

    public function cederDocumentos(array $documentos, array $factoring): object
    {
        $payload = array_merge(['documentos' => $documentos], $factoring);

        return $this->post('/documentos/cesionar', $payload);
    }

    public function consultarCesion(string $facturapiCesionId): object
    {
        return $this->get("/cesiones/{$facturapiCesionId}");
    }

    public function rcvDetalle(int $tipoDoc, string $periodoYyyyMm, ?string $detalle = null): object
    {
        $query = array_filter([
            'periodo' => $periodoYyyyMm,
            'detalle' => $detalle,
        ]);

        return $this->get("/rcv/detalle/recibidos/{$tipoDoc}", $query);
    }

    /**
     * Documentos del RCV cacheados en `rcv_documentos` (no le pega en vivo al SII, ya viene
     * sincronizado por FacturAPI). Filtrando `estado_contab` en la URL nos ahorra traer y
     * descartar filas: para "pendientes de acuse" siempre se pide estado_contab=PENDIENTE.
     */
    public function rcvPendientes(string $periodoYyyyMm, ?int $tipoDoc = null, ?string $operacion = null): object
    {
        $query = array_filter([
            'periodo' => $periodoYyyyMm,
            'estado_contab' => 'PENDIENTE',
            'operacion' => $operacion,
            'tipo_doc' => $tipoDoc,
        ]);

        return $this->get('/rcv/pendientes', $query);
    }

    public function rcvAgregarEvento(int $tipoDoc, string $rutReceptor, int $folio, string $evento): object
    {
        return $this->post('/rcv/agregarevento', [
            'rut_receptor' => $rutReceptor,
            'tipo' => $tipoDoc,
            'folio' => $folio,
            'evento' => $evento,
        ]);
    }

    /**
     * @param  array{per_page?: int, page?: int, tipo_doc?: int, rut_emisor?: string, periodo?: string, fecha_desde?: string, fecha_hasta?: string, orden_compra_id?: int}  $filtros
     */
    public function listarComprasIntercambio(array $filtros = []): object
    {
        return $this->get('/compras-intercambio', $filtros);
    }

    public function obtenerXmlCompraIntercambio(int $id): ?string
    {
        return $this->getRaw("/compras-intercambio/{$id}/xml");
    }

    /**
     * Listado unificado de compras del período (RCV + correo de intercambio ya cruzados
     * por FacturAPI, campo `fuente` indica de dónde viene cada fila: rcv, intercambio o ambos).
     *
     * @param  array{estado_contab?: string, tipo_doc?: int}  $filtros
     */
    public function listarComprasUnificadas(string $periodoYyyyMm, array $filtros = []): object
    {
        return $this->get('/compras-unificadas', array_merge(['periodo' => $periodoYyyyMm], $filtros));
    }

    public function detalleCompraUnificadaIntercambio(int $intercambioId): object
    {
        return $this->get("/compras-unificadas/intercambio/{$intercambioId}/detalle");
    }

    /**
     * XML crudo del documento de compra recibido por correo de intercambio, vía el endpoint
     * unificado (reemplaza a obtenerXmlCompraIntercambio()/`/compras-intercambio/{id}/xml`,
     * que dejó de existir en v3 — confirmado con 404 real).
     */
    public function obtenerXmlCompraUnificadaIntercambio(int $tipoDoc, $folio, ?string $rutEmisor = null): ?string
    {
        // rut_emisor se envía como filtro para desambiguar cuando dos emisores usan el mismo folio.
        $query = $rutEmisor !== null ? ['rut_emisor' => $rutEmisor] : [];

        return $this->getRaw("/compras-unificadas/intercambio/{$tipoDoc}/{$folio}/xml", $query);
    }

    public function consultarDatosTributarios(): object
    {
        return $this->get('/datos-tributarios');
    }

    /**
     * Catálogo de contribuyente por RUT (razón social, giros/actividades económicas,
     * direcciones registradas). Reemplaza al lookup directo por SOAP/cookies del SII
     * (`MaestroController::getInfoContribuyente()`, vía `Sii::getInfoContribuyente()`).
     */
    public function consultarCatalogoContribuyente(string $rut): object
    {
        return $this->get('/catalogo/contribuyente', ['rut' => $rut]);
    }

    public function consultarEstadoSii(string $trackId): object
    {
        return $this->post('/documentos/consulta-estado', ['trackid' => $trackId]);
    }

    public function obtenerXmlDocumentoPropio(int $tipo, $folio): ?string
    {
        return $this->getRaw("/documentos/{$tipo}/{$folio}/xml");
    }

    /*
    |--------------------------------------------------------------------------
    | Endpoints NO verificados contra v3 (mismas rutas que v2)
    |--------------------------------------------------------------------------
    | facturacionv2 no lo usa porque resolvió el estado vía webhooks en vez de
    | polling. Si FacturAPI no lo mantuvo en v3, hay que implementarlo allí
    | antes de que este flujo funcione en producción.
    */

    /**
     * @unverified-endpoint GET documentos/{tipo}/{folio} — sin confirmar en v3.
     */
    public function consultarEstadoCorreo(int $tipo, $folio): object
    {
        Log::warning('FacturapiService::consultarEstadoCorreo - endpoint no verificado en v3', [
            'tipo' => $tipo,
            'folio' => $folio,
        ]);

        return $this->get("/documentos/{$tipo}/{$folio}");
    }
}

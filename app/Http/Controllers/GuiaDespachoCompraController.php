<?php

namespace App\Http\Controllers;

use App\Services\ComprasUnificadasSyncService;
use App\Services\FacturapiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use SolucionTotal\CoreDTE\Sii\EnvioDte;

class GuiaDespachoCompraController extends Controller
{
    protected FacturapiService $facturapi;

    public function __construct(FacturapiService $facturapi)
    {
        $this->facturapi = $facturapi;
    }

    public function index(){
        return view('pages.compras.guias.index', ['documentos' => $this->listarGuiasCompra()]);
    }

    /**
     * Listado de guías de despacho de compra recibidas por correo de intercambio, vía el endpoint
     * unificado de v3 (`/compras-unificadas`, tipo_doc 52), mes actual + anterior. Reemplaza a
     * `/compras-intercambio`, que dejó de existir en v3 (por eso el listado salía vacío).
     *
     * Las guías de compra no se persisten localmente: se consultan siempre en tiempo real.
     *
     * @return array<int, object>
     */
    protected function listarGuiasCompra(): array
    {
        $documentos = [];
        foreach (ComprasUnificadasSyncService::periodosPorDefecto() as $periodo) {
            $response = $this->facturapi->listarComprasUnificadas($periodo, ['tipo_doc' => 52]);
            foreach ($response->data ?? [] as $fila) {
                $folio = $fila->folio ?? null;
                $rut = $fila->rut_proveedor ?? null;
                if ($folio === null || $rut === null) {
                    continue;
                }
                $documentos[$rut.'|'.$folio] = (object) [
                    'folio' => $folio,
                    'rut_emisor' => $rut,
                    'razon_social_emisor' => $fila->razon_social_proveedor ?? '',
                    'fecha_emision' => $fila->fecha_emision ?? null,
                    'monto_total' => $fila->monto_total ?? 0,
                    'tipo_doc' => 52,
                ];
            }
        }

        return array_values($documentos);
    }

    /**
     * Obtiene y parsea el XML de una guía de despacho de compra por folio (+ rut_emisor),
     * vía `/compras-unificadas/intercambio/52/{folio}/xml`.
     *
     * @return array{data: array, ted: mixed, caratula: array}|null
     */
    protected function obtenerGuiaCompra($rutEmisor, $folio): ?array
    {
        $result = $this->facturapi->obtenerXmlCompraUnificadaIntercambio(52, $folio, $rutEmisor);
        if (empty($result)) {
            return null;
        }

        $EnvioDTE = new EnvioDte();
        $EnvioDTE->loadXML($result);
        $documentos = $EnvioDTE->getDocumentos();
        if (empty($documentos)) {
            Log::warning('GuiaDespachoCompraController: el XML de compra unificada no contiene documentos DTE', ['folio' => $folio]);
            return null;
        }

        $dte = $documentos[0];
        $caratula = str_contains($result, '<EnvioDTE')
            ? $EnvioDTE->getCaratula()
            : ['FchResol' => date('Y'), 'NroResol' => 0];

        return [
            'data' => $dte->getDatos(),
            'ted' => $dte->getTED(),
            'caratula' => $caratula,
        ];
    }

    public function show($rutEmisor, $folio){
        $doc = $this->obtenerGuiaCompra($rutEmisor, $folio);
        if($doc == null){
            return back()->with('error', 'No se pudo obtener el XML de esta guía de despacho desde FacturAPI.');
        }

        return view('pages.compras.guias.detail', ['documento' => $doc['data']]);
    }

    /*
        DESDE AQUI HACIA ABAJO ESTARAN LAS FUNCIONES DE LA API
    */
    public function getAll(){
        return (object) ['data' => $this->listarGuiasCompra()];
    }

    public function vistaPreviaFactura(Request $request, $rutEmisor, $tipo, $folio){
        $doc = $this->obtenerGuiaCompra($rutEmisor, $folio);
        if($doc == null){
            return response()->json([
                'status' => 500,
                'msg' => 'No se pudo obtener el XML de esta guía de despacho desde FacturAPI.'
            ], 500);
        }

        $pdf = new \SolucionTotal\CorePDF\PDF($doc['data'], 1, url('/vacio.png'), 2, $doc['ted']);
        $pdf->setResolucion(date('Y', strtotime($doc['caratula']['FchResol'])), $doc['caratula']['NroResol']);
        $pdf->construir();
        $pdf->generar(1);
    }
}

<?php

namespace App\Http\Controllers;

use App\CategoriaDocumento;
use App\Helpers\Ajustes;
use App\NotaCreditoCompra;
use App\Notifications\DocumentoRecibido;
use App\PagoNotaCreditoCompra;
use App\Services\ComprasUnificadasSyncService;
use App\Services\FacturapiService;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use SolucionTotal\CoreDTE\Sii\EnvioDte;
use Yajra\DataTables\Facades\DataTables;

class NotaCreditoCompraController extends Controller
{
    protected FacturapiService $facturapi;

    public function __construct(FacturapiService $facturapi)
    {
        $this->facturapi = $facturapi;
    }

    public function index(Request $request){
        return view('pages.compras.notascredito.index');
    }

    public function show($rutEmisor, $folio){
        $categorias = CategoriaDocumento::all();
        $fact = NotaCreditoCompra::where('rut_emisor', $rutEmisor)->where('folio', $folio)->first();
        if($fact == null){
            return back()->with('error', 'No se encontró la nota de crédito de compra solicitada.');
        }
        $doc = $this->obtenerDocumentoCompra($fact);
        if($doc == null){
            return back()->with('error', 'No se pudo obtener el XML de este documento desde FacturAPI.');
        }
        return view('pages.compras.notascredito.detail', ['documento' => $doc['data'], 'factura' => $fact, 'categorias' => $categorias]);
    }

    /**
     * Obtiene y parsea el XML de una nota de crédito de compra recibida, vía el endpoint
     * unificado `/compras-unificadas/intercambio/61/{folio}/xml`. No depende de
     * `tiene_xml` / `facturapi_compra_id` locales: pregunta directo a FacturAPI y, si el XML
     * existe, deja el registro local marcado.
     *
     * @return array{data: array, ted: mixed, caratula: array}|null
     */
    protected function obtenerDocumentoCompra(NotaCreditoCompra $fact): ?array
    {
        $result = $this->facturapi->obtenerXmlCompraUnificadaIntercambio(61, $fact->folio, $fact->rut_emisor);
        if (empty($result)) {
            return null;
        }

        if (! $fact->tiene_xml) {
            $fact->tiene_xml = true;
            $fact->save();
        }

        $EnvioDTE = new EnvioDte();
        $EnvioDTE->loadXML($result);
        $documentos = $EnvioDTE->getDocumentos();
        if (empty($documentos)) {
            Log::warning('NotaCreditoCompraController: el XML de compra unificada no contiene documentos DTE', ['folio' => $fact->folio]);
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

    /*
        DESDE AQUI HACIA ABAJO ESTARAN LAS FUNCIONES DE LA API
    */
    public function getAll(Request $request){
        /*$emisor = Ajustes::getEmisor();
        $endpoint =  env('FACTURAPI_ENDPOINT').'documentos/compras?contribuyente='.$emisor['rut'].'&tipo=33';
        $ch = curl_init( $endpoint );
            curl_setopt( $ch, CURLOPT_POST, false);
            curl_setopt( $ch, CURLOPT_HTTPHEADER, [
                'Content-Type:application/json',
                'Authorization: Bearer '.env('FACTURAPI_TOKEN')
            ]);
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            $result = curl_exec($ch);
            curl_close($ch);
            $docData = json_decode($result);
            Log::info("ENDPOINT FACTURAS COMPRA: ". $endpoint);

            return $result;*/
            // Se excluyen las notas de crédito que el SII todavía tiene PENDIENTE de acuse de recibo.
            $data = NotaCreditoCompra::where('pendiente_acuse', false);

            if($request->has('feMinDate') and $request->has('feMaxDate')){
                $data->where('fecha_emision', '>=', $request->feMinDate);
                $data->where('fecha_emision', '<=', date('Y-m-d', strtotime($request->feMaxDate)));
                //$data->whereBetween('fecha_emision', [$request->feMinDate, $request->feMaxDate]);
            }
            return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function vistaPreviaNC(Request $request, $rutEmisor, $tipo, $folio){
        $fact = NotaCreditoCompra::where('rut_emisor', $rutEmisor)->where('folio', $folio)->first();
        if($fact == null){
            return response()->json([
                'status' => 500,
                'msg' => 'No se encontró la nota de crédito de compra solicitada.'
            ], 500);
        }
        $doc = $this->obtenerDocumentoCompra($fact);
        if($doc == null){
            return response()->json([
                'status' => 500,
                'msg' => 'No se pudo obtener el XML de este documento desde FacturAPI.'
            ], 500);
        }

        $pdf = new \SolucionTotal\CorePDF\PDF($doc['data'], 1, url('/vacio.png'), 2, $doc['ted']);
        $pdf->setResolucion(date('Y', strtotime($doc['caratula']['FchResol'])), $doc['caratula']['NroResol']);
        $pdf->construir();
        if($request->descargar==1){
            $pdf->generar(0);
        }else{
            $pdf->generar(1);
        }
    }

    public function descargarPDF(Request $request, $emisor, $folio){
        try{
            $fact = NotaCreditoCompra::where('rut_emisor', $emisor)->where('folio', $folio)->first();
            if($fact == null){
                return response()->json([
                    'success' => false,
                    'msg' => 'No se encontró la nota de crédito de compra solicitada.'
                ]);
            }
            $doc = $this->obtenerDocumentoCompra($fact);
            if($doc == null){
                return response()->json([
                    'success' => false,
                    'msg' => 'No se pudo obtener el XML de este documento desde FacturAPI.'
                ]);
            }

            $pdf = new \SolucionTotal\CorePDF\PDF($doc['data'], 1, url('/vacio.png'), 2, $doc['ted']);
            $pdf->setResolucion(date('Y', strtotime($doc['caratula']['FchResol'])), $doc['caratula']['NroResol']);
            $pdf->construir();
            return $pdf->generar(0);
        }catch(Exception $ex){
            return $ex;
        }
    }

    public function sincronizarDocumentos(Request $request){
        try{
            // Por defecto mes actual + anterior; se puede forzar un periodo puntual con ?periodo=YYYYMM
            $periodos = ComprasUnificadasSyncService::periodosPorDefecto();
            if(isset($request->periodo))
                $periodos = [$request->periodo];
            $emisor = Ajustes::getEmisor();
            Log::info("[COMPRA] Se inicia revision de notas de credito en contribuyente ".$emisor['razon_social']);

            $sync = new ComprasUnificadasSyncService($this->facturapi);
            $recienRecibidos = $sync->sincronizar(61, $periodos);

            $users = User::all();
            foreach($recienRecibidos as $doc){
                Notification::sendNow($users, new DocumentoRecibido($doc->rut_emisor, 61, $doc->folio));
                Log::info("Se envia notificacion a usuarios por doc ". $doc->rut_emisor." - ".$doc->folio);
            }
            return response()->json([
                'success' => true,
                'msg' => 'Los documentos fueron sincronizados correctamente',
                'timestamp' => now(),
            ]);
        }catch(Exception $ex){
            Log::info("Error sincronizado las facturas de compra");
            Log::error($ex);
            return response()->json([
                'success' => false,
                'msg' => 'Hubo un error intentando sincronizar los documentos con la API',
                'timestamp' => now(),
            ]);
        }
    }

}

<?php

namespace App\Http\Controllers;

use App\CategoriaDocumento;
use App\FacturaCompra;
use App\Helpers\Ajustes;
use App\Notifications\DocumentoRecibido;
use App\PagoFacturaCompra;
use App\Proyecto;
use App\Services\ComprasUnificadasSyncService;
use App\Services\FacturapiService;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use phpseclib\Crypt\RC2;
use SolucionTotal\CoreDTE\Sii\EnvioDte;
use Yajra\DataTables\Facades\DataTables;

class FacturaCompraController extends Controller
{
    protected FacturapiService $facturapi;

    public function __construct(FacturapiService $facturapi)
    {
        $this->facturapi = $facturapi;
    }

    public function index(Request $request){
        return view('pages.compras.facturas.index');
    }

    public function indexPendientes(Request $request){
        $emisor = Ajustes::getEmisor();
        Log::info("[COMPRA] Revision de documentos pendientes en contribuyente ".$emisor['razon_social']);
        // RCV de Compra del mes actual + el anterior: el SII mantiene los documentos como
        // PENDIENTE de acuse hasta 8 días, así que a inicios de mes siguen quedando del mes previo.
        $data = $this->rcvDetalleMultiPeriodo(33, 'PENDIENTE');
        return view('pages.compras.facturas.pendientes.index', ['emisor' => $emisor, 'documentos' => $data]);
    }

    public function indexReclamadas(Request $request){
        $emisor = Ajustes::getEmisor();
        Log::info("[COMPRA] Revision de documentos reclamados en contribuyente ".$emisor['razon_social']);
        $data = $this->rcvDetalleMultiPeriodo(33, 'RECLAMADO');
        return view('pages.compras.facturas.reclamadas.index', ['emisor' => $emisor, 'documentos' => $data]);
    }

    /**
     * Junta el RCV de compra de varios periodos (por defecto mes actual + anterior) en un solo
     * arreglo, sin duplicar documentos que aparezcan en ambos periodos.
     *
     * @return array<int, object>
     */
    protected function rcvDetalleMultiPeriodo(int $tipoDoc, string $estado): array
    {
        $documentos = [];
        foreach (ComprasUnificadasSyncService::periodosPorDefecto() as $periodo) {
            $response = $this->facturapi->rcvDetalle($tipoDoc, $periodo, $estado);
            foreach ($response->data ?? [] as $fila) {
                $clave = ($fila->detRutDoc ?? '').'-'.($fila->detDvDoc ?? '').'|'.($fila->detNroDoc ?? '');
                $documentos[$clave] = $fila;
            }
        }

        return array_values($documentos);
    }

    public function show($rutEmisor, $folio){
        $categorias = CategoriaDocumento::all();
        $fact = FacturaCompra::where('rut_emisor', $rutEmisor)->where('folio', $folio)->first();
        if($fact == null){
            return back()->with('error', 'No se encontró la factura de compra solicitada.');
        }
        $pagos = PagoFacturaCompra::where('factura_compra_id', $fact->id)->get();
        $proyectos = Proyecto::orderBy('nombre', 'asc')->get();
        $doc = $this->obtenerDocumentoCompra($fact);
        if($doc == null){
            return back()->with('error', 'No se pudo obtener el XML de este documento desde FacturAPI.');
        }
        return view('pages.compras.facturas.detail', ['documento' => $doc['data'], 'factura' => $fact, 'categorias' => $categorias, 'pagos' => $pagos, 'proyectos' => $proyectos]);
    }

    /**
     * Obtiene y parsea el XML de un documento de compra recibido, vía el endpoint unificado
     * `/compras-unificadas/intercambio/{tipoDoc}/{folio}/xml` (reemplaza a
     * `/compras-intercambio/{id}/xml`, que dejó de existir en v3 — confirmado con 404 real).
     *
     * No depende de `tiene_xml` / `facturapi_compra_id` locales (que el listado unificado no
     * siempre reporta, p. ej. documentos migrados sin `origen_message_id`): pregunta directo a
     * FacturAPI y, si el XML existe, deja el registro local marcado para futuras consultas.
     *
     * @return array{data: array, ted: mixed, caratula: array}|null
     */
    protected function obtenerDocumentoCompra(FacturaCompra $fact): ?array
    {
        $result = $this->facturapi->obtenerXmlCompraUnificadaIntercambio(33, $fact->folio, $fact->rut_emisor);
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
            Log::warning('FacturaCompraController: el XML de compra unificada no contiene documentos DTE', ['folio' => $fact->folio]);
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
            // Se excluyen los documentos que el SII todavía tiene PENDIENTE de acuse de recibo:
            // se sincronizan (para tener sus datos) pero no corresponde mostrarlos como recibidos
            // hasta que se les dé acuse. El sync los desmarca en la siguiente corrida.
            $data = FacturaCompra::with('proyecto')->withSum('pagos', 'monto_pago')
                ->where('pendiente_acuse', false);

            if($request->has('feMinDate') and $request->has('feMaxDate')){
                $data->where('fecha_emision', '>=', $request->feMinDate);
                $data->where('fecha_emision', '<=', date('Y-m-d', strtotime($request->feMaxDate)));
                //$data->whereBetween('fecha_emision', [$request->feMinDate, $request->feMaxDate]);
            }
            if($request->has('proyecto')){
                if($request->proyecto == 1){
                    $data->where('proyecto_id', '!=', null);
                }else if($request->proyecto == 2){
                    $data->where('proyecto_id', null);
                }
            }
            return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function categorizarFactura(Request $request, $emisor, $folio){
        $validator = Validator::make($request->all(), [
            'categoria' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => 'false',
                'msg' => 'La información ingresada no es suficiente para completar el registro',
                'error' => $validator->errors()
            ]);
        }

        $documento = FacturaCompra::where('rut_emisor', $emisor)->where('folio', $folio)->first();
        $documento->categoria_documento_id = $request->categoria;
        $documento->save();

        return response()->json([
            'success' => true,
            'msg' => 'Documento categorizado exitosamente',
        ]);
    }

    public function asignarProyectoFactura(Request $request, $emisor, $folio){
        $validator = Validator::make($request->all(), [
            'proyecto' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => 'false',
                'msg' => 'La información ingresada no es suficiente para completar el registro',
                'error' => $validator->errors()
            ]);
        }

        $documento = FacturaCompra::where('rut_emisor', $emisor)->where('folio', $folio)->first();
        $documento->proyecto_id = $request->proyecto;
        $documento->save();

        return response()->json([
            'success' => true,
            'msg' => 'Proyecto asignado exitosamente',
        ]);
    }



    public function vistaPreviaFactura(Request $request, $rutEmisor, $tipo, $folio){
        $fact = FacturaCompra::where('rut_emisor', $rutEmisor)->where('folio', $folio)->first();
        if($fact == null){
            return response()->json([
                'status' => 500,
                'msg' => 'No se encontró la factura de compra solicitada.'
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
            $fact = FacturaCompra::where('rut_emisor', $emisor)->where('folio', $folio)->first();
            if($fact == null){
                return response()->json([
                    'success' => false,
                    'msg' => 'No se encontró la factura de compra solicitada.'
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

    public function agregarPago(Request $request, $id){
        try{
            $validator = Validator::make($request->all(), [
                'tipo_pago' => 'required',
                'fecha_pago' => 'required|date',
                'monto_pago' => 'required',
                'glosa' => '',
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => false,
                    'msg' => 'La información ingresada no es suficiente para completar el registro',
                    'error' => $validator->errors()
                ]);
            }

            $pago = new PagoFacturaCompra();
            $pago->tipo_pago = $request->tipo_pago;
            $pago->fecha_pago = date('Y-m-d', strtotime($request->fecha_pago));
            $pago->monto_pago = $request->monto_pago;
            $pago->factura_compra_id = $id;
            $glosa = $request->glosa;
            if($glosa==null)
                $glosa='';
            $pago->glosa = $glosa;
            $pago->save();
            Log::info("Pago creado hasta aqqui");
            return response()->json([
                'success' => true,
                'msg' => 'Pago agregado exitosamente a la factura de compra'
            ]);

        }catch(Exception $ex){
            Log::error($ex);
            return response()->json([
                'success' => false,
                'msg' => 'Hubo un problema al agregar el pago',
                'error' => $ex->getMessage()
            ]);
        }
    }

    public function eliminarPago(Request $request, $id){
        try{
            $pago = PagoFacturaCompra::find($id);
            $pago->delete();

            return response()->json([
                'success' => true,
                'msg' => 'Pago eliminado correctamente'
            ]);
        }catch(Exception $ex){
            return response()->json([
                'success' => false,
                'msg' => 'Hubo un problema al eliminar el pago',
                'error' => $ex->getMessage()
            ]);
        }
    }

    public function sincronizarDocumentos(Request $request){
        try{
            // Por defecto mes actual + anterior; se puede forzar un periodo puntual con ?periodo=YYYYMM
            $periodos = ComprasUnificadasSyncService::periodosPorDefecto();
            if(isset($request->periodo))
                $periodos = [$request->periodo];
            $emisor = Ajustes::getEmisor();

            Log::info("[COMPRA] Se inicia revision de facturas en contribuyente ".$emisor['razon_social']);

            $sync = new ComprasUnificadasSyncService($this->facturapi);
            $recienRecibidos = $sync->sincronizar(33, $periodos);

            $users = User::all();
            foreach($recienRecibidos as $doc){
                Notification::sendNow($users, new DocumentoRecibido($doc->rut_emisor, 33, $doc->folio));
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

    public function agregarEventoDTE(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'rut' => 'required',
                'folio' => 'required|integer|min:1',
                'evento' => 'required|in:ERM,ACD,RCD,RFP,RFT',
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => false,
                    'msg' => 'La información ingresada no es suficiente para completar el registro',
                    'error' => $validator->errors()
                ]);
            }

            $result = $this->facturapi->rcvAgregarEvento(33, $request->rut, (int) $request->folio, $request->evento);
            Log::info('Respuesta agregarEventoDTE: '.json_encode($result));
            return response()->json($result);
        }catch(Exception $ex){
            Log::error($ex);
            return response()->json([
                'success' => false,
                'msg' => 'Hubo un error al agregar el evento al DTE',
                'error' => $ex->getMessage()
            ]);
        }
    }

}

<?php

namespace App\Http\Controllers;

use App\Helpers\Ajustes;
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
        $response = $this->facturapi->listarComprasIntercambio(['tipo_doc' => 52]);
        $data = $response->data ?? [];

        return view('pages.compras.guias.index', ['documentos' => $data]);
    }

    /**
     * Este controlador nunca persiste localmente las guías de compra (siempre proxy en tiempo real),
     * así que no existe un id de FacturAPI guardado para resolver el XML directamente: se busca en el
     * listado por rut_emisor + folio para obtener el id necesario en /compras-intercambio/{id}/xml.
     */
    protected function resolverIdCompraIntercambio($rutEmisor, $tipo, $folio): ?int
    {
        $response = $this->facturapi->listarComprasIntercambio([
            'tipo_doc' => $tipo,
            'rut_emisor' => $rutEmisor,
        ]);
        foreach (($response->data ?? []) as $item) {
            if ((string) $item->folio === (string) $folio) {
                return (int) $item->id;
            }
        }

        return null;
    }

    public function show($rutEmisor, $folio){
        $id = $this->resolverIdCompraIntercambio($rutEmisor, 52, $folio);
        if($id == null){
            return back()->with('error', 'Este documento aún no tiene XML disponible (no ha sido sincronizado desde el correo de intercambio).');
        }
        $result = $this->facturapi->obtenerXmlCompraIntercambio($id);
        $EnvioDTE = new EnvioDte();
        $EnvioDTE->loadXML($result);
        $dte = $EnvioDTE->getDocumentos()[0];
        $caratula = $EnvioDTE->getCaratula();
        $data = $dte->getDatos();

        return view('pages.compras.guias.detail', ['documento' => $data]);
    }

    /*
        DESDE AQUI HACIA ABAJO ESTARAN LAS FUNCIONES DE LA API
    */
    public function getAll(){
        $response = $this->facturapi->listarComprasIntercambio(['tipo_doc' => 52]);

        return $response;
    }

    public function vistaPreviaFactura(Request $request, $rutEmisor, $tipo, $folio){
        $id = $this->resolverIdCompraIntercambio($rutEmisor, $tipo, $folio);
        if($id == null){
            return response()->json([
                'status' => 500,
                'msg' => 'Este documento aún no tiene XML disponible (no ha sido sincronizado desde el correo de intercambio).'
            ], 500);
        }
        $result = $this->facturapi->obtenerXmlCompraIntercambio($id);
        $EnvioDTE = new EnvioDte();
        $EnvioDTE->loadXML($result);
        $dte = $EnvioDTE->getDocumentos()[0];
        $caratula = $EnvioDTE->getCaratula();
        $data = $dte->getDatos();

        $pdf = new \SolucionTotal\CorePDF\PDF($data, 1, url('/vacio.png'), 2, $dte->getTED());
        //$pdf->setLeyendaImpresion('Sistema de facturacion por SoluciónTotal');
        $pdf->setResolucion(date('Y', strtotime($caratula['FchResol'])), $caratula['NroResol']);
        $pdf->construir();
        $pdf->generar(1);
    }
}

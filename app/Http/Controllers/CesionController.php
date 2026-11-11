<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use SolucionTotal\CoreDTE\Sii\EnvioDte;

class CesionController extends Controller
{
    public function cederDocumento(){
        try{

                $emisor = Ajustes::getEmisor();
            $fact = Factura::with('cliente', 'cliente.comuna')->where('folio', $folio)->first();
            $ch = curl_init( env('FACTURAPI_ENDPOINT').'documentos/generar/xml/33/'.$folio.'?contribuyente='.$emisor['rut']);
            curl_setopt( $ch, CURLOPT_POST, false);
            curl_setopt( $ch, CURLOPT_HTTPHEADER, [
                'Content-Type:application/json',
                'Authorization: Bearer '.env('FACTURAPI_TOKEN')
            ]);
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            $result = curl_exec($ch);
            curl_close($ch);
            if($result == null){
                return response()->back();
            }

            $EnvioDTE = new EnvioDte();
            $EnvioDTE->loadXML($result);
            $dte = $EnvioDTE->getDocumentos()[0];
            $caratula = $EnvioDTE->getCaratula();
            $data = $dte->getDatos();
                // armar el DTE cedido
                $DteCedido = new \SolucionTotal\CoreDTE\Sii\Factoring\DteCedido($dte);
                $DteCedido->firmar($Firma);

                // crear declaración de cesión y monto a cesionar
                $Cesion = new \SolucionTotal\CoreDTE\Sii\Factoring\Cesion($DteCedido);
                $Cesion->setCesionario([
                    'RUT' => str_replace('.', '', $factoring->rut),
                    'RazonSocial' => $factoring->razonsocial,
                    'Direccion' => $factoring->direccion. ','.$factoring->comuna->nombre,
                    'eMail' => $factoring->correo,
                ]);
                $Cesion->setCedente([
                    'eMail' => Auth::user()->email,
                    'RUTAutorizado' => [
                        'RUT' => $Firma->getID(),
                        'Nombre' => $Firma->getName(),
                    ],
                ]);
                $Cesion->firmar($Firma);

                // crear AEC
                $AEC = new \SolucionTotal\CoreDTE\Sii\Factoring\Aec();
                $AEC->setFirma($Firma);
                $AEC->agregarDteCedido($DteCedido);
                $AEC->agregarCesion($Cesion);

                // generar XML del archivo electrónico de cesión
                $aec = $AEC->generar();
                $trackid = $AEC->enviar($token);
                if($trackid == 0){
                    continue;
                }

                $fileNameXML = 'AEC_DTET'.$doc['tipo'].'F'.$doc['folio'].'.xml';
                $ruta = (\SolucionTotal\CoreDTE\Sii::getAmbiente()==1)?'/certificacion/':'/';
                $filePrefix = 'contribuyentes/'.\Auth::user()->ref_contribuyente.$ruta.date('Y').'/'.date('m').'/';
                $filePathXML = $filePrefix. $fileNameXML;
                \Storage::disk('s3')->put($filePathXML, $aec, 'public');
                $url = \Storage::disk('s3')->url($filePathXML);

        }catch(Exception $ex){
            return response()->json([
                'success' => false,
                'msg' => 'Hubo un error al generar el archivo AEC'
            ]);
        }
    }
}

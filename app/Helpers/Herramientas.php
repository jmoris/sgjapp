<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use stdClass;

class Herramientas {

    private static $tipo = [
        33 => 'Factura',
        34 => 'Factura exenta',
        39 => 'Boleta',
        52 => 'Guia despacho',
        56 => 'Nota debito',
        61 => 'Nota credito'
    ];

    private static $tipos_doc  = [
        30 => 'Factura',
        32 => 'Factura de venta bienes y servicios no afectos o exentos de iva ',
        33 => 'Factura electronica',
        34 => 'Factura no afecta o exenta electronica ',
        35 => 'Boleta ',
        38 => 'Boleta exenta ',
        39 => 'Boleta electronica ',
        40 => 'Liquidacion factura',
        41 => 'Boleta exenta electronica',
        43 => 'Liquidacion factura electronica ',
        45 => 'Factura de compra ',
        46 => 'Factura de compra electronica ',
        47 => 'Vale electronico especial',
        48 => 'Comprobantes pago electronico ',
        50 => 'Guía de despacho ',
        52 => 'Guía de despacho electronica',
        55 => 'Nota de debito ',
        56 => 'Nota de debito electronica',
        60 => 'Nota de credito ',
        61 => 'Nota de credito electronica',
        101 => 'Factura de exportacion',
        102 => 'Factura vta exenta a zona franca prim ',
        103 => 'Liquidacion ',
        104 => 'Nota de debito de exportacion ',
        105 => 'Boleta liq res 1423 76',
        106 => 'Nota de credito de exportacion ',
        108 => 'Srf solicitud registro de factura ',
        109 => 'Factura turista ',
        110 => 'Factura de exportacion electronica ',
        111 => 'Nota de debito de exportacion electronica ',
        112 => 'Nota de credito de exportacion electronica ',
        801 => 'Orden de compra ',
        802 => 'Nota de pedido ',
        803 => 'Contrato ',
        804 => 'Resolucion ',
        805 => 'Proceso chilecompra',
        806 => 'Ficha chilecompra ',
        807 => 'Dus ',
        808 => 'B l conocimiento de embarque',
        809 => 'AWB (Air Will Bill)',
        810 => 'MIC/DTA ',
        811 => 'Carta de porte ',
        812 => 'Resolución del SNA donde califica Servicios de Exportación',
        813 => 'Pasaporte ',
        814 => ' Certificado de Depósito Bolsa Prod. Chile.',
        815 => 'Vale de Prenda Bolsa Prod. Chile'
    ];

    public static function getTipoText($tipo){
        return self::$tipo[$tipo];
    }

    public static function getTipoDocumento($tipo){
        return self::$tipos_doc[$tipo];
    }

    public static function saveExecutionTime($et){
        $data = null;
        if(Storage::disk('local')->exists('execution_time.json')){
            $data = Storage::disk('local')->get('execution_time.json');
            $data = json_decode($data);
            $data->tiempo = ($data->tiempo + $et) / 2;

            Storage::disk('local')->put('execution_time.json', json_encode($data));
        }else{
            $obj = new stdClass;
            $obj->tiempo = 0;
            Storage::disk('local')->put('execution_time.json', json_encode($obj));
        }

    }

    public static function getExecutionTime(){
        $data = Storage::disk('local')->get('execution_time.json');
        $data = json_decode($data);
        return round($data->tiempo, 1);
    }

    public static function formatRut($rut){
        $rutE = explode('-', $rut);
        $primero = substr($rutE[0], 0, 2);
        $fin = 2;
        if(strlen($rutE[0]) == 7){
            $primero = substr($rutE[0], 0, 1);
            $fin = 1;
        }
        $rutF = $primero.'.'.substr($rutE[0], $fin, 3).'.'.substr($rutE[0], $fin+3, 3).'-'.$rutE[1];
        return $rutF;
    }

    public static function calcularNetoIVA($total, $tasa = null)
    {
        if ($tasa === 0 or $tasa === false)
            return [0, 0];
        if ($tasa === null)
            $tasa = \SolucionTotal\CoreDTE\Sii::getIVA();
        // WARNING: el IVA obtenido puede no ser el NETO*(TASA/100)
        // se calcula el monto neto y luego se obtiene el IVA haciendo la resta
        // entre el total y el neto, ya que hay casos de borde como:
        //  - BRUTO:   680 => NETO:   571 e IVA:   108 => TOTAL:   679
        //  - BRUTO: 86710 => NETO: 72866 e IVA: 13845 => TOTAL: 86711
        //$neto = round($total / (1+($tasa/100)));
        $neto = round($total / ((100+$tasa) / 100));
        $iva = $total - $neto;
        return [$neto, $iva];
    }

    public static function sanitizarString($string) {
        $string = trim($string);

        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );

        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );

        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );

        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );

        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );

        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C',),
            $string
        );

        return $string;
    }

}
?>

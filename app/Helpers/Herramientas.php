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

    public static function getTipoText($tipo){
        return self::$tipo[$tipo];
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

<?php
namespace App\Helpers;

use App\Config;
use App\Models\Contribuyente;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SolucionTotal\CoreDTE\FirmaElectronica as CoreDTEFirmaElectronica;

class SII {

    public static function temporalPEM(){
        $cert = [];
        $pass = "Moris.234";
        //$p12 = Storage::disk('local')->get('cert.p12');
        $p12 = file_get_contents(storage_path('app/cert.p12'));
        openssl_pkcs12_read($p12, $cert, $pass);

        if (!Storage::disk('local')->exists('cert.crt.pem')) {
            Storage::disk('local')->put('cert.crt.pem',  $cert['cert']);
            Storage::disk('local')->put('cert.key.pem',  $cert['pkey']);
        }
        //$urlcert = Storage::disk('local')->url('contribuyentes/'.$contribuyente->id.'/cert.crt.pem');
        //$urlkey = Storage::disk('local')->url('contribuyentes/'.$contribuyente->id.'/cert.key.pem');
        $urlcert = storage_path('app/cert.crt.pem');
        $urlkey = storage_path('app/cert.key.pem');
        \SolucionTotal\CoreDTE\Sii::setAmbiente(env('AMBIENTE', 1));
        $firma = new CoreDTEFirmaElectronica([
            'data' => $p12,
            'pass' => $pass,
            'path_pkey' => $urlkey,
            'path_cert' => $urlcert
        ]);
        return $firma;
    }

    public static function statusCert(){
        try{
            $cert = [];
            $config =Config::where('key', 'password_cert')->first();
            $pass = $config->value;
            if($pass == null){
                throw new Exception("Clave incorrecta o nula");
            }
            $p12 = Storage::get('app/cert.p12');
            openssl_pkcs12_read($p12, $cert, $pass);
            $firma = new CoreDTEFirmaElectronica([
                'data' => $p12,
                'pass' => $pass,
            ]);
            $desde = $firma->getFrom();
            $hasta = $firma->getTo();
            if(date('Y-m-d H:i:s') < $hasta){
                return [
                    'desde' => $desde,
                    'hasta' => $hasta,
                    'valido' => true
                ];
            }else{
                return [
                    'desde' => $desde,
                    'hasta' => $hasta,
                    'valido' => false
                ];
            }
        }catch(Exception $ex){
            Log::error("El archivo del certificado no se encontro o la clave es incorrecta.");
            return [
                'desde' => date('Y-m-d'),
                'hasta' => date('Y-m-d'),
                'valido' => false
        ];
        }
    }

    public static function getCookies($contribuyente){
        $cookiestxt = "";
        list($rut, $dv) = explode('-', str_replace('.', '', $contribuyente->rut));
        $urlcert = storage_path('app/contribuyentes/'.$contribuyente->id.'/cert.crt.pem');
        $urlkey = storage_path('app/contribuyentes/'.$contribuyente->id.'/cert.crt.pem');
        try{
            $url = 'https://'.\SolucionTotal\CoreDTE\Sii::getServidor().'.sii.cl/cvc_cgi/dte/of_solicita_folios';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSLCERT, $urlcert);
            curl_setopt($ch, CURLOPT_SSLKEY, $urlkey);
            curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $contribuyente->config->pass_certificado);
            curl_setopt($ch, CURLOPT_SSLKEYPASSWD, $contribuyente->config->pass_certificado);
            curl_setopt($ch, CURLOPT_SSLVERSION, 6);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Origin: https://'.\SolucionTotal\CoreDTE\Sii::getServidor().'.sii.cl',
                'Referer:  https://'.\SolucionTotal\CoreDTE\Sii::getServidor().'.sii.cl/cvc_cgi/dte/of_solicita_folios'
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "RUT_EMP=".$rut."&DV_EMP=".$dv."&ACEPTAR=Continuar");
            curl_setopt($ch, CURLOPT_HEADER, 1);
            $result = curl_exec($ch);
            echo $result;
            preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
            $cookies = array();
            $cookiestxt = "";
            foreach($matches[1] as $item) {
                parse_str($item, $cookie);
                $cookies = array_merge($cookies, $cookie);
                $cookiestxt .= $item."; ";
            }
            $cookiestxt .= 'cert_Origin=www.sii.cl; NETSCAPE_LIVEWIRE.locexp='.rawurlencode(gmdate('D, d M Y H:i:s \G\M\T', time()+10800)).'; ';
            return $cookiestxt;
        }catch(Exception $ex){
            return $ex;
        }
    }
}

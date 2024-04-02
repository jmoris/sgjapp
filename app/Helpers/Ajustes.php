<?php

namespace App\Helpers;

use App\Config;

class Ajustes
{
    public static function getEmisor(){
        $configs = Config::where('key', 'like', 'emisor_%')->orderBy('id', 'asc')->get()->select('id', 'key', 'value');
        $emisor = [
            'razon_social' => $configs[0]['value'],
            'rut' => $configs[1]['value'],
            'giro' => $configs[7]['value'],
            'direccion' => $configs[2]['value'],
            'comuna' => $configs[3]['value'],
            'telefono' => $configs[4]['value'],
            'email' => $configs[5]['value'],
            'web' => $configs[6]['value'],
        ];
        return $emisor;
    }
}

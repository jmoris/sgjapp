<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Factura extends Model
{
    use HasFactory, UsesTenantConnection;

    public function cliente(){
        return $this->hasOne(Cliente::class, 'id', 'cliente_id');
    }

    public function proyecto(){
        return $this->hasOne(Proyecto::class, 'id', 'proyecto_id');
    }

    public function lineas(){
        return $this->hasMany(LineaFactura::class);
    }
}

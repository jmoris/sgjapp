<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class FacturaCompra extends Model
{
    use HasFactory, UsesTenantConnection;

    public function proyecto(){
        return $this->hasOne(Proyecto::class, 'id', 'proyecto_id');
    }

    public function pagos(){
        return $this->hasMany(PagoFacturaCompra::class, 'factura_compra_id');
    }
}

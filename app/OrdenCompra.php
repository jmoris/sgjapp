<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class OrdenCompra extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $fillable = [
        'monto_neto', 'monto_iva', 'monto_total',
    ];

    public function proveedor(){
        return $this->hasOne(Proveedor::class, 'id', 'proveedor_id');
    }

    public function lineas(){
        return $this->hasMany(LineaOC::class);
    }

    public function usuario(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}

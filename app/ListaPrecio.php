<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ListaPrecio extends Model
{
    use HasFactory, UsesTenantConnection;

    public function productos(){
        return $this->belongsToMany(
            Producto::class,
            'lista_precios_productos',
            'lista_precio_id',
            'producto_id')
            ->withPivot(['precio', 'activo']);
    }
}

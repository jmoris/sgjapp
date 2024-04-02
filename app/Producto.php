<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    public function unidades(){
        return $this->hasOne(Unidad::class, 'id', 'unidad_id');
    }

    public function precios(){
        return $this->hasMany(ListaPrecioProducto::class);
    }

    public function lista_precios(){
        return $this->belongsToMany(ListaPrecio::class, 'lista_precios_productos', 'producto_id', 'lista_precio_id');
    }

    public function proveedores(){
        return $this->belongsToMany(Proveedor::class)->withPivot(['precio']);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;


class ListaPrecioProducto extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $table = 'lista_precios_productos';
}

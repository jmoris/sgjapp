<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListaPrecioProducto extends Model
{
    use HasFactory;
    protected $table = 'lista_precios_productos';
}

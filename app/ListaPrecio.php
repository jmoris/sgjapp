<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListaPrecio extends Model
{
    use HasFactory;

    public function productos(){
        return $this->hasMany(Producto::class);
    }
}

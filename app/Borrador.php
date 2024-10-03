<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Borrador extends Model
{
    use HasFactory, UsesTenantConnection;

    public function lineas(){
        return $this->hasMany(LineaBorrador::class);
    }

    public function datos(){
        return $this->hasMany(InfoBorrador::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Cliente extends Model
{
    use HasFactory, UsesTenantConnection;

    public function comuna(){
        return $this->hasOne(Comuna::class, 'id', 'comuna_id');
    }

    public function productos(){
        return $this->belongsToMany(Producto::class)->withPivot(['precio']);
    }
}

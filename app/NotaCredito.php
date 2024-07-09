<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class NotaCredito extends Model
{
    use HasFactory, UsesTenantConnection;

    public function cliente(){
        return $this->hasOne(Cliente::class, 'id', 'cliente_id');
    }

    public function lineas(){
        return $this->hasMany(LineaNC::class);
    }
}

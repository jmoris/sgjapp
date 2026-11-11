<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Cesion extends Model
{
    use HasFactory, UsesTenantConnection;

    public function factoring()
    {
        return $this->belongsTo(Factoring::class, 'factoring_id', 'id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id');
    }

    public function aecs()
    {
        return $this->hasMany(AEC::class, 'cesion_id', 'id');
    }
}

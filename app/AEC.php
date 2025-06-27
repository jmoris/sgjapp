<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class AEC extends Model
{
    use HasFactory, UsesTenantConnection;

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    public function cesion()
    {
        return $this->belongsTo(Cesion::class);
    }
}

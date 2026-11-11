<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Factoring extends Model
{
    use HasFactory, UsesTenantConnection;

    public function comuna()
    {
        return $this->hasOne(Comuna::class, 'id', 'comuna_id');
    }
    public function cesiones()
    {
        return $this->hasMany(Cesion::class, 'factoring_id', 'id');
    }
}

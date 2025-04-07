<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Permiso extends Model
{
    use HasFactory, UsesTenantConnection;
    public $timestamps = false;

    public function roles(){
        return $this->belongsToMany(Role::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Proyecto extends Model
{
    use HasFactory, UsesTenantConnection;

    public function proyectopadre(){
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function subproyectos(){
        return $this->hasMany(Proyecto::class, 'proyecto_id', 'id');
    }

    public function adjuntos(){
        return $this->hasMany(Proyecto::class, 'proyecto_id', 'id');
    }

}

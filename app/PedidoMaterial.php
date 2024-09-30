<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class PedidoMaterial extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $fillable = [
        'peso_total', 'peso_faltante', 'peso_recibido',
    ];

    public function mandante(){
        return $this->hasOne(Proveedor::class, 'id', 'cliente_id');
    }

    public function proyecto(){
        return $this->hasOne(Proyecto::class, 'id', 'proyecto_id');
    }

    public function lineas(){
        return $this->hasMany(LineaPM::class);
    }

    public function usuario(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}

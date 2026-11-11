<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ListaCorreo extends Model
{
    use HasFactory, UsesTenantConnection;

    public function usuario(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}

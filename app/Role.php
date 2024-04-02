<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function permisos(){
        return $this->belongsToMany(Permiso::class, 'permisos_roles', 'role_id', 'permiso_id');
    }
}

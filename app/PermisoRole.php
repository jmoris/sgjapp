<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class PermisoRole extends Model
{
    use HasFactory, UsesTenantConnection;
    protected $table = 'permisos_roles';
}

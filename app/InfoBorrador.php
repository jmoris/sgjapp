<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class InfoBorrador extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $primaryKey = null;
    public $incrementing = false;
}

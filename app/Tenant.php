<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;

class Tenant extends \Spatie\Multitenancy\Models\Tenant
{
    use HasFactory, UsesLandlordConnection;

    protected $fillable = ['rut', 'name', 'domain', 'database', 'facturapi_token', 'facturapi_tenant_id', 'facturapi_webhook_secret'];

    protected $casts = [
        'facturapi_token' => 'encrypted',
        'facturapi_webhook_secret' => 'encrypted',
    ];

    protected static function booted(){
        static::creating(function(Tenant $tenant){
            $query = "CREATE DATABASE ".$tenant->database;
            DB::statement($query);
        });

    }

}

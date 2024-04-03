<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Tenant extends \Spatie\Multitenancy\Models\Tenant
{
    use HasFactory;

    protected $fillable = ['rut', 'name', 'domain', 'database'];

    protected static function booted(){
        static::creating(function(Tenant $tenant){

            $query = "CREATE DATABASE 'sgjapp_".$tenant->database."'";
            DB::statement($query);
        });

        static::creating(function(Tenant $tenant){
            Artisan::call("tenant:aristan \"migrate --database=tenant\"");
        });
    }

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class FacturapiWebhookDelivery extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $fillable = ['delivery_id', 'event_type'];
}

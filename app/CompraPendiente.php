<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * Documento de compra que el SII tiene como PENDIENTE de acuse de recibo, según
 * /compras-unificadas (fecha_acuse y fecha_reclamado nulos, evento_receptor sin ser pago
 * contado ni acuse automático). La sincronización mantiene esta tabla (ver
 * ComprasUnificadasSyncService::sincronizar()) y la pantalla de compras pendientes lee
 * directo de aquí — no consulta al SII en vivo.
 */
class CompraPendiente extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $guarded = [];

    protected $casts = [
        'fecha_emision' => 'date',
        'last_seen_at' => 'datetime',
    ];
}

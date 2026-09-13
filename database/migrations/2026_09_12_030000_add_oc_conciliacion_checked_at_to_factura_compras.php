<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Marca cuándo se evaluó por última vez la conciliación de OC de una factura de compra que
     * NO calzó (monto o razón social distintos). Sin esto, ComprasUnificadasSyncService repetía
     * el mismo warning "no se concilia automáticamente" en cada corrida del cron (cada 30 min,
     * para siempre) para facturas cuyo desajuste nunca cambia, inundando el log real.
     */
    public function up(): void
    {
        Schema::table('factura_compras', function (Blueprint $table) {
            $table->timestamp('oc_conciliacion_checked_at')->nullable()->after('oc_id');
        });
    }

    public function down(): void
    {
        Schema::table('factura_compras', function (Blueprint $table) {
            $table->dropColumn('oc_conciliacion_checked_at');
        });
    }
};

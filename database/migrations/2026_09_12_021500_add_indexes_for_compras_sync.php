<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ComprasUnificadasSyncService::sincronizar() hace, por cada fila que trae FacturAPI
     * (cientos por periodo por tenant), un lookup por rut_emisor+folio contra factura_compras /
     * nota_credito_compras, y por cada fila con orden_compra_folio_ref un lookup por folio
     * contra orden_compras. Ninguna de las tres tenía índice más allá de la PK: con tablas ya
     * en varios miles de filas, cada corrida del cron (cada 30 min, x3 tenants) degenera en
     * full table scans repetidos que pueden hacer que la sincronización no alcance a terminar
     * dentro de la ventana de `withoutOverlapping`, dejando el mutex tomado y saltando la
     * corrida siguiente en silencio.
     *
     * No se agrega UNIQUE (aunque semánticamente rut_emisor+folio debería serlo) para no
     * arriesgar que la migración falle si ya existen duplicados históricos.
     */
    public function up(): void
    {
        Schema::table('factura_compras', function (Blueprint $table) {
            $table->index(['rut_emisor', 'folio']);
        });

        Schema::table('nota_credito_compras', function (Blueprint $table) {
            $table->index(['rut_emisor', 'folio']);
        });

        Schema::table('orden_compras', function (Blueprint $table) {
            $table->index('folio');
        });
    }

    public function down(): void
    {
        Schema::table('factura_compras', function (Blueprint $table) {
            $table->dropIndex(['rut_emisor', 'folio']);
        });

        Schema::table('nota_credito_compras', function (Blueprint $table) {
            $table->dropIndex(['rut_emisor', 'folio']);
        });

        Schema::table('orden_compras', function (Blueprint $table) {
            $table->dropIndex(['folio']);
        });
    }
};

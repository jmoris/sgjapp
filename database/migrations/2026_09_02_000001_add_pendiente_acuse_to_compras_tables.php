<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Marca los documentos de compra que el SII todavía tiene como PENDIENTE de acuse de
     * recibo. Se sincronizan igual (para tener sus datos listos) pero no se muestran en el
     * listado principal hasta que se les dé acuse; el sync los desmarca automáticamente.
     */
    public function up(): void
    {
        Schema::table('factura_compras', function (Blueprint $table) {
            $table->boolean('pendiente_acuse')->default(false)->after('tiene_xml');
        });

        Schema::table('nota_credito_compras', function (Blueprint $table) {
            $table->boolean('pendiente_acuse')->default(false)->after('tiene_xml');
        });
    }

    public function down(): void
    {
        Schema::table('factura_compras', function (Blueprint $table) {
            $table->dropColumn('pendiente_acuse');
        });

        Schema::table('nota_credito_compras', function (Blueprint $table) {
            $table->dropColumn('pendiente_acuse');
        });
    }
};

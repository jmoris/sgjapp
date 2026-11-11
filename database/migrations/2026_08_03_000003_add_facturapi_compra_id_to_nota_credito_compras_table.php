<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('nota_credito_compras', function (Blueprint $table) {
            $table->unsignedBigInteger('facturapi_compra_id')->nullable()->after('folio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nota_credito_compras', function (Blueprint $table) {
            $table->dropColumn('facturapi_compra_id');
        });
    }
};

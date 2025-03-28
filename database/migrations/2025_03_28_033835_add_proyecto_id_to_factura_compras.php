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
        Schema::table('factura_compras', function (Blueprint $table) {
            $table->foreignIdFor(\App\Proyecto::class)->nullable()->after('categoria_documento_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('factura_compras', function (Blueprint $table) {
            $table->dropColumn('proyecto_id');
        });
    }
};

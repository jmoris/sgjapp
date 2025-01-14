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
        Schema::create('nota_credito_compras', function (Blueprint $table) {
            $table->id();
            $table->string('rut_emisor');
            $table->string('razon_social_emisor');
            $table->bigInteger('folio');
            $table->date('fecha_emision');
            $table->bigInteger('monto_neto');
            $table->bigInteger('monto_iva');
            $table->bigInteger('monto_total');
            $table->string('glosa')->nullable();
            $table->boolean('tiene_xml')->default(false);
            $table->foreignIdFor(\App\CategoriaDocumento::class)->nullable()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nota_credito_compras');
    }
};

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
        Schema::create('linea_facturas', function (Blueprint $table) {
            $table->id();
            $table->string('sku');
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->float('cantidad');
            $table->string('unidad');
            $table->bigInteger('precio_unitario');
            $table->smallInteger('descuento');
            $table->foreignIdFor(\App\Factura::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linea_facturas');
    }
};

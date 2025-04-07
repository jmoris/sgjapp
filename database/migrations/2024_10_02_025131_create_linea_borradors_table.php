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
        Schema::create('linea_borradors', function (Blueprint $table) {
            $table->string('sku')->nullable();
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->string('unidad');
            $table->float('cantidad');
            $table->float('stock')->default(0);
            $table->float('recibido')->default(0);
            $table->bigInteger('precio_unitario');
            $table->float('largo');
            $table->float('ancho');
            $table->float('peso');

            $table->smallInteger('descuento');
            $table->foreignIdFor(\App\Borrador::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linea_borradors');
    }
};

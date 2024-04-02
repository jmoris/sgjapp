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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('sku');
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->unsignedBigInteger('categoria_id');
            $table->unsignedBigInteger('unidad_id');
            $table->boolean('es_afecto')->default(false);
            $table->boolean('se_compra')->default(false);;
            $table->boolean('se_vende')->default(false);;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};

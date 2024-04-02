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
        Schema::create('lista_precios_productos', function (Blueprint $table) {
            $table->foreignIdFor(\App\ListaPrecio::class)->constrained();
            $table->foreignIdFor(\App\Producto::class)->constrained();
            $table->bigInteger('precio');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lista_precios_productos');
    }
};

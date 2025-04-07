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
        Schema::create('producto_proveedor', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Producto::class)->constrained();
            $table->foreignIdFor(\App\Proveedor::class)->constrained();
            $table->bigInteger('precio');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_proveedor');
    }
};

<?php

use App\Cliente;
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
        Schema::create('pedido_materials', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('folio');
            $table->foreignIdFor(Cliente::class)->constrained();
            $table->dateTime('fecha_emision');
            $table->string('materia');
            $table->unsignedBigInteger('proyecto_id');
            $table->smallInteger('estado')->default(0);
            $table->bigInteger('peso_total');
            $table->bigInteger('peso_faltante');
            $table->bigInteger('peso_recibido');
            $table->string('glosa')->nullable();
            $table->foreignIdFor(\App\User::class)->constrained();
            $table->smallInteger('rev')->default(1);
            $table->boolean('rev_activa')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido_materials');
    }
};

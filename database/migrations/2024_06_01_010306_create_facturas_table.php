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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('folio');
            $table->foreignIdFor(Cliente::class)->constrained();
            $table->dateTime('fecha_emision');
            $table->smallInteger('tipo_pago');
            $table->text('estado')->default('000');
            $table->smallInteger('tipo_descuento')->nullable();
            $table->bigInteger('descuento')->default(0);
            $table->bigInteger('monto_neto');
            $table->bigInteger('monto_iva');
            $table->bigInteger('monto_total');
            $table->string('glosa')->nullable();
            $table->foreignIdFor(\App\User::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};

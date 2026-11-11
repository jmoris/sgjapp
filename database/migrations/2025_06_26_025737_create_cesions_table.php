<?php

use App\Cliente;
use App\Factoring;
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
        Schema::create('cesions', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_cesion');
            $table->bigInteger('monto_cesion');
            $table->smallInteger('estado')->default(0); // 0: Enviado, 1: Aceptado, 2: Rechazado
            $table->foreignIdFor(Factoring::class, 'factoring_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignIdFor(Cliente::class, 'cliente_id')
                ->constrained()
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cesions');
    }
};

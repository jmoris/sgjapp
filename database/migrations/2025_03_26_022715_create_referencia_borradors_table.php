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
        Schema::create('referencia_borradors', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('tipo');
            $table->string('folio');
            $table->date('fecha');
            $table->string('razon');
            $table->smallInteger('codigo')->nullable();
            $table->foreignIdFor(\App\Borrador::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referencia_borradors');
    }
};
